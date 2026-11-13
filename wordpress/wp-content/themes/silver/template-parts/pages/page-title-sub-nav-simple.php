<?php
/**
 * Page title with sub nav simple.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

?>
<div class="page-title <?php silver_header_title_classes() ?>">

	<div class="page-title-bg fill"><div class="page-title-bg-overlay"></div></div>

	<div class="page-title-inner container flex-row medium-flex-wrap medium-text-center">
	 	<div class="flex-col flex-grow">
	 		<?php get_silver_subnav(); ?>
	 	</div>
	</div>
</div>
