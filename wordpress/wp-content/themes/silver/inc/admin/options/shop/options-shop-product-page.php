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

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'product_lightbox',
		'active_callback' => function() {
			return ! get_theme_mod( 'product_gallery_woocommerce' );
		},
		'label'           => __( 'Product Image Lightbox', 'silver' ),
		'description'     => __( 'Show images in a lightbox when clicking on image in gallery. You might need to save and close Customizer for this to work properly.', 'silver' ),
		'section'         => 'product-page',
		'default'         => 'default',
		'choices'         => array(
			'default'     => __( 'New WooCommerce 3.0 Lightbox', 'silver' ),
			'flatsome'    => __( 'Silver Lightbox', 'silver' ),
			'woocommerce' => __( 'Old WooCommerce Lightbox', 'silver' ),
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

	Silver_Option::add_field( 'option', array(
		'type'     => 'slider',
		'settings' => 'related_products_pr_row_tablet',
		'label'    => __( 'Products per row - Tablet', 'silver' ),
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
