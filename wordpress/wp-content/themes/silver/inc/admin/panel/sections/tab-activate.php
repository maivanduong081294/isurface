<?php
/**
 * Welcome screen getting started template
 */

use function Silver\Admin\status;
?>

<div id="tab-activate" class="col cols panel silver-panel">
	<div class="cols">
		<div class="inner-panel">
			<h3><?php esc_html_e( 'Status', 'silver' ); ?></h3>
			<?php status()->render_section_overview(); ?>


		</div>
	</div>
</div>
