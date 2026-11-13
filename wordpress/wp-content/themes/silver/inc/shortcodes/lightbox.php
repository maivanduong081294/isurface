<?php

/**
 * [lightbox]
 */
function ux_lightbox( $atts, $content = null ) {
	extract( ux_builder_shortcode_atts( array(
		'id'         => 'enter-id-here',
		'width'      => '650px',
		'padding'    => '20px',
		'class'      => '',
		'auto_open'  => false,
		'auto_timer' => '2500',
		'auto_show'  => '',
		'version'    => '1',
	), $atts ) );

	ob_start();
	?>
	<div id="<?php echo esc_attr( $id ); ?>"
	     class="lightbox-by-id lightbox-content mfp-hide lightbox-white <?php echo esc_attr( $class ); ?>"
	     style="max-width:<?php echo esc_attr( $width ); ?> ;padding:<?php echo esc_attr( $padding ); ?>">
		<?php echo do_shortcode( $content ); ?>
	</div>
	<?php if ( $auto_open ) : ?>
		<script>
			// Auto open lightboxes
			jQuery(document).ready(function ($) {
				/* global silverVars */
				'use strict'
				var cookieId = '<?php echo 'lightbox_' . esc_js( $id ); ?>'
				var cookieValue = '<?php echo 'opened_' . esc_js( $version ); ?>'
				var timer = parseInt('<?php echo intval( $auto_timer ); ?>', 10)

				// Auto open lightbox
				<?php if ( $auto_show == 'always' ) : ?>
				Silver.Cookies.set(cookieId, false)
				<?php endif; ?>

				// Run lightbox if no cookie is set
				if (Silver.Cookies.get(cookieId) !== cookieValue) {

					// Ensure closing off canvas
					setTimeout(function () {
						if (jQuery.fn.magnificPopup) jQuery.magnificPopup.close()
					}, timer - 350)

					// Open lightbox
					setTimeout(function () {
						$.loadMagnificPopup().then(function() {
							$.magnificPopup.open({
								midClick: true,
								removalDelay: 300,
								// closeBtnInside: silverVars.lightbox.close_btn_inside,
								// closeMarkup: silverVars.lightbox.close_markup,
								items: {
									src: '#<?php echo esc_js( $id ); ?>',
									type: 'inline'
								}
							})
						})
					}, timer)

					Silver.Cookies.set(cookieId, cookieValue, { expires: 365 })
				}
			})
		</script>
	<?php endif; ?>

	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'lightbox', 'ux_lightbox' );
