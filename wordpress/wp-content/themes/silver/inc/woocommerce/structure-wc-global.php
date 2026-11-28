<?php

function silver_woocommerce_setup() {
	// Theme support for default WC gallery.
	if ( get_theme_mod( 'product_gallery_woocommerce') ) {
		add_theme_support( 'wc-product-gallery-slider' );
	}
	// Remove default row and column options.
	remove_theme_support( 'product_grid' );
}
add_action( 'after_setup_theme', 'silver_woocommerce_setup', 90 );


if ( ! function_exists( 'silver_woocommerce_add_notice' ) ) {
	/**
	 * Add wc notices except for the cart page
	 */
	function silver_woocommerce_add_notice() {
		if ( silver_is_login_request() || silver_is_register_request() ) {
			return;
		}

		if ( is_woocommerce_activated() && ! is_cart() ) {
			if ( function_exists( 'wc_print_notices' ) ) wc_print_notices();
		}
	}
}
add_action( 'silver_after_header', 'silver_woocommerce_add_notice', 100 );

function silver_my_account_menu_classes( $classes ) {
	if ( in_array( 'is-active', $classes ) ) {
		array_push( $classes, 'active' );
	}

	return $classes;
}

add_filter( 'woocommerce_account_menu_item_classes', 'silver_my_account_menu_classes' );

/**
 * My Account Dashboard overview
 */
function silver_my_account_dashboard() {
	wc_get_template( 'myaccount/dashboard-links.php' );
}

add_action( 'woocommerce_account_dashboard', 'silver_my_account_dashboard' );

/**
 * Remove logout from my account menu
 */
function silver_remove_logout_account_item( $items ) {
	unset( $items['customer-logout'] );

	return $items;
}

add_filter( 'woocommerce_account_menu_items', 'silver_remove_logout_account_item' );

/**
 * Conditionally remove WooCommerce styles and/or scripts.
 */
function silver_woocommerce_scripts_styles() {
	// Remove default WooCommerce Lightbox.
	wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
	wp_deregister_style( 'woocommerce_prettyPhoto_css' );

	wp_dequeue_script( 'prettyPhoto' );
	wp_dequeue_script( 'prettyPhoto-init' );

	if ( ! is_admin() ) {
		wp_dequeue_style( 'woocommerce-layout' );
		wp_deregister_style( 'woocommerce-layout' );
		wp_dequeue_style( 'woocommerce-smallscreen' );
		wp_deregister_style( 'woocommerce-smallscreen' );
		wp_dequeue_style( 'woocommerce-general' );
		wp_deregister_style( 'woocommerce-general' );
	}
}

add_action( 'wp_enqueue_scripts', 'silver_woocommerce_scripts_styles', 98 );


/**
 * Add shop widgets
 */
function silver_shop_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Shop Sidebar', 'silver' ),
		'id'            => 'shop-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<span class="widget-title shop-sidebar">',
		'after_title'   => '</span><div class="is-divider small"></div>',
	) );

	register_sidebar( array(
		'name'          => __( 'Product Sidebar', 'silver' ),
		'id'            => 'product-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<span class="widget-title shop-sidebar">',
		'after_title'   => '</span><div class="is-divider small"></div>',
	) );
}

add_action( 'widgets_init', 'silver_shop_widgets_init' );

/**
 * Modify default Shop Breadcrumbs
 */
function silver_woocommerce_breadcrumbs() {
	$home = ( get_theme_mod( 'breadcrumb_home', 1 ) ) ? _x( 'Home', 'breadcrumb', 'woocommerce' ) : false;
	if ( 'Home' === $home ) {
		$home = _x( 'Home', 'breadcrumb', 'silver' );
	}

	return array(
		'delimiter'   => silver_breadcrumb_divider(), // Customizer > Breadcrumbs > Divider icon.
		'wrap_before' => '<nav class="woocommerce-breadcrumb breadcrumbs ' . get_theme_mod( 'breadcrumb_case', '' ) . '" aria-label="Breadcrumb">',
		'wrap_after'  => '</nav>',
		'before'      => '',
		'after'       => '',
		'home'        => $home,
	);
}

add_filter( 'woocommerce_breadcrumb_defaults', 'silver_woocommerce_breadcrumbs' );

/**
 * Localize the default search label of the trail. The shop crumb keeps the
 * title of the page chosen as shop (WooCommerce > Settings > Products), like
 * the shop page title.
 */
function silver_localize_catalog_breadcrumbs( $crumbs ) {
	if ( ! is_search() ) {
		return $crumbs;
	}

	$query = get_search_query();
	foreach ( $crumbs as $index => $crumb ) {
		if ( sprintf( 'Search results for &ldquo;%s&rdquo;', $query ) === $crumb[0] ) {
			$crumbs[ $index ][0] = sprintf( __( 'Search Results for: %s', 'silver' ), $query );
		}
	}
	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'silver_localize_catalog_breadcrumbs' );

/**
 * Add default breadcrumbs.
 *
 * @see woocommerce_breadcrumb()
 */
add_action( 'silver_breadcrumb' , 'woocommerce_breadcrumb', 20 );

/**
 * Puts the main category of a post first (see silver_post_main_category()).
 * WooCommerce builds the single post trail from its first category.
 *
 * @param WP_Term[] $categories Post categories.
 * @param int|false $post_id    Post ID.
 *
 * @return WP_Term[]
 */
function silver_woocommerce_breadcrumb_post_categories( $categories, $post_id ) {
	if ( count( $categories ) < 2 || ! function_exists( 'silver_post_main_category' ) ) {
		return $categories;
	}

	remove_filter( 'get_the_categories', 'silver_woocommerce_breadcrumb_post_categories', 10 );
	$main = silver_post_main_category( $post_id );
	add_filter( 'get_the_categories', 'silver_woocommerce_breadcrumb_post_categories', 10, 2 );

	if ( ! $main ) {
		return $categories;
	}

	$others = array_filter( $categories, function ( $category ) use ( $main ) {
		return (int) $category->term_id !== (int) $main->term_id;
	} );

	return array_merge( array( $main ), array_values( $others ) );
}

/**
 * Single posts: Home > root category > ... > main category > post.
 * The main category is only moved first while the breadcrumbs are built.
 */
add_action( 'silver_breadcrumb', function () {
	if ( is_singular( 'post' ) ) {
		add_filter( 'get_the_categories', 'silver_woocommerce_breadcrumb_post_categories', 10, 2 );
	}
}, 19 );
add_action( 'silver_breadcrumb', function () {
	remove_filter( 'get_the_categories', 'silver_woocommerce_breadcrumb_post_categories', 10 );
}, 21 );

/**
 * Update cart price.
 */
function silver_header_add_to_cart_fragment( $fragments ) {
	ob_start();
	?> <span class="cart-price"><?php echo WC()->cart->get_cart_subtotal(); ?></span><?php
	$fragments['.cart-price'] = ob_get_clean();

	return $fragments;

}

add_filter( 'woocommerce_add_to_cart_fragments', 'silver_header_add_to_cart_fragment' );


if ( ! function_exists( 'silver_header_add_to_cart_fragment_count' ) ) {
	/**
	 * Update cart number when default cart icon is selected
	 *
	 * @param $fragments
	 *
	 * @return mixed
	 */
	function silver_header_add_to_cart_fragment_count( $fragments ) {
		ob_start();
		?>
		<span class="cart-icon image-icon">
			<strong><?php echo WC()->cart->get_cart_contents_count(); ?></strong>
		</span>
		<?php
		$fragments['.header .cart-icon'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'silver_header_add_to_cart_fragment_count' );


if ( ! function_exists( 'silver_header_add_to_cart_fragment_count_label' ) ) {
	/**
	 * Update cart label when a build-in cart icon is selected
	 *
	 * @param $fragments
	 *
	 * @return mixed
	 */
	function silver_header_add_to_cart_fragment_count_label( $fragments ) {
		if ( ! get_theme_mod( 'cart_icon_style' ) ) {
			return $fragments;
		}

		$icon = get_theme_mod( 'cart_icon', 'basket' );
		ob_start();
		echo get_silver_icon( 'icon-shopping-' . $icon, null, array( 'data-icon-label' => WC()->cart->get_cart_contents_count() ) );
		$fragments[ '.cart-item i.icon-shopping-' . $icon ] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'silver_header_add_to_cart_fragment_count_label' );

if ( ! function_exists( 'silver_header_add_to_cart_custom_icon_fragment_count_label' ) ) {
	/**
	 * Update cart label when custom cart icon is selected
	 *
	 * @param $fragments
	 *
	 * @return mixed
	 */
	function silver_header_add_to_cart_custom_icon_fragment_count_label( $fragments ) {
		$custom_cart_icon_id = get_theme_mod( 'custom_cart_icon' );
		$custom_cart_icon    = wp_get_attachment_image_src( $custom_cart_icon_id, 'large' );

		if ( ! $custom_cart_icon ) {
			return $fragments;
		}

		ob_start();
		?>
		<span class="image-icon header-cart-icon" data-icon-label="<?php echo WC()->cart->get_cart_contents_count(); ?>">
			<img class="cart-img-icon" alt="<?php echo esc_attr__( 'Cart', 'woocommerce' ); ?>" src="<?php echo esc_url( $custom_cart_icon[0] ); ?>" width="<?php echo esc_attr( $custom_cart_icon[1] ); ?>" height="<?php echo esc_attr( $custom_cart_icon[2] ); ?>"/>
		</span>
		<?php
		$fragments['.image-icon.header-cart-icon'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'silver_header_add_to_cart_custom_icon_fragment_count_label' );

/**
 * Add Pages and blog posts to top of search results if set.
 */
function silver_pages_in_search_results() {
	// Advanced > Site Search: posts and / or pages.
	$show_posts = silver_search_shows( 'post' );
	$show_pages = silver_search_shows( 'page' );
	if ( ! is_search() || ( ! $show_posts && ! $show_pages ) ) return;
	// Search type chips (structure-search-types.php): posts and pages have
	// their own chip, the products search lists the products only.
	if ( function_exists( 'silver_search_has_types' ) && silver_search_has_types() ) return;
	global $post;
	?>
	<?php if ( get_search_query() ) : ?>
		<?php
		/**
		 * Include pages and posts in search
		 */
		$posts = array();
		if ( $show_posts ) {
			query_posts( array( 'post_type' => array( 'post' ), 's' => get_search_query() ) );
			while ( have_posts() ) : the_post();
				array_push( $posts, $post->ID );
			endwhile;
			wp_reset_query();
		}

		// Get pages.
		$pages = array();
		if ( $show_pages ) query_posts( array( 'post_type' => array( 'page' ), 's' => get_search_query() ) );
		while ( $show_pages && have_posts() ) : the_post();
			$wc_page = false;
			if ( $post->post_type == 'page' ) {
				foreach ( array( 'shop', 'cart', 'checkout', 'view_order', 'terms' ) as $wc_page_type ) {
					if ( $post->ID == wc_get_page_id( $wc_page_type ) ) $wc_page = true;
				}
			}
			if ( ! $wc_page ) array_push( $pages, $post->ID );
		endwhile;

		wp_reset_query();

		if ( ! empty( $posts ) ) {
			echo '<hr/><h4 class="uppercase">' . esc_html__( 'Posts found', 'silver' ) . '</h4>';
			echo silver_apply_shortcode( 'blog_posts', array(
				'columns'      => '2',
				'columns__md'  => '3',
				'columns__lg'  => '3',
				'type'         => get_theme_mod( 'search_result_style', 'slider' ),
				'image_height' => '56.25%',
				'show_date'    => get_theme_mod( 'blog_badge', 1 ) ? 'true' : 'false',
				'ids'          => implode( ',', $posts ),
			) );
		}

		if ( ! empty( $pages ) ) {
			echo '<hr/><h4 class="uppercase">' . esc_html__( 'Pages found', 'silver' ) . '</h4>';
			echo silver_apply_shortcode( 'ux_pages', array(
				'columns'      => '2',
				'columns__md'  => '3',
				'columns__lg'  => '3',
				'type'         => get_theme_mod( 'search_result_style', 'slider' ),
				'image_height' => '56.25%',
				'ids'          => implode( ',', $pages ),
			) );
		}
		?>
	<?php endif; ?>

	<?php
}

add_action( 'woocommerce_after_main_content', 'silver_pages_in_search_results' );

/**
 * Filters the WooCommerce sale flash.
 *
 * @param string     $html    The HTML string.
 * @param WP_Post    $post    Post object.
 * @param WC_Product $product Product object.
 *
 * @return false|mixed
 */
function silver_woocommerce_sale_flash( $html, $post, $product ) {
	if ( ! get_theme_mod( 'sale_bubble', 1 ) ) {
		return '';
	}

	return $html;
}

add_filter( 'woocommerce_sale_flash', 'silver_woocommerce_sale_flash', 10, 3 );

function silver_new_flash( $html, $post, $product, $badge_style ) {
	if ( ! get_theme_mod( 'new_bubble_auto' ) ) {
		return $html;
	}

	$datetime_created = $product->get_date_created();

	if ( ! $datetime_created ) {
		return $html;
	}

	$timestamp_created = $datetime_created->getTimestamp();
	$datetime_now      = new WC_DateTime();
	$timestamp_now     = $datetime_now->getTimestamp();
	$time_delta        = $timestamp_now - $timestamp_created;
	$days              = (int) get_theme_mod( 'new_bubble_auto' );
	$days_in_seconds   = 60 * 24 * 60 * $days;

	if ( $time_delta < $days_in_seconds ) {
		$html .= apply_filters( 'silver_new_flash_html', '<div class="badge callout badge-' . $badge_style . '"><div class="badge-inner is-small new-bubble-auto">' . esc_html__( 'New', 'silver' ) . '</div></div>', $post, $product, $badge_style );
	}

	return $html;
}

add_filter( 'silver_product_labels', 'silver_new_flash', 20, 4 );

/**
 * Calculates discount percentage for the product sale bubble for
 * simple, variable or external product types. Returns base bubble text
 * with or without formatting otherwise.
 *
 * @param WC_Product $product Product object.
 * @param string     $text    Default text.
 *
 * @return string
 */
function silver_presentage_bubble( $product, $text ) {
	$post_id = $product->get_id();

	if ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) || $product->is_type( 'variation' ) ) {
		$regular_price  = $product->get_regular_price();
		$sale_price     = $product->get_sale_price();
		$bubble_content = round( ( ( floatval( $regular_price ) - floatval( $sale_price ) ) / floatval( $regular_price ) ) * 100 );
	} elseif ( $product->is_type( 'variable' ) ) {
		$bubble_content = silver_percentage_get_cache( $post_id );
		if ( $bubble_content && apply_filters( 'silver_sale_bubble_percentage_cache_enabled', true ) ) {
			return silver_percentage_format( $bubble_content );
		}

		$available_variations = $product->get_available_variations();
		$maximumper           = 0;

		for ( $i = 0; $i < count( $available_variations ); ++ $i ) {
			$variation_id     = $available_variations[ $i ]['variation_id'];
			$variable_product = new WC_Product_Variation( $variation_id );
			if ( ! $variable_product->is_on_sale() ) {
				continue;
			}
			$regular_price = $variable_product->get_regular_price();
			$sale_price    = $variable_product->get_sale_price();
			$percentage    = round( ( ( floatval( $regular_price ) - floatval( $sale_price ) ) / floatval( $regular_price ) ) * 100 );
			if ( $percentage > $maximumper ) {
				$maximumper = $percentage;
			}
		}

		$bubble_content = sprintf( __( '%s', 'woocommerce' ), $maximumper );

		// Cache percentage for variable products to reduce database queries.
		if ( apply_filters( 'silver_sale_bubble_percentage_cache_enabled', true ) ) {
			silver_percentage_set_cache( $post_id, $bubble_content );
		}
	} else {
		// Return default if the product type doesn't meet specification.
		return $text;
	}

	return silver_percentage_format( $bubble_content );
}

function silver_percentage_get_cache( $post_id ) {
	return get_post_meta( $post_id, '_flatsome_product_percentage', true );
}

function silver_percentage_set_cache( $post_id, $bubble_content ) {
	update_post_meta( $post_id, '_flatsome_product_percentage', $bubble_content );
}

/**
 * Process custom formatting. Keep mod value double check
 * to process % for default parameter (See sprintf()).
 */
function silver_percentage_format( $value ) {
	$formatting = get_theme_mod( 'sale_bubble_percentage_formatting' );
	$formatting = $formatting ? $formatting : '-{value}%';

	return str_replace( '{value}', $value, $formatting );
}

/**
 * Clear cached percentage whenever a product or variation is saved.
 */
function silver_percentage_clear( $object ) {
	if ( ! get_theme_mod( 'sale_bubble_percentage', 1 ) ) return;

	$post_id = 'variation' === $object->get_type()
		? $object->get_parent_id()
		: $object->get_id();

	delete_post_meta( $post_id, '_flatsome_product_percentage' );
}

add_action( 'woocommerce_before_product_object_save', 'silver_percentage_clear' );

/**
 * Clear all cached percentages when disabling bubble percentage.
 */
function silver_percentage_clear_all( $value, $old_value ) {
	if ( ! $value && $old_value ) {
		delete_metadata( 'post', null, '_flatsome_product_percentage', '', true );
	}

	return $value;
}

add_filter( 'pre_set_theme_mod_sale_bubble_percentage', 'silver_percentage_clear_all', 10, 2 );

/**
 * Account login style
 */
function silver_account_login_lightbox() {
	global $silver_header_account_add_lightbox_template;

	if (
		is_user_logged_in()
		|| get_theme_mod( 'account_login_style', 'lightbox' ) !== 'lightbox'
		|| is_checkout()
		|| is_account_page()
		|| empty( $silver_header_account_add_lightbox_template )
	) {
		return;
	}

	$is_facebook_login    = is_nextend_facebook_login();
	$is_google_login      = is_nextend_google_login();
	$layout               = get_theme_mod( 'account_login_lightbox_layout' );
	$registration_enabled = get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes';

	if ( empty( $layout ) && $registration_enabled && 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
		wp_enqueue_script( 'wc-password-strength-meter' );
	}

	?>
	<div id="login-form-popup" class="lightbox-content mfp-hide">
		<?php if ( get_theme_mod( 'social_login_pos', 'top' ) == 'top' && ( $is_facebook_login || $is_google_login ) ) wc_get_template( 'myaccount/header.php' ); ?>
		<div class="woocommerce">
			<?php wc_get_template_part( 'myaccount/form-login', $layout ); ?>
		</div>
		<?php if ( get_theme_mod( 'social_login_pos', 'top' ) == 'bottom' && ( $is_facebook_login || $is_google_login ) ) wc_get_template( 'myaccount/header.php' ); ?>
	</div>
	<?php
}

add_action( 'wp_footer', 'silver_account_login_lightbox' );

/**
 * Disable reviews globally
 */
if ( get_theme_mod( 'disable_reviews' ) ) {
	remove_filter( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_filter( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
	function wcs_woo_remove_reviews_tab( $tabs ) {
		unset( $tabs['reviews'] );

		return $tabs;
	}
}

if ( ! function_exists( 'silver_wc_get_gallery_image_html' ) ) {
	/**
	 * Get HTML for a gallery image. Copied and modified from woocommerce plugin and wc_get_gallery_image_html helper function.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param bool   $main_image    Is this the main image or a thumbnail?.
	 * @param string $size          Image size.
	 * @param int    $image_index   The image index in the gallery.
	 *
	 * @return string
	 * @see wc_get_gallery_image_html()
	 */
	function silver_wc_get_gallery_image_html( $attachment_id, $main_image = false, $size = 'woocommerce_single', $image_index = -1 ) {
		global $product;

		// $flexslider     = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
		// $image_size     = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
		$image_size        = apply_filters( 'woocommerce_gallery_image_size', $size );
		$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$thumbnail_srcset  = wp_get_attachment_image_srcset( $attachment_id, $thumbnail_size );
		$thumbnail_sizes   = wp_get_attachment_image_sizes( $attachment_id, $thumbnail_size );
		$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
		$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

		if ( fl_woocommerce_version_check( '9.3.3' ) && function_exists( 'woocommerce_get_alt_from_product_title_and_position' ) && is_a( $product, 'WC_Product' ) ) {
			$alt_text = empty( $alt_text ) ? woocommerce_get_alt_from_product_title_and_position( $product->get_title(), $main_image, $image_index ) : $alt_text;
		}

		if ( empty( $full_src ) ) {
			return '';
		}

		/**
		 * Filters the attributes for the image markup.
		 *
		 * @since 3.3.2
		 *
		 * @param array $image_attributes Attributes for the image markup.
		 */
		$image_params = apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
				'alt'                     => esc_attr( $alt_text ),
			),
			$attachment_id,
			$image_size,
			$main_image
		);

		if ( isset( $image_params['title'] ) ) {
			unset( $image_params['title'] );
		}

		$image = wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			$image_params
		);

		$image_wrapper_class = $main_image ? 'slide first' : 'slide';

		// Gallery image fit "Whole image" (Product Images > Gallery image
		// ratio / fit): the empty sides of the box show the same image blurred
		// (function-custom-css.php), from the small gallery thumbnail the strip
		// of thumbnails loads anyway.
		$link_style = '';
		if ( function_exists( 'silver_product_gallery_ratio' ) && '' !== silver_product_gallery_ratio() && 'cover' !== get_theme_mod( 'product_gallery_fit', 'contain' ) && ! empty( $thumbnail_src[0] ) ) {
			$link_style = ' style="' . esc_attr( '--sv-gallery-backdrop:url("' . esc_url( $thumbnail_src[0] ) . '")' ) . '"';
		}

		return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" data-thumb-srcset="' . esc_attr( $thumbnail_srcset ) . '"  data-thumb-sizes="' . esc_attr( $thumbnail_sizes ) . '" class="woocommerce-product-gallery__image ' . $image_wrapper_class . '"><a href="' . esc_url( $full_src[0] ) . '"' . $link_style . '>' . $image . '</a></div>';
	}
}

/**
 * Move demo store notice to top.
 */
function silver_move_store_notice() {
	if ( ! fl_woocommerce_version_check( '9.9.0' ) ) {
		if ( get_theme_mod( 'woocommerce_store_notice_top' ) ) {
			remove_action( 'wp_footer', 'woocommerce_demo_store' );
			add_action( 'silver_after_body_open', 'woocommerce_demo_store', 0 );
		}
	}
}

add_action( 'wp_loaded', 'silver_move_store_notice' );

/**
 * Filter WC Product shortcode attributes,
 *
 * @param array $attrs Attributes.
 *
 * @return array Attributes.
 */
function silver_filter_shortcode_atts_products( $attrs ) {
	if ( ! isset( $attrs['limit'] ) || $attrs['limit'] == '-1' ) {
		$attrs['limit'] = '12';
	}

	if ( ! isset( $attrs['columns'] ) || $attrs['columns'] == '' ) {
		$attrs['columns'] = '4';
	}

	return $attrs;
}

add_filter( 'shortcode_atts_products', 'silver_filter_shortcode_atts_products' );

if ( silver_is_mini_cart_reveal() ) {
	/**
	* Adds a span tag with the "added-to-cart" class to Add to Cart notice to trigger auto reveal mini cart.
	*
	* @param  string $message    Default WooCommerce added to cart notice.
	* @param  int    $product_id Product id.
	*
	* @return string             The modified message.
	*/
	add_filter( 'wc_add_to_cart_message_html', function ( $message ) {
		$message .= '<span class="added-to-cart" data-timer=""></span>';

		return $message;
	});
}

/**
 * Get HTML for ratings.
 *
 *  @param  float $rating         Rating being shown.
 *  @param  int   $count          Total number of ratings.
 *  @param  bool  $single_product Whether the rating is being shown on a single product page.
 *
 * @see wc_get_rating_html()
 */
function silver_get_rating_html( float $rating, int $count = 0, bool $single_product = false ): string {
	global $product;

	if ( ! is_a( $product, 'WC_Product' ) ) {
		return '';
	}

	$review_count = $product->get_review_count();
	$label        = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$html         = '';

	$show_empty_rating = get_theme_mod( 'product_box_empty_rating' );

	if ( $rating > 0 || $show_empty_rating ) {
		if ( $single_product ) {
			$style = get_theme_mod( 'product_info_review_count_style', 'inline' );
			// Default to 'simple' when review count visibility is disabled.
			$style = get_theme_mod( 'product_info_review_count' ) ? $style : 'simple';

			switch ( $style ) {
				case 'tooltip':
					$title = sprintf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), $review_count ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					$html  = '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">';
					$html .= '<div class="star-rating tooltip" role="img" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $title ) . '">';
					$html .= wc_get_star_rating_html( $rating, $count );
					$html .= '</div>';
					$html .= '</a>';
					break;
				case 'inline':
					$html  = '<div class="star-rating star-rating--inline" role="img" aria-label="' . esc_attr( $label ) . '">';
					$html .= wc_get_star_rating_html( $rating, $count );
					$html .= '</div>';
					break;
				case 'stacked':
					$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
					$html .= wc_get_star_rating_html( $rating, $count );
					$html .= '</div>';
					break;
				case 'simple':
					$html  = '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">';
					$html .= '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
					$html .= wc_get_star_rating_html( $rating, $count );
					$html .= '</div>';
					$html .= '</a>';
					break;
			}
		} else {
			$html  = '<div class="star-rating star-rating--inline" role="img" aria-label="' . esc_attr( $label ) . '">';
			$html .= wc_get_star_rating_html( $rating, $count );
			$html .= '</div>';
		}
	}

	return apply_filters( 'woocommerce_product_get_rating_html', $html, $rating, $count );
}

if ( ! function_exists( 'silver_variable_price_from' ) ) {
	/**
	 * Variable products show their lowest price, "Từ 15 ₫", instead of a
	 * range. When the cheapest variation is on sale, its regular price is
	 * struck out, as for a simple product on sale. Variations that all cost
	 * the same keep WooCommerce's own output; wp-admin is left alone.
	 *
	 * "Từ" goes inside the amount of the selling price (inside <ins> on sale),
	 * so it is never read with the struck-out price and takes the same colour,
	 * size and weight as the price:
	 * <span class="woocommerce-Price-amount amount"><span class="price-from">Từ</span> <bdi>15 ₫</bdi></span>
	 *
	 * @param string     $price   Price HTML.
	 * @param WC_Product $product Variable product.
	 *
	 * @return string
	 */
	function silver_variable_price_from( $price, $product ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $price;
		}

		$prices = $product->get_variation_prices( true ); // Display prices, sorted from low to high.
		if ( empty( $prices['price'] ) ) {
			return $price;
		}

		$min_id    = array_key_first( $prices['price'] );
		$min_price = (float) $prices['price'][ $min_id ];
		$max_price = (float) end( $prices['price'] );

		if ( $min_price === $max_price ) {
			return $price;
		}

		$from   = '<span class="price-from">' . esc_html_x( 'Từ', 'variable product price', 'silver' ) . '</span> ';
		$amount = wc_price( $min_price );
		$pos    = strpos( $amount, '<bdi>' );
		$amount = false === $pos ? $from . $amount : substr_replace( $amount, $from, $pos, 0 );

		$min_regular = isset( $prices['regular_price'][ $min_id ] ) ? (float) $prices['regular_price'][ $min_id ] : $min_price;
		if ( $min_regular > $min_price ) {
			$amount = wc_format_sale_price( $min_regular, $amount );
		}

		return $amount . $product->get_price_suffix();
	}
}
add_filter( 'woocommerce_variable_price_html', 'silver_variable_price_from', 10, 2 );


/**
 * WooCommerce "Coming soon" mode (store not live): it adds its own Inter and
 * Cardo fonts to theme.json for its coming soon page. In a classic theme they
 * are printed on every page, and its Inter (a 319 KB variable font with every
 * alphabet) replaces the Inter of the theme (downloaded by the Customizer,
 * only the alphabets needed, ~70 KB): it delays the first display (PageSpeed
 * "Network dependency tree").
 *
 * Their @font-face rules are left out, except on the coming soon page itself
 * (WooCommerce renders it inside the template_include filter). Nothing
 * changes once the store is live (WooCommerce adds no font then).
 */
function silver_wc_coming_soon_font_faces() {
	if ( is_admin() || ! function_exists( 'WC' ) || ! class_exists( 'WP_Font_Face_Resolver' ) ) {
		return;
	}

	$fonts = WP_Font_Face_Resolver::get_fonts_from_theme_json();

	if ( ! doing_filter( 'template_include' ) ) {
		$wc_fonts = trailingslashit( WC()->plugin_url() ) . 'assets/fonts/';
		foreach ( $fonts as $family => $faces ) {
			$fonts[ $family ] = array_values( array_filter( (array) $faces, static function ( $face ) use ( $wc_fonts ) {
				foreach ( (array) ( isset( $face['src'] ) ? $face['src'] : array() ) as $src ) {
					if ( 0 === strpos( (string) $src, $wc_fonts ) ) {
						return false;
					}
				}
				return true;
			} ) );
			if ( ! $fonts[ $family ] ) {
				unset( $fonts[ $family ] );
			}
		}
	}

	// wp_print_font_faces() reads theme.json again when given nothing.
	if ( $fonts ) {
		wp_print_font_faces( $fonts );
	}
}

/**
 * Prints the theme.json fonts through silver_wc_coming_soon_font_faces()
 * instead of WordPress (same place: wp_head, 50).
 */
function silver_wc_coming_soon_fonts_setup() {
	if ( has_action( 'wp_head', 'wp_print_font_faces' ) ) {
		remove_action( 'wp_head', 'wp_print_font_faces', 50 );
		add_action( 'wp_head', 'silver_wc_coming_soon_font_faces', 50 );
	}
}
add_action( 'wp', 'silver_wc_coming_soon_fonts_setup' );
