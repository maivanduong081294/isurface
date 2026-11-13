<?php
/**
 * Icon helper functions.
 *
 * @package Silver
 */

/**
 * Font Awesome aliases for existing theme content and saved icon settings.
 *
 * @return array
 */
function silver_fontawesome_icon_map() {
	static $icons = array(
		'icon-lock' => 'fa-solid fa-lock',
		'icon-chat' => 'fa-solid fa-comment-dots',
		'icon-user' => 'fa-solid fa-user',
		'icon-shopping-cart' => 'fa-solid fa-cart-shopping',
		'icon-gift' => 'fa-solid fa-gift',
		'icon-phone' => 'fa-solid fa-phone',
		'icon-play' => 'fa-solid fa-play',
		'icon-menu' => 'fa-solid fa-bars',
		'icon-equalizer' => 'fa-solid fa-sliders',
		'icon-shopping-basket' => 'fa-solid fa-basket-shopping',
		'icon-shopping-bag' => 'fa-solid fa-bag-shopping',
		'icon-heart' => 'fa-solid fa-heart',
		'icon-angle-left' => 'fa-solid fa-angle-left',
		'icon-angle-right' => 'fa-solid fa-angle-right',
		'icon-angle-up' => 'fa-solid fa-angle-up',
		'icon-angle-down' => 'fa-solid fa-angle-down',
		'icon-envelop' => 'fa-solid fa-envelope',
		'icon-tag' => 'fa-solid fa-tag',
		'icon-star' => 'fa-solid fa-star',
		'icon-feed' => 'fa-solid fa-rss',
		'icon-checkmark' => 'fa-solid fa-check',
		'icon-plus' => 'fa-solid fa-plus',
		'icon-cross' => 'fa-solid fa-xmark',
		'icon-search' => 'fa-solid fa-magnifying-glass',
		'icon-certificate' => 'fa-solid fa-certificate',
		'icon-expand' => 'fa-solid fa-expand',
		'icon-map-pin-fill' => 'fa-solid fa-location-dot',
		'icon-pen-alt-fill' => 'fa-solid fa-pen',
		'icon-user-o' => 'fa-regular fa-user',
		'icon-heart-o' => 'fa-regular fa-heart',
		'icon-star-o' => 'fa-regular fa-star',
		'icon-clock' => 'fa-regular fa-clock',
		'icon-line' => 'fa-brands fa-line',
		'icon-tumblr' => 'fa-brands fa-tumblr',
		'icon-google-plus' => 'fa-brands fa-google-plus-g',
		'icon-500px' => 'fa-brands fa-500px',
		'icon-vk' => 'fa-brands fa-vk',
		'icon-x' => 'fa-brands fa-x-twitter',
		'icon-twitter' => 'fa-brands fa-twitter',
		'icon-facebook' => 'fa-brands fa-facebook-f',
		'icon-instagram' => 'fa-brands fa-instagram',
		'icon-tiktok' => 'fa-brands fa-tiktok',
		'icon-pinterest' => 'fa-brands fa-pinterest',
		'icon-skype' => 'fa-brands fa-skype',
		'icon-dribbble' => 'fa-brands fa-dribbble',
		'icon-linkedin' => 'fa-brands fa-linkedin-in',
		'icon-youtube' => 'fa-brands fa-youtube',
		'icon-flickr' => 'fa-brands fa-flickr',
		'icon-snapchat' => 'fa-brands fa-snapchat',
		'icon-whatsapp' => 'fa-brands fa-whatsapp',
		'icon-telegram' => 'fa-brands fa-telegram',
		'icon-twitch' => 'fa-brands fa-twitch',
		'icon-discord' => 'fa-brands fa-discord',
		'icon-threads' => 'fa-brands fa-threads',
	);

	return $icons;
}

/**
 * Get theme icon by classname.
 *
 * @param string $name The icon name.
 * @param string $size Optional size corresponding to font size.
 * @param array  $atts Optional element attributes.
 *
 * @return string Icon markup.
 */
function get_silver_icon( $name, $size = null, $atts = null ) {
	$default_atts = array(
		'class'       => [],
		'aria-hidden' => 'true',
	);

	if ( $size ) {
		$default_atts['style'] = 'font-size:' . esc_attr( $size ) . ';';
	}

	$atts = wp_parse_args( $atts, $default_atts );

	if ( ! is_array( $atts['class'] ) ) {
		$atts['class'] = $atts['class'] ? [ $atts['class'] ] : [];
	}

	$icons = silver_fontawesome_icon_map();
	if ( isset( $icons[ $name ] ) ) {
		$atts['class'] = array_merge( $atts['class'], explode( ' ', $icons[ $name ] ) );
	}
	$atts['class'][] = esc_attr( $name );

	$icon_html = '<i ' . silver_html_atts( $atts ) . '></i>';

	return apply_filters( 'silver_icon', $icon_html, $name, $size, $atts );
}

/**
 * Load self-hosted Font Awesome before the theme styles.
 */
function silver_add_icons_css() {
	wp_enqueue_style(
		'silver-fontawesome',
		get_template_directory_uri() . '/assets/css/icons/fontawesome/css/all.min.css',
		array(),
		'7.3.1'
	);
}

add_action( 'wp_enqueue_scripts', 'silver_add_icons_css', 90 );
