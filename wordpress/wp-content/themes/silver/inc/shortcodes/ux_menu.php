<?php
/**
 * Registers the `ux_menu` shortcode.
 *
 * @package silver
 */

/**
 * Renders the `ux_menu` shortcode.
 *
 * @param array  $atts    An array of attributes.
 * @param string $content The shortcode content.
 * @param string $tag     The name of the shortcode, provided for context to enable filtering.
 *
 * @return string
 */
function silver_render_ux_menu_shortcode( $atts, $content, $tag ) {
	$atts = ux_builder_shortcode_atts(
		array(
			'_id'        => '',
			'visibility' => '',
			'class'      => '',
			'divider'    => '',
		),
		$atts,
		$tag
	);

	$classes = array( 'ux-menu', 'stack', 'stack-col', 'justify-start' );

	if ( ! empty( $atts['class'] ) )      $classes[] = $atts['class'];
	if ( ! empty( $atts['divider'] ) )    $classes[] = 'ux-menu--divider-' . $atts['divider'];
	if ( ! empty( $atts['visibility'] ) ) $classes[] = $atts['visibility'];

	ob_start();

	?>
	<div<?php if ( ! empty( $atts['_id'] ) ) : ?> id="<?php echo esc_attr( $atts['_id'] ); ?>"<?php endif; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php echo do_shortcode( $content ); ?>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'ux_menu', 'silver_render_ux_menu_shortcode' );
