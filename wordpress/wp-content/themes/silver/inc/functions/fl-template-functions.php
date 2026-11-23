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
 * Product category chips of the bar under the header.
 *
 * Product category: its subcategories, or, when it has none, the categories
 * next to it (same parent, itself included). Shop page: the top-level
 * categories. Customizer > Breadcrumbs > Show category chips.
 *
 * @return WP_Term[]
 */
function silver_page_header_product_cats() {
	if ( ! get_theme_mod( 'breadcrumbs_bar_subcategories', 1 ) ) {
		return array();
	}

	$args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 12,
		'orderby'    => 'menu_order',
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	);

	if ( is_product_category() ) {
		$term = get_queried_object();
		if ( ! $term instanceof WP_Term ) {
			return array();
		}
		$children = get_terms( array_merge( $args, array( 'parent' => $term->term_id ) ) );
		if ( ! is_wp_error( $children ) && $children ) {
			$args['parent'] = $term->term_id;
		} else {
			$args['parent'] = $term->parent;
		}
	} elseif ( is_shop() ) {
		$args['parent'] = 0;
	} else {
		return array();
	}

	$terms = get_terms( apply_filters( 'silver_page_header_product_cats_args', $args ) );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Title, description and category chips of the bar under the header.
 *
 * Shop page and product taxonomies (default category title layout only):
 * the shop title, the number of products and the category chips. Blog page
 * and blog archives: the archive title and description. Single products and
 * posts get the breadcrumbs only. Customizer > Breadcrumbs > Show page title.
 *
 * @param string $shop Shop view, see silver_shop_breadcrumbs_view().
 *
 * @return array Empty, or array( 'title' => string, 'desc' => string (HTML),
 *               'cats' => WP_Term[] ).
 */
function silver_page_header_title_data( $shop = '' ) {
	$data = array();

	if ( get_theme_mod( 'breadcrumbs_bar_title', 1 ) ) {
		if ( 'category' === $shop ) {
			// Featured category headers print their own title.
			if ( ! get_theme_mod( 'category_title_style' ) && ! get_theme_mod( 'category_header_transparent', 0 ) ) {
				$count = (int) $GLOBALS['wp_query']->found_posts;
				$data  = array(
					'title' => woocommerce_page_title( false ),
					/* translators: %s: Number of products. */
					'desc'  => esc_html( sprintf( _n( '%s product available', '%s products available', $count, 'silver' ), number_format_i18n( $count ) ) ),
					'cats'  => silver_page_header_product_cats(),
				);
			}
		} elseif ( ! $shop && ! is_singular() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );

			if ( is_home() && ! is_front_page() && $page_for_posts ) {
				$data = array(
					'title' => get_the_title( $page_for_posts ),
					'desc'  => has_excerpt( $page_for_posts ) ? wpautop( esc_html( get_the_excerpt( $page_for_posts ) ) ) : '',
				);
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
		'desc' => '',
		'cats' => array(),
	) );
}

/**
 * Category chips of the bar under the header, as HTML.
 *
 * @param WP_Term[] $terms Product categories.
 *
 * @return string
 */
function silver_page_header_cats_html( $terms ) {
	if ( empty( $terms ) ) {
		return '';
	}

	$current = is_product_category() ? get_queried_object_id() : 0;
	$items   = '';

	foreach ( $terms as $term ) {
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}

		$active   = (int) $term->term_id === (int) $current;
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
			$active ? ' aria-current="page"' : '',
			$image,
			esc_html( $term->name )
		);
	}

	if ( '' === $items ) {
		return '';
	}

	return '<nav class="page-header-cats" aria-label="' . esc_attr__( 'Product categories', 'silver' ) . '">' . $items . '</nav>';
}

/**
 * Outputs the breadcrumbs in a bar under the header when Breadcrumbs position
 * is "Below header", on blog, shop, product category and product pages, then
 * takes them out of their usual place.
 *
 * On the shop, product categories, the blog page and blog archives the bar is
 * a page header: breadcrumbs, the page title (H1), a description and, on the
 * shop, the category chips (silver_page_header_title_data()).
 *
 * Runs after the custom "HTML after header" (priority 1) and before the blog
 * header, the "Top" post title, the shop category header and the product
 * header (priority 10).
 */
function silver_breadcrumbs_bar() {
	if ( 'header' !== silver_breadcrumbs_position() ) {
		return;
	}

	$blog = function_exists( 'silver_blog_breadcrumbs_enabled' ) && silver_blog_breadcrumbs_enabled();
	$shop = $blog ? '' : silver_shop_breadcrumbs_view();

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

	if ( 'category' === $shop ) {
		// Shop toolbar (loop/breadcrumbs.php): title, result count and ordering stay.
		remove_action( 'silver_category_title', 'silver_shop_loop_tools_breadcrumbs', 10 );
	} elseif ( 'product' === $shop ) {
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

		// Customizer > Breadcrumbs > Use H1 for the bar title.
		$title_tag = get_theme_mod( 'breadcrumbs_bar_title_h1', 1 ) ? 'h1' : 'div';
		$title_tag = 'div' === apply_filters( 'silver_page_header_title_tag', $title_tag ) ? 'div' : 'h1';

		$classes .= ' page-header-bar has-title';
		// .heading-font: the heading font of the theme also for the <div>.
		$content .= '<' . $title_tag . ' class="page-header-title heading-font">' . wp_kses_post( $page_header['title'] ) . '</' . $title_tag . '>';

		if ( '' !== trim( (string) $page_header['desc'] ) ) {
			$content .= '<div class="page-header-desc">' . wp_kses_post( $page_header['desc'] ) . '</div>';
		}

		$content .= silver_page_header_cats_html( $page_header['cats'] );
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
