<?php
/**
 * Upsells and Cross-sells (Product data > Linked Products): a heading of their
 * own and two buttons.
 *
 * - "Tiêu đề Upsells / Cross-sells": heading of the section on the site. Up-sells:
 *   product page; cross-sells: cart and mini cart, with the heading of the last
 *   product added to the cart that has one. Empty: heading of WooCommerce.
 * - "Gửi sang SP khác": the list shown in the field (saved or not) is written
 *   into the products picked in the dialog: it replaces their list or is added
 *   to it, and the current product can be added too (products that point at
 *   each other, such as the colours of one model). The heading always goes with it.
 * - "Lấy từ SP khác": the opposite; fills the field (and the heading) with
 *   those of the product picked in the dialog, stored with "Update".
 *
 * A product is never linked to itself.
 *
 * Script: assets/js/src/admin/silver-linked-sync.js.
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field => getter / setter of WC_Product.
 *
 * @return array
 */
function silver_linked_sync_fields() {
	return array(
		'upsell'    => array( 'get_upsell_ids', 'set_upsell_ids' ),
		'crosssell' => array( 'get_cross_sell_ids', 'set_cross_sell_ids' ),
	);
}

/**
 * Field => meta of its heading.
 *
 * @return array
 */
function silver_linked_heading_keys() {
	return array(
		'upsell'    => '_silver_upsell_heading',
		'crosssell' => '_silver_crosssell_heading',
	);
}

/**
 * Heading fields, under their list (moved there by the script).
 */
function silver_linked_heading_fields() {
	global $product_object;

	$product = $product_object instanceof WC_Product ? $product_object : wc_get_product( get_the_ID() );
	$keys    = silver_linked_heading_keys();
	$fields  = array(
		'upsell'    => array( __( 'Tiêu đề Upsells', 'silver' ), __( 'You may also like&hellip;', 'woocommerce' ), __( 'Tiêu đề của section Upsells trên trang sản phẩm. Để trống: dùng tiêu đề mặc định.', 'silver' ), '' ),
		'crosssell' => array( __( 'Tiêu đề Cross-sells', 'silver' ), __( 'You may be interested in&hellip;', 'woocommerce' ), __( 'Tiêu đề của section Cross-sells trong giỏ hàng (lấy theo sản phẩm thêm vào giỏ sau cùng có đặt tiêu đề). Để trống: dùng tiêu đề mặc định.', 'silver' ), ' hide_if_grouped hide_if_external' ),
	);

	foreach ( $fields as $field => $args ) {
		woocommerce_wp_text_input( array(
			'id'            => $keys[ $field ],
			'label'         => $args[0],
			'placeholder'   => html_entity_decode( $args[1], ENT_QUOTES, 'UTF-8' ),
			'desc_tip'      => true,
			'description'   => $args[2],
			'value'         => $product ? $product->get_meta( $keys[ $field ], true, 'edit' ) : '',
			'wrapper_class' => 'silver-lsync-heading silver-lsync-heading--' . $field . $args[3],
		) );
	}
}
add_action( 'woocommerce_product_options_related', 'silver_linked_heading_fields' );

/**
 * Saves the headings.
 *
 * @param WC_Product $product Product.
 */
function silver_linked_heading_save( $product ) {
	foreach ( silver_linked_heading_keys() as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked by WooCommerce.
			continue;
		}

		$heading = sanitize_text_field( wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' === $heading ) {
			$product->delete_meta_data( $key );
		} else {
			$product->update_meta_data( $key, $heading );
		}
	}
}
add_action( 'woocommerce_admin_process_product_object', 'silver_linked_heading_save' );

/**
 * Heading of the up-sells of the product shown.
 *
 * @param string $heading Heading of WooCommerce.
 * @return string
 */
function silver_linked_upsell_heading( $heading ) {
	global $product;

	$custom = $product instanceof WC_Product ? (string) $product->get_meta( '_silver_upsell_heading' ) : '';
	return '' !== $custom ? $custom : $heading;
}
add_filter( 'woocommerce_product_upsells_products_heading', 'silver_linked_upsell_heading' );

/**
 * Heading of the cross-sells of the cart: that of the last product added which
 * has cross-sells and a heading.
 *
 * @param string $heading Heading of WooCommerce.
 * @return string
 */
function silver_linked_cross_sell_heading( $heading ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $heading;
	}

	foreach ( array_reverse( WC()->cart->get_cart() ) as $item ) {
		// Variations take the cross-sells of their product.
		$product = wc_get_product( $item['product_id'] );
		$custom  = $product && $product->get_cross_sell_ids() ? (string) $product->get_meta( '_silver_crosssell_heading' ) : '';
		if ( '' !== $custom ) {
			return $custom;
		}
	}

	return $heading;
}
add_filter( 'woocommerce_product_cross_sells_products_heading', 'silver_linked_cross_sell_heading' );

/**
 * Checks the request (nonce, rights on the current product).
 *
 * @return int Current product ID.
 */
function silver_linked_sync_check_request() {
	check_ajax_referer( 'silver_linked_sync', 'security' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id || ! current_user_can( 'edit_product', $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Bạn không có quyền sửa sản phẩm này.', 'silver' ) ), 403 );
	}

	return $product_id;
}

/**
 * Products of the dialog: the search, or else the products of the ticked categories.
 */
function silver_linked_sync_products_ajax() {
	$product_id = silver_linked_sync_check_request();
	$search     = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$categories = isset( $_POST['categories'] ) ? array_filter( array_map( 'absint', (array) wp_unslash( $_POST['categories'] ) ) ) : array();
	$limit      = 60;

	$args = array(
		'post_type'              => 'product',
		'post_status'            => array( 'publish', 'private', 'draft', 'pending', 'future' ),
		'posts_per_page'         => $limit + 1,
		'post__not_in'           => array( $product_id ),
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);

	if ( '' !== $search ) {
		// The search of WooCommerce: title, content, excerpt and SKU.
		$ids              = WC_Data_Store::load( 'product' )->search_products( $search, '', false, true, $limit + 1, array(), array( $product_id ) );
		$ids              = array_values( array_diff( array_map( 'absint', $ids ), array( 0, $product_id ) ) );
		$args['post__in'] = $ids ? $ids : array( 0 );
		unset( $args['post__not_in'] );
	} elseif ( $categories ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $categories,
			),
		);
	}

	$ids  = get_posts( $args );
	$more = count( $ids ) > $limit;
	$ids  = array_slice( $ids, 0, $limit );

	_prime_post_caches( $ids );

	$products = array();
	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || ! current_user_can( 'edit_product', $id ) ) {
			continue;
		}

		$image      = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );
		$products[] = array(
			'id'        => $id,
			'name'      => wp_strip_all_tags( $product->get_name() ),
			'sku'       => $product->get_sku(),
			'status'    => 'publish' === $product->get_status() ? '' : get_post_status_object( $product->get_status() )->label,
			'image'     => $image ? $image[0] : wc_placeholder_img_src( 'thumbnail' ),
			'upsell'    => count( $product->get_upsell_ids( 'edit' ) ),
			'crosssell' => count( $product->get_cross_sell_ids( 'edit' ) ),
			// Grouped and external products have no cross-sells.
			'noCross'   => $product->is_type( array( 'grouped', 'external' ) ),
		);
	}

	wp_send_json_success( array(
		'products' => $products,
		'more'     => $more,
	) );
}
add_action( 'wp_ajax_silver_linked_sync_products', 'silver_linked_sync_products_ajax' );

/**
 * Writes the list into the picked products.
 */
function silver_linked_sync_apply_ajax() {
	$product_id = silver_linked_sync_check_request();
	$fields     = silver_linked_sync_fields();
	$field      = isset( $_POST['field'] ) ? sanitize_key( $_POST['field'] ) : '';
	$mode       = isset( $_POST['mode'] ) && 'merge' === $_POST['mode'] ? 'merge' : 'replace';
	$with_self  = ! empty( $_POST['with_self'] ) && 'false' !== $_POST['with_self'];
	$ids        = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
	$targets    = isset( $_POST['targets'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['targets'] ) ) : array();
	// The heading goes with the list, as it is: an empty one clears theirs (default heading).
	$retitle    = isset( $_POST['heading'] );
	$heading    = $retitle ? sanitize_text_field( wp_unslash( $_POST['heading'] ) ) : '';

	if ( ! isset( $fields[ $field ] ) ) {
		wp_send_json_error( array( 'message' => 'Unknown field.' ), 400 );
	}

	list( $getter, $setter ) = $fields[ $field ];
	$heading_key             = silver_linked_heading_keys()[ $field ];

	// Only products that exist (a product or a variation, as the field allows).
	$ids = array_values( array_filter( array_unique( $ids ), function ( $id ) {
		return $id && in_array( get_post_type( $id ), array( 'product', 'product_variation' ), true );
	} ) );
	if ( $with_self ) {
		$ids[] = $product_id;
	}

	$updated = array();
	$skipped = 0;

	foreach ( array_unique( array_filter( $targets ) ) as $target_id ) {
		$product = $target_id !== $product_id && current_user_can( 'edit_product', $target_id ) ? wc_get_product( $target_id ) : null;
		if ( ! $product || $product->is_type( 'variation' ) || ( 'crosssell' === $field && $product->is_type( array( 'grouped', 'external' ) ) ) ) {
			++$skipped;
			continue;
		}

		$current = array_map( 'absint', $product->$getter( 'edit' ) );
		$next    = 'merge' === $mode ? array_merge( $current, $ids ) : $ids;
		$next    = array_values( array_unique( array_diff( $next, array( $target_id ) ) ) );
		$titled  = $retitle && (string) $product->get_meta( $heading_key, true, 'edit' ) !== $heading;

		if ( $next === $current && ! $titled ) {
			continue;
		}

		$product->$setter( $next );
		if ( $titled && '' === $heading ) {
			$product->delete_meta_data( $heading_key );
		} elseif ( $titled ) {
			$product->update_meta_data( $heading_key, $heading );
		}
		$product->save();
		$updated[] = $target_id;
	}

	wp_send_json_success( array(
		'updated' => count( $updated ),
		'skipped' => $skipped,
	) );
}
add_action( 'wp_ajax_silver_linked_sync_apply', 'silver_linked_sync_apply_ajax' );

/**
 * List of the source product ("Lấy từ SP khác"), as the options of the field.
 */
function silver_linked_sync_source_ajax() {
	$product_id  = silver_linked_sync_check_request();
	$fields      = silver_linked_sync_fields();
	$field       = isset( $_POST['field'] ) ? sanitize_key( $_POST['field'] ) : '';
	$source_id   = isset( $_POST['source'] ) ? absint( $_POST['source'] ) : 0;
	$with_source = ! empty( $_POST['with_source'] ) && 'false' !== $_POST['with_source'];
	$source      = $source_id && current_user_can( 'edit_product', $source_id ) ? wc_get_product( $source_id ) : null;

	if ( ! isset( $fields[ $field ] ) || ! $source ) {
		wp_send_json_error( array( 'message' => __( 'Không tìm thấy sản phẩm nguồn.', 'silver' ) ), 400 );
	}

	$getter = $fields[ $field ][0];
	$ids    = array_map( 'absint', $source->$getter( 'edit' ) );
	if ( $with_source ) {
		$ids[] = $source_id;
	}

	// The current product is never linked to itself.
	$items = array();
	foreach ( array_unique( array_diff( $ids, array( 0, $product_id ) ) ) as $id ) {
		$product = wc_get_product( $id );
		if ( $product ) {
			// Same text as the options printed by WooCommerce.
			$items[] = array(
				'id'   => $id,
				'text' => wp_strip_all_tags( $product->get_formatted_name() ),
			);
		}
	}

	wp_send_json_success( array(
		'name'    => wp_strip_all_tags( $source->get_name() ),
		'items'   => $items,
		'heading' => (string) $source->get_meta( silver_linked_heading_keys()[ $field ], true, 'edit' ),
	) );
}
add_action( 'wp_ajax_silver_linked_sync_source', 'silver_linked_sync_source_ajax' );

/**
 * Script of the buttons (Edit product screen).
 *
 * @param string $hook Admin page.
 */
function silver_linked_sync_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== $screen->post_type ) {
		return;
	}

	$file = '/assets/js/admin/silver-linked-sync.js';
	wp_enqueue_script( 'silver-linked-sync', get_template_directory_uri() . $file, array( 'jquery', 'wc-admin-product-meta-boxes', 'silver-dialog' ), filemtime( get_template_directory() . $file ), true );
	wp_localize_script( 'silver-linked-sync', 'silverLinkedSync', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'silver_linked_sync' ),
		'productId' => get_the_ID(),
		'i18n'      => array(
			'button'      => __( 'Gửi sang SP khác', 'silver' ),
			'buttonTitle' => __( 'Ghi danh sách này vào các sản phẩm khác (lưu ngay)', 'silver' ),
			/* translators: %s: Upsells or Cross-sells. */
			'title'       => __( 'Gửi %s sang sản phẩm khác', 'silver' ),
			'fields'      => array(
				'upsell'    => __( 'Upsells', 'silver' ),
				'crosssell' => __( 'Cross-sells', 'silver' ),
			),
			/* translators: %d: number of products. */
			'list'        => __( 'Danh sách hiện tại: %d sản phẩm.', 'silver' ),
			'emptyList'   => __( 'Danh sách hiện tại đang trống.', 'silver' ),
			'search'      => __( 'Tìm sản phẩm theo tên hoặc SKU…', 'silver' ),
			'products'    => __( 'Gửi sang', 'silver' ),
			'all'         => __( 'Chọn tất cả', 'silver' ),
			/* translators: %d: number of products. */
			'picked'      => __( 'Đã chọn %d', 'silver' ),
			'loading'     => __( 'Đang tải…', 'silver' ),
			'none'        => __( 'Không tìm thấy sản phẩm nào.', 'silver' ),
			'sameCat'     => __( 'Sản phẩm cùng danh mục. Gõ để tìm sản phẩm khác.', 'silver' ),
			'typeToSearch' => __( 'Gõ để tìm sản phẩm.', 'silver' ),
			'more'        =>__( 'Còn nhiều sản phẩm khác, gõ để tìm cụ thể hơn.', 'silver' ),
			/* translators: %d: number of products in the list of that product. */
			'has'         => __( 'đang có %d', 'silver' ),
			'noCross'     => __( 'không có cross-sells', 'silver' ),
			'replace'     => __( 'Thay thế danh sách của sản phẩm được chọn', 'silver' ),
			'merge'       => __( 'Thêm vào danh sách đang có', 'silver' ),
			'withSelf'    => __( 'Thêm cả sản phẩm này vào danh sách của chúng', 'silver' ),
			/* translators: %s: heading. */
			'heading'      => __( 'Tiêu đề "%s" cũng được gửi kèm.', 'silver' ),
			'headingEmpty' => __( 'Tiêu đề đang trống: các sản phẩm được chọn cũng sẽ dùng tiêu đề mặc định.', 'silver' ),
			'apply'       => __( 'Gửi đi', 'silver' ),
			'cancel'      => __( 'Huỷ', 'silver' ),
			'pick'        => __( 'Hãy chọn ít nhất một sản phẩm.', 'silver' ),
			'error'       => __( 'Không gửi được. Hãy thử lại.', 'silver' ),
			/* translators: %d: number of products. */
			'done'        => __( 'Đã cập nhật %d sản phẩm.', 'silver' ),
			'same'        => __( 'Các sản phẩm được chọn đã có danh sách giống hệt, không có gì thay đổi.', 'silver' ),
			/* translators: %d: number of products. */
			'skipped'     => __( '%d sản phẩm bị bỏ qua (không có quyền hoặc không hỗ trợ cross-sells).', 'silver' ),
			'pull'        => array(
				'button'      => __( 'Lấy từ SP khác', 'silver' ),
				'buttonTitle' => __( 'Điền danh sách của một sản phẩm khác vào đây (bấm Update để lưu)', 'silver' ),
				/* translators: %s: Upsells or Cross-sells. */
				'title'       => __( 'Lấy %s từ sản phẩm khác', 'silver' ),
				'products'    => __( 'Lấy từ', 'silver' ),
				'picked'      => __( 'Chọn 1 sản phẩm', 'silver' ),
				'replace'     => __( 'Thay thế danh sách hiện tại', 'silver' ),
				'merge'       => __( 'Thêm vào danh sách hiện tại', 'silver' ),
				'withSource'  => __( 'Thêm cả sản phẩm nguồn vào danh sách', 'silver' ),
				'heading'     => __( 'Tiêu đề của sản phẩm nguồn cũng được lấy kèm (trống: dùng tiêu đề mặc định).', 'silver' ),
				'apply'       => __( 'Lấy danh sách', 'silver' ),
				'pick'        => __( 'Hãy chọn sản phẩm nguồn.', 'silver' ),
				/* translators: %s: product name. */
				'empty'       => __( '"%s" không có sản phẩm nào để lấy.', 'silver' ),
				'error'       => __( 'Không lấy được danh sách. Hãy thử lại.', 'silver' ),
				/* translators: 1: number of products, 2: product name. */
				'done'        => __( 'Đã lấy %1$d sản phẩm từ "%2$s". Bấm "Update" để lưu.', 'silver' ),
				/* translators: %s: product name. */
				'same'        => __( 'Danh sách và tiêu đề đã giống "%s".', 'silver' ),
			),
		),
	) );
	wp_add_inline_style( 'woocommerce_admin_styles', silver_linked_sync_css() );
}
add_action( 'admin_enqueue_scripts', 'silver_linked_sync_enqueue', 20 );

/**
 * Styles of the buttons and of the dialog.
 *
 * @return string
 */
function silver_linked_sync_css() {
	// The two buttons: one compact group next to the help tip, icon centred
	// (WordPress buttons are 40px high and push their icon to the top).
	return '#linked_product_data .silver-lsync-actions{display:inline-flex;flex-wrap:wrap;gap:6px;margin-left:8px;vertical-align:top}'
		. '#linked_product_data .silver-lsync-actions .button.silver-lsync-btn{display:inline-flex;align-items:center;gap:5px;height:32px;min-height:32px;margin:0;padding:0 10px;font-size:13px;line-height:1}'
		. '#linked_product_data .silver-lsync-actions .button.silver-lsync-btn .dashicons{width:16px;height:16px;margin:0;font-size:16px;line-height:1;vertical-align:middle}'
		. '.silver-dialog.silver-lsync-dialog{--silver-dialog-width:620px}'
		. '.silver-lsync-dialog .silver-dialog__box{text-align:start}'
		. '.silver-lsync{display:grid;gap:12px;font-size:13px}'
		. '.silver-lsync p{margin:0}'
		. '.silver-lsync__search{width:100%;min-height:34px}'
		. '.silver-lsync__bar{display:flex;justify-content:space-between;align-items:center;font-weight:600}'
		. '.silver-lsync__bar label{font-weight:400}'
		. '.silver-lsync__list{max-height:300px;overflow-y:auto;border:1px solid #dcdcde;border-radius:4px}'
		. '.silver-lsync__item{display:flex;gap:10px;align-items:center;padding:6px 10px;border-top:1px solid #f0f0f1;cursor:pointer}'
		. '.silver-lsync__item:first-child{border-top:0}'
		. '.silver-lsync__item:hover{background:#f6f7f7}'
		. '.silver-lsync__item.is-disabled{opacity:.5;cursor:not-allowed}'
		. '.silver-lsync__item img{width:36px;height:36px;object-fit:cover;border-radius:3px;flex:none}'
		. '.silver-lsync__name{flex:1;min-width:0}'
		. '.silver-lsync__name small{display:block;color:#646970}'
		. '.silver-lsync__empty{padding:16px;text-align:center;color:#646970}'
		. '.silver-lsync__muted{color:#646970}'
		. '.silver-lsync__options{display:grid;gap:6px}'
		. '.silver-lsync label{cursor:pointer}'
		. '.silver-lsync__item.is-picked{background:#f0f6fc}'
		. '.silver-lsync input[type=checkbox],.silver-lsync input[type=radio]{margin:0 6px 0 0}'
		. '.silver-lsync__error{color:#d63638}'
		. '.silver-lsync__error:empty{display:none}';
}
