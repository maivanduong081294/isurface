<?php

Silver_Option::add_section( 'woocommerce_product_catalog', array(
  'title' => __( 'Product Catalog', 'woocommerce' ),
  'panel' => 'woocommerce',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_homepage',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Shop Page</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'textarea',
	'settings'    => 'html_shop_page',
	'label'       => __( 'Shop Page Header', 'silver' ),
	'description' => __( 'Enter HTML that should be placed on top of main shop page. Shortcodes are allowed. This will replace Shop Homepage Header', 'silver' ),
	'section'     => 'woocommerce_product_catalog',
	'default'     => '',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'textarea',
	'settings'    => 'html_shop_page_content',
	'label'       => __( 'Shop Page Content', 'silver' ),
	'description' => __( 'Enter HTML/Shortcodes that should replace Shop Homepage content.', 'silver' ),
	'section'     => 'woocommerce_product_catalog',
	'default'     => '',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_layout',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Catalog Layout</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-image',
	'settings' => 'category_sidebar',
	'label'    => __( 'Layout', 'silver' ),
	'section'  => 'woocommerce_product_catalog',
	'default'  => 'left-sidebar',
	'choices'  => array(
		'none'          => $image_url . 'category-no-sidebar.svg',
		'left-sidebar'  => $image_url . 'category-left-sidebar.svg',
		'right-sidebar' => $image_url . 'category-right-sidebar.svg',
		'off-canvas'    => $image_url . 'category-off-canvas.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'category_sticky_sidebar',
	'label'           => __( 'Sticky sidebar', 'silver' ),
	'section'         => 'woocommerce_product_catalog',
	'active_callback' => array(
		array(
			'setting'  => 'category_sidebar',
			'operator' => '!==',
			'value'    => 'none',
		),
		array(
			'setting'  => 'category_sidebar',
			'operator' => '!==',
			'value'    => 'off-canvas',
		),
	),
	'default'         => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'category_sticky_sidebar_mode',
	'label'           => __( 'Sticky mode', 'silver' ),
	'section'         => 'woocommerce_product_catalog',
	'default'         => '',
	'choices'         => array(
		''           => __( 'CSS (native)', 'silver' ),
		'javascript' => __( 'JavaScript (enhanced)', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'category_sidebar',
			'operator' => '!==',
			'value'    => 'none',
		),
		array(
			'setting'  => 'category_sidebar',
			'operator' => '!==',
			'value'    => 'off-canvas',
		),
		array(
			'setting'  => 'category_sticky_sidebar',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'category_grid_style',
	'label'     => __( 'List Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'grid',
	'choices'   => array(
		'grid'    => $image_url . 'category-style-grid.svg',
		'list'    => $image_url . 'category-style-list.svg',
		'masonry' => $image_url . 'category-style-masonry.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'category_force_image_height',
	'label'       => __( 'EQUAL IMAGE HEIGHTS', 'silver' ),
	'description' => 'Force all images to have the same height',
	'section'     => 'woocommerce_product_catalog',
	'default'     => false,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'category_image_height',
	'label'           => __( 'Equal Image height', 'silver' ),
	'description'     => 'Change the image height in %. (100% = 1:1)',
	'section'         => 'woocommerce_product_catalog',
	'active_callback' => array(
		array(
			'setting'  => 'category_force_image_height',
			'operator' => '==',
			'value'    => true,
		),
	),
	'transport'       => silver_customizer_transport(),
	'default'         => 100,
	'choices'         => array(
		'min'  => 50,
		'max'  => 200,
		'step' => 1,
	),
) );


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_html_woocommerce_image_shortcut_category',
    'label'       => '',
    'section'         => 'woocommerce_product_catalog',
    'default'     => '<button style="margin-top: 15px; margin-bottom:15px" class="button button-primary" data-to-section="woocommerce_product_images">Thumbnail Image Settings →</button>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'products_pr_page',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Products per Page', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 12,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'category_row_count',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Products per row - Desktop', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 3,
	'choices'   => array(
		'min'  => 1,
		'max'  => 6,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'category_row_count_tablet',
	'label'     => __( 'Products per row - Tablet', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 3,
	'choices'   => array(
		'min'  => 1,
		'max'  => 4,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'category_row_count_mobile',
	'label'     => __( 'Products per row - Mobile', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 2,
	'choices'   => array(
		'min'  => 1,
		'max'  => 3,
		'step' => 1,
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_header',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Header</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'category_title_style',
	'label'     => __( 'Title Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => '',
	'choices'   => array(
		''                => $image_url . 'category-title.svg',
		'featured'        => $image_url . 'category-title-featured.svg',
		'featured-center' => $image_url . 'category-title-featured-center.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'category_show_title',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Show title', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => '0',
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'breadcrumb_home',
	'transport'       => silver_customizer_transport(),
	'label'           => __( 'Show home link in breadcrumb', 'silver' ),
	'section'         => 'woocommerce_product_catalog',
	'active_callback' => function () {
		$wpseo     = class_exists( 'WPSEO_Options' ) && get_theme_mod( 'wpseo_breadcrumb' ) ? true : false;
		$rank_math = class_exists( 'RankMath' ) && get_theme_mod( 'rank_math_breadcrumb' ) ? true : false;

		return ! $wpseo && ! $rank_math;
	},
	'default'         => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'category_show_result_count',
	'transport' => silver_customizer_transport(),
	'label'     => esc_html__( 'Show result count', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'category_show_catalog_ordering',
	'transport' => silver_customizer_transport(),
	'label'     => esc_html__( 'Show catalog ordering', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'category_header_transparent',
	'label'    => __( 'Transparent Header', 'silver' ),
	'section'  => 'woocommerce_product_catalog',
	'default'  => '0',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'header_shop_bg_featured',
	'transport' => silver_customizer_transport(),
	'help'      => __( 'Use Featured Images from categories and products as background. Will fallback to default Shop Title background if nothing is set.', 'silver' ),
	'label'     => __( 'Featured Image as Background', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'image',
	'settings'  => 'header_shop_bg_image',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Shop Title Background', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'alpha'     => true,
	'settings'  => 'header_shop_bg_color',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Title Background Color', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 'rgba(0,0,0,.3)',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'category_filter_text',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Custom Filter Text', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'category_filter_overlay_position',
	'label'     => __( 'Filter Overlay Position', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'left',
	'choices'   => array(
		'left'   => silver_customizer_images_uri() . '/overlay-left.svg',
		'right'  => silver_customizer_images_uri() . '/overlay-right.svg',
		'center' => silver_customizer_images_uri() . '/overlay-center.svg',
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_breadcrumbs',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Breadcrumbs</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'breadcrumb_size',
	'label'    => __( 'Breadcrumb Size', 'silver' ),
	'help'     => __( 'Change size of breadcrumb on product categories. Useful if you have long breadcrumbs.', 'silver' ),
	'section'  => 'woocommerce_product_catalog',
	'default'  => 'large',
	'choices'  => $sizes,
) );


Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'breadcrumb_case',
	'label'    => esc_attr__( 'Breadcrumbs Case', 'silver' ),
	'section'  => 'woocommerce_product_catalog',
	'default'  => 'uppercase',
	'choices'  => array(
		'uppercase' => 'UPPERCASE',
		''          => 'Normal',
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_category_box',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Category Box</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'cat_style',
	'label'     => __( 'Category Box Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'badge',
	'choices'   => array(
		'normal'  => $image_url . 'category-box.svg',
		'badge'   => $image_url . 'category-box-badge.svg',
		'overlay' => $image_url . 'category-box-overlay.svg',
		'label'   => $image_url . 'category-box-label.svg',
		'shade'   => $image_url . 'category-box-shade.svg',
		'bounce'  => $image_url . 'category-box-bounce.svg',
		'push'    => $image_url . 'category-box-push.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'category_show_count',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Show product count', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_category_product_box',
	'label'    => '',
	'section'  => 'woocommerce_product_catalog',
	'default'  => '<div class="options-title-divider">Product Box</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'grid_style',
	'label'     => __( 'Grid Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'grid1',
	'choices'   => array(
		'grid1' => $image_url . 'product-box.svg',
		'grid2' => $image_url . 'product-box-center.svg',
		'grid3' => $image_url . 'product-box-wide.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'select',
	'settings'  => 'product_hover',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Product Image Hover style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 'fade_in_back',
	'choices'   => array(
		'none'         => __( 'None', 'silver' ),
		'fade_in_back' => __( 'Back Image - Fade In', 'silver' ),
		'zoom_in'      => __( 'Back Image - Zoom In', 'silver' ),
		'zoom'         => 'Zoom',
		'zoom-fade'    => 'Zoom Fade',
		'blur'         => 'Blur',
		'fade-in'      => 'Fade In',
		'fade-out'     => 'Fade Out',
		'glow'         => 'Glow',
		'color'        => 'Add Color',
		'grayscale'    => 'Grayscale',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'category_shadow',
	'label'     => __( 'Drop Shadow', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 0,
	'choices'   => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'category_shadow_hover',
	'label'     => __( 'Drop Shadow :hover', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 0,
	'choices'   => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'add_to_cart_icon',
	'label'     => __( 'Add To Cart Button', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'disable',
	'choices'   => array(
		'disable' => $image_url . 'product-box.svg',
		'show'    => $image_url . 'product-box-add-to-cart-icon.svg',
		'button'  => $image_url . 'product-box-add-to-cart-button.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'select',
	'settings'  => 'add_to_cart_style',
	'label'     => __( 'Button Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'outline',
	'choices'   => array(
		'flat'      => __( 'Plain', 'silver' ),
		'outline'   => __( 'Outline', 'silver' ),
		'underline' => __( 'Underline', 'silver' ),
		'shade'     => __( 'Shade', 'silver' ),
		'bevel'     => __( 'Bevel', 'silver' ),
		'gloss'     => __( 'Gloss', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'product_box_category',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Show Category', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'product_box_rating',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Show Ratings', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'product_box_empty_rating',
	'transport'       => silver_customizer_transport(),
	'label'           => esc_html__( 'Show empty ratings', 'silver' ),
	'section'         => 'woocommerce_product_catalog',
	'default'         => 0,
	'active_callback' => array(
		array(
			'setting'  => 'product_box_rating',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'product_box_review_count',
	'transport'       => silver_customizer_transport(),
	'label'           => esc_html__( 'Show review count', 'silver' ),
	'section'         => 'woocommerce_product_catalog',
	'default'         => 0,
	'active_callback' => array(
		array(
			'setting'  => 'product_box_rating',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'short_description_in_grid',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Show Short Description', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => '0',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'disable_quick_view',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Disable Quick View', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'default'   => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'equalize_product_box',
	'transport'   => silver_customizer_transport(),
	'label'       => esc_attr__( 'Equalize Items', 'silver' ),
	'section'     => 'woocommerce_product_catalog',
	'default'     => '0',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'bubble_style',
	'label'     => __( 'Bubble Style', 'silver' ),
	'section'   => 'woocommerce_product_catalog',
	'transport' => silver_customizer_transport(),
	'default'   => 'style1',
	'choices'   => array(
		'style1' => $image_url . 'badge-circle.svg',
		'style2' => $image_url . 'badge-square.svg',
		'style3' => $image_url . 'badge-border.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'sale_bubble',
	'transport'   => silver_customizer_transport(),
	'label'       => __( 'Show sale bubble', 'silver' ),
	'section'     => 'woocommerce_product_catalog',
	'default'     => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'              => 'text',
	'settings'          => 'sale_bubble_text',
	'transport'         => silver_customizer_transport(),
	'label'             => __( 'Custom sale bubble text', 'silver' ),
	'section'           => 'woocommerce_product_catalog',
	'sanitize_callback' => 'wp_kses_post',
	'active_callback' => array(
		array(
			'setting'  => 'sale_bubble',
			'operator' => '!=',
			'value'    => false,
		),
	),
	'default'           => '',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'sale_bubble_percentage',
	'label'    => __( 'Enable % instead of "Sale!"', 'silver' ),
	'section'  => 'woocommerce_product_catalog',
	'active_callback' => array(
		array(
			'setting'  => 'sale_bubble',
			'operator' => '!=',
			'value'    => false,
		),
	),
	'default'  => '0',
) );

Silver_Option::add_field( 'option', array(
	'type'              => 'text',
	'settings'          => 'sale_bubble_percentage_formatting',
	'transport'         => silver_customizer_transport(),
	'label'             => __( 'Sale bubble % formatting', 'silver' ),
	'description'       => __( 'How the discount should be displayed. e.g. -{value}%', 'silver' ),
	'section'           => 'woocommerce_product_catalog',
	'sanitize_callback' => 'wp_kses_post',
	'active_callback'   => array(
		array(
			'setting'  => 'sale_bubble_percentage',
			'operator' => '==',
			'value'    => true,
		),
		array(
			'setting'  => 'sale_bubble',
			'operator' => '!=',
			'value'    => false,
		),
	),
	'default'           => '-{value}%',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'number',
	'settings'    => 'new_bubble_auto',
	'label'       => __( 'Auto "New" bubble', 'silver' ),
	'description' => __( 'Shown on products witch age is x and lower in days.', 'silver' ),
	'section'     => 'woocommerce_product_catalog',
	'transport'   => silver_customizer_transport(),
	'default'     => 0,
	'choices'     => array(
		'min'  => 0,
		'step' => 1,
	),
) );

if ( get_theme_mod( 'swatches' ) ) :
	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_category_swatches',
		'label'    => '',
		'section'  => 'woocommerce_product_catalog',
		'default'  => '<div class="options-title-divider">Swatches</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'     => 'select',
		'settings' => 'swatches_box_attribute',
		'label'    => __( 'Product attribute', 'silver' ),
		'tooltip'  => __( 'The product (variation) atrribute that swatches will be populated for (usually an attribute that determines colors).', 'silver' ),
		'section'  => 'woocommerce_product_catalog',
		'default'  => '',
		'choices'  => silver_get_product_attribute_taxonomies_list_by_id( array( 'option_none' => '-- None --' ) ),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'swatches_box_layout',
		'label'           => __( 'Layout', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
		'choices'         => array(
			''      => 'Normal',
			'limit' => 'Limited',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'swatches_box_limit',
		'label'           => __( 'Limit', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
			array(
				'setting'  => 'swatches_box_layout',
				'operator' => '===',
				'value'    => 'limit',
			),
		),
		'default'         => 5,
		'choices'         => array(
			'min'  => 1,
			'max'  => 30,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'swatches_box_size',
		'label'           => __( 'Size', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'transport'       => silver_customizer_transport(),
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
		'choices'         => array(
			'x-small' => 'X Small',
			'small'   => 'Small',
			''        => 'Medium',
			'large'   => 'Large',
			'x-large' => 'X Large',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'swatches_box_shape',
		'label'           => __( 'Shape', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'transport'       => silver_customizer_transport(),
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
		'choices'         => array(
			''        => 'Square',
			'rounded' => 'Rounded',
			'circle'  => 'Circle',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'swatches_box_select_event',
		'label'           => __( 'Select event - Desktop', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
		'choices'         => array(
			''      => 'On hover',
			'click' => 'On click',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'color',
		'settings'        => 'swatches_box_color_selected',
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Color :selected', 'silver' ),
		'description'     => __( 'Default is Secondary color', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => 'swatches_box_behavior_selected',
		'label'           => __( 'Behavior :selected', 'silver' ),
		'description'     => __( 'When clicking an already selected swatch.', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => '',
		'choices'         => array(
			''         => 'Go to product page',
			'unselect' => 'Unselect swatch',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'checkbox',
		'settings'    => 'swatches_box_out_of_stock_inactive',
		'label'       => esc_html__( 'Inactive out of stock', 'silver' ),
		'description' => esc_html__( 'Show out of stock items as inactive.', 'silver' ),
		'section'     => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'     => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'swatches_box_update_urls',
		'label'           => __( 'Update links to selected variation', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'swatches_box_reset',
		'label'           => __( 'Auto reset', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'swatches_box_reset_limited',
		'label'           => __( 'Reset limited', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
			array(
				'setting'  => 'swatches_box_layout',
				'operator' => '==',
				'value'    => 'limit',
			),
			array(
				'setting'  => 'swatches_box_reset',
				'operator' => '!=',
				'value'    => 0,
			),
		),
		'default'         => 0,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'swatches_box_reset_extent',
		'label'           => __( 'Reset extent', 'silver' ),
		'description'     => __( 'The area the pointer should leave for a reset.', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
			array(
				'setting'  => 'swatches_box_reset',
				'operator' => '!=',
				'value'    => 0,
			),
		),
		'default'         => '',
		'choices'         => array(
			''            => __( 'Swatch', 'silver' ),
			'product-box' => __( 'Product box', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'swatches_box_reset_time',
		'label'           => __( 'Reset time', 'silver' ),
		'description'     => __( 'In milliseconds (1000ms = 1sec).', 'silver' ),
		'section'         => 'woocommerce_product_catalog',
		'active_callback' => array(
			array(
				'setting'  => 'swatches_box_attribute',
				'operator' => '!=',
				'value'    => '',
			),
			array(
				'setting'  => 'swatches_box_reset',
				'operator' => '!=',
				'value'    => 0,
			),
		),
		'default'         => 300,
		'choices'         => array(
			'min'  => 100,
			'max'  => 10000,
			'step' => 100,
		),
	) );
endif;
