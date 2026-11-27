<?php
/**
 * "Đồng bộ" button on each variation (Product data > Variations).
 *
 * Copies the chosen data of one variation (images, prices, stock, shipping...)
 * into the other variations shown on the page. Only the form changes: the
 * variations are marked as edited and are stored with "Save changes", like a
 * manual edit. SKU, GTIN, attributes and downloadable files are never copied.
 *
 * Fields added by plugins are copied too when they are named by variation
 * ("field[loop]..." or "field[variation_id]..."). Lists of rows (e.g. a price
 * per warehouse) get a checkbox of their own and are copied with their rows.
 *
 * Script: assets/js/src/admin/silver-variation-sync.js.
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Script of the button (Edit product screen).
 *
 * @param string $hook Admin page.
 */
function silver_variation_sync_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== $screen->post_type ) {
		return;
	}

	$file = '/assets/js/admin/silver-variation-sync.js';
	wp_enqueue_script( 'silver-variation-sync', get_template_directory_uri() . $file, array( 'jquery', 'wc-admin-variation-meta-boxes', 'silver-dialog' ), filemtime( get_template_directory() . $file ), true );
	wp_localize_script( 'silver-variation-sync', 'silverVariationSync', array(
		'i18n' => array(
			'button'      => __( 'Đồng bộ', 'silver' ),
			'buttonTitle' => __( 'Sao chép dữ liệu của biến thể này sang các biến thể khác', 'silver' ),
			'title'       => __( 'Đồng bộ biến thể', 'silver' ),
			/* translators: %s: variation (#ID and attributes). */
			'from'        => __( 'Sao chép từ %s sang các biến thể được chọn.', 'silver' ),
			'data'        => __( 'Dữ liệu', 'silver' ),
			'targets'     => __( 'Áp dụng cho', 'silver' ),
			'all'         => __( 'Chọn tất cả', 'silver' ),
			'apply'       => __( 'Đồng bộ', 'silver' ),
			'cancel'      => __( 'Huỷ', 'silver' ),
			'pickData'    => __( 'Hãy chọn ít nhất một loại dữ liệu.', 'silver' ),
			'pickTarget'  => __( 'Hãy chọn ít nhất một biến thể.', 'silver' ),
			'noOther'     => __( 'Không có biến thể nào khác ở trang này.', 'silver' ),
			/* translators: 1: variations on this page, 2: all variations. */
			'pageOnly'    => __( 'Chỉ áp dụng cho %1$d/%2$d biến thể đang hiển thị ở trang này. Với các trang khác, mở trang đó rồi đồng bộ lại.', 'silver' ),
			/* translators: %d: number of variations. */
			'done'        => __( 'Đã đồng bộ sang %d biến thể. Bấm "Save changes" để lưu.', 'silver' ),
			'same'        => __( 'Các biến thể được chọn đã có dữ liệu giống hệt, không có gì thay đổi.', 'silver' ),
			'notes'       => __( 'Không sao chép: SKU, GTIN, thuộc tính và file tải về.', 'silver' ),
			'groups'      => array(
				'image'         => __( 'Ảnh & gallery', 'silver' ),
				'regular_price' => __( 'Giá thường', 'silver' ),
				'sale_price'    => __( 'Giá khuyến mãi & lịch', 'silver' ),
				'options'       => __( 'Bật / Tải về / Ảo', 'silver' ),
				'stock'         => __( 'Kho hàng', 'silver' ),
				'shipping'      => __( 'Cân nặng, kích thước, vận chuyển', 'silver' ),
				'tax'           => __( 'Thuế', 'silver' ),
				'description'   => __( 'Mô tả', 'silver' ),
				'other'         => __( 'Trường khác (giá vốn, plugin…)', 'silver' ),
			),
		),
	) );
	wp_add_inline_style( 'woocommerce_admin_styles', silver_variation_sync_css() );
}
add_action( 'admin_enqueue_scripts', 'silver_variation_sync_enqueue', 20 );

/**
 * Styles of the button and of its dialog.
 *
 * @return string
 */
function silver_variation_sync_css() {
	// Remove, Đồng bộ and Edit: same text size, height, line and spacing.
	$links = '#variable_product_options .woocommerce_variation > h3 > %s';
	$all   = implode( ',', array_map( function ( $selector ) use ( $links ) {
		return sprintf( $links, $selector );
	}, array( 'a.remove_variation', 'button.silver-vsync-btn', 'a.edit_variation' ) ) );

	return $all . '{float:right;margin:.25em 0 0 12px;padding:0;border:0;background:none;font-family:inherit;font-size:13px;font-weight:400;line-height:26px;text-decoration:none;cursor:pointer;box-shadow:none}'
		. sprintf( $links, 'a.edit_variation' ) . '{margin-right:12px}'
		. sprintf( $links, 'button.silver-vsync-btn' ) . '{color:var(--wp-admin-theme-color,#2271b1)}'
		. sprintf( $links, 'button.silver-vsync-btn:hover' ) . ',' . sprintf( $links, 'a.edit_variation:hover' ) . ',' . sprintf( $links, 'a.remove_variation:hover' ) . '{text-decoration:underline}'
		. sprintf( $links, 'button.silver-vsync-btn:focus-visible' ) . '{outline:2px solid var(--wp-admin-theme-color,#2271b1);outline-offset:2px}'
		. '.woocommerce_variation.silver-vsync-flash{animation:silver-vsync-flash 1.6s ease-out}'
		. '@keyframes silver-vsync-flash{0%,30%{box-shadow:inset 4px 0 0 var(--wp-admin-theme-color,#2271b1);background:#f0f6fc}100%{box-shadow:inset 4px 0 0 transparent}}'
		. '.silver-dialog.silver-vsync-dialog{--silver-dialog-width:560px}'
		. '.silver-vsync-dialog .silver-dialog__box{text-align:start}'
		. '.silver-vsync{display:grid;gap:14px;font-size:13px}'
		. '.silver-vsync p{margin:0}'
		. '.silver-vsync fieldset{margin:0;padding:0;border:0;min-width:0}'
		. '.silver-vsync legend{display:flex;width:100%;justify-content:space-between;align-items:center;margin-bottom:6px;padding:0;font-weight:600}'
		. '.silver-vsync legend label{font-weight:400}'
		. '.silver-vsync__groups{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:6px 12px}'
		. '.silver-vsync__targets{display:grid;gap:2px;max-height:220px;overflow-y:auto;padding:6px 8px;border:1px solid #dcdcde;border-radius:4px}'
		. '.silver-vsync label{display:flex;gap:6px;align-items:center;cursor:pointer}'
		. '.silver-vsync input[type=checkbox]{margin:0}'
		. '.silver-vsync__muted{color:#646970}'
		. '.silver-vsync__note{padding:8px 10px;border-left:4px solid #dba617;background:#fcf9e8}'
		. '.silver-vsync__error{color:#d63638}'
		. '.silver-vsync__error:empty{display:none}';
}
