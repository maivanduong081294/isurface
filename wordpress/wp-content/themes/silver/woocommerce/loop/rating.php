<?php
/**
 * Loop Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          3.6.0
 * @silver-version 3.20.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();

// Compact rating (Shop > Product Catalog > Rating Style): one star, the
// average and the number of reviews, "★ 4,6 (92 đánh giá)".
if ( 'compact' === get_theme_mod( 'product_box_rating_style', 'compact' ) ) {
	$average = (float) $product->get_average_rating();

	if ( $rating_count < 1 && ! get_theme_mod( 'product_box_empty_rating', 0 ) ) {
		return;
	}

	$label = $rating_count > 0
		/* translators: %s: average rating, e.g. 4.6 */
		? sprintf( __( 'Được đánh giá %s trên 5', 'silver' ), number_format_i18n( $average, 1 ) )
		: __( 'Chưa có đánh giá', 'silver' );

	$html  = '<div class="star-rating-compact" role="img" aria-label="' . esc_attr( $label ) . '">';
	// The theme's star icon (Font Awesome, see helpers-icons.php).
	$html .= get_silver_icon( 'icon-star', null, array( 'class' => 'star-rating-compact__icon' ) );
	if ( $rating_count > 0 ) {
		$html .= '<span class="star-rating-compact__value" aria-hidden="true">' . esc_html( number_format_i18n( $average, 1 ) ) . '</span>';
		if ( get_theme_mod( 'product_box_review_count', 1 ) ) {
			// "(2 đánh giá)": the number and the words get their own spans, so
			// narrow cards can show "(2)" only (see .star-rating-compact__label).
			/* translators: %s: number of reviews */
			$parts = explode( '%s', _n( '%s đánh giá', '%s đánh giá', $review_count, 'silver' ), 2 );
			$html .= '<span class="star-rating-compact__count" aria-hidden="true">(';
			if ( '' !== trim( $parts[0] ) ) {
				$html .= '<span class="star-rating-compact__label">' . esc_html( $parts[0] ) . '</span>';
			}
			$html .= '<span class="star-rating-compact__num">' . esc_html( number_format_i18n( $review_count ) ) . '</span>';
			if ( isset( $parts[1] ) && '' !== trim( $parts[1] ) ) {
				$html .= '<span class="star-rating-compact__label">' . esc_html( $parts[1] ) . '</span>';
			}
			$html .= ')</span>';
		}
	} else {
		$html .= '<span class="star-rating-compact__count" aria-hidden="true">' . esc_html( $label ) . '</span>';
	}
	$html .= '</div>';

	/**
	 * Filters the compact rating of product cards.
	 *
	 * @param string     $html    HTML.
	 * @param WC_Product $product Product.
	 */
	echo apply_filters( 'silver_loop_rating_compact_html', $html, $product ); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

echo silver_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput

if ( $rating_count > 0 ) :
	if ( get_theme_mod( 'product_box_review_count', 1 ) ) :
		if ( comments_open() ) :
			$span_atts = array(
				'class' => 'review-count is-small op-8',
				/* translators: %s: number of reviews */
				'aria-label' => esc_attr( sprintf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), $review_count ) ),
			);
			$review_count_html = sprintf(
				'<span %1$s>(%2$s)</span>',
				silver_html_atts( $span_atts ),
				esc_html( $review_count )
			);
			echo apply_filters( 'silver_loop_review_count_html', $review_count_html, $product ); // phpcs:ignore WordPress.Security.EscapeOutput
		endif;
	endif;
endif;
