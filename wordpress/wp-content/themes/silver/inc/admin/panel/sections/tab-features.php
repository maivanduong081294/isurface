<?php
/**
 * Silver Features
 *
 * @package Silver\Admin
 */

?>

<div id="tab-features">
	<div class="col cols panel silver-panel">
		<div class="inner-panel">
			<form method="post" action="options.php">
				<?php settings_errors(); ?>
				<?php settings_fields( 'silver-features' ); ?>
				<?php do_settings_sections( 'silver-features' ); ?>
				<?php submit_button( '', 'primary large' ); ?>
			</form>
		</div>
	</div>
</div>
