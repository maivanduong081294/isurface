<?php
/**
 * Version info.
 *
 * @package          Silver\Templates
 * @silver-version 3.18.0
 */

iframe_header();
?>
<div id="wpwrap" class="silver-panel" style="text-align:center;">
	<a href="https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319#item-description__change-log" style="display:inline-block;" target="_blank" rel="noopener">
		<div class="wp-badge fl-badge">
			<?php /* translators: 1: Version. */ ?>
			<?php echo sprintf( __( 'Version %s', 'silver' ), $version ); ?>
		</div>
		<div style="margin-top:8px;">
			<?php echo __( 'Read change log here', 'silver' ); ?>
		</div>
	</a>
</div>

<?php iframe_footer(); ?>
