<?php
/**
 * "Thêm nhanh" in Product data > Attributes: adds the global attributes set
 * on the product's categories, in both fields of Products > Categories:
 * "Nhóm thông số kỹ thuật" (structure-wc-spec-groups.php) and "Thuộc tính
 * trên thẻ sản phẩm" (structure-wc-card-attributes.php). The categories are
 * the ones ticked on the screen (saved or not); like on the product page,
 * the deepest category with a setting wins, a category without one takes its
 * parent's. Attributes already on the product are skipped.
 *
 * The attributes are added by WooCommerce itself (assets/js/src/admin/silver-quick-attributes.js
 * selects them in "Add global", one after the other).
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attribute taxonomies of some product categories: the specification groups
 * first (in their order), then the product card attributes.
 *
 * @param int[] $term_ids Product category IDs.
 *
 * @return string[]
 */
function silver_quick_attributes_for_categories( $term_ids ) {
	$depth = array();
	foreach ( array_unique( array_map( 'absint', (array) $term_ids ) ) as $term_id ) {
		$term = $term_id ? get_term( $term_id, 'product_cat' ) : null;
		if ( $term instanceof WP_Term ) {
			$depth[ $term->term_id ] = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
		}
	}
	arsort( $depth );

	$list = array();

	if ( function_exists( 'silver_spec_groups_for_term' ) ) {
		foreach ( array_keys( $depth ) as $term_id ) {
			$groups = silver_spec_groups_for_term( $term_id );
			if ( null !== $groups ) {
				foreach ( $groups as $group ) {
					$list = array_merge( $list, $group['attributes'] );
				}
				break;
			}
		}
	}

	if ( function_exists( 'silver_card_attrs_for_term' ) ) {
		foreach ( array_keys( $depth ) as $term_id ) {
			$card = silver_card_attrs_for_term( $term_id );
			if ( null !== $card ) {
				$list = array_merge( $list, $card );
				break;
			}
		}
	}

	$list = array_values( array_filter( array_unique( $list ), 'taxonomy_exists' ) );

	return (array) apply_filters( 'silver_quick_attributes', $list, $term_ids );
}

/** AJAX: the attributes of the ticked categories. */
function silver_quick_attributes_ajax() {
	check_ajax_referer( 'silver_quick_attributes', 'security' );

	if ( ! current_user_can( 'edit_products' ) ) {
		wp_send_json_error( null, 403 );
	}

	$categories = isset( $_POST['categories'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['categories'] ) ) : array();
	$attributes = silver_quick_attributes_for_categories( $categories );
	$labels     = array();

	foreach ( $attributes as $taxonomy ) {
		$labels[ $taxonomy ] = wc_attribute_label( $taxonomy );
	}

	wp_send_json_success( array(
		'attributes' => $attributes,
		'labels'     => $labels,
	) );
}
add_action( 'wp_ajax_silver_quick_attributes', 'silver_quick_attributes_ajax' );

/**
 * Script of the button (Edit product screen).
 *
 * @param string $hook Admin page.
 */
function silver_quick_attributes_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== $screen->post_type ) {
		return;
	}

	$file = '/assets/js/admin/silver-quick-attributes.js';
	wp_enqueue_script( 'silver-quick-attributes', get_template_directory_uri() . $file, array( 'jquery', 'wc-admin-product-meta-boxes' ), filemtime( get_template_directory() . $file ), true );
	wp_localize_script( 'silver-quick-attributes', 'silverQuickAttributes', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'silver_quick_attributes' ),
		'i18n'    => array(
			'button'        => __( 'Thêm nhanh', 'silver' ),
			'title'         => __( 'Thêm các thuộc tính đã chọn ở danh mục sản phẩm (Nhóm thông số kỹ thuật, Thuộc tính trên thẻ sản phẩm). Thuộc tính đã có được bỏ qua.', 'silver' ),
			'noCategory'    => __( 'Hãy chọn danh mục sản phẩm trước.', 'silver' ),
			'noAttributes'  => __( 'Danh mục chưa cấu hình thuộc tính nào.', 'silver' ),
			'allThere'      => __( 'Sản phẩm đã có đủ thuộc tính của danh mục.', 'silver' ),
			/* translators: 1: number of attributes added, 2: their names */
			'added'         => __( 'Đã thêm %1$d thuộc tính: %2$s.', 'silver' ),
			'error'         => __( 'Không lấy được thuộc tính của danh mục. Hãy thử lại.', 'silver' ),
		),
	) );
	wp_add_inline_style( 'woocommerce_admin_styles', '#product_attributes .silver-quick-attributes{margin-left:4px}#product_attributes .silver-quick-attributes .dashicons{font-size:16px;width:16px;height:16px;vertical-align:-3px;margin-right:2px}#product_attributes .silver-quick-attributes-status{display:block;margin:6px 0 0;color:#50575e}#product_attributes .silver-quick-attributes-status:empty{display:none}' );
}
add_action( 'admin_enqueue_scripts', 'silver_quick_attributes_enqueue', 20 );
