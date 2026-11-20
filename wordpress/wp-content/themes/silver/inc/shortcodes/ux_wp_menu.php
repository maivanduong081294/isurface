<?php
/**
 * [ux_wp_menu] — a menu from Appearance > Menus.
 *
 * Horizontal: the header walker (SilverNavDropdown), sub menus are dropdowns
 * on hover. Vertical: the mobile sidebar walker (SilverNavSidebar), sub menus
 * open / close as an accordion. Both use the theme nav styles (nav-line,
 * nav-pills...), so they look like the header menus.
 *
 * @package silver
 */

/**
 * Render [ux_wp_menu].
 *
 * @param array  $atts    Attributes.
 * @param string $content Unused.
 * @param string $tag     Shortcode tag.
 *
 * @return string
 */
function silver_wp_menu_shortcode( $atts, $content = null, $tag = 'ux_wp_menu' ) {
	$atts = ux_builder_shortcode_atts( array(
		'_id'        => '',
		'class'      => '',
		'visibility' => '',
		'menu'       => '',
		'direction'  => 'horizontal', // horizontal / vertical
		'depth'      => '0',
		'style'      => 'simple',
		'align'      => 'left',
		'size'       => '',
		'case'       => '',
	), $atts, $tag );

	if ( 'hidden' === $atts['visibility'] ) return '';

	$menu = $atts['menu'] ? wp_get_nav_menu_object( $atts['menu'] ) : false;
	if ( ! $menu ) {
		// Hint for editors only (nothing on the live site).
		$can_edit = current_user_can( 'edit_theme_options' ) || ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() );
		return $can_edit ? '<p class="lead shortcode-error text-center">' . esc_html__( 'WP Menu: select a menu (Appearance > Menus).', 'silver' ) . '</p>' : '';
	}

	$vertical = 'vertical' === $atts['direction'];
	$align    = in_array( $atts['align'], array( 'left', 'center', 'right' ), true ) ? $atts['align'] : 'left';
	$id       = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : '';

	$classes = array( 'nav', $vertical ? 'nav-vertical' : 'nav-horizontal' );
	if ( $atts['style'] ) $classes[] = 'nav-' . sanitize_html_class( $atts['style'] );
	if ( $atts['case'] ) $classes[] = 'nav-' . sanitize_html_class( $atts['case'] );
	if ( $atts['size'] ) $classes[] = 'nav-size-' . sanitize_html_class( $atts['size'] );
	$classes[] = 'nav-' . $align;
	$classes[] = 'text-' . $align;

	$wrapper = array( 'ux-wp-menu', 'nav-wrapper', $vertical ? 'ux-wp-menu-vertical' : 'ux-wp-menu-horizontal' );
	if ( $atts['class'] ) $wrapper[] = $atts['class'];
	if ( $atts['visibility'] ) $wrapper[] = $atts['visibility'];

	$walker = $vertical ? 'SilverNavSidebar' : 'SilverNavDropdown';
	$items  = wp_nav_menu( array(
		'menu'        => $menu->term_id,
		'container'   => false,
		'items_wrap'  => '%3$s',
		'depth'       => max( 0, absint( $atts['depth'] ) ),
		'walker'      => class_exists( $walker ) ? new $walker() : '',
		'fallback_cb' => false,
		'echo'        => false,
	) );

	// Vertical: every sub level gets the accordion toggle (the theme JS adds a
	// toggle button before each "ul.children"; the walker marks level 1 only).
	if ( $vertical && $items ) {
		$items = str_replace( 'class="sub-menu nav-sidebar-ul"', 'class="sub-menu nav-sidebar-ul children"', $items );
	}

	// Responsive alignment (larger screens).
	$style = '';
	if ( silver_responsive_has_overrides( (array) $atts, 'align' ) ) {
		if ( ! $id ) $id = 'ux-wp-menu-' . wp_rand();
		$nav_map = array();
		foreach ( silver_responsive_map( 'text_align' ) as $value => $declaration ) {
			$nav_map[ $value ] = $declaration . '; ' . silver_responsive_map( 'nav_align' )[ $value ];
		}
		$style = silver_responsive_style_tag( $id, (array) $atts, array(
			'align' => array( 'selector' => '> .nav', 'only_overrides' => true, 'default' => 'left', 'map' => $nav_map ),
		) );
	}

	return $style
		. '<div' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' class="' . esc_attr( implode( ' ', $wrapper ) ) . '">'
		. '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $items . '</ul>'
		. '</div>';
}
add_shortcode( 'ux_wp_menu', 'silver_wp_menu_shortcode' );
