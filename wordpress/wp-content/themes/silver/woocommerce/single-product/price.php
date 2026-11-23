<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          3.0.0
 * @silver-version 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$classes = array();
if($product->is_on_sale()) $classes[] = 'price-on-sale';
if(!$product->is_in_stock()) $classes[] = 'price-not-in-stock'; ?>
<?php
// Buy box (structure-wc-product-buy-box.php): the sale badge "-16%" after the
// price; silver-variation-pills.js updates both for the chosen variation.
$silver_badge = function_exists( 'silver_product_buy_box_enabled' ) && silver_product_buy_box_enabled() ? silver_product_buy_box_badge( $product ) : '';
$silver_badge_format = get_theme_mod( 'sale_bubble_percentage_formatting' ) ? get_theme_mod( 'sale_bubble_percentage_formatting' ) : '-{value}%';
$silver_price_html   = $silver_badge || ( function_exists( 'silver_product_buy_box_enabled' ) && silver_product_buy_box_enabled() )
	? silver_product_buy_box_price_html( $product )
	: $product->get_price_html();
?>
<div class="price-wrapper" data-badge-format="<?php echo esc_attr( $silver_badge_format ); ?>">
	<p class="price product-page-price <?php echo implode(' ', $classes); ?>">
  <?php echo $silver_price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce price HTML. ?></p>
	<?php echo $silver_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_product_buy_box_badge(). ?>
</div>
