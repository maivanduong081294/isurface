<?php
/**
 * Full-width UX Blocks before / after the content of post categories,
 * product categories, the shop, blog posts and products.
 *
 * Where:
 * - Categories / shop: before = after the blog header or before the product
 *   sorting toolbar; after = at the end of the page, after the posts and the
 *   related products (silver_after_blog) or after the products, the category
 *   description and the FAQs (silver_after_category_layout). On every page of
 *   the category (pagination included).
 * - Blog posts: before = under the page header, above the post and the
 *   sidebar; after = at the end of the page (silver_after_blog).
 * - Products: before = above the gallery and the summary
 *   (silver_before_product_page); after = after the tabs and the related
 *   products (silver_after_product_page).
 *
 * Which block (each field: "" = Default, -1 = Hide, or a UX Block ID):
 * - Category / shop page: its own field, else the theme option of the archive.
 * - Post / product: its own field, else the "items" field of its categories
 *   (primary category first, then the other ones, then their parents), else
 *   the theme option of the single page.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

// Category archive / shop page (term meta / shop page meta), and post / product (post meta).
const SILVER_TERM_BLOCK_BEFORE = 'silver_before_content_block';
const SILVER_TERM_BLOCK_AFTER  = 'silver_after_content_block';
// Category: block of the posts / products of the category (term meta).
const SILVER_TERM_ITEM_BLOCK_BEFORE = 'silver_item_before_content_block';
const SILVER_TERM_ITEM_BLOCK_AFTER  = 'silver_item_after_content_block';
// "Hide" value of the fields.
const SILVER_CONTENT_BLOCK_HIDE = '-1';

/**
 * Taxonomies that get the fields.
 *
 * @return string[]
 */
function silver_term_blocks_taxonomies() {
	$taxonomies = array( 'category' );
	if ( taxonomy_exists( 'product_cat' ) ) {
		$taxonomies[] = 'product_cat';
	}

	return apply_filters( 'silver_term_blocks_taxonomies', $taxonomies );
}

/**
 * Post types that get the fields, with the category taxonomy of their items.
 *
 * @return array<string, string> Post type => taxonomy.
 */
function silver_content_blocks_post_types() {
	$types = array( 'post' => 'category' );
	if ( post_type_exists( 'product' ) ) {
		$types['product'] = 'product_cat';
	}

	return apply_filters( 'silver_content_blocks_post_types', $types );
}

/**
 * Whether an ID is a published UX Block.
 *
 * @param int $block_id Post ID.
 * @return bool
 */
function silver_is_content_block( $block_id ) {
	return $block_id > 0 && 'blocks' === get_post_type( $block_id ) && 'publish' === get_post_status( $block_id );
}

/**
 * A field value: null = Default (look further), 0 = Hide, or a UX Block ID.
 *
 * @param mixed $value Stored value.
 * @return int|null
 */
function silver_content_block_value( $value ) {
	if ( SILVER_CONTENT_BLOCK_HIDE === (string) $value ) {
		return 0;
	}
	$block_id = absint( $value );

	return silver_is_content_block( $block_id ) ? $block_id : null;
}

/**
 * Theme option of a page type.
 *
 * @param string $page     blog_archive, blog_single, product_archive or product_single.
 * @param string $position before or after.
 * @return int UX Block ID, 0 = none.
 */
function silver_content_block_option( $page, $position ) {
	$block_id = absint( get_theme_mod( $page . '_' . $position . '_block', 0 ) );

	return silver_is_content_block( $block_id ) ? $block_id : 0;
}

/**
 * Categories of a post, primary first (Rank Math), then the other ones,
 * then their parents (closest first).
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy.
 * @return int[] Term IDs.
 */
function silver_content_block_post_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$ids = wp_list_pluck( $terms, 'term_id' );

	$primary = (int) get_post_meta( $post_id, 'rank_math_primary_' . $taxonomy, true );
	if ( $primary && in_array( $primary, $ids, true ) ) {
		$ids = array_merge( array( $primary ), array_diff( $ids, array( $primary ) ) );
	}

	$all = $ids;
	foreach ( $ids as $term_id ) {
		$all = array_merge( $all, get_ancestors( $term_id, $taxonomy, 'taxonomy' ) );
	}

	return array_values( array_unique( array_map( 'intval', $all ) ) );
}

/**
 * The UX Block of the page being shown.
 *
 * @param string $position before or after.
 * @return int UX Block ID, 0 = none.
 */
function silver_content_block_id( $position ) {
	$before   = 'before' === $position;
	$own_key  = $before ? SILVER_TERM_BLOCK_BEFORE : SILVER_TERM_BLOCK_AFTER;
	$item_key = $before ? SILVER_TERM_ITEM_BLOCK_BEFORE : SILVER_TERM_ITEM_BLOCK_AFTER;

	// Shop page.
	if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$value = silver_content_block_value( get_post_meta( wc_get_page_id( 'shop' ), $own_key, true ) );
		return null !== $value ? $value : silver_content_block_option( 'product_archive', $position );
	}

	// Category archives.
	if ( is_category() || is_tax( 'product_cat' ) ) {
		$term = get_queried_object();
		if ( ! $term instanceof WP_Term || ! in_array( $term->taxonomy, silver_term_blocks_taxonomies(), true ) ) {
			return 0;
		}
		$value = silver_content_block_value( get_term_meta( $term->term_id, $own_key, true ) );
		if ( null !== $value ) {
			return $value;
		}
		return silver_content_block_option( 'product_cat' === $term->taxonomy ? 'product_archive' : 'blog_archive', $position );
	}

	// Blog posts and products.
	$types = silver_content_blocks_post_types();
	if ( is_singular( array_keys( $types ) ) ) {
		$post_id   = get_queried_object_id();
		$post_type = get_post_type( $post_id );

		$value = silver_content_block_value( get_post_meta( $post_id, $own_key, true ) );
		if ( null !== $value ) {
			return $value;
		}
		foreach ( silver_content_block_post_terms( $post_id, $types[ $post_type ] ) as $term_id ) {
			$value = silver_content_block_value( get_term_meta( $term_id, $item_key, true ) );
			if ( null !== $value ) {
				return $value;
			}
		}
		return silver_content_block_option( 'product' === $post_type ? 'product_single' : 'blog_single', $position );
	}

	return 0;
}

/**
 * The UX Block of the page being shown, as HTML.
 *
 * @param string $key SILVER_TERM_BLOCK_BEFORE or SILVER_TERM_BLOCK_AFTER.
 * @return string
 */
function silver_term_block_html( $key ) {
	$position = SILVER_TERM_BLOCK_BEFORE === $key ? 'before' : 'after';
	$block_id = silver_content_block_id( $position );
	if ( ! $block_id ) {
		return '';
	}

	$html = do_shortcode( '[block id="' . $block_id . '"]' );
	if ( '' === trim( $html ) ) {
		return '';
	}

	return '<div class="term-content-block term-content-block--' . $position . '">' . $html . '</div>';
}

/** Before content. */
function silver_term_block_before() {
	echo silver_term_block_html( SILVER_TERM_BLOCK_BEFORE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- UX Block output.
}

/** After content. */
function silver_term_block_after() {
	echo silver_term_block_html( SILVER_TERM_BLOCK_AFTER ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- UX Block output.
}

/** Register once the queried page is known. The shop toolbar runs at 10. */
function silver_term_block_register() {
	if ( is_tax( 'product_cat' ) || ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) ) {
		add_action( 'silver_after_header', 'silver_term_block_before', 9 );
		add_action( 'silver_after_category_layout', 'silver_term_block_after', 30 );
	} elseif ( is_category() || is_singular( 'post' ) ) {
		// Under the page header (single post header: silver_after_header 10).
		add_action( 'silver_after_header', 'silver_term_block_before', 50 );
		add_action( 'silver_after_blog', 'silver_term_block_after', 30 );
	} elseif ( is_singular( 'product' ) ) {
		// After the custom "top content" of the product (10).
		add_action( 'silver_before_product_page', 'silver_term_block_before', 20 );
		add_action( 'silver_after_product_page', 'silver_term_block_after', 20 );
	}
}
add_action( 'wp', 'silver_term_block_register' );

/* --------------------------------------------------------------- Admin */

/**
 * A UX Block select: Default, Hide, then the blocks.
 *
 * @param string $name     Field name.
 * @param mixed  $selected Stored value.
 * @return string
 */
function silver_content_block_select( $name, $selected ) {
	static $blocks = null;
	if ( null === $blocks ) {
		$blocks = get_posts( array(
			'post_type'      => 'blocks',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		) );
	}

	$selected = (string) $selected;
	$id       = str_replace( '_', '-', $name );
	$html     = '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="silver-term-block-select">';
	$html    .= '<option value="">' . esc_html__( '— Default —', 'silver' ) . '</option>';
	$html    .= '<option value="' . esc_attr( SILVER_CONTENT_BLOCK_HIDE ) . '"' . selected( $selected, SILVER_CONTENT_BLOCK_HIDE, false ) . '>' . esc_html__( '— Hide —', 'silver' ) . '</option>';
	foreach ( $blocks as $block ) {
		$title = '' !== $block->post_title ? $block->post_title : '#' . $block->ID;
		$html .= '<option value="' . esc_attr( $block->ID ) . '"' . selected( $selected, (string) $block->ID, false ) . '>' . esc_html( $title ) . '</option>';
	}
	$html .= '</select>';

	if ( ! $blocks ) {
		$html .= '<p class="description"><em>' . esc_html__( 'No UX Block yet (UX Blocks > Add New).', 'silver' ) . '</em></p>';
	}

	return $html;
}

/**
 * The selects of a category (or of the shop page).
 *
 * @param WP_Term|WP_Post|string|null $object Category or shop page being edited; the taxonomy name on the Add screen.
 */
function silver_term_blocks_fields( $object = null ) {
	$is_term  = $object instanceof WP_Term;
	$is_post  = $object instanceof WP_Post;
	$taxonomy = $is_term ? $object->taxonomy : ( is_string( $object ) ? $object : '' );
	$default  = __( 'Default: the theme options.', 'silver' );

	$fields = array(
		SILVER_TERM_BLOCK_BEFORE => array(
			'label' => __( 'Before content (UX Block)', 'silver' ),
			'help'  => __( 'Full width, under the page header and before the list.', 'silver' ) . ' ' . $default,
		),
		SILVER_TERM_BLOCK_AFTER  => array(
			'label' => __( 'After content (UX Block)', 'silver' ),
			'help'  => __( 'Full width, at the end of the page, before the footer.', 'silver' ) . ' ' . $default,
		),
	);

	// Categories: the block of their posts / products.
	if ( ! $is_post && '' !== $taxonomy ) {
		$products = 'product_cat' === $taxonomy;
		$items    = $products
			? __( 'Used by the products of this category (and of its subcategories) that keep "Default". Default: the theme options.', 'silver' )
			: __( 'Used by the posts of this category (and of its subcategories) that keep "Default". Default: the theme options.', 'silver' );

		$fields[ SILVER_TERM_ITEM_BLOCK_BEFORE ] = array(
			'label' => $products ? __( 'Before content of its products (UX Block)', 'silver' ) : __( 'Before content of its posts (UX Block)', 'silver' ),
			'help'  => $items,
		);
		$fields[ SILVER_TERM_ITEM_BLOCK_AFTER ]  = array(
			'label' => $products ? __( 'After content of its products (UX Block)', 'silver' ) : __( 'After content of its posts (UX Block)', 'silver' ),
			'help'  => $items,
		);
	}

	$nonce = wp_nonce_field( 'silver_term_blocks', 'silver_term_blocks_nonce', true, false );
	$first = true;

	foreach ( $fields as $key => $field ) {
		$selected = '';
		if ( $is_term ) {
			$selected = get_term_meta( $object->term_id, $key, true );
		} elseif ( $is_post ) {
			$selected = get_post_meta( $object->ID, $key, true );
		}
		$select = silver_content_block_select( $key, $selected );
		$label  = '<label for="' . esc_attr( str_replace( '_', '-', $key ) ) . '">' . esc_html( $field['label'] ) . '</label>';

		if ( $is_term ) {
			echo '<tr class="form-field"><th scope="row">' . $label . '</th><td>' . ( $first ? $nonce : '' ) . $select . '<p class="description">' . esc_html( $field['help'] ) . '</p></td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		} else {
			echo '<div class="form-field">' . ( $first ? $nonce : '' ) . $label . $select . '<p>' . esc_html( $field['help'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
		$first = false;
	}
}

/**
 * Add category screen.
 *
 * @param string $taxonomy Taxonomy.
 */
function silver_term_blocks_add_fields( $taxonomy ) {
	silver_term_blocks_fields( $taxonomy );
}

/**
 * Edit category screen.
 *
 * @param WP_Term $term Category.
 */
function silver_term_blocks_edit_fields( $term ) {
	silver_term_blocks_fields( $term );
}

/**
 * Saves the fields of a category.
 *
 * @param int $term_id Term ID.
 */
function silver_term_blocks_save( $term_id ) {
	if ( current_user_can( 'edit_term', $term_id ) ) {
		silver_save_content_blocks( 'term', $term_id, array( SILVER_TERM_BLOCK_BEFORE, SILVER_TERM_BLOCK_AFTER, SILVER_TERM_ITEM_BLOCK_BEFORE, SILVER_TERM_ITEM_BLOCK_AFTER ) );
	}
}

/**
 * Meta box of the shop page.
 *
 * @param WP_Post $post Page.
 */
function silver_shop_blocks_meta_box( $post ) {
	if ( function_exists( 'wc_get_page_id' ) && $post->ID === wc_get_page_id( 'shop' ) ) {
		add_meta_box( 'silver-shop-content-blocks', __( 'UX Blocks', 'silver' ), 'silver_term_blocks_fields', 'page', 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes_page', 'silver_shop_blocks_meta_box' );

function silver_shop_blocks_save( $post_id ) {
	if ( function_exists( 'wc_get_page_id' ) && $post_id === wc_get_page_id( 'shop' )
		&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) && current_user_can( 'edit_post', $post_id ) ) {
		silver_save_content_blocks( 'post', $post_id );
	}
}
add_action( 'save_post_page', 'silver_shop_blocks_save' );

/**
 * Meta box of the blog posts and products (sidebar).
 */
function silver_post_blocks_meta_box() {
	add_meta_box( 'silver-post-content-blocks', __( 'UX Blocks', 'silver' ), 'silver_post_blocks_fields', array_keys( silver_content_blocks_post_types() ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'silver_post_blocks_meta_box' );

/**
 * The selects of a post / product.
 *
 * @param WP_Post $post Post.
 */
function silver_post_blocks_fields( $post ) {
	$products = 'product' === $post->post_type;
	$help     = $products
		? __( 'Default: the block of its category, else the theme options (Product Page).', 'silver' )
		: __( 'Default: the block of its category, else the theme options (Blog Single Post).', 'silver' );

	wp_nonce_field( 'silver_term_blocks', 'silver_term_blocks_nonce' );
	foreach ( array(
		SILVER_TERM_BLOCK_BEFORE => __( 'Before content (UX Block)', 'silver' ),
		SILVER_TERM_BLOCK_AFTER  => __( 'After content (UX Block)', 'silver' ),
	) as $key => $label ) {
		echo '<p><label for="' . esc_attr( str_replace( '_', '-', $key ) ) . '"><strong>' . esc_html( $label ) . '</strong></label></p>';
		echo silver_content_block_select( $key, get_post_meta( $post->ID, $key, true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
	}
	echo '<p class="description">' . esc_html( $help ) . '</p>';
}

/**
 * Saves the fields of a post / product.
 *
 * @param int $post_id Post ID.
 */
function silver_post_blocks_save( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! array_key_exists( get_post_type( $post_id ), silver_content_blocks_post_types() ) ) {
		return;
	}
	silver_save_content_blocks( 'post', $post_id );
}
add_action( 'save_post', 'silver_post_blocks_save' );

/**
 * Saves the selects ("" = Default: no meta, -1 = Hide, or a UX Block ID).
 *
 * @param string   $meta_type term or post.
 * @param int      $object_id Object ID.
 * @param string[] $keys      Fields.
 */
function silver_save_content_blocks( $meta_type, $object_id, $keys = array( SILVER_TERM_BLOCK_BEFORE, SILVER_TERM_BLOCK_AFTER ) ) {
	if ( ! isset( $_POST['silver_term_blocks_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_term_blocks_nonce'] ) ), 'silver_term_blocks' ) ) {
		return;
	}

	foreach ( $keys as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw      = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		$block_id = absint( $raw );

		if ( SILVER_CONTENT_BLOCK_HIDE === $raw ) {
			update_metadata( $meta_type, $object_id, $key, SILVER_CONTENT_BLOCK_HIDE );
		} elseif ( $block_id && 'blocks' === get_post_type( $block_id ) ) {
			update_metadata( $meta_type, $object_id, $key, $block_id );
		} else {
			delete_metadata( $meta_type, $object_id, $key );
		}
	}
}

/**
 * Searchable selects (selectWoo, the select2 of WooCommerce) on the category
 * screens, the shop page and the post / product screens. Without
 * WooCommerce they stay plain selects.
 */
function silver_term_blocks_admin_scripts() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen ) {
		return;
	}
	$is_term = in_array( $screen->base, array( 'edit-tags', 'term' ), true ) && in_array( $screen->taxonomy, silver_term_blocks_taxonomies(), true );
	$is_shop = 'post' === $screen->base && 'page' === $screen->post_type && function_exists( 'wc_get_page_id' ) && get_the_ID() === wc_get_page_id( 'shop' );
	$is_item = 'post' === $screen->base && array_key_exists( $screen->post_type, silver_content_blocks_post_types() );
	if ( ! $is_term && ! $is_shop && ! $is_item ) {
		return;
	}
	if ( ! wp_script_is( 'selectWoo', 'registered' ) ) {
		return;
	}

	wp_enqueue_script( 'selectWoo' );
	wp_enqueue_style( 'woocommerce_admin_styles' );
	wp_add_inline_script(
		'selectWoo',
		'jQuery( function ( $ ) {
			var $selects = $( ".silver-term-block-select" );
			$selects.selectWoo( { width: "100%" } );
			// Add category screen: the form is reset after each new category.
			$( document ).ajaxComplete( function ( event, xhr, settings ) {
				if ( settings && "string" === typeof settings.data && -1 !== settings.data.indexOf( "action=add-tag" ) ) {
					$selects.val( "" ).trigger( "change" );
				}
			} );
		} );'
	);
}
add_action( 'admin_enqueue_scripts', 'silver_term_blocks_admin_scripts', 20 );

/** Hooks for each taxonomy (on init, after WooCommerce registers product_cat). */
function silver_term_blocks_register() {
	foreach ( silver_term_blocks_taxonomies() as $taxonomy ) {
		add_action( "{$taxonomy}_add_form_fields", 'silver_term_blocks_add_fields', 30 );
		add_action( "{$taxonomy}_edit_form_fields", 'silver_term_blocks_edit_fields', 30 );
		add_action( "created_{$taxonomy}", 'silver_term_blocks_save' );
		add_action( "edited_{$taxonomy}", 'silver_term_blocks_save' );
	}
}
add_action( 'init', 'silver_term_blocks_register', 20 );
