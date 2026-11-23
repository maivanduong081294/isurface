<?php

function silver_product_summary_fix(){
  if(is_product()){
    if(!get_theme_mod('product_info_meta', 1)){
      remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta',40);
    }
    if(!get_theme_mod('product_info_share', 1)){
      remove_action('woocommerce_single_product_summary','woocommerce_template_single_sharing',50);
    }
  }
}
add_action('wp_head','silver_product_summary_fix', 9999);

// Product summary classes
function silver_product_summary_classes( $main = true, $align = true, $form = true ) {
	$classes = $main ? array( 'product-summary' ) : array();
	if ( $align && get_theme_mod( 'product_info_align' ) ) {
		$classes[] = 'text-' . get_theme_mod( 'product_info_align', 'left' );
	}
	if ( $form && get_theme_mod( 'product_info_form' ) ) {
		$classes[] = 'form-' . get_theme_mod( 'product_info_form', '' );
	}
	echo implode( ' ', $classes );
}

function silver_product_upsell_sidebar(){
  // Product Upsell
    if(get_theme_mod('product_upsell','sidebar') == 'sidebar') {
        remove_action( 'woocommerce_after_single_product_summary' , 'woocommerce_upsell_display', 15);
        add_action('silver_before_product_sidebar','woocommerce_upsell_display', 2);
    }
    else if(get_theme_mod('product_upsell', 'sidebar') == 'disabled') {
        remove_action( 'woocommerce_after_single_product_summary' , 'woocommerce_upsell_display', 15);
    }
}
add_action('silver_before_product_sidebar','silver_product_upsell_sidebar', 1);

/* Add Share to product description */
if ( ! function_exists( 'silver_product_share' ) ) {
	function silver_product_share() {
		$icons = silver_apply_shortcode( 'share', array(
			'style'   => get_theme_mod( 'social_icons_style', 'outline' ),
			'tooltip' => get_theme_mod( 'social_icons_tooltip', 1 ) ? 'true' : 'false',
		) );

		if ( '' === trim( (string) $icons ) ) {
			return;
		}

		// A group named "Chia sẻ" for screen readers (no visible label, the
		// icons say it); the icons keep the format of [share].
		echo '<div class="product-share" role="group" aria-label="' . esc_attr__( 'Share', 'silver' ) . '">'
			. $icons // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output.
			. '</div>';
	}
}
add_action( 'woocommerce_share', 'silver_product_share', 10 );


/* Remove Product Description Heading */
function silver_remove_product_description_heading($heading){

     return $heading = '';
}
add_filter('woocommerce_product_description_heading','silver_remove_product_description_heading');


/* Remove Additional Product Information Heading */
function silver_remove_product_information_heading($heading){
     return $heading = '';
}
add_filter('woocommerce_product_additional_information_heading','silver_remove_product_information_heading');

// Move Sale Flash to another hook
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash',10);
add_action('silver_sale_flash','woocommerce_show_product_sale_flash',10);


if ( ! function_exists( 'silver_product_video_button' ) ) {
	/**
	 * Add product video button.
	 *
	 * @return void
	 */
	function silver_product_video_button() {
		global $wc_cpdf;

		if ( $wc_cpdf->get_value( get_the_ID(), '_product_video' ) ) {
			printf('<a %s>%s</a>',
				silver_html_atts( [
					'role'       => 'button',
					'href'       => esc_url( trim( $wc_cpdf->get_value( get_the_ID(), '_product_video' ) ) ),
					'class'      => 'button open-video is-outline circle icon button product-video-popup tip-top',
					'title'      => esc_attr__( 'Video', 'silver' ),
					'aria-label' => esc_attr__( 'Open video in lightbox', 'silver' ),
				] ),
				get_silver_icon( 'icon-play' )
			);
			?>
			<style>
			<?php
			// Set product video height.
			$height       = '900px';
			$width        = '900px';
			$iframe_scale = '100%';
			$custom_size  = $wc_cpdf->get_value( get_the_ID(), '_product_video_size' );
			if ( $custom_size ) {
				$split = explode( 'x', $custom_size );

				$height = $split[0];
				$width  = $split[1];

				$iframe_scale = ( $width / $height * 100 ) . '%';
			}
			echo '.has-product-video .mfp-iframe-holder .mfp-content{max-width: ' . $width . ';}';
			echo '.has-product-video .mfp-iframe-scaler{padding-top: ' . $iframe_scale . ';}';
			?>
			</style>
			<?php
		}
	}
}

add_action( 'silver_product_image_tools_bottom', 'silver_product_video_button', 1 );

/**
 * Product image lightbox button.
 */
function silver_product_lightbox_button() {
	if ( get_theme_mod( 'product_lightbox', 'default' ) === 'disabled' ) {
		return;
	}
	printf('<a %s>%s</a>',
		silver_html_atts( [
			'role'       => 'button',
			'href'       => '#product-zoom',
			'class'      => 'zoom-button button is-outline circle icon tooltip hide-for-small',
			'title'      => esc_attr__( 'Zoom', 'silver' ),
			'aria-label' => esc_attr__( 'Zoom', 'silver' ),
		] ),
		get_silver_icon( 'icon-expand' )
	);
}

add_action( 'silver_product_image_tools_bottom', 'silver_product_lightbox_button', 2 );


// Add Product Body Classes
function silver_product_body_classes( $classes ) {

    // Add Frame Class for Posts
    if ( is_product() && 'disabled' !== get_theme_mod( 'product_lightbox', 'default' ) ) {
       $classes[] = 'has-lightbox';
    }

    return $classes;
}
add_filter( 'body_class', 'silver_product_body_classes' );


function silver_product_video_tab() {
	global $wc_cpdf;
	echo silver_apply_shortcode( 'ux_video', array(
		'url' => trim( $wc_cpdf->get_value( get_the_ID(), '_product_video' ) ),
	) );
}

// Custom Product Tabs
function silver_custom_product_tabs( $tabs ) {
  global $wc_cpdf;

    // Product video Tab
  if($wc_cpdf->get_value(get_the_ID(), '_product_video_placement') == 'tab'){
      $tabs['ux_video_tab'] = array(
        'title'   => __('Video','silver'),
        'priority'  => 10,
        'callback'  => 'silver_product_video_tab'
      );
  }

  // Adds the new tab
  if($wc_cpdf->get_value(get_the_ID(), '_custom_tab_title')){
    $tabs['ux_custom_tab'] = array(
      'title'   =>  $wc_cpdf->get_value(get_the_ID(), '_custom_tab_title'),
      'priority'  => 40,
      'callback'  => 'silver_custom_tab_content'
    );
  }

  // Custom Global Section
  if(get_theme_mod('tab_title')){
      $tabs['ux_global_tab'] = array(
        'title'   => get_theme_mod('tab_title'),
        'priority'  => 50,
        'callback'  => 'silver_global_tab_content'
      );
  }

  // Move review tab to the last position
  //$tabs['reviews']['priority'] = 100;

  return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'silver_custom_product_tabs' );

function silver_custom_tab_content() {
  // The new tab content
  global $wc_cpdf;
  echo do_shortcode($wc_cpdf->get_value(get_the_ID(), '_custom_tab'));
}

function silver_global_tab_content() {
  // The new tab content
  echo do_shortcode(get_theme_mod('tab_content'));
}


function silver_product_tabs_classes(){
    $classes = array('nav','nav-uppercase');
    $tab_style = get_theme_mod('product_display','tabs');
    if($tab_style == 'tabs' || !$tab_style){
      $classes[] = 'nav-line';
    } else{
      $tab_style = str_replace("tabs_","",$tab_style);
      if($tab_style == 'vertical') $classes[] = 'nav-line';
      if($tab_style == 'normal') $classes[] = 'nav-tabs';
      $classes[] = 'nav-'.$tab_style;
    }

    $align = get_theme_mod('product_tabs_align','left');

    if($align){
        $classes[] = 'nav-'.$align;
    }

    echo implode(' ', $classes);
}

/**
 * Displays the product brand(s).
 */
function silver_product_brands() {
	if ( ! get_theme_mod( 'product_brands' ) ) {
		return;
	}

	echo '<div class="ux-product-brands">';
	echo silver_apply_shortcode( 'product_brand', array(
		'class' => '',
	) );
	echo '</div>';
}

add_action( 'woocommerce_single_product_summary', 'silver_product_brands', 1 );

// Add Custom HTML Blocks
function silver_before_add_to_cart_html(){
    echo do_shortcode(get_theme_mod('html_before_add_to_cart'));
}
add_action( 'woocommerce_single_product_summary', 'silver_before_add_to_cart_html', 20);


// Add HTML after Add to Cart button
function silver_after_add_to_cart_html(){
    echo do_shortcode(get_theme_mod('html_after_add_to_cart'));
}
add_action( 'woocommerce_single_product_summary', 'silver_after_add_to_cart_html', 30);


// Add Custom HTML to top of product page
function silver_product_top_content(){
  global $wc_cpdf;
  if($wc_cpdf->get_value(get_the_ID(), '_top_content')){
    echo do_shortcode($wc_cpdf->get_value(get_the_ID(), '_top_content'));
  }
}

add_action('silver_before_product_page','silver_product_top_content', 10);

// Add Custom HTML to bottom of product page
function silver_product_bottom_content(){
  global $wc_cpdf;
  if($wc_cpdf->get_value(get_the_ID(), '_bottom_content')){
    echo do_shortcode($wc_cpdf->get_value(get_the_ID(), '_bottom_content'));
  }
}
add_action('silver_after_product_page','silver_product_bottom_content', 10);

function silver_related_products_args( $args ) {
	$args['posts_per_page'] = (int) get_theme_mod( 'max_related_products', 8 );

  return $args;
}

add_filter( 'woocommerce_output_related_products_args', 'silver_related_products_args' );

/**
 * Sticky add to cart template.
 *
 * @return void
 */
function silver_sticky_add_to_cart_template() {
	global $product;

	if (
		! get_theme_mod( 'product_sticky_cart', 0 )
		|| ! is_product()
		|| ! is_a( $product, 'WC_Product' )
		|| ! $product->is_purchasable()
		|| ! apply_filters( 'silver_sticky_add_to_cart_enabled', true, $product )
	) {
		return;
	}

	$classes         = array( 'sticky-add-to-cart' );
	$product_classes = implode( ' ', array_diff( wc_get_product_class( 'sticky-add-to-cart__product', $product ), [ 'product' ] ) );

	if ( get_theme_mod( 'content_color' ) === 'dark' ) {
		$classes[] = 'dark';
	}
	?>
	<!--Legacy wrapper-->
	<div class="sticky-add-to-cart-wrapper">
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
			<div class="<?php echo esc_attr( $product_classes ); ?>">
				<?php
				echo woocommerce_get_product_thumbnail( 'woocommerce_gallery_thumbnail', array( // phpcs:ignore WordPress.Security.EscapeOutput
					'class' => 'attachment-woocommerce_gallery_thumbnail size-woocommerce_gallery_thumbnail sticky-add-to-cart-img',
				) );
				?>
				<div class="product-title-small hide-for-small"><strong><?php the_title(); ?></strong></div>
				<?php
				if ( $product->is_type( 'simple' ) ) :
					// The price before the form (quantity, buttons), as for the
					// other products; regular price struck out when on sale.
					$silver_sticky_price = function_exists( 'silver_product_buy_box_price_html' ) ? silver_product_buy_box_price_html( $product ) : $product->get_price_html();
					if ( '' !== $silver_sticky_price ) {
						echo '<span class="price sticky-add-to-cart__price">' . $silver_sticky_price . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					woocommerce_simple_add_to_cart();
				else :
					// Same price as the buy box: "Từ 15 ₫" for variable products
					// (lowest price, "Từ" in the format of the price), in a .price
					// so it takes the Sale Price colour (silver-modern.css).
					$silver_sticky_price = function_exists( 'silver_product_buy_box_price_html' ) ? silver_product_buy_box_price_html( $product ) : $product->get_price_html();
					echo '<span class="price sticky-add-to-cart__price">' . $silver_sticky_price . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput

					// Variable products, once every attribute is chosen
					// (silver-variation-pills.js): quantity, "Add to cart" and
					// "Buy now", which press the buttons of the product form (the
					// chosen variation); "Select options" until then. The theme
					// keeps both quantities in step (woocommerce.js).
					if ( $product->is_type( 'variable' ) ) :
						?>
						<div class="sticky-add-to-cart__variation" hidden>
							<?php
							woocommerce_quantity_input( array(
								'min_value'   => $product->get_min_purchase_quantity(),
								'max_value'   => $product->get_max_purchase_quantity(),
								'input_value' => $product->get_min_purchase_quantity(),
							), $product );
							?>
							<button type="button" class="single_add_to_cart_button button alt sticky-add-to-cart__add"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
							<?php if ( get_theme_mod( 'product_buy_now' ) ) : ?>
								<button type="button" class="ux-buy-now-button button primary sticky-add-to-cart__buy-now"><?php esc_html_e( 'Buy now', 'silver' ); ?></button>
							<?php endif; ?>
						</div>
						<?php
					endif;

					echo silver_apply_shortcode( 'button', array(
						'as'    => 'button',
						'class' => 'sticky-add-to-cart-select-options-button mb-0 ml-half',
						'color' => 'secondary',
						'text'  => $product->is_type( 'variable' ) ? esc_html__( 'Select options', 'silver' ) : $product->single_add_to_cart_text(),
					) );
				endif;
				?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'wp_footer', 'silver_sticky_add_to_cart_template' );

/**
 * Modifies the HTML attributes for a gallery image attachment.
 *
 * @param array  $atts          The existing HTML attributes.
 * @param int    $attachment_id The ID of the attachment.
 * @param string $image_size    The requested image size.
 * @param bool   $main_image    Whether the image is the main gallery image.
 *
 * @return array                The modified HTML attributes.
 */
function silver_woocommerce_gallery_image_html_attachment_image_params( $atts, $attachment_id, $image_size, $main_image ) {
	$classes = ! empty( $atts['class'] ) ? explode( ' ', $atts['class'] ) : array();

	// Skip lazy load on main product gallery image, attribute fetchpriority="high" may not always be present.
	if ( $main_image ) {
		$classes[] = 'ux-skip-lazy';
	}

	if (
		class_exists( 'Jetpack' )
		&& Jetpack::is_module_active( 'lazy-images' )
		&& ! get_theme_mod( 'product_gallery_woocommerce' )
	) {
		// skip-lazy, blacklist for Jetpack's lazy load.
		$classes[] = 'skip-lazy';
	}

	$atts['class'] = implode( ' ', $classes );

	return $atts;
}

add_filter( 'woocommerce_gallery_image_html_attachment_image_params', 'silver_woocommerce_gallery_image_html_attachment_image_params', 10, 4 );

/**
 * The parts under the product (woocommerce_after_single_product_summary:
 * tabs, upsells, related products, and what plugins add), each in a section
 * of its own with its own container, so a section can have a full-width
 * background. Used by the layouts whose .product-footer is full width
 * (single-product/layouts/product.php...). The callbacks run in the order and
 * with the priority of the hook; a part that prints nothing gets no section.
 */
function silver_product_footer_sections() {
	global $wp_filter;

	$tag = 'woocommerce_after_single_product_summary';

	// Tells the tabs template that the section has the container.
	do_action( 'silver_product_footer_sections' );

	if ( empty( $wp_filter[ $tag ] ) || ! $wp_filter[ $tag ] instanceof WP_Hook ) {
		return;
	}

	// The hook still runs for code that listens to it (did_action()).
	$GLOBALS['wp_actions'][ $tag ] = isset( $GLOBALS['wp_actions'][ $tag ] ) ? $GLOBALS['wp_actions'][ $tag ] + 1 : 1;

	$callbacks = $wp_filter[ $tag ]->callbacks;
	ksort( $callbacks );

	foreach ( $callbacks as $priority => $items ) {
		foreach ( $items as $item ) {
			// Unhooked while the previous parts printed.
			if ( false === has_action( $tag, $item['function'] ) ) {
				continue;
			}

			ob_start();
			call_user_func( $item['function'] );
			$html = (string) ob_get_clean();

			if ( '' === trim( $html ) ) {
				continue;
			}

			$name = is_string( $item['function'] ) ? $item['function'] : 'part';
			$name = sanitize_html_class( str_replace( '_', '-', preg_replace( '/^(woocommerce_output_|woocommerce_|silver_)/', '', $name ) ) );

			// The tabs in the "Sections" style have a full-width section and a
			// container per tab (single-product/tabs/sections.php).
			$contain = ! ( 'woocommerce_output_product_data_tabs' === $item['function'] && 'sections' === get_theme_mod( 'product_display', 'tabs' ) );

			/**
			 * Whether a part under the product gets a container in its section.
			 *
			 * @param bool  $contain  True for a container.
			 * @param mixed $callback The hooked callback.
			 */
			$contain = apply_filters( 'silver_product_footer_section_container', $contain, $item['function'] );

			echo '<div class="product-footer-section product-footer-section--' . esc_attr( $name ) . '">'
				. ( $contain ? '<div class="container">' . $html . '</div>' : $html )
				. '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output of the hooked templates.
		}
	}
}

/*
 * Order of the product tabs (Product Page > Product tabs order): a sortable
 * list of the tabs of the theme and WooCommerce, the eye hides one. Applies to
 * every tabs style (tabs, sections, accordion). Tabs added by plugins and not
 * in the list keep their own priority.
 */

/**
 * Tabs that can be sorted: key => label.
 *
 * @return array
 */
function silver_product_tabs_order_choices() {
	return (array) apply_filters( 'silver_product_tabs_order_choices', array(
		'description'            => __( 'Mô tả', 'silver' ),
		'additional_information' => __( 'Thông số kỹ thuật', 'silver' ),
		'silver_faq'             => __( 'Câu hỏi thường gặp', 'silver' ),
		'reviews'                => __( 'Đánh giá', 'silver' ),
		'ux_custom_tab'          => __( 'Tab tùy chỉnh của sản phẩm', 'silver' ),
		'ux_global_tab'          => __( 'Tab chung', 'silver' ),
	) );
}

/**
 * The saved order with the tabs added to the list since it was saved.
 *
 * @param mixed $order Saved order.
 *
 * @return mixed
 */
function silver_product_tabs_order_add_new( $order ) {
	if ( ! is_array( $order ) || ! function_exists( 'silver_sortable_insert_new' ) ) {
		return $order;
	}

	$defaults = array_keys( silver_product_tabs_order_choices() );

	return silver_sortable_insert_new( $order, $defaults, get_theme_mod( 'product_tabs_order_known', $defaults ) );
}
add_filter( 'theme_mod_product_tabs_order', 'silver_product_tabs_order_add_new' );

/** Remembers the tabs known when the Customizer is saved. */
function silver_product_tabs_order_remember_known() {
	set_theme_mod( 'product_tabs_order_known', array_keys( silver_product_tabs_order_choices() ) );
}
add_action( 'customize_save_after', 'silver_product_tabs_order_remember_known' );

/**
 * Sorts and hides the tabs (after woocommerce_sort_product_tabs, 99): the
 * listed tabs take the priorities 10, 20, 30... in the saved order; the
 * hidden ones are removed; the other tabs keep their priority.
 *
 * @param array $tabs Product tabs.
 *
 * @return array
 */
function silver_product_tabs_order( $tabs ) {
	$choices = array_keys( silver_product_tabs_order_choices() );
	$order   = get_theme_mod( 'product_tabs_order', $choices );
	$order   = is_array( $order ) ? array_values( array_intersect( $order, $choices ) ) : $choices;

	foreach ( $choices as $key ) {
		if ( isset( $tabs[ $key ] ) && ! in_array( $key, $order, true ) ) {
			unset( $tabs[ $key ] );
		}
	}

	foreach ( $order as $index => $key ) {
		if ( isset( $tabs[ $key ] ) ) {
			$tabs[ $key ]['priority'] = ( $index + 1 ) * 10;
		}
	}

	uasort( $tabs, function ( $a, $b ) {
		$pa = isset( $a['priority'] ) ? (float) $a['priority'] : 0;
		$pb = isset( $b['priority'] ) ? (float) $b['priority'] : 0;
		return $pa === $pb ? 0 : ( $pa < $pb ? -1 : 1 );
	} );

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'silver_product_tabs_order', 100 );

/*
 * Table of contents in the Description tab: the content goes through
 * [ux_toc] (inc/shortcodes/ux_toc.php) with the h3 and h4 headings: a box
 * "Nội dung chính" before the content that opens and closes. No such headings: the content as it is.
 * Filters "silver_product_description_toc" (false turns it off) and
 * "silver_product_description_toc_atts".
 */

/**
 * The Description tab prints through silver_product_description_toc_tab().
 *
 * @param array $tabs Product tabs.
 *
 * @return array
 */
function silver_product_description_toc_tabs( $tabs ) {
	if ( empty( $tabs['description']['callback'] ) || ! function_exists( 'silver_toc_shortcode' ) || ! apply_filters( 'silver_product_description_toc', true ) ) {
		return $tabs;
	}

	$tabs['description']['silver_toc_callback'] = $tabs['description']['callback'];
	$tabs['description']['callback']            = 'silver_product_description_toc_tab';

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'silver_product_description_toc_tabs', 98 );

/**
 * The Description tab: its content with the table of contents.
 *
 * @param string $key Tab key.
 * @param array  $tab Tab.
 */
function silver_product_description_toc_tab( $key, $tab ) {
	ob_start();
	if ( ! empty( $tab['silver_toc_callback'] ) && is_callable( $tab['silver_toc_callback'] ) ) {
		call_user_func( $tab['silver_toc_callback'], $key, $tab );
	}
	$html = (string) ob_get_clean();

	// Product page: before the content, a box "Nội dung chính" that opens and
	// closes (closed at first); on phones the box stays and the bar sticks
	// under the header while the description is read.
	$atts = apply_filters( 'silver_product_description_toc_atts', array(
		'_id'      => 'product-description-toc',
		'title'    => __( 'Nội dung chính', 'silver' ),
		'levels'   => 'h3,h4',
		'position' => 'top',
		'mobile'   => 'bar',
		'collapse' => 'closed',
	) );

	echo silver_toc_shortcode( $atts, $html, 'ux_toc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the tab content, with the list built by ux_toc.
}

/*
 * Read more in the product tabs (Product Page > Read more in the product
 * tabs): the content of each tab goes through [ux_readmore]
 * (inc/shortcodes/ux_readmore.php), cut to a height with a "Xem thêm" button
 * when it is taller. Every tabs style; a hidden tab is measured once shown
 * (silver-readmore.js). Filter "silver_product_tabs_readmore_keys" picks the
 * tabs (all by default).
 */

/**
 * The tabs print through silver_product_tab_readmore() (after the order of
 * the tabs, 100).
 *
 * @param array $tabs Product tabs.
 *
 * @return array
 */
function silver_product_tabs_readmore( $tabs ) {
	if ( ! get_theme_mod( 'product_tabs_readmore', 1 ) || ! function_exists( 'silver_readmore_shortcode' ) ) {
		return $tabs;
	}

	$keys = apply_filters( 'silver_product_tabs_readmore_keys', array_keys( $tabs ), $tabs );

	foreach ( $tabs as $key => $tab ) {
		if ( empty( $tab['callback'] ) || ! in_array( $key, (array) $keys, true ) ) {
			continue;
		}
		$tabs[ $key ]['silver_readmore_callback'] = $tab['callback'];
		$tabs[ $key ]['callback']                 = 'silver_product_tab_readmore';
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'silver_product_tabs_readmore', 101 );

/**
 * A tab: its content in a read more.
 *
 * @param string $key Tab key.
 * @param array  $tab Tab.
 */
function silver_product_tab_readmore( $key, $tab ) {
	ob_start();
	if ( ! empty( $tab['silver_readmore_callback'] ) && is_callable( $tab['silver_readmore_callback'] ) ) {
		call_user_func( $tab['silver_readmore_callback'], $key, $tab );
	}
	$html = (string) ob_get_clean();

	if ( '' === trim( $html ) ) {
		return;
	}

	echo silver_readmore_shortcode( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the tab content in the read more.
		'_id'    => 'product-tab-readmore-' . sanitize_html_class( $key ),
		'height' => absint( get_theme_mod( 'product_tabs_readmore_height', 600 ) ),
	), $html, 'ux_readmore' );
}
