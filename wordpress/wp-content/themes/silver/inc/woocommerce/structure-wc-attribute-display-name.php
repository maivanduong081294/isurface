<?php
/**
 * Display Name of global product attributes (Products > Attributes).
 *
 * - Display Name: the name customers see (attributes table, variation
 *   selects, cart, filters...). The attribute Name stays the admin name, so
 *   "RAM laptop" can show as "RAM". Empty: the Name is used.
 * - Show on product cards: the attribute chips of product cards (see
 *   structure-wc-card-attributes.php) show the name with the values, in one
 *   chip: "RAM: 16GB". Off: one chip per value, without the name.
 *
 * Stored in one option, keyed by attribute ID.
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/** Option holding attribute ID => array( display_name, card_label ). */
const SILVER_ATTRIBUTE_META_OPTION = 'silver_attribute_meta';

/**
 * Settings of an attribute.
 *
 * @param int $attribute_id Attribute ID.
 * @return array display_name (string), card_label (bool).
 */
function silver_attribute_meta( $attribute_id ) {
	$all  = get_option( SILVER_ATTRIBUTE_META_OPTION, array() );
	$meta = isset( $all[ (int) $attribute_id ] ) ? (array) $all[ (int) $attribute_id ] : array();

	return array(
		'display_name' => isset( $meta['display_name'] ) ? (string) $meta['display_name'] : '',
		'card_label'   => ! empty( $meta['card_label'] ),
	);
}

/**
 * Settings of an attribute by its taxonomy name (pa_*).
 *
 * @param string $taxonomy Taxonomy name.
 * @return array
 */
function silver_attribute_meta_by_taxonomy( $taxonomy ) {
	$id = wc_attribute_taxonomy_id_by_name( $taxonomy );
	return silver_attribute_meta( $id );
}

/**
 * Name customers see: the Display Name, else the attribute Name.
 *
 * @param string $taxonomy Taxonomy name (pa_*).
 * @return string
 */
function silver_attribute_frontend_label( $taxonomy ) {
	$meta = silver_attribute_meta_by_taxonomy( $taxonomy );
	if ( '' !== $meta['display_name'] ) {
		return $meta['display_name'];
	}
	$labels = wc_get_attribute_taxonomy_labels();
	$slug   = wc_attribute_taxonomy_slug( $taxonomy );
	return isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;
}

/**
 * Whether the current request renders for customers: the front end, or an
 * AJAX request sent from it (not from the admin screens).
 *
 * @return bool
 */
function silver_attribute_is_frontend() {
	if ( ! is_admin() ) {
		return true;
	}
	if ( wp_doing_ajax() ) {
		$referer = wp_get_referer();
		return ! $referer || 0 !== strpos( $referer, admin_url() );
	}
	return false;
}

/* --------------------------------------------------------------- Frontend */

/**
 * Use the Display Name wherever WooCommerce prints an attribute name for
 * customers. The admin keeps the attribute Name.
 *
 * @param string $label Label.
 * @param string $name  Attribute name (taxonomy for global attributes).
 * @return string
 */
function silver_attribute_label_display_name( $label, $name ) {
	if ( ! silver_attribute_is_frontend() || ! taxonomy_is_product_attribute( $name ) ) {
		return $label;
	}
	$meta = silver_attribute_meta_by_taxonomy( $name );
	return '' !== $meta['display_name'] ? $meta['display_name'] : $label;
}
add_filter( 'woocommerce_attribute_label', 'silver_attribute_label_display_name', 10, 2 );

/* ------------------------------------------------------------------ Admin */

/**
 * The two fields, for the add form (div) or the edit form (table row).
 *
 * @param string $layout div / row.
 * @param array  $meta   Current settings.
 */
function silver_attribute_meta_fields( $layout, $meta ) {
	$name  = '<label for="silver_attribute_display_name">' . esc_html__( 'Display Name', 'silver' ) . '</label>';
	$input = '<input name="silver_attribute_display_name" id="silver_attribute_display_name" type="text" value="' . esc_attr( $meta['display_name'] ) . '" maxlength="200" />'
		. '<p class="description">' . esc_html__( 'Tên hiển thị cho khách (bảng thông số, chọn biến thể, giỏ hàng, bộ lọc). Để trống: dùng Tên ở trên.', 'silver' ) . '</p>';
	$card  = '<label><input name="silver_attribute_card_label" type="checkbox" value="1" ' . checked( $meta['card_label'], true, false ) . ' /> '
		. esc_html__( 'Hiện tên kèm giá trị trên chip của thẻ sản phẩm (ví dụ "RAM: 16GB")', 'silver' ) . '</label>';

	if ( 'row' === $layout ) {
		echo '<tr class="form-field"><th scope="row" valign="top">' . $name . '</th><td>' . $input . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<tr class="form-field"><th scope="row" valign="top">' . esc_html__( 'Thẻ sản phẩm', 'silver' ) . '</th><td>' . $card . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		echo '<div class="form-field">' . $name . $input . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="form-field">' . $card . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/** Fields on Products > Attributes (add). */
function silver_attribute_add_fields() {
	silver_attribute_meta_fields( 'div', silver_attribute_meta( 0 ) );
}
add_action( 'woocommerce_after_add_attribute_fields', 'silver_attribute_add_fields' );

/** Fields on the edit screen of an attribute. */
function silver_attribute_edit_fields() {
	$id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	silver_attribute_meta_fields( 'row', silver_attribute_meta( $id ) );
}
add_action( 'woocommerce_after_edit_attribute_fields', 'silver_attribute_edit_fields' );

/**
 * Save the fields. Runs inside WooCommerce's add / edit handlers, after they
 * verified their nonce.
 *
 * @param int $attribute_id Attribute ID.
 */
function silver_attribute_save_meta( $attribute_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) || ! isset( $_POST['silver_attribute_display_name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$all = get_option( SILVER_ATTRIBUTE_META_OPTION, array() );
	$all = is_array( $all ) ? $all : array();

	$display_name = sanitize_text_field( wp_unslash( $_POST['silver_attribute_display_name'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$card_label   = ! empty( $_POST['silver_attribute_card_label'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( '' === $display_name && ! $card_label ) {
		unset( $all[ (int) $attribute_id ] );
	} else {
		$all[ (int) $attribute_id ] = array(
			'display_name' => $display_name,
			'card_label'   => $card_label,
		);
	}

	update_option( SILVER_ATTRIBUTE_META_OPTION, $all );
}
add_action( 'woocommerce_attribute_added', 'silver_attribute_save_meta' );
add_action( 'woocommerce_attribute_updated', 'silver_attribute_save_meta' );

/**
 * Forget the settings of a deleted attribute.
 *
 * @param int $attribute_id Attribute ID.
 */
function silver_attribute_delete_meta( $attribute_id ) {
	$all = get_option( SILVER_ATTRIBUTE_META_OPTION, array() );
	if ( is_array( $all ) && isset( $all[ (int) $attribute_id ] ) ) {
		unset( $all[ (int) $attribute_id ] );
		update_option( SILVER_ATTRIBUTE_META_OPTION, $all );
	}
}
add_action( 'woocommerce_attribute_deleted', 'silver_attribute_delete_meta' );
