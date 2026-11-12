<?php
/**
 * Welcome screen getting started template
 */

use function Flatsome\Admin\status;
?>

<div id="tab-activate" class="col cols panel flatsome-panel">
	<div class="cols">
		<div class="inner-panel">
			<h3><?php esc_html_e( 'Status', 'flatsome' ); ?></h3>
			<?php status()->render_section_overview(); ?>


		</div>
	</div>
</div>
