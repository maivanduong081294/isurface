<?php
/**
 * Shop breadcrumb
 *
 * @see              woocommerce_breadcrumb()
 *
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          2.3.0
 * @silver-version 3.19.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !empty($breadcrumb) ) {

	do_action('silver_before_breadcrumb');

	echo $wrap_before;

	// Single products: the product name ends the trail in the bar under the
	// header (Home > Category > Product). Above the product title it would
	// repeat the title, so there the trail ends at the category.
	if ( is_product() && count( $breadcrumb ) > 1 && ! apply_filters( 'silver_breadcrumb_show_product_title', 'header' === silver_breadcrumbs_position() ) ) {
		array_pop( $breadcrumb );
		$link_last = true;
	} else {
		$link_last = false;
	}

	$last = count( $breadcrumb ) - 1;

	foreach ( array_values( $breadcrumb ) as $key => $crumb ) {

		echo $before;

		if ( ! empty( $crumb[1] ) && ( $key < $last || $link_last ) ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} elseif ( $key === $last ) {
			echo '<span class="breadcrumb-current" aria-current="page">' . esc_html( $crumb[0] ) . '</span>';
		} else {
			echo esc_html( $crumb[0] );
		}

		echo $after;

		if ( $key < $last ) {
			echo ' <span class="divider" aria-hidden="true">' . $delimiter . '</span> ';
		}
	}

	echo $wrap_after;

	do_action('silver_after_breadcrumb');

}
