<?php
/**
 * UX Blocks before and after the content of post categories and product
 * categories: two fields on the Add / Edit category screens (term meta), each
 * a UX Block printed full width with [block].
 *
 * - Before content: under the page header (bar under the header, shop title),
 *   before the posts / products (silver_after_header).
 * - After content: at the end of the page, after the posts and the related
 *   products (silver_after_blog) or after the products, the category
 *   description and the FAQs (silver_after_category_layout).
 * On every page of the category (pagination included).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_TERM_BLOCK_BEFORE = 'silver_before_content_block';
const SILVER_TERM_BLOCK_AFTER  = 'silver_after_content_block';

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
 * The UX Block chosen on the category being shown, as HTML.
 *
 * @param string $key SILVER_TERM_BLOCK_BEFORE or SILVER_TERM_BLOCK_AFTER.
 *
 * @return string
 */
function silver_term_block_html( $key ) {
	if ( ! is_category() && ! is_tax( 'product_cat' ) ) {
		return '';
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || ! in_array( $term->taxonomy, silver_term_blocks_taxonomies(), true ) ) {
		return '';
	}

	$block_id = (int) get_term_meta( $term->term_id, $key, true );
	if ( ! $block_id || 'publish' !== get_post_status( $block_id ) || 'blocks' !== get_post_type( $block_id ) ) {
		return '';
	}

	$html = do_shortcode( '[block id="' . $block_id . '"]' );
	if ( '' === trim( $html ) ) {
		return '';
	}

	$position = SILVER_TERM_BLOCK_BEFORE === $key ? 'before' : 'after';

	return '<div class="term-content-block term-content-block--' . $position . '">' . $html . '</div>';
}

/** Before content: under the page header, before the posts / products. */
function silver_term_block_before() {
	echo silver_term_block_html( SILVER_TERM_BLOCK_BEFORE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- UX Block output.
}
add_action( 'silver_after_header', 'silver_term_block_before', 50 );

/** After content: the end of the page (after the related products / the description and FAQs). */
function silver_term_block_after() {
	echo silver_term_block_html( SILVER_TERM_BLOCK_AFTER ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- UX Block output.
}
add_action( 'silver_after_blog', 'silver_term_block_after', 30 );
add_action( 'silver_after_category_layout', 'silver_term_block_after', 30 );

/* --------------------------------------------------------------- Admin */

/**
 * The two selects.
 *
 * @param WP_Term|null $term Category being edited, null on the Add screen.
 */
function silver_term_blocks_fields( $term = null ) {
	$fields = array(
		SILVER_TERM_BLOCK_BEFORE => array(
			'label' => __( 'Before content (UX Block)', 'silver' ),
			'help'  => __( 'Full width, under the page header and before the list.', 'silver' ),
		),
		SILVER_TERM_BLOCK_AFTER  => array(
			'label' => __( 'After content (UX Block)', 'silver' ),
			'help'  => __( 'Full width, at the end of the page, before the footer.', 'silver' ),
		),
	);

	if ( ! $term ) {
		wp_nonce_field( 'silver_term_blocks', 'silver_term_blocks_nonce' );
	}

	foreach ( $fields as $key => $field ) {
		$select = wp_dropdown_pages( array(
			'post_type'         => 'blocks',
			'name'              => $key,
			'id'                => str_replace( '_', '-', $key ),
			'class'             => 'silver-term-block-select',
			'show_option_none'  => __( '— None —', 'silver' ),
			// Empty value: the placeholder / clear button of select2.
			'option_none_value' => '',
			'selected'          => $term ? (int) get_term_meta( $term->term_id, $key, true ) : 0,
			'echo'              => 0,
		) );
		if ( '' === $select ) {
			$select = '<em>' . esc_html__( 'No UX Block yet (UX Blocks > Add New).', 'silver' ) . '</em>';
		}

		if ( $term ) {
			echo '<tr class="form-field"><th scope="row"><label for="' . esc_attr( str_replace( '_', '-', $key ) ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
			if ( SILVER_TERM_BLOCK_BEFORE === $key ) {
				wp_nonce_field( 'silver_term_blocks', 'silver_term_blocks_nonce' );
			}
			echo $select . '<p class="description">' . esc_html( $field['help'] ) . '</p></td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core dropdown.
		} else {
			echo '<div class="form-field"><label for="' . esc_attr( str_replace( '_', '-', $key ) ) . '">' . esc_html( $field['label'] ) . '</label>' . $select . '<p>' . esc_html( $field['help'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core dropdown.
		}
	}
}

/** Add category screen. */
function silver_term_blocks_add_fields() {
	silver_term_blocks_fields();
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
 * Saves the fields.
 *
 * @param int $term_id Term ID.
 */
function silver_term_blocks_save( $term_id ) {
	if ( ! isset( $_POST['silver_term_blocks_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_term_blocks_nonce'] ) ), 'silver_term_blocks' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	foreach ( array( SILVER_TERM_BLOCK_BEFORE, SILVER_TERM_BLOCK_AFTER ) as $key ) {
		$block_id = isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : 0;

		if ( $block_id && 'blocks' === get_post_type( $block_id ) ) {
			update_term_meta( $term_id, $key, $block_id );
		} else {
			delete_term_meta( $term_id, $key );
		}
	}
}

/**
 * Searchable selects (selectWoo, the select2 of WooCommerce) on the category
 * screens. Without WooCommerce they stay plain selects.
 */
function silver_term_blocks_admin_scripts() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->base, array( 'edit-tags', 'term' ), true ) || ! in_array( $screen->taxonomy, silver_term_blocks_taxonomies(), true ) ) {
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
			$selects.selectWoo( {
				width: "100%",
				allowClear: true,
				placeholder: ' . wp_json_encode( __( '— None —', 'silver' ) ) . '
			} );
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
