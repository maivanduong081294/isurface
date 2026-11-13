<?php

$sizes = array(
	'xxsmall' => 'XX-Small',
	'xsmall'  => 'X-Small',
	'smaller' => 'Smaller',
	'small'   => 'Small',
	''        => 'Normal',
	'large'   => 'Large',
	'larger'  => 'Larger',
	'xlarge'  => 'X-Large',
	'xxlarge' => 'XX-Large',
);

add_ux_builder_shortcode( 'ux_product_gallery', array(
	'name'      => __( 'Product Gallery' ),
	'category'  => __( 'Product Page' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_gallery' ),
	'overlay'   => true,
	'wrap'      => true,
	'priority'  => 9999,
	'options'   => array(
		'style'                 => array(
			'type'    => 'select',
			'heading' => 'Style',
			'default' => 'normal',
			'options' => array(
				'normal'     => __( 'Normal', 'silver' ),
				'vertical'   => __( 'Vertical', 'silver' ),
				'full-width' => __( 'Full Width', 'silver' ),
				'stacked'    => __( 'Stacked', 'silver' ),
			),
		),
		'grid_layout' => array(
			'type'       => 'select',
			'heading'    => 'Grid layout',
			'full_width' => true,
			'conditions' => 'style == "stacked"',
			'default'    => '',
			'options' => array(
				''      => __( '1 column', 'silver' ),
				'2'     => __( '2 columns', 'silver' ),
				'1-2'   => __( 'Featured - 2 columns', 'silver' ),
				'3-1-2' => __( '3 columns - featured - 2 columns', 'silver' ),
			),
		),
		'slider_type' => array(
			'type'       => 'radio-buttons',
			'heading'    => 'Slider type',
			'full_width' => true,
			'conditions' => 'style == "normal" || style == "vertical" || style == "stacked"',
			'default'    => '',
			'options'    => array(
				''     => array( 'title' => esc_html__( 'Slide', 'silver' ) ),
				'fade' => array( 'title' => esc_html__( 'Fade', 'silver' ) ),
			),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_title', array(
	'name'      => __( 'Product Title' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_title' ),
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'size'      => array(
			'type'    => 'select',
			'heading' => 'Size',
			'default' => '',
			'options' => $sizes,
		),
		'divider'   => array(
			'type'    => 'checkbox',
			'heading' => 'Divider',
			'default' => 'true',
		),
		'uppercase' => array(
			'type'    => 'checkbox',
			'heading' => 'Uppercase',
			'default' => 'false',
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_rating', array(
	'name'      => __( 'Product Rating' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_rating' ),
	'wrap'      => true,
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'count'      => array(
			'type'    => 'checkbox',
			'heading' => 'Review Count',
			'default' => 'false',
		),
		'style'      => array(
			'type'    => 'select',
			'heading' => 'Review Count Style',
			'default' => 'inline',
			'options' => array(
				'tooltip' => __( 'Tooltip', 'silver' ),
				'stacked' => __( 'Stacked', 'silver' ),
				'inline'  => __( 'Inline', 'silver' ),
			),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_price', array(
	'name'      => __( 'Product Price' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_price' ),
	'wrap'      => false,
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'size' => array(
			'type'    => 'select',
			'heading' => 'Size',
			'default' => '',
			'options' => $sizes,
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_excerpt', array(
	'name'      => __( 'Product Short Description' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_short_desc' ),
	'wrap'      => false,
	'category'  => __( 'Product Page' ),
) );

add_ux_builder_shortcode( 'ux_product_add_to_cart', array(
	'name'      => __( 'Product Add To Cart' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_add_to_cart' ),
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'style' => array(
			'type'    => 'select',
			'heading' => 'Form Style',
			'default' => 'normal',
			'options' => array(
				'normal' => __( 'Normal', 'silver' ),
				'flat'   => __( 'Flat', 'silver' ),
				'minimal'   => __( 'Minimal', 'silver' ),
			),
		),
		'size'  => array(
			'type'    => 'select',
			'heading' => 'Size',
			'default' => '',
			'options' => $sizes,
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_meta', array(
	'name'      => __( 'Product Meta' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_meta' ),
	'category'  => __( 'Product Page' ),
) );

add_ux_builder_shortcode( 'ux_product_upsell', array(
	'name'      => __( 'Product Up-sells' ),
	'category'  => __( 'Product Page' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_upsells' ),
	'options'   => array(
		'style' => array(
			'type'    => 'select',
			'heading' => 'Style',
			'default' => 'sidebar',
			'options' => array(
				'sidebar' => __( 'List', 'silver' ),
				'grid'    => __( 'Grid', 'silver' ),
			),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_tabs', array(
	'name'      => __( 'Product Tabs' ),
	'category'  => __( 'Product Page' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_tabs' ),
	'options'   => array(
		'style' => array(
			'type'    => 'select',
			'heading' => 'Style',
			'default' => 'tabs',
			'options' => array(
				'tabs'          => __( 'Line Tabs', 'silver' ),
				'tabs_normal'         => __( 'Tabs Normal', 'silver' ),
				'line-grow'           => __( 'Line Tabs - Grow', 'silver' ),
				'tabs_vertical'       => __( 'Tabs vertical', 'silver' ),
				'tabs_pills'          => __( 'Pills', 'silver' ),
				'tabs_outline'        => __( 'Outline', 'silver' ),
				'sections'            => __( 'Sections', 'silver' ),
				'accordian'           => __( 'Accordion', 'silver' ),
				'accordian-collapsed' => __( 'Accordion - Collapsed', 'silver' ),
			),
		),
		'align' => array(
			'responsive' => true,
			'type'    => 'select',
			'heading' => 'Align',
			'default' => 'left',
			'options' => array(
				'left'   => __( 'Left', 'silver' ),
				'center' => __( 'Center', 'silver' ),
				'right'  => __( 'Right', 'silver' ),
			),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_related', array(
	'name'      => __( 'Product Related' ),
	'category'  => __( 'Product Page' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_related' ),
	'options'   => array(
		'style' => array(
			'type'    => 'select',
			'heading' => 'Style',
			'default' => 'slider',
			'options' => array(
				'slider' => __( 'Slider', 'silver' ),
				'grid'   => __( 'Grid', 'silver' ),
			),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_hook', array(
	'name'      => __( 'Product Hooks' ),
	'category'  => __( 'Product Page' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_hooks' ),
	'options'   => array(
		'hook' => array(
			'type'    => 'select',
			'heading' => 'Hook',
			'default' => 'woocommerce_single_product_summary',
			'options' => apply_filters( 'silver_custom_product_single_product_hooks', array(
				'woocommerce_before_single_product_summary' => 'woocommerce_before_single_product_summary',
				'woocommerce_single_product_summary'        => 'woocommerce_single_product_summary',
				'woocommerce_after_single_product_summary'  => 'woocommerce_after_single_product_summary',
				'silver_custom_single_product_1'          => 'silver_custom_single_product_1',
				'silver_custom_single_product_2'          => 'silver_custom_single_product_2',
				'silver_custom_single_product_3'          => 'silver_custom_single_product_3',
			) ),
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_breadcrumbs', array(
	'name'      => __( 'Product Breadcrumbs' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_breadcrumbs' ),
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'size' => array(
			'type'    => 'select',
			'heading' => 'Size',
			'default' => '',
			'options' => $sizes,
		),
	),
) );

add_ux_builder_shortcode( 'ux_product_next_prev_nav', array(
	'name'      => __( 'Product Next/Prev' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'woo_next_prev' ),
	'category'  => __( 'Product Page' ),
	'options'   => array(
		'class' => array(
			'type'    => 'textfield',
			'heading' => 'Class',
			'default' => '',
		),
	),
) );
