<?php

global $extensions_url;
global $extensions_uri;
$extensions_url = get_template_directory() . '/inc/extensions';
$extensions_uri = get_template_directory_uri() . '/inc/extensions';

// Shortcode Inserter
if(is_admin()){ require $extensions_url.'/silver-shortcode-insert/tinymce.php'; }

// Lazy load
if ( ! is_admin() && ( silver_perf( 'lazy_load_images' ) || silver_perf( 'lazy_load_iframes' ) || silver_perf( 'lazy_load_video' ) || silver_perf( 'lazy_load_audio' ) ) ) {
  require $extensions_url . '/silver-lazy-load/silver-lazy-load.php';
}

if ( silver_perf( 'perf_instant_page' ) ) {
  require $extensions_url.'/silver-instant-page/silver-instant-page.php';
}

if(get_theme_mod('live_search', 1)){
  require $extensions_url.'/silver-live-search/silver-live-search.php';
}

if ( get_theme_mod( 'cookie_notice' ) || is_customize_preview() ) {
	require $extensions_url . '/silver-cookie-notice/silver-cookie-notice.php';
}

if ( get_theme_mod( 'floating_contact' ) || is_customize_preview() ) {
	require $extensions_url . '/silver-floating-contact/silver-floating-contact.php';
}

// Infinite scroll / Load more button: shop archives (Shop pagination) and
// blog archives (Blog pagination, no WooCommerce needed).
if (
	( is_woocommerce_activated() && in_array( get_theme_mod( 'shop_pagination' ), array( 'infinite-scroll', 'load-more' ), true ) )
	|| 'load-more' === get_theme_mod( 'blog_pagination' )
) {
	require $extensions_url . '/silver-infinite-scroll/class-silver-infinite-scroll.php';
}

if(is_woocommerce_activated()){
	if(!get_theme_mod('disable_quick_view', 1)){
		require $extensions_url.'/silver-wc-quick-view/silver-quick-view.php';
	}
	if ( get_theme_mod( 'cart_auto_refresh' ) ) {
		require $extensions_url . '/silver-cart-refresh/silver-cart-refresh.php';
	}

	if ( get_theme_mod( 'swatches' ) ) {
		require $extensions_url . '/silver-swatches/index.php';
	}

	if ( get_theme_mod( 'additional_variation_images' ) && ! get_theme_mod( 'product_gallery_woocommerce' ) ) {
		require $extensions_url . '/silver-variation-images/index.php';
	}

	if ( get_theme_mod( 'ajax_add_to_cart' ) ) {
		require $extensions_url . '/silver-ajax-add-to-cart/silver-ajax-add-to-cart.php';
	}
}
