<?php


// Move Cross sell product to under cart
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart_table', 'woocommerce_cross_sell_display' );

// Add Content after Cart
if(!function_exists('silver_html_cart_footer')) {
  function silver_html_cart_footer(){
  	$content = get_theme_mod('html_cart_footer');
      echo '<div class="cart-footer-content after-cart-content relative">'.do_shortcode($content).'</div>';
  }
  add_action( 'woocommerce_after_cart', 'silver_html_cart_footer', 99);
}


// Add Content in cart sidebar
if(!function_exists('silver_html_cart_sidebar')) {
  function silver_html_cart_sidebar(){
  	$icons = get_theme_mod('payment_icons_placement');
	if ( $content = get_theme_mod('html_cart_sidebar') ) {
		echo '<div class="cart-sidebar-content relative">' . do_shortcode( $content) . '</div>';
	}
	if(is_array($icons) && in_array('cart', $icons)) echo do_shortcode('[ux_payment_icons]');
  }
  add_action( 'silver_cart_sidebar', 'silver_html_cart_sidebar', 10);
}


// Continue Shopping button
if(!function_exists('silver_continue_shopping')) {
  function silver_continue_shopping(){
    wc_get_template_part('cart/continue-shopping');
  }
}
add_action('woocommerce_cart_actions', 'silver_continue_shopping', 10);


// Move Privacy policy to bottom
function silver_fix_policy_text(){
  if(function_exists('wc_checkout_privacy_policy_text')) {
    remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
    add_action( 'woocommerce_checkout_after_order_review', 'wc_checkout_privacy_policy_text', 1 );
  }
}
add_action( 'init', 'silver_fix_policy_text', 10);
