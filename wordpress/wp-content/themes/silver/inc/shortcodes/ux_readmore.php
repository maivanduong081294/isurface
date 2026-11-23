<?php
/**
 * [ux_readmore]...[/ux_readmore] — Long content cut to a height, with a
 * "Read more" / "Read less" button (expand / collapse).
 *
 * The content is cut only when it is taller than height + 80px (no button for
 * a few lines), and only by the script: without JS it is shown in full. The
 * end of the cut content fades out with a mask, so it works on any background.
 *
 * assets/js/src/silver-readmore.js, assets/css/src/silver-readmore.css; both
 * merged into silver.js / silver.css by build/build.mjs.
 */

// [ux_readmore]...[/ux_readmore]
function silver_readmore_shortcode( $atts, $content = null, $tag = 'ux_readmore' ) {
	$atts = ux_builder_shortcode_atts( array(
		'_id'        => '',
		'class'      => '',
		'visibility' => '',
		'height'     => '400',
		'more_text'  => __( 'Read more', 'silver' ),
		'less_text'  => __( 'Read less', 'silver' ),
		'align'      => 'center',
	), $atts, $tag );

	if ( 'hidden' === $atts['visibility'] ) {
		return '';
	}

	// In the classic editor, wpautop leaves "</p>" / "<p>" at the edges of
	// the wrapped content.
	$html = do_shortcode( preg_replace( '#^\s*</p>|<p>\s*$#i', '', (string) $content ) );
	if ( '' === trim( $html ) ) {
		return '';
	}

	$id      = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : 'ux-readmore-' . wp_rand();
	$height  = max( 80, absint( $atts['height'] ) );
	$align   = in_array( $atts['align'], array( 'left', 'center', 'right' ), true ) ? $atts['align'] : 'center';
	$more    = trim( wp_strip_all_tags( $atts['more_text'] ) );
	$less    = trim( wp_strip_all_tags( $atts['less_text'] ) );
	$more    = '' !== $more ? $more : __( 'Read more', 'silver' );
	$less    = '' !== $less ? $less : __( 'Read less', 'silver' );
	$classes = array( 'ux-readmore', 'ux-readmore--' . $align );

	if ( $atts['class'] ) {
		$classes[] = $atts['class'];
	}
	if ( $atts['visibility'] ) {
		$classes[] = $atts['visibility'];
	}

	ob_start();
	?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-height="<?php echo esc_attr( $height ); ?>" style="--ux-readmore-height: <?php echo esc_attr( $height ); ?>px;">
		<div class="ux-readmore__content" id="<?php echo esc_attr( $id ); ?>-content">
			<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered content. ?>
		</div>
		<div class="ux-readmore__actions">
			<button type="button" class="ux-readmore__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>-content" data-more="<?php echo esc_attr( $more ); ?>" data-less="<?php echo esc_attr( $less ); ?>">
				<span class="ux-readmore__label"><?php echo esc_html( $more ); ?></span>
				<svg class="ux-readmore__icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ux_readmore', 'silver_readmore_shortcode' );
