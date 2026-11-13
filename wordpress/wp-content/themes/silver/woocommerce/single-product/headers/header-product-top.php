<?php
/**
 * Header product top.
 *
 * @package          Silver/WooCommerce/Templates
 * @silver-version 3.16.0
 */

?>
<div class="page-title shop-page-title product-page-title">
	<div class="page-title-inner flex-row medium-flex-wrap container">
	  <div class="flex-col flex-grow medium-text-center">
	  		<?php do_action('silver_product_title') ;?>
	  </div>

	   <div class="flex-col medium-text-center">
		   	<?php do_action('silver_product_title_tools') ;?>
	   </div>
	</div>
</div>
