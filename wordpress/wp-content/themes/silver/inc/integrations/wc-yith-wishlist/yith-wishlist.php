<?php
/**
 * YITH wishlist integration
 *
 * @author      UX Themes
 * @package     Silver/Integrations
 */

if ( ! function_exists( 'silver_wishlist_integrations_scripts' ) ) {
	/**
	 * Enqueues wishlist integrations scripts
	 */
	function silver_wishlist_integrations_scripts() {
		global $integrations_uri;

		wp_dequeue_style( 'yith-wcwl-main' );
		wp_deregister_style( 'yith-wcwl-main' );
		wp_dequeue_style( 'yith-wcwl-font-awesome' );
		wp_deregister_style( 'yith-wcwl-font-awesome' );

		// TODO 4.0 Move and apply on AJAX search plugin.
		wp_dequeue_style( 'yith_wcas_frontend' );
		wp_deregister_style( 'yith_wcas_frontend' );

		wp_enqueue_script( 'silver-woocommerce-wishlist', $integrations_uri . '/wc-yith-wishlist/wishlist.js', array( 'jquery', 'silver-js' ), silver()->version(), true );
		wp_enqueue_style( 'silver-woocommerce-wishlist', $integrations_uri . '/wc-yith-wishlist/wishlist.css', 'silver-woocommerce-style', silver()->version() );
	}
}

add_action( 'wp_enqueue_scripts', 'silver_wishlist_integrations_scripts' );

/**
 * Force YITH Wishlist to use PHP templates.
 *
 * @return string
 */
function silver_yith_wcwl_rendering_method(): string {
	return 'php-templates';
}

add_filter( 'yith_wcwl_rendering_method', 'silver_yith_wcwl_rendering_method' );

if ( ! function_exists( 'silver_wishlist_account_item' ) ) {
	/**
	 * Add wishlist button to my account dropdown
	 */
	function silver_wishlist_account_item() {
		$page_id = get_option( 'yith_wcwl_wishlist_page_id' );
		if ( ! $page_id ) {
			return;
		}

		$wishlist_page = yith_wcwl_object_id( $page_id );
		?>
		<li class="wishlist-account-element <?php if ( is_page( $wishlist_page ) ) echo 'active'; ?>">
			<a href="<?php echo YITH_WCWL()->get_wishlist_url(); ?>"><?php echo get_the_title( $wishlist_page ); ?></a>
		</li>
		<?php
	}
}
add_action( 'silver_account_links', 'silver_wishlist_account_item' );


if ( ! function_exists( 'silver_product_wishlist_button' ) ) {
	/**
	 * Add wishlist Button to Product Image
	 */
	function silver_product_wishlist_button() {
		$icon                = get_theme_mod( 'wishlist_icon', 'heart' );
		if ( ! $icon ) $icon = 'heart';
		?>
		<div class="wishlist-icon">
			<button class="wishlist-button button is-outline circle icon" aria-label="<?php echo esc_html__( 'Wishlist', 'silver' ); ?>">
				<?php echo get_silver_icon( 'icon-' . $icon ); ?>
			</button>
			<div class="wishlist-popup dark">
				<?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); ?>
			</div>
		</div>
		<?php
	}
}

add_action( 'silver_product_image_tools_top', 'silver_product_wishlist_button', 2 );
add_action( 'silver_product_box_tools_top', 'silver_product_wishlist_button', 2 );

if ( ! function_exists( 'silver_header_wishlist' ) ) {
	/**
	 * Registers the wishlist header element.
	 *
	 * @param array $elements Registered header elements.
	 *
	 * @return array
	 */
	function silver_header_wishlist( $elements ) {
		$elements['wishlist'] = esc_html__( 'Wishlist', 'silver' );

		return $elements;
	}
}

add_filter( 'silver_header_element', 'silver_header_wishlist' );

if ( ! function_exists( 'silver_update_wishlist_count' ) ) {
	/**
	 * Update Wishlist Count
	 */
	function silver_update_wishlist_count() {
		$count = function_exists( 'yith_wcwl_wishlists' )
			? yith_wcwl_wishlists()->count_items_in_wishlist()
			: YITH_WCWL()->count_products();

		wp_send_json( $count );
	}
}

add_action( 'wp_ajax_silver_update_wishlist_count', 'silver_update_wishlist_count' );
add_action( 'wp_ajax_nopriv_silver_update_wishlist_count', 'silver_update_wishlist_count' );
