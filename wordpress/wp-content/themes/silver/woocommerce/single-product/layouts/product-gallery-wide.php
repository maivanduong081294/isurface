<?php
/**
 * Product gallery wide.
 *
 * @package          Silver/WooCommerce/Templates
 * @silver-version 3.19.9
 */

?>
<div class="product-container">

	<div class="product-gallery product-gallery-wide">
	<?php
		/**
		 * woocommerce_before_single_product_summary hook
		 *
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>
	</div>

	<div class="row">
		<div class="col xlarge-12">
			<div class="product-info">
				<div class="row">
					<div class="xlarge-7 col">
						<div class="entry-summary <?php silver_product_summary_classes();?>">
							<?php
								 silver_breadcrumb();
								 woocommerce_template_single_title();
								 woocommerce_template_single_rating();
								 woocommerce_template_single_excerpt();

								 if(get_theme_mod('product_info_share', 1)){
								 	woocommerce_template_single_sharing();
								 }
							?>
						</div>
					</div>
					<div class="xlarge-5 col">
						<div class="is-well add-to-cart-wrapper <?php silver_product_summary_classes( false, false, true );?>">
							<?php

								if ( ! get_theme_mod( 'catalog_mode', 0 ) ) {
									woocommerce_template_single_price();
									silver_before_add_to_cart_html();
									woocommerce_template_single_add_to_cart();
									silver_after_add_to_cart_html();
								} else {
									if ( get_theme_mod( 'catalog_mode_prices', 0 ) ) {
										woocommerce_template_single_price();
									}
									echo '<div class="catalog-product-text pb relative">';
								    echo do_shortcode( get_theme_mod( 'catalog_mode_product', '' ) );
								    echo '</div>';
								}
								woocommerce_template_single_meta();
							?>
						</div>
					</div>
					<?php if ( get_theme_mod( 'product_offcanvas_sidebar' ) ) : ?>
					<div id="product-sidebar" class="mfp-hide">
						<div class="sidebar-inner">
							<?php
								do_action( 'silver_before_product_sidebar' );
								if ( is_active_sidebar( 'product-sidebar' ) ) {
									dynamic_sidebar( 'product-sidebar' );
								} else if ( is_active_sidebar( 'shop-sidebar' ) ) {
									dynamic_sidebar( 'shop-sidebar' );
								}
							?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="product-footer" style="margin-top: 30px;">
					<?php
						/**
						 * woocommerce_after_single_product_summary hook
						 *
						 * @hooked woocommerce_output_product_data_tabs - 10
						 * @hooked woocommerce_upsell_display - 15
						 * @hooked woocommerce_output_related_products - 20
						 */
						do_action( 'woocommerce_after_single_product_summary' );
					?>
			</div>
		</div>
	</div>
</div>
