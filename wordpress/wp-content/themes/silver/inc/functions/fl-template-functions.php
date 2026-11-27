<?php

/**
 * Display placeholder with tooltip message on header elements when they miss a resource.
 *
 * @param string $resource Name of the resource.
 */
function fl_header_element_error( $resource ) {
	$title = '';
	switch ( $resource ) {
		case 'woocommerce':
			$title = 'WooCommerce needed';
	}
	echo '<li><a class="element-error tooltip" title="' . esc_attr( $title ) . '">-</a></li>';
}

/**
 * Get silver_breadcrumb hooked content.
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param bool         $display Whether to display the breadcrumb (true) or return it (false).
 */
function silver_breadcrumb( $class = '', $display = true ) {
	do_action( 'silver_breadcrumb', $class, $display );
}

/**
 * The divider between breadcrumb links, as HTML.
 *
 * Customizer > Breadcrumbs > Divider icon: the chosen Font Awesome icon, or a
 * slash when none is chosen. Used by the WooCommerce, blog and portfolio
 * breadcrumbs, and by Yoast SEO / AIOSEO breadcrumbs when an icon is chosen.
 *
 * @param string $fallback Divider when no icon is chosen (escaped HTML).
 *
 * @return string
 */
function silver_breadcrumb_divider( $fallback = '&#47;' ) {
	$icon    = silver_sanitize_icon_class( get_theme_mod( 'breadcrumb_divider_icon', '' ) );
	$divider = $icon ? get_silver_icon( $icon ) : $fallback;

	return apply_filters( 'silver_breadcrumb_divider', $divider, $icon );
}

/**
 * Where the breadcrumbs go on the current view.
 *
 * Customizer > Breadcrumbs > Breadcrumbs position:
 * - "title":  their usual place (above the blog title, in the shop toolbar,
 *             above the product title);
 * - "header": in a bar right under the header (silver_breadcrumbs_bar()).
 * A transparent header covers the top of the page, so there they keep their
 * usual place.
 *
 * @return string "title" or "header".
 */
function silver_breadcrumbs_position() {
	// blog_breadcrumbs_position: the first, blog-only version of this option.
	$position = get_theme_mod( 'breadcrumbs_position', get_theme_mod( 'blog_breadcrumbs_position', 'title' ) );
	$position = 'header' === $position ? 'header' : 'title';

	if ( 'header' === $position ) {
		$header_classes = explode( ' ', implode( ' ', (array) apply_filters( 'silver_header_class', array() ) ) );
		if ( in_array( 'transparent', $header_classes, true ) ) {
			$position = 'title';
		}
	}

	return apply_filters( 'silver_breadcrumbs_position', $position );
}

/**
 * The shop view whose breadcrumbs can move to the bar under the header.
 *
 * @return string "category" (shop page, product categories, tags and other
 *                product taxonomies), "product" (single product, except UX
 *                Builder "Custom" layouts, which place their own breadcrumbs)
 *                or "" for anything else.
 */
function silver_shop_breadcrumbs_view() {
	if ( ! function_exists( 'is_woocommerce_activated' ) || ! is_woocommerce_activated() ) {
		return '';
	}

	if ( is_product() ) {
		return 'custom' === get_theme_mod( 'product_layout' ) ? '' : 'product';
	}

	if ( is_shop() || is_product_taxonomy() ) {
		return 'category';
	}

	return '';
}

/**
 * Whether the bar under the header printed the breadcrumbs of this page.
 * The usual places check it so the breadcrumbs never show twice.
 *
 * @param bool|null $printed Set by silver_breadcrumbs_bar().
 *
 * @return bool
 */
function silver_breadcrumbs_bar_printed( $printed = null ) {
	static $done = false;

	if ( null !== $printed ) {
		$done = (bool) $printed;
	}

	return $done;
}

/**
 * Whether the bar under the header printed the page title (H1) of this page.
 * The archive title templates check it so the title never shows twice.
 *
 * @param bool|null $printed Set by silver_breadcrumbs_bar().
 *
 * @return bool
 */
function silver_page_header_title_printed( $printed = null ) {
	static $done = false;

	if ( null !== $printed ) {
		$done = (bool) $printed;
	}

	return $done;
}

/**
 * Whether the page prints an H1 of its own, so the title of the bar under the
 * header is a <div> (one H1 per page). "h1" always, "div" never, "auto"
 * (default) when the content shown on this page has an H1:
 * - product category / tag: its description, first page only (where
 *   silver_category_description() prints it);
 * - post category / tag / taxonomy: its description (printed in the bar);
 * - shop page: its page content, first page only (WooCommerce prints it).
 *
 * Setting: Customizer > Breadcrumbs > Bar title tag for the shop pages,
 * Customizer > Blog > Blog Layout > Page title tag for the blog pages.
 *
 * @return bool
 */
function silver_page_header_has_own_h1() {
	$is_product = function_exists( 'is_product_taxonomy' ) && is_product_taxonomy();
	$is_shop    = $is_product || ( function_exists( 'is_shop' ) && is_shop() );
	$mode       = $is_shop ? get_theme_mod( 'breadcrumbs_bar_title_tag', 'auto' ) : get_theme_mod( 'blog_title_tag', 'auto' );

	if ( 'h1' === $mode || 'div' === $mode ) {
		return 'div' === $mode;
	}

	$content    = '';
	$first_page = 0 === absint( get_query_var( 'paged' ) );

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && ( ! $is_product || $first_page ) ) {
			if ( $is_product ) {
				$content = apply_filters( 'woocommerce_taxonomy_archive_description_raw', $term->description, $term );
			} elseif ( ! function_exists( 'silver_queried_short_description' ) || '' === silver_queried_short_description() ) {
				// The short description, when set, takes its place in the bar.
				$content = $term->description;
			}
		}
	} elseif ( $first_page && function_exists( 'is_shop' ) && is_shop() ) {
		$shop_page = get_post( wc_get_page_id( 'shop' ) );
		$content   = $shop_page ? $shop_page->post_content : '';
	}

	$has_h1 = '' !== (string) $content && (bool) preg_match( '/<h1[\s>]/i', (string) $content );

	return (bool) apply_filters( 'silver_page_header_has_own_h1', $has_h1, $content );
}

/**
 * Where the category list of the blog page and post categories goes.
 * Customizer > Blog > Blog Layout > Category list.
 *
 * @return string '' (hidden), 'content' (above the posts) or 'bar' (bar
 *                under the header, falls back to 'content' when the bar does
 *                not print the page title).
 */
function silver_blog_category_list_position() {
	$position = get_theme_mod( 'blog_category_list', 'content' );

	return in_array( $position, array( 'content', 'bar' ), true ) ? $position : '';
}

/**
 * Category chips, for product categories (shop page, product category) or
 * post categories (blog page, category).
 *
 * Settings: Customizer > Breadcrumbs for the shop (chips in the bar under the
 * header), Customizer > Blog > Blog Layout for the blog:
 * - Level: "context" = the subcategories of the current category, or, when
 *   it has none, the categories next to it (same parent, itself included);
 *   "top" = always the top-level categories (the one holding the current
 *   category is active).
 * - "All" chip: first chip, links to the category whose subcategories are
 *   shown, else to the shop / blog page.
 * - Maximum number of chips (0 = no limit).
 *
 * @param string $taxonomy product_cat or category.
 *
 * @return array Empty, or array( 'cats' => WP_Term[], 'cats_all' => array( 'url' => string, 'active' => bool ) ).
 */
function silver_page_header_term_chips( $taxonomy ) {
	$is_product = 'product_cat' === $taxonomy;

	if ( $is_product ) {
		$enabled = get_theme_mod( 'breadcrumbs_bar_subcategories', 1 );
		$mods    = array(
			'level'  => 'breadcrumbs_bar_cats_level',
			'all'    => 'breadcrumbs_bar_cats_all',
			'number' => 'breadcrumbs_bar_cats_number',
		);
	} else {
		$enabled = '' !== silver_blog_category_list_position();
		$mods    = array(
			'level'  => 'blog_category_list_level',
			'all'    => 'blog_category_list_all',
			'number' => 'blog_category_list_number',
		);
	}

	if ( ! $enabled || ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	if ( $is_product ) {
		$on_term = is_product_category();
		$on_root = function_exists( 'is_shop' ) && is_shop();
	} else {
		$on_term = is_category();
		$on_root = is_home();
	}

	if ( ! $on_term && ! $on_root ) {
		return array();
	}

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'number'     => absint( get_theme_mod( $mods['number'], 12 ) ),
		'orderby'    => $is_product ? 'menu_order' : 'name',
		'exclude'    => array( (int) get_option( $is_product ? 'default_product_cat' : 'default_category' ) ),
		'parent'     => 0,
	);

	$term = $on_term ? get_queried_object() : null;
	if ( $on_term && ! $term instanceof WP_Term ) {
		return array();
	}

	if ( $term && 'top' !== get_theme_mod( $mods['level'], 'context' ) ) {
		$children       = get_terms( array_merge( $args, array(
			'parent' => $term->term_id,
			'number' => 1,
			'fields' => 'ids',
		) ) );
		$args['parent'] = ! is_wp_error( $children ) && $children ? $term->term_id : $term->parent;
	}

	$filter = $is_product ? 'silver_page_header_product_cats_args' : 'silver_page_header_post_cats_args';
	$terms  = get_terms( apply_filters( $filter, $args, $term ) );

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	// "All": the category whose subcategories are shown, else the shop / blog page.
	$all = array();
	if ( get_theme_mod( $mods['all'], 1 ) ) {
		$parent = (int) $args['parent'];

		if ( $parent ) {
			$url = get_term_link( $parent, $taxonomy );
			$all = array(
				'url'    => is_wp_error( $url ) ? '' : $url,
				'active' => $term && (int) $term->term_id === $parent,
			);
		} else {
			if ( $is_product ) {
				$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
			} else {
				$page_for_posts = (int) get_option( 'page_for_posts' );
				$url            = $page_for_posts ? get_permalink( $page_for_posts ) : ( 'posts' === get_option( 'show_on_front' ) ? home_url( '/' ) : '' );
			}
			$all = array(
				'url'    => (string) $url,
				'active' => $on_root,
			);
		}

		if ( '' === $all['url'] ) {
			$all = array();
		}
	}

	return array(
		'cats'     => $terms,
		'cats_all' => $all,
	);
}

/**
 * Product category chips of the bar under the header.
 *
 * @see silver_page_header_term_chips()
 *
 * @return WP_Term[]
 */
function silver_page_header_product_cats() {
	$chips = silver_page_header_term_chips( 'product_cat' );

	return $chips ? $chips['cats'] : array();
}

/**
 * Title, description and category chips of the bar under the header.
 *
 * Shop page and product taxonomies (default category title layout only):
 * the shop title, the number of products and the category chips. Blog page
 * and blog archives: the archive title and description, plus the category
 * chips on the blog page and post categories. Single products and posts get
 * the breadcrumbs only. Customizer > Breadcrumbs > Show page title.
 *
 * @param string $shop Shop view, see silver_shop_breadcrumbs_view().
 *
 * @return array Empty, or array( 'title' => string, 'desc' => string (HTML),
 *               'cats' => WP_Term[], 'cats_all' => array, see
 *               silver_page_header_term_chips() ).
 */
function silver_page_header_title_data( $shop = '' ) {
	$data = array();

	if ( get_theme_mod( 'breadcrumbs_bar_title', 1 ) ) {
		if ( 'category' === $shop ) {
			// Featured category headers print their own title.
			if ( ! get_theme_mod( 'category_title_style' ) && ! get_theme_mod( 'category_header_transparent', 0 ) ) {
				$count = (int) $GLOBALS['wp_query']->found_posts;
				$data  = array(
					'title' => is_search() ? sprintf( __( 'Search Results for: %s', 'silver' ), get_search_query() ) : woocommerce_page_title( false ),
					/* translators: %s: Number of products. */
					'desc'  => get_theme_mod( 'category_show_result_count', 1 ) ? esc_html( sprintf( _n( '%s product available', '%s products available', $count, 'silver' ), number_format_i18n( $count ) ) ) : '',
				);
				if ( ! is_search() ) {
					$data += silver_page_header_term_chips( 'product_cat' );
				}
			}
		} elseif ( ! $shop && ! is_singular() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );
			// Blog > Blog Layout > Category list = In the bar under the header.
			$blog_chips     = 'bar' === silver_blog_category_list_position();

			if ( is_home() && ! is_front_page() && $page_for_posts ) {
				$data = array(
					'title' => get_the_title( $page_for_posts ),
					'desc'  => has_excerpt( $page_for_posts ) ? wpautop( esc_html( get_the_excerpt( $page_for_posts ) ) ) : '',
				);

				if ( $blog_chips ) {
					$data += silver_page_header_term_chips( 'category' );
				}
			} elseif ( is_search() ) {
				$data = array(
					/* translators: %s: Search query. */
					'title' => esc_html( sprintf( __( 'Search Results for: %s', 'silver' ), get_search_query() ) ),
					'desc'  => '',
				);
			} elseif ( is_category() || is_tag() || is_tax() || is_author() || is_date() ) {
				add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
				$title = get_the_archive_title();
				remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

				$data = array(
					'title' => $title,
					'desc'  => get_the_archive_description(),
				);

				if ( $blog_chips && is_category() ) {
					$data += silver_page_header_term_chips( 'category' );
				}
			}
		}
	}

	// The short description of the term replaces the default description
	// (inc/functions/function-short-description.php).
	if ( $data ) {
		$short_description = silver_queried_short_description();
		if ( '' !== $short_description ) {
			$data['desc'] = $short_description;
		}
	}

	$data = apply_filters( 'silver_page_header_title_data', $data, $shop );

	if ( empty( $data['title'] ) ) {
		return array();
	}

	return wp_parse_args( $data, array(
		'desc'     => '',
		'cats'     => array(),
		'cats_all' => array(),
	) );
}

/**
 * Category chips of the bar under the header, as HTML.
 *
 * The chip of the current category, or of the category holding it (chip
 * level "Top level"), is active.
 *
 * @param WP_Term[] $terms Product or post categories.
 * @param array     $all   "All" chip: array( 'url' => string, 'active' => bool ), or empty.
 *
 * @return string
 */
function silver_page_header_cats_html( $terms, $all = array() ) {
	if ( empty( $terms ) ) {
		return '';
	}

	$taxonomy = reset( $terms )->taxonomy;
	$queried  = get_queried_object();
	$current  = array();

	if ( $queried instanceof WP_Term && $queried->taxonomy === $taxonomy ) {
		$current   = array_map( 'intval', get_ancestors( $queried->term_id, $taxonomy, 'taxonomy' ) );
		$current[] = (int) $queried->term_id;
	}

	$items = '';

	if ( ! empty( $all['url'] ) ) {
		$active = ! empty( $all['active'] );
		$items .= sprintf(
			'<a class="page-header-cat page-header-cat--all%1$s" href="%2$s"%3$s><span>%4$s</span></a>',
			$active ? ' is-active' : '',
			esc_url( $all['url'] ),
			$active ? ' aria-current="page"' : '',
			esc_html( apply_filters( 'silver_page_header_cats_all_label', __( 'All', 'silver' ), $taxonomy ) )
		);
	}

	foreach ( $terms as $term ) {
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}

		$active   = in_array( (int) $term->term_id, $current, true );
		$image_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		$image    = $image_id ? wp_get_attachment_image( $image_id, 'thumbnail', false, array(
			'class'   => 'page-header-cat__img',
			'alt'     => '',
			'loading' => 'lazy',
		) ) : '';

		$items .= sprintf(
			'<a class="page-header-cat%1$s" href="%2$s"%3$s>%4$s<span>%5$s</span></a>',
			$active ? ' is-active' : '',
			esc_url( $link ),
			// Only the current page itself, not the category holding it.
			$active && (int) $term->term_id === (int) end( $current ) ? ' aria-current="page"' : '',
			$image,
			esc_html( $term->name )
		);
	}

	if ( '' === $items ) {
		return '';
	}

	$label = 'product_cat' === $taxonomy ? __( 'Product categories', 'silver' ) : __( 'Categories', 'silver' );

	return '<nav class="page-header-cats" aria-label="' . esc_attr( $label ) . '">' . $items . '</nav>';
}

/**
 * Outputs the breadcrumbs in a bar under the header when Breadcrumbs position
 * is "Below header", on blog, shop, product category and product pages, then
 * takes them out of their usual place.
 *
 * On the shop, product categories, the blog page and blog archives the bar is
 * a page header: breadcrumbs, the page title (H1), a description and, on the
 * shop, product categories, the blog page and post categories, the category
 * chips (silver_page_header_title_data()).
 *
 * Runs after the custom "HTML after header" (priority 1) and before the blog
 * header, the "Top" post title, the shop category header and the product
 * header (priority 10).
 */
function silver_breadcrumbs_bar() {
	if ( 'header' !== silver_breadcrumbs_position() ) {
		return;
	}

	$shop = silver_shop_breadcrumbs_view();
	$blog = ! $shop && function_exists( 'silver_blog_breadcrumbs_enabled' ) && silver_blog_breadcrumbs_enabled();

	ob_start();
	if ( $blog ) {
		silver_blog_breadcrumbs( 'header' );
	} elseif ( $shop ) {
		silver_breadcrumb();
	}
	$breadcrumbs = trim( ob_get_clean() );

	if ( '' === $breadcrumbs ) {
		return; // Nothing to move: the usual place keeps them.
	}

	silver_breadcrumbs_bar_printed( true );

	// The bar already contains the breadcrumb trail.
	remove_action( 'silver_category_title', 'silver_shop_loop_tools_breadcrumbs', 10 );

	if ( 'product' === $shop ) {
		// Above the product title, and in the featured product header.
		// The "Gallery wide" layout checks silver_breadcrumbs_bar_printed().
		remove_action( 'woocommerce_single_product_summary', 'silver_woocommerce_product_breadcrumb', 0 );
		remove_action( 'silver_product_title', 'silver_product_page_breadcrumbs', 20 );
	}

	$page_header = silver_page_header_title_data( $shop );
	$content     = $breadcrumbs;
	$classes     = 'breadcrumbs-bar';

	// Customizer > Breadcrumbs > Bar text color: light text on a dark background.
	if ( 'dark' === get_theme_mod( 'breadcrumbs_bar_text', '' ) ) {
		$classes .= ' dark';
	}

	if ( $page_header ) {
		silver_page_header_title_printed( true );

		if ( 'category' === $shop ) {
			// The H1 of the shop toolbar, and its result count: the bar shows
			// the number of products.
			remove_action( 'silver_category_title', 'silver_category_title', 1 );
			remove_action( 'silver_category_title_alt', 'silver_woocommerce_result_count', 20 );
		}

		// Customizer > Breadcrumbs > Bar title tag.
		$title_tag = silver_page_header_has_own_h1() ? 'div' : 'h1';
		$title_tag = 'div' === apply_filters( 'silver_page_header_title_tag', $title_tag ) ? 'div' : 'h1';

		$classes .= ' page-header-bar has-title';
		// .heading-font: the heading font of the theme also for the <div>.
		$content .= '<' . $title_tag . ' class="page-header-title heading-font">' . wp_kses_post( $page_header['title'] ) . '</' . $title_tag . '>';

		if ( '' !== trim( (string) $page_header['desc'] ) ) {
			$content .= '<div class="page-header-desc">' . wp_kses_post( $page_header['desc'] ) . '</div>';
		}

		$content .= silver_page_header_cats_html( $page_header['cats'], $page_header['cats_all'] );
	}

	echo '<div class="' . esc_attr( $classes ) . '"><div class="container">' . $content . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the breadcrumb source and above.
}
add_action( 'silver_after_header', 'silver_breadcrumbs_bar', 5 );

/**
 * Outputs the beginning markup of a sticky column.
 *
 * Outputs the markup directly if no theme modification name has been
 * given. Else based on the return value of the mod.
 *
 * If a theme mod was given and will output, sticky mode gets set based on $name . '_mode' theme mod.
 *
 * @param string $name  Theme modification name.
 * @param string $mode  Sticky mode (css or javascript).
 */
function silver_sticky_column_open( $name = '', $mode = '' ) {
	if ( empty( $name ) || get_theme_mod( $name ) ) {
		if ( ! empty( $name ) && empty( $mode ) ) {
			$mode = get_theme_mod( $name . '_mode' );
		}

		echo sprintf( '<div class="is-sticky-column"%s>',
			! empty( $mode ) ? ' data-sticky-mode="' . esc_attr( $mode ) . '"' : ''
		);
		echo '<div class="is-sticky-column__inner">';
	}
}

/**
 * Outputs the end markup of a sticky column.
 *
 * Outputs the markup directly if no theme modification name has been
 * given. Else based on the return value of the mod.
 *
 * @param string $name   Theme modification name.
 */
function silver_sticky_column_close( $name = '' ) {
	if ( empty( $name ) || get_theme_mod( $name ) ) {
		echo '</div></div>';
	}
}

/**
 * @deprecated 3.7
 */
function get_silver_breadcrumbs() {
	_deprecated_function( __FUNCTION__, '3.7', 'silver_breadcrumb' );
	silver_breadcrumb();
}
