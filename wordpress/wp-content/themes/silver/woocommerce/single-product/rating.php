<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @package          WooCommerce/Templates
 * @version          3.6.0
 * @silver-version 3.18.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;
$review_ratings_enabled = wc_review_ratings_enabled();
if ( ! $review_ratings_enabled ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

// Buy box (structure-wc-product-buy-box.php): "★★★★★ 4,8 (74 đánh giá)".
if ( $rating_count > 0 && function_exists( 'silver_product_buy_box_enabled' ) && silver_product_buy_box_enabled() ) {
	$average = (float) $average;
	// Font Awesome stars in two layers: five outline stars (fa-regular), and
	// over them five full stars cut at the average, as a % of the width
	// (3,5 → 70%), all in the colour of the stars.
	$percent = round( max( 0, min( 5, $average ) ) / 5 * 100, 2 );
	$outline = str_repeat( get_silver_icon( 'icon-star-o', null, array( 'class' => 'product-rating-summary__star' ) ), 5 );
	$solid   = str_repeat( get_silver_icon( 'icon-star', null, array( 'class' => 'product-rating-summary__star' ) ), 5 );
	$stars   = '<span class="product-rating-summary__stars-base">' . $outline . '</span>'
		. '<span class="product-rating-summary__stars-fill" style="width:' . esc_attr( $percent ) . '%">' . $solid . '</span>';
	$value   = number_format_i18n( $average, 1 );
	/* translators: %s: average rating, e.g. 4.6 */
	$label = sprintf( __( 'Được đánh giá %s trên 5', 'silver' ), $value );
	/* translators: %s: number of reviews */
	$count = sprintf( _n( '%s đánh giá', '%s đánh giá', $review_count, 'silver' ), number_format_i18n( $review_count ) );
	?>
	<div class="woocommerce-product-rating product-rating-summary">
		<span class="product-rating-summary__stars" role="img" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo $stars; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icons. ?></span>
		<span class="product-rating-summary__value" aria-hidden="true"><?php echo esc_html( $value ); ?></span>
		<?php if ( comments_open() ) : ?>
			<a href="#reviews" class="woocommerce-review-link product-rating-summary__count" rel="nofollow">(<?php echo esc_html( $count ); ?>)</a>
		<?php else : ?>
			<span class="product-rating-summary__count">(<?php echo esc_html( $count ); ?>)</span>
		<?php endif; ?>
	</div>
	<?php
	return;
}

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating">
		<?php echo silver_get_rating_html( $average, $rating_count, true ); // WPCS: XSS ok. ?>
		<?php if ( get_theme_mod( 'product_info_review_count' ) && get_theme_mod( 'product_info_review_count_style' ) != 'tooltip' ) : ?>
			<?php if ( comments_open() ) : ?>
			<?php //phpcs:disable ?>
				<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
			<?php // phpcs:enable ?>
			<?php endif ?>
		<?php endif; ?>
	</div>

<?php endif; ?>
