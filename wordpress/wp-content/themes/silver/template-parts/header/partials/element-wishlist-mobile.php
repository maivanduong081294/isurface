<?php
/**
 * Mobile wishlist element.
 *
 * @package          Silver\Templates
 * @silver-version 3.20.0
 */

if ( ! class_exists( 'YITH_WCWL' ) ) {
	return;
}

$icon       = get_theme_mod( 'wishlist_icon', 'heart' );
$icon_style = get_theme_mod( 'wishlist_icon_style' );
$count      = function_exists( 'yith_wcwl_wishlists' ) ? yith_wcwl_wishlists()->count_items_in_wishlist() : YITH_WCWL()->count_products();
$has_items  = $count > 0;

$link_atts = [
	'href'       => YITH_WCWL()->get_wishlist_url(),
	'class'      => [ 'wishlist-link' ],
	'title'      => esc_attr__( 'Wishlist', 'silver' ),
	'aria-label' => esc_attr__( 'Wishlist', 'silver' ),
];

if ( $icon_style ) {
	$link_atts['class'][] = get_silver_icon_class( $icon_style, 'small' );
}

$icon_atts = [
	'class'           => [
		'wishlist-icon',
	],
	'data-icon-label' => $has_items ? $count : null,
];

?>
<li class="header-wishlist-icon has-icon">
	<?php if ( $icon_style ) echo '<div class="header-button">'; ?>
	<a <?php echo silver_html_atts( $link_atts ); ?>>
		<?php echo get_silver_icon( 'icon-' . $icon, null, $icon_atts ); ?>
	</a>
	<?php if ( $icon_style ) echo '</div>'; ?>
</li>
