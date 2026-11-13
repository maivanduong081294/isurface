<?php
/**
 * Portfolio next/prev.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

if(get_theme_mod('portfolio_next_prev',1) == 0) return;
?>
<div class="row">
<div class="xlarge-12 col pb-0">
	<div class="flex-row flex-has-center next-prev-nav bt bb">
		<div class="flex-col flex-left text-left">
			<?php silver_previous_post_link_portfolio(); ?>
		</div>
		<div class="flex-col flex-right text-right">
		    <?php silver_next_post_link_portfolio(); ?>
		</div>
	</div>
</div>
</div>
