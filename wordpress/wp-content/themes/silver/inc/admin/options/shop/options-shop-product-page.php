<?php

Silver_Option::add_section( 'product-page', array(
	'title' => __( 'Product Page', 'silver' ),
	'panel' => 'woocommerce',
) );

function silver_customizer_shop_product_page_options() {

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_product_layout',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<div class="options-title-divider">Layout</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'radio-image',
		'settings' => 'product_layout',
		'label'    => __( 'Product Layout', 'silver' ),
		'section'  => 'product-page',
		'default'  => silver_product_layout(),
		'choices'  => array(
			'no-sidebar'          => silver_customizer_images_uri() . '/layout-no-sidebar.svg',
			'left-sidebar'        => silver_customizer_images_uri() . '/layout-left.svg',
			'left-sidebar-full'   => silver_customizer_images_uri() . '/layout-left-full.svg',
			'left-sidebar-small'  => silver_customizer_images_uri() . '/layout-left-small.svg',
			'right-sidebar'       => silver_customizer_images_uri() . '/layout-right.svg',
			'right-sidebar-small' => silver_customizer_images_uri() . '/layout-right-small.svg',
			'right-sidebar-full'  => silver_customizer_images_uri() . '/layout-right-full.svg',
			'gallery-wide'        => silver_customizer_images_uri() . '/layout-wide-gallery.svg',
			'stacked-right'       => silver_customizer_images_uri() . '/product-stacked.svg',
			'custom'              => silver_customizer_images_uri() . '/layout-custom.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_custom_layout',
		'label'           => __( 'Custom product layout', 'silver' ),
		'description'     => __( 'Create a custom product layout by using the UX Builder. You need to select a Block and then open it in the UX Builder from a product page in the front-end.', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			array(
				'setting'  => 'product_layout',
				'operator' => '==',
				'value'    => 'custom',
			),
		),
		'default'         => false,
		'choices'         => silver_customizer_blocks(),
	) );

	$hide_on_gallery_wide   = array(
		'setting'  => 'product_layout',
		'operator' => '!==',
		'value'    => 'gallery-wide',
	);
	$hide_on_custom_product = array(
		'setting'  => 'product_layout',
		'operator' => '!==',
		'value'    => 'custom',
	);

	Silver_Option::add_field( 'option', array(
		'type'     => 'checkbox',
		'settings' => 'product_offcanvas_sidebar',
		'label'    => __( 'Off-canvas Sidebar for Mobile', 'silver' ),
		'section'  => 'product-page',
		'default'  => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'product_offcanvas_sidebar_position',
		'label'           => __( 'Off-canvas Sidebar Position', 'silver' ),
		'section'         => 'product-page',
		'default'         => 'left',
		'transport'       => silver_customizer_transport(),
		'choices'         => array(
			'left'   => silver_customizer_images_uri() . '/overlay-left.svg',
			'right'  => silver_customizer_images_uri() . '/overlay-right.svg',
			'center' => silver_customizer_images_uri() . '/overlay-center.svg',
		),
		'active_callback' => array(
			array(
				'setting'  => 'product_offcanvas_sidebar',
				'operator' => '==',
				'value'    => 1,
			),
		),
	));

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'product_header',
		'label'           => __( 'Product Header', 'silver' ),
		'section'         => 'product-page',
		'default'         => '',
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
		),
		'choices'         => array(
			''                => silver_customizer_images_uri() . '/product-title.svg',
			'top'             => silver_customizer_images_uri() . '/product-title-top.svg',
			'featured'        => silver_customizer_images_uri() . '/product-title-featured.svg',
			'featured-center' => silver_customizer_images_uri() . '/product-title-featured-center.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'checkbox',
		'settings' => 'product_header_transparent',
		'label'    => __( 'Transparent Header', 'silver' ),
		'section'  => 'product-page',
		'default'  => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_next_prev_nav',
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
		),
		'label'           => __( 'Next / Prev Navigation', 'silver' ),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_product_gallery',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<div class="options-title-divider">Gallery</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'select',
		'settings' => 'product_video_style',
		'label'    => __( 'Product video button', 'silver' ),
		'section'  => 'product-page',
		'default'  => 'overlay',
		'choices'  => array(
			'overlay'   => __( 'On the product image', 'silver' ),
			'thumbnail' => __( 'In the gallery thumbnails', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_video_playback',
		'label'           => __( 'Thumbnail video playback', 'silver' ),
		'section'         => 'product-page',
		'default'         => 'popup',
		'choices'         => array(
			'popup'   => __( 'Open in popup', 'silver' ),
			'gallery' => __( 'Play in the gallery', 'silver' ),
		),
		'active_callback' => array(
			array( 'setting' => 'product_video_style', 'operator' => '==', 'value' => 'thumbnail' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_image_width',
		'label'           => __( 'Product Image Width', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => function () {
			return ! get_theme_mod( 'product_gallery_woocommerce' )
						&& get_theme_mod( 'product_layout' ) !== 'gallery-wide'
						&& get_theme_mod( 'product_layout' ) !== 'custom';
		},
		'transport'       => silver_customizer_transport(),
		'default'         => '6',
		'choices'         => array(
			'8' => __( '8/12', 'silver' ),
			'7' => __( '7/12', 'silver' ),
			'6' => __( '6/12 (50%)', 'silver' ),
			'5' => __( '5/12', 'silver' ),
			'4' => __( '4/12', 'silver' ),
			'3' => __( '3/12', 'silver' ),
			'2' => __( '2/12', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'product_image_style',
		'label'           => __( 'Product Image Style', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => function () {
			return ! get_theme_mod( 'product_gallery_woocommerce' )
						&& get_theme_mod( 'product_layout' ) !== 'gallery-wide'
						&& get_theme_mod( 'product_layout' ) !== 'custom'
						&& get_theme_mod( 'product_layout' ) !== 'stacked-right';
		},
		'default'         => 'normal',
		'choices'         => array(
			'normal'   => silver_customizer_images_uri() . '/product-gallery.svg',
			'vertical' => silver_customizer_images_uri() . '/product-gallery-vertical.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'product_gallery_grid_layout',
		'label'           => __( 'Grid layout', 'silver' ),
		'section'         => 'product-page',
		'default'         => '',
		'active_callback' => array(
			array(
				'setting'  => 'product_layout',
				'operator' => '===',
				'value'    => 'stacked-right',
			),
		),
		'choices'         => array(
			''      => silver_customizer_images_uri() . '/product-gallery-grid-1.svg',
			'1-2'   => silver_customizer_images_uri() . '/product-gallery-grid-1-2.svg',
			'2'     => silver_customizer_images_uri() . '/product-gallery-grid-2.svg',
			'3-1-2' => silver_customizer_images_uri() . '/product-gallery-grid-3-1-2.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_gallery_slider_type',
		'active_callback' => function () {
			return ! get_theme_mod( 'product_gallery_woocommerce' )
			       && get_theme_mod( 'product_layout' ) !== 'gallery-wide';
		},
		'label'           => esc_html__( 'Type', 'silver' ),
		'section'         => 'product-page',
		'default'         => '',
		'choices'         => array(
			''     => esc_html__( 'Slide', 'silver' ),
			'fade' => esc_html__( 'Fade', 'silver' ),
		),
	) );

	// Thumbnails per view under the gallery, per screen. The video tile
	// (Product video > In the gallery thumbnails) counts as one. See
	// silver_product_gallery_thumbs_per_view() in
	// inc/woocommerce/structure-wc-product-page.php.
	$thumbs_choices = array( 0 => __( 'Auto', 'silver' ) );
	foreach ( range( 2, 8 ) as $thumbs_count ) {
		$thumbs_choices[ $thumbs_count ] = (string) $thumbs_count;
	}
	$thumbs_screens = array(
		'desktop' => __( 'Thumbnails per view: desktop (1200px and up)', 'silver' ),
		'laptop'  => __( 'Thumbnails per view: tablet landscape (850 to 1199px)', 'silver' ),
		'tablet'  => __( 'Thumbnails per view: tablet portrait (550 to 849px)', 'silver' ),
		'mobile'  => __( 'Thumbnails per view: mobile (under 550px)', 'silver' ),
	);
	foreach ( $thumbs_screens as $thumbs_screen => $thumbs_label ) {
		Silver_Option::add_field( 'option', array(
			'type'            => 'select',
			'settings'        => 'product_gallery_thumbs_' . $thumbs_screen,
			'label'           => $thumbs_label,
			'description'     => 'desktop' === $thumbs_screen ? __( 'Auto keeps the theme size. A video thumbnail counts as one.', 'silver' ) : '',
			'section'         => 'product-page',
			'default'         => 0,
			'choices'         => $thumbs_choices,
			'active_callback' => function () {
				return ! get_theme_mod( 'product_gallery_woocommerce' )
				       && get_theme_mod( 'product_layout' ) !== 'gallery-wide'
				       && get_theme_mod( 'product_image_style', 'normal' ) !== 'vertical';
			},
		) );
	}

	// Keep the saved enabled value selectable for sites using either older viewer.
	$viewer = get_theme_mod( 'product_lightbox', 'default' );
	$enabled_viewer = in_array( $viewer, array( 'flatsome', 'woocommerce' ), true ) ? $viewer : 'default';

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_lightbox',
		'label'           => __( 'Product Image Lightbox', 'silver' ),
		'section'         => 'product-page',
		'default'         => 'default',
		'choices'         => array(
			$enabled_viewer => 'silverDialog',
			'disabled'    => __( 'Disable Lightbox', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_zoom',
		'active_callback' => function () {
			return ! get_theme_mod( 'product_gallery_woocommerce' )
						&& get_theme_mod( 'product_layout' ) !== 'gallery-wide';
		},
		'label'           => __( 'Product Image Hover Zoom', 'silver' ),
		'description'     => __( 'Show a zoomed version of image when hovering gallery', 'silver' ),
		'section'         => 'product-page',
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_sticky_gallery',
		'active_callback' => function () {
			$product_layout = get_theme_mod( 'product_layout' );

			return $product_layout !== 'gallery-wide'
					&& $product_layout !== 'custom';
		},
		'label'           => esc_html__( 'Sticky gallery', 'silver' ),
		'section'         => 'product-page',
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_sticky_gallery_mode',
		'label'           => esc_html__( 'Sticky mode', 'silver' ),
		'section'         => 'product-page',
		'default'         => '',
		'choices'         => array(
			''           => esc_html__( 'CSS (native)', 'silver' ),
			'javascript' => esc_html__( 'JavaScript (enhanced)', 'silver' ),
		),
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
			array(
				'setting'  => 'product_sticky_gallery',
				'operator' => '==',
				'value'    => true,
			),
		),
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_html_woocommerce_image_shortcut_product',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<button style="margin-top: 15px; margin-bottom:15px" class="button button-primary" data-to-section="woocommerce_product_images">WooCommerce Image Settings →</button>',
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_section_gallery_message',
		'label'           => __( '', 'silver' ),
		'active_callback' => function () {
			return get_theme_mod( 'product_gallery_woocommerce' ) ? true : false;
		},
		'section'         => 'product-page',
		'default'         => '<span style="margin-top: -10px" class="description customize-control-description">The default WooCommerce gallery has been activated in section Advanced → WooCommerce. Multiple Silver gallery options are disabled.</span>',
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_product_info',
		'label'           => __( '', 'silver' ),
		'section'         => 'product-page',
		'default'         => '<div class="options-title-divider">Product Summary</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_info_align',
		'label'           => __( 'Text Align', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
		),
		'default'         => 'left',
		'choices'         => array(
			'left'   => __( 'Left', 'silver' ),
			'center' => __( 'Center', 'silver' ),
			'right'  => __( 'Right', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_info_form',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'label'           => __( 'Form Style', 'silver' ),
		'section'         => 'product-page',
		'default'         => '',
		'choices'         => array(
			''     => __( 'Default', 'silver' ),
			'flat' => __( 'Flat', 'silver' ),
			'minimal' => __( 'Minimal', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_title_divider',
		'label'           => __( 'Product Title divider', 'silver' ),
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	// Buy box (inc/woocommerce/structure-wc-product-buy-box.php).
	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_buy_box',
		'label'           => __( 'Buy box', 'silver' ),
		'description'     => __( 'Price, attribute buttons, "Add to cart" and "Call now" in a card under the short description; rating with five stars; no title divider unless turned on.', 'silver' ),
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	if ( function_exists( 'silver_product_info_elements' ) ) {
		$silver_info_elements = silver_product_info_elements();

		Silver_Option::add_field( 'option', array(
			'type'            => 'sortable',
			'settings'        => 'product_info_elements',
			'label'           => __( 'Product info elements', 'silver' ),
			'description'     => __( 'Drag to sort the parts next to the gallery, click the eye to show or hide one. Hiding the buy box hides "Add to cart".', 'silver' ),
			'multiple'        => 99,
			'default'         => array_keys( $silver_info_elements ),
			'choices'         => wp_list_pluck( $silver_info_elements, 'label' ),
			'active_callback' => array(
				$hide_on_custom_product,
				array(
					'setting'  => 'product_buy_box',
					'operator' => '==',
					'value'    => true,
				),
			),
			'section'         => 'product-page',
		) );
	}

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_buy_box_quantity',
		'label'           => __( 'Buy box: show quantity', 'silver' ),
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_buy_box',
				'operator' => '==',
				'value'    => true,
			),
		),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'text',
		'settings'        => 'product_call_phone',
		'label'           => __( 'Buy box: "Call now" phone', 'silver' ),
		'description'     => __( 'E.g. 0900 000 000. Empty: no "Call now" button.', 'silver' ),
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_buy_box',
				'operator' => '==',
				'value'    => true,
			),
		),
		'section'         => 'product-page',
		'default'         => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_brands',
		'label'           => __( 'Show brand image', 'silver' ),
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'section'         => 'product-page',
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_info_review_count',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'label'           => __( 'Show Review Count', 'silver' ),
		'section'         => 'product-page',
		'default'         => false,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_info_review_count_style',
		'label'           => __( 'Review Count Style', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_info_review_count',
				'operator' => '==',
				'value'    => true,
			),
		),
		'default'         => 'inline',
		'choices'         => array(
			'tooltip' => __( 'Tooltip', 'silver' ),
			'stacked' => __( 'Stacked', 'silver' ),
			'inline'  => __( 'Inline', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'checkbox',
		'settings' => 'product_sticky_cart',
		'label'    => esc_html__( 'Sticky add to cart', 'silver' ),
		'section'  => 'product-page',
		'default'  => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'checkbox',
		'settings' => 'product_buy_now',
		'label'    => esc_html__( 'Buy now button', 'silver' ),
		'section'  => 'product-page',
		'default'  => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_buy_now_redirect',
		'label'           => esc_html__( 'Buy now redirect', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			array(
				'setting'  => 'product_buy_now',
				'operator' => '==',
				'value'    => true,
			),
		),
		'default'         => 'checkout',
		'choices'         => array(
			'checkout' => esc_html__( 'Checkout page', 'silver' ),
			'cart'     => esc_html__( 'Cart page', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_info_meta',
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
		),
		'label'           => __( 'Show Meta / Categories / Brands', 'silver' ),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_info_share',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'label'           => __( 'Show Share Icons', 'silver' ),
		'section'         => 'product-page',
		'default'         => 1,
	) );

	if ( get_theme_mod( 'swatches' ) ) :
		Silver_Option::add_field( '', array(
			'type'     => 'custom',
			'settings' => 'custom_title_swatches',
			'label'    => '',
			'section'  => 'product-page',
			'default'  => '<div class="options-title-divider">Swatches</div>',
		) );

		Silver_Option::add_field( 'option', array(
			'type'     => 'radio-image',
			'settings' => 'swatches_layout',
			'label'    => __( 'Layout', 'silver' ),
			'section'  => 'product-page',
			'default'  => '',
			'choices'  => array(
				''        => silver_customizer_images_uri() . '/product-swatches.svg',
				'stacked' => silver_customizer_images_uri() . '/product-swatches-stacked.svg',
			),
		) );

		Silver_Option::add_field( 'option', array(
			'type'        => 'checkbox',
			'settings'    => 'swatches_tooltip',
			'label'       => __( 'Tooltip', 'silver' ),
			'description' => __( 'Show a tooltip with the term or term description.', 'silver' ),
			'section'     => 'product-page',
			'default'     => 1,
		) );

		Silver_Option::add_field( 'option', array(
			'type'        => 'checkbox',
			'settings'    => 'swatches_out_of_stock_inactive',
			'label'       => __( 'Inactive out of stock', 'silver' ),
			'description' => __( 'Show out of stock items as inactive.', 'silver' ),
			'section'     => 'product-page',
			'default'     => 0,
		) );

		Silver_Option::add_field( 'option', array(
			'type'     => 'checkbox',
			'settings' => 'swatches_disable_deselect',
			'label'    => __( 'Disable deselection', 'silver' ),
			'section'  => 'product-page',
			'default'  => 0,
		) );

		Silver_Option::add_field( 'option', array(
			'type'        => 'color',
			'settings'    => 'swatches_color_selected',
			'transport'   => silver_customizer_transport(),
			'label'       => __( 'Color :selected', 'silver' ),
			'description' => __( 'Default is Secondary color', 'silver' ),
			'section'     => 'product-page',
			'default'     => '',
		) );
	endif;

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_product_tabs',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<div class="options-title-divider">Tabs</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_display',
		'label'           => __( 'Product Tabs Style', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'default'         => 'tabs',
		'choices'         => array(
			'tabs'                => __( 'Line Tabs', 'silver' ),
			'tabs_normal'         => __( 'Tabs Normal', 'silver' ),
			'line-grow'           => __( 'Line Tabs - Grow', 'silver' ),
			'tabs_vertical'       => __( 'Tabs vertical', 'silver' ),
			'tabs_pills'          => __( 'Pills', 'silver' ),
			'tabs_outline'        => __( 'Outline', 'silver' ),
			'sections'            => __( 'Sections', 'silver' ),
			'accordian'           => __( 'Accordion', 'silver' ),
			'accordian-collapsed' => __( 'Accordion - Collapsed', 'silver' ),
		),
	) );

	if ( function_exists( 'silver_product_tabs_order_choices' ) ) {
		Silver_Option::add_field( 'option', array(
			'type'            => 'sortable',
			'settings'        => 'product_tabs_order',
			'label'           => __( 'Thứ tự tab sản phẩm', 'silver' ),
			'description'     => __( 'Kéo để sắp xếp các tab, bấm biểu tượng con mắt để ẩn/hiện. Tab chỉ hiện khi sản phẩm có nội dung cho tab đó.', 'silver' ),
			'multiple'        => 99,
			'default'         => array_keys( silver_product_tabs_order_choices() ),
			'choices'         => silver_product_tabs_order_choices(),
			'active_callback' => array(
				$hide_on_custom_product,
			),
			'section'         => 'product-page',
		) );
	}

	// Read more in the product tabs (structure-wc-product-page.php).
	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_tabs_readmore',
		'label'           => __( 'Xem thêm trong tab sản phẩm', 'silver' ),
		'description'     => __( 'Nội dung tab dài hơn chiều cao bên dưới được thu gọn, kèm nút "Xem thêm".', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'product_tabs_readmore_height',
		'label'           => __( 'Chiều cao khi thu gọn (px)', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_tabs_readmore',
				'operator' => '==',
				'value'    => true,
			),
		),
		'default'         => 600,
		'choices'         => array(
			'min'  => 200,
			'max'  => 1200,
			'step' => 20,
		),
	) );

	// Sections style: description and specifications in one section
	// (woocommerce/single-product/tabs/sections.php).
	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_sections_merge_specs',
		'label'           => __( 'Gộp Mô tả và Thông số kỹ thuật thành 1 section', 'silver' ),
		'description'     => __( 'Hai cột cạnh nhau trong cùng một section; trên điện thoại xếp chồng, mô tả ở trên.', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_display',
				'operator' => '==',
				'value'    => 'sections',
			),
		),
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_sections_merge_position',
		'label'           => __( 'Vị trí mô tả', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
			array(
				'setting'  => 'product_display',
				'operator' => '==',
				'value'    => 'sections',
			),
			array(
				'setting'  => 'product_sections_merge_specs',
				'operator' => '==',
				'value'    => true,
			),
		),
		'default'         => 'left',
		'choices'         => array(
			'left'  => __( 'Bên trái', 'silver' ),
			'right' => __( 'Bên phải', 'silver' ),
		),
	) );

	// Width of the description column (the specifications take the rest):
	// Desktop >= 1200px, Tablet landscape 850-1199px (0: as Desktop). Below
	// 850px the two columns are stacked.
	$silver_merge_width = array(
		'product_sections_merge_width'                  => array( __( 'Độ rộng mô tả - Desktop (%)', 'silver' ), '', 58, 30 ),
		'product_sections_merge_width_tablet_landscape' => array( __( 'Độ rộng mô tả - Tablet landscape (%)', 'silver' ), __( 'Màn hình 850 - 1199px. 0 = giống Desktop. Thông số kỹ thuật chiếm phần còn lại.', 'silver' ), 0, 0 ),
	);

	foreach ( $silver_merge_width as $silver_setting => $silver_args ) {
		Silver_Option::add_field( 'option', array(
			'type'            => 'slider',
			'settings'        => $silver_setting,
			'label'           => $silver_args[0],
			'description'     => $silver_args[1],
			'section'         => 'product-page',
			'active_callback' => array(
				$hide_on_custom_product,
				array(
					'setting'  => 'product_display',
					'operator' => '==',
					'value'    => 'sections',
				),
				array(
					'setting'  => 'product_sections_merge_specs',
					'operator' => '==',
					'value'    => true,
				),
			),
			'default'         => $silver_args[2],
			'choices'         => array(
				'min'  => $silver_args[3],
				'max'  => 80,
				'step' => 1,
			),
		) );
	}

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_tabs_align',
		'label'           => __( 'Product Tabs Align', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'default'         => 'left',
		'choices'         => array(
			'left'   => __( 'Left', 'silver' ),
			'center' => __( 'Center', 'silver' ),
			'right'  => __( 'Right', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'text',
		'settings'          => 'tab_title',
		'label'             => __( 'Global custom tab title', 'silver' ),
		'section'           => 'product-page',
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'tab_content',
		'label'             => __( 'Global custom tab content', 'silver' ),
		'section'           => 'product-page',
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );


	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_product_related',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<div class="options-title-divider">Related / Upsell</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_upsell',
		'label'           => __( 'Product Upsell', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_gallery_wide,
			$hide_on_custom_product,
		),
		'default'         => 'sidebar',
		'choices'         => array(
			'sidebar'  => __( 'In Sidebar', 'silver' ),
			'bottom'   => __( 'Below Description', 'silver' ),
			'disabled' => __( 'Disabled', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'related_products',
		'label'           => __( 'Related Products Style', 'silver' ),
		'section'         => 'product-page',
		'active_callback' => array(
			$hide_on_custom_product,
		),
		'default'         => 'slider',
		'choices'         => array(
			'slider' => __( 'Slider', 'silver' ),
			'grid'   => __( 'Grid', 'silver' ),
			'hidden' => __( 'Disabled', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'slider',
		'settings' => 'related_products_pr_row',
		'label'    => __( 'Products per row - Desktop', 'silver' ),
		'section'  => 'product-page',
		'default'  => 4,
		'choices'  => array(
			'min'  => 1,
			'max'  => 6,
			'step' => 1,
		),
	) );

	// Tablet landscape (850-1199px). 0: the Tablet portrait value.
	Silver_Option::add_field( 'option', array(
		'type'        => 'slider',
		'settings'    => 'related_products_pr_row_tablet_landscape',
		'label'       => __( 'Products per row - Tablet landscape', 'silver' ),
		'description' => __( '0 = same as Tablet portrait.', 'silver' ),
		'section'     => 'product-page',
		'default'     => 0,
		'choices'     => array(
			'min'  => 0,
			'max'  => 6,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'slider',
		'settings' => 'related_products_pr_row_tablet',
		'label'    => __( 'Products per row - Tablet portrait', 'silver' ),
		'section'  => 'product-page',
		'default'  => 3,
		'choices'  => array(
			'min'  => 1,
			'max'  => 4,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'slider',
		'settings' => 'related_products_pr_row_mobile',
		'label'    => __( 'Products per row - Mobile', 'silver' ),
		'section'  => 'product-page',
		'default'  => 2,
		'choices'  => array(
			'min'  => 1,
			'max'  => 3,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'text',
		'settings' => 'max_related_products',
		'label'    => __( 'Max number of Related Products', 'silver' ),
		'section'  => 'product-page',
		'default'  => 8,
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_product_html',
		'label'    => __( '', 'silver' ),
		'section'  => 'product-page',
		'default'  => '<div class="options-title-divider">Custom HTML</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'html_before_add_to_cart',
		'label'             => __( 'HTML before Add To Cart button', 'silver' ),
		'section'           => 'product-page',
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'html_after_add_to_cart',
		'label'             => __( 'HTML after Add To Cart button', 'silver' ),
		'section'           => 'product-page',
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );
}
add_action( 'init', 'silver_customizer_shop_product_page_options' );
