<?php
/**
 * Directory warning.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

if ( $template !== 'silver' ) : ?>
<div class="notice notice-warning notice-alt inline" style="display:block!important;margin-bottom:15px!important">
	<p>
		<?php /* translators: 1. Template */ ?>
		<?php echo sprintf( __( 'An unusual theme directory name was detected: <em>%s</em>. The Silver parent theme should be installed in a directory named <em>silver</em> to ensure updates are handled correctly.', 'silver' ), $template ); ?>
	</p>
</div>
<?php endif; ?>
