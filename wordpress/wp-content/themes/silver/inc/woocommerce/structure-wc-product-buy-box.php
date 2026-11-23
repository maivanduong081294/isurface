<?php
/**
 * Product page buy box (Shop > Product Page > Buy box).
 *
 * Summary of the product page, in this order:
 * - Title (no divider by default), rating "★★★★★ 4,8 (74 đánh giá)"
 *   (woocommerce/single-product/rating.php), short description.
 * - A card: the price ("Từ 15 ₫" for variable products) with the sale badge "-16%" (single-product/price.php),
 *   the attributes as buttons instead of dropdowns, then "Add to cart" and
 *   "Call now 0900 000 000" side by side.
 *
 * The buttons of the attributes keep the dropdowns of WooCommerce (hidden),
 * assets/js/src/silver-variation-pills.js selects their values. Attributes
 * shown by the Swatches extension keep its swatches.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the product page uses the buy box layout.
 *
 * @return bool
 */
function silver_product_buy_box_enabled() {
	return (bool) apply_filters( 'silver_product_buy_box', get_theme_mod( 'product_buy_box', 1 ) );
}

/**
 * Moves the price into the card, after the short description.
 */
function silver_product_buy_box_setup() {
	if ( ! function_exists( 'is_product' ) || ! is_product() || ! silver_product_buy_box_enabled() ) {
		return;
	}

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

	// Excerpt at 20, add to cart at 30.
	add_action( 'woocommerce_single_product_summary', 'silver_product_buy_box_open', 25 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 26 );
	add_action( 'woocommerce_single_product_summary', 'silver_product_buy_box_close', 35 );
	// Meta (40) in a panel (opened and closed by the renderer below).
	add_action( 'woocommerce_single_product_summary', 'silver_product_details_open', 39 );
	add_action( 'woocommerce_single_product_summary', 'silver_product_details_close', 51 );
	add_action( 'woocommerce_after_add_to_cart_button', 'silver_product_call_button' );
	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'silver_product_variation_pills', 20, 2 );
	add_filter( 'woocommerce_reset_variations_link', 'silver_product_reset_variations_link' );

	// Order of the parts (Product Page > Product info elements).
	add_action( 'woocommerce_single_product_summary', 'silver_product_info_elements_render', 1 );
}

/**
 * Parts of the summary that can be sorted and hidden (Product Page >
 * Product info elements), with the callbacks of woocommerce_single_product_summary
 * that print them.
 *
 * @return array Key => array( label, callbacks (callback => priority) ).
 */
function silver_product_info_elements() {
	return array(
		'title'   => array(
			'label'     => __( 'Title', 'silver' ),
			'callbacks' => array(
				'woocommerce_template_single_title'   => 5,
				'silver_product_mobile_next_prev_nav' => 7,
			),
		),
		'rating'  => array(
			'label'     => __( 'Rating', 'silver' ),
			'callbacks' => array( 'woocommerce_template_single_rating' => 10 ),
		),
		'excerpt' => array(
			'label'     => __( 'Short description', 'silver' ),
			'callbacks' => array( 'woocommerce_template_single_excerpt' => 20 ),
		),
		'siblings' => array(
			'label'     => __( 'Product siblings', 'silver' ),
			'callbacks' => array( 'silver_product_siblings_html' => 22 ),
		),
		'buy_box' => array(
			'label'     => __( 'Buy box', 'silver' ),
			'callbacks' => array(
				'silver_product_buy_box_open'           => 25,
				'woocommerce_template_single_price'     => 26,
				'woocommerce_template_single_add_to_cart' => 30,
				'silver_product_buy_box_close'          => 35,
			),
		),
		'meta'    => array(
			'label'     => __( 'SKU and categories', 'silver' ),
			'callbacks' => array( 'woocommerce_template_single_meta' => 40 ),
		),
		'share'   => array(
			'label'     => __( 'Share', 'silver' ),
			'callbacks' => array( 'woocommerce_template_single_sharing' => 50 ),
		),
	);
}

/**
 * Parts added after the order was saved show at their default place: the
 * saved value lists the shown parts only, so the parts known when it was
 * saved are kept (product_info_elements_known; the first six before that)
 * and the others are inserted after the part before them by default. Also
 * the value of the Customizer control, so it shows them turned on.
 *
 * @param mixed $order Saved order.
 *
 * @return mixed
 */
function silver_product_info_elements_add_new( $order ) {
	if ( ! is_array( $order ) ) {
		return $order;
	}

	$known = get_theme_mod( 'product_info_elements_known', array( 'title', 'rating', 'excerpt', 'buy_box', 'meta', 'share' ) );

	return silver_sortable_insert_new( $order, array_keys( silver_product_info_elements() ), $known );
}

/**
 * A saved sortable value (shown items, in order) with the items added since
 * it was saved: an item missing from $known goes after the nearest item
 * before it (by default) that is shown. Items in $known but not in the value
 * were hidden on purpose and stay out.
 *
 * @param array $order    Saved value.
 * @param array $defaults All the items, in their default order.
 * @param mixed $known    Items known when the value was saved.
 *
 * @return array
 */
function silver_sortable_insert_new( $order, $defaults, $known ) {
	$known = is_array( $known ) ? $known : array();

	foreach ( array_values( $defaults ) as $index => $key ) {
		if ( in_array( $key, $known, true ) || in_array( $key, $order, true ) ) {
			continue;
		}
		$at = 0;
		for ( $j = $index - 1; $j >= 0; $j-- ) {
			$found = array_search( $defaults[ $j ], $order, true );
			if ( false !== $found ) {
				$at = $found + 1;
				break;
			}
		}
		array_splice( $order, $at, 0, array( $key ) );
	}

	return $order;
}
add_filter( 'theme_mod_product_info_elements', 'silver_product_info_elements_add_new' );

/** Remembers the parts known when the Customizer is saved. */
function silver_product_info_elements_remember_known() {
	set_theme_mod( 'product_info_elements_known', array_keys( silver_product_info_elements() ) );
}
add_action( 'customize_save_after', 'silver_product_info_elements_remember_known' );

/**
 * Keys in their order, the hidden ones left out.
 *
 * @return string[]
 */
function silver_product_info_elements_order() {
	$elements = silver_product_info_elements();
	$order    = get_theme_mod( 'product_info_elements', array_keys( $elements ) );
	$order    = is_array( $order ) ? array_values( array_intersect( $order, array_keys( $elements ) ) ) : array_keys( $elements );

	return (array) apply_filters( 'silver_product_info_elements_order', $order );
}

/**
 * Prints the parts of the summary in the chosen order (first callback of
 * woocommerce_single_product_summary). Only the parts still hooked print:
 * other options turn some off (featured header title, Disable reviews,
 * catalog mode prices, Product meta / share). They are unhooked from their
 * own place, so they print once. SKU / categories go in a panel, the share
 * icons stand on their own.
 */
function silver_product_info_elements_render() {
	$tag      = 'woocommerce_single_product_summary';
	$elements = silver_product_info_elements();
	$hooked   = array();

	foreach ( $elements as $key => $element ) {
		foreach ( $element['callbacks'] as $callback => $priority ) {
			if ( has_action( $tag, $callback ) === $priority ) {
				$hooked[ $key ][] = $callback;
				remove_action( $tag, $callback, $priority );
			}
		}
	}

	// The panel is opened and closed here (meta and share may be apart).
	remove_action( $tag, 'silver_product_details_open', 39 );
	remove_action( $tag, 'silver_product_details_close', 51 );

	$order = silver_product_info_elements_order();
	$count = count( $order );

	foreach ( $order as $key ) {
		// SKU / categories in a panel; the share icons on their own.
		if ( 'meta' === $key ) {
			silver_product_details_open();
		}

		foreach ( isset( $hooked[ $key ] ) ? $hooked[ $key ] : array() as $callback ) {
			call_user_func( $callback );
		}

		if ( 'meta' === $key ) {
			silver_product_details_close();
		}
	}
}

/**
 * "Xóa lựa chọn" instead of "Clear": an icon and words of the theme, top
 * right of the attributes (silver-modern.css). WooCommerce still shows and
 * hides it (visibility) and clears the choice on click. The output goes
 * through wp_kses_post(), so the icon is a CSS mask, not an <svg>.
 *
 * @return string
 */
function silver_product_reset_variations_link() {
	return '<a class="reset_variations sv-reset-variations" href="#" aria-label="' . esc_attr__( 'Clear all choices', 'silver' ) . '">'
		. '<span class="sv-reset-variations__icon" aria-hidden="true"></span>'
		. esc_html__( 'Clear choices', 'silver' )
		. '</a>';
}
add_action( 'wp', 'silver_product_buy_box_setup' );

/**
 * Whether the "Call now" button was printed in the card (static flag).
 *
 * @param bool|null $set True to mark it printed.
 *
 * @return bool
 */
function silver_product_call_button_printed( $set = null ) {
	static $printed = false;

	if ( null !== $set ) {
		$printed = (bool) $set;
	}

	return $printed;
}

/** Opens the card. */
function silver_product_buy_box_open() {
	$classes = array( 'product-buy-box' );
	if ( get_theme_mod( 'product_buy_box_quantity', 1 ) ) {
		$classes[] = 'has-quantity';
	}

	silver_product_call_button_printed( false );

	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
}

/** Closes the card; "Call now" alone when there is no add to cart form (out of stock). */
function silver_product_buy_box_close() {
	if ( ! silver_product_call_button_printed() ) {
		ob_start();
		silver_product_call_button();
		$button = ob_get_clean();

		if ( '' !== $button ) {
			echo '<div class="product-buy-box__actions">' . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_product_call_button().
		}
	}

	echo '</div>';
}

/**
 * Opens the panel of the product meta and share row (buffered: no empty
 * panel when both are turned off).
 */
function silver_product_details_open() {
	ob_start();
}

/** Closes the panel of the product meta and share row. */
function silver_product_details_close() {
	$html = (string) ob_get_clean();

	if ( '' !== trim( $html ) ) {
		echo '<div class="product-details-panel">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- templates output.
	}
}

/**
 * Phone number of the "Call now" button (Product Page > Call button phone).
 *
 * @return string Empty when not set.
 */
function silver_product_call_phone() {
	return trim( (string) apply_filters( 'silver_product_call_phone', get_theme_mod( 'product_call_phone', '' ) ) );
}

/** "Call now 0900 000 000", next to "Add to cart". */
function silver_product_call_button() {
	$phone = silver_product_call_phone();
	$tel   = preg_replace( '/[^0-9+]/', '', $phone );

	if ( '' === $tel ) {
		return;
	}

	silver_product_call_button_printed( true );

	printf(
		'<a class="button product-call-button" href="%1$s"><svg class="product-call-button__icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>%2$s</span></a>',
		esc_url( 'tel:' . $tel ),
		/* translators: %s: phone number */
		esc_html( sprintf( __( 'Call now %s', 'silver' ), $phone ) )
	);
}

/**
 * Price of the card. Variable products, before every attribute is chosen:
 * the lowest price only, "Từ 15 ₫" (no struck-out price, no highest price),
 * "Từ" inside the amount so it has the format of the price
 * (silver_variable_price_from()). silver-variation-pills.js shows the price of
 * the chosen variation.
 *
 * @param WC_Product $product Product.
 *
 * @return string HTML.
 */
function silver_product_buy_box_price_html( $product ) {
	if ( ! $product->is_type( 'variable' ) ) {
		return $product->get_price_html();
	}

	$prices = $product->get_variation_prices( true ); // Display prices, low to high.
	if ( empty( $prices['price'] ) ) {
		return $product->get_price_html();
	}

	$min = (float) reset( $prices['price'] );
	$max = (float) end( $prices['price'] );

	// Every variation at the same price: WooCommerce's price (struck-out
	// regular price included when on sale).
	if ( $min === $max ) {
		return $product->get_price_html();
	}

	$from   = '<span class="price-from">' . esc_html_x( 'Từ', 'variable product price', 'silver' ) . '</span> ';
	$amount = wc_price( $min );
	$pos    = strpos( $amount, '<bdi>' );
	$amount = false === $pos ? $from . $amount : substr_replace( $amount, $from, $pos, 0 );

	return apply_filters( 'silver_product_buy_box_price_html', $amount . $product->get_price_suffix(), $product );
}

/**
 * Sale badge "-16%" next to the price (single-product/price.php). Not for
 * variable products until a variation is chosen.
 *
 * @param WC_Product $product Product.
 *
 * @return string HTML, empty when not on sale.
 */
function silver_product_buy_box_badge( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() || ! function_exists( 'silver_presentage_bubble' ) ) {
		return '';
	}

	// Variable products: no badge before every attribute is chosen, only the
	// one of the chosen variation (silver-variation-pills.js).
	if ( $product->is_type( 'variable' ) ) {
		return '';
	}

	$text = silver_presentage_bubble( $product, '' );
	if ( '' === $text || ! preg_match( '/[1-9]/', $text ) ) {
		return '';
	}

	return '<span class="product-price-badge">' . esc_html( $text ) . '</span>';
}

/**
 * Attributes as buttons: the dropdown of WooCommerce stays (hidden) and
 * holds the value, silver-variation-pills.js selects it.
 *
 * @param string $html Dropdown.
 * @param array  $args wc_dropdown_variation_attribute_options() arguments.
 *
 * @return string
 */
function silver_product_variation_pills( $html, $args ) {
	// Swatches extension, or a dropdown printed outside the product page.
	if ( false !== strpos( $html, 'ux-swatches' ) || ! is_product() || ! apply_filters( 'silver_product_variation_pills', true, $args ) ) {
		return $html;
	}

	$product   = isset( $args['product'] ) ? $args['product'] : null;
	$attribute = isset( $args['attribute'] ) ? $args['attribute'] : '';
	$options   = isset( $args['options'] ) ? (array) $args['options'] : array();

	if ( ! $product instanceof WC_Product || '' === $attribute ) {
		return $html;
	}

	if ( ! $options ) {
		$attributes = $product->get_variation_attributes();
		$options    = isset( $attributes[ $attribute ] ) ? (array) $attributes[ $attribute ] : array();
	}

	// Value of the option in the dropdown => label.
	$items = array();
	if ( taxonomy_exists( $attribute ) ) {
		foreach ( wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) ) as $term ) {
			if ( in_array( $term->slug, $options, true ) ) {
				$items[ $term->slug ] = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
			}
		}
	} else {
		foreach ( $options as $option ) {
			$items[ $option ] = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
		}
	}

	if ( ! $items ) {
		return $html;
	}

	$name     = ! empty( $args['name'] ) ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$selected = isset( $args['selected'] ) ? (string) $args['selected'] : '';
	$buttons  = '';

	foreach ( $items as $value => $label ) {
		$value    = (string) $value;
		$checked  = '' !== $selected && ( $selected === $value || sanitize_title( $selected ) === $value );
		$buttons .= sprintf(
			'<button type="button" class="sv-variation-pill%1$s" role="radio" aria-checked="%2$s" data-value="%3$s">%4$s</button>',
			$checked ? ' is-selected' : '',
			$checked ? 'true' : 'false',
			esc_attr( $value ),
			esc_html( $label )
		);
	}

	return '<div class="sv-variation-select">' . $html . '</div>'
		. '<div class="sv-variation-pills" role="radiogroup" aria-label="' . esc_attr( wc_attribute_label( $attribute, $product ) ) . '" data-attribute_name="' . esc_attr( $name ) . '">'
		. $buttons
		. '</div>';
}
