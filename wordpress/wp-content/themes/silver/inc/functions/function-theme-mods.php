<?php

$_silver_theme_mod_site_url = site_url( '', 'http' );
$_silver_theme_mod_site_url_secure = site_url( '', 'https' );

function silver_theme_mod_fix ( $value ) {
  if ( ! is_string( $value ) ) return $value;
  global $_silver_theme_mod_site_url;
  global $_silver_theme_mod_site_url_secure;
  return str_replace(
    array( '[site_url]', '[site_url_secure]' ),
    array( $_silver_theme_mod_site_url, $_silver_theme_mod_site_url_secure ),
    $value
  );
}

add_filter( 'theme_mod_footer_1_bg_image', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_footer_2_bg_image', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_custom_cart_icon', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_site_logo', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_site_logo_dark', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_header_bg_img', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_header_newsletter_bg', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_site_logo_sticky', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_body_bg_image', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_portfolio_archive_bg', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_header_shop_bg_image', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_facebook_login_bg', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_payment_icons_custom', 'silver_theme_mod_fix' );
add_filter( 'theme_mod_follow_snapchat', 'silver_theme_mod_fix' );
