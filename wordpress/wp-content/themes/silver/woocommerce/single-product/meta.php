<?php
/**
 * Single Product Meta
 *
 * Rows of a label and a value (silver-modern.css): SKU (hidden when the
 * product has none), categories and tags as chips. The .product_meta and .sku classes stay, WooCommerce updates the
 * SKU of the chosen variation through them.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          11.2.0
 */

use Automattic\WooCommerce\Enums\ProductType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta product-meta-list">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( ProductType::VARIABLE ) ) ) : ?>

		<?php // No SKU: the row stays hidden until a variation with one is chosen (silver-variation-pills.js). ?>
		<div class="sku_wrapper product-meta-row"<?php echo $product->get_sku() ? '' : ' hidden'; ?>>
			<span class="product-meta-row__label"><?php esc_html_e( 'SKU', 'silver' ); ?></span>
			<span class="product-meta-row__value"><span class="sku"><?php echo ( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'silver' ); ?></span></span>
		</div>

	<?php endif; ?>

	<?php
	/** This filter is documented in woocommerce/templates/single-product/meta.php */
	$product_category_orderby = apply_filters( 'woocommerce_product_meta_category_orderby', 'breadcrumb', $product );

	$product_category_list = wc_get_product_category_list(
		$product->get_id(),
		'',
		'<div class="posted_in product-meta-row"><span class="product-meta-row__label">' . esc_html( _n( 'Category', 'Categories', count( $product->get_category_ids() ), 'silver' ) ) . '</span><span class="product-meta-row__value product-meta-row__chips">',
		'</span></div>',
		is_string( $product_category_orderby ) ? $product_category_orderby : ''
	);
	echo is_string( $product_category_list ) ? $product_category_list : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

	<?php
	echo wc_get_product_tag_list( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$product->get_id(),
		'',
		'<div class="tagged_as product-meta-row"><span class="product-meta-row__label">' . esc_html( _n( 'Tag', 'Tags', count( $product->get_tag_ids() ), 'silver' ) ) . '</span><span class="product-meta-row__value product-meta-row__chips">',
		'</span></div>'
	);
	?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
