<?php
/**
 * Theme Options > WooCommerce > Product Reviews
 * (inc/woocommerce/structure-wc-product-reviews.php).
 *
 * Settings of the review form and list only: the WordPress discussion
 * settings (comments of the blog) stay as they are.
 *
 * @package Silver
 */

Silver_Option::add_section( 'product-reviews', array(
	'title' => __( 'Product Reviews', 'silver' ),
	'panel' => 'woocommerce',
) );

function silver_customizer_shop_product_reviews_options() {
	$layout_on = array(
		array(
			'setting'  => 'product_reviews_layout',
			'operator' => '==',
			'value'    => true,
		),
	);
	$images_on = array_merge( $layout_on, array(
		array(
			'setting'  => 'product_reviews_images',
			'operator' => '==',
			'value'    => true,
		),
	) );
	$field_choices = array(
		'hidden'   => __( 'Hidden', 'silver' ),
		'optional' => __( 'Optional', 'silver' ),
		'required' => __( 'Required', 'silver' ),
	);

	Silver_Option::add_field( 'option', array(
		'type'        => 'checkbox',
		'settings'    => 'product_reviews_layout',
		'label'       => __( 'Theme review layout', 'silver' ),
		'description' => __( 'Overview with a bar per star, the form in a popup, customer photos, "More reviews". Off: the WooCommerce layout.', 'silver' ),
		'section'     => 'product-reviews',
		'default'     => 1,
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_product_reviews_list',
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => '<div class="options-title-divider">' . esc_html__( 'List', 'silver' ) . '</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'product_reviews_per_page',
		'label'           => __( 'Reviews shown at a time', 'silver' ),
		'description'     => __( 'Shown first, then on each "More reviews".', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => 5,
		'choices'         => array(
			'min'  => 1,
			'max'  => 30,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_product_reviews_form',
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => '<div class="options-title-divider">' . esc_html__( 'Form', 'silver' ) . '</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_reviews_phone',
		'label'           => __( 'Phone', 'silver' ),
		'description'     => __( 'Vietnamese mobile numbers. A phone of a completed order gives the review the "Purchased" badge.', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => 'required',
		'choices'         => $field_choices,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'product_reviews_email',
		'label'           => __( 'Email', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => 'hidden',
		'choices'         => $field_choices,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'product_reviews_images',
		'label'           => __( 'Customer photos', 'silver' ),
		'description'     => __( 'JPG, PNG, WebP; checked by their content.', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'product_reviews_images_max',
		'label'           => __( 'Photos per review', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $images_on,
		'default'         => 3,
		'choices'         => array(
			'min'  => 1,
			'max'  => 10,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => 'product_reviews_image_size',
		'label'           => __( 'Size of a photo (MB)', 'silver' ),
		'section'         => 'product-reviews',
		'active_callback' => $images_on,
		'default'         => 1,
		'choices'         => array(
			'min'  => 1,
			'max'  => 10,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_product_reviews_telegram',
		'section'         => 'product-reviews',
		'active_callback' => $layout_on,
		'default'         => '<div class="options-title-divider">' . esc_html__( 'Telegram notification', 'silver' ) . '</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'generic',
		'settings'          => 'product_reviews_telegram_token',
		'label'             => __( 'Bot token', 'silver' ),
		'description'       => __( 'A message for each new review. Empty: none.', 'silver' ),
		'section'           => 'product-reviews',
		'active_callback'   => $layout_on,
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'choices'           => array(
			'element'      => 'input',
			'type'         => 'password',
			'autocomplete' => 'off',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'text',
		'settings'          => 'product_reviews_telegram_chat',
		'label'             => __( 'Chat ID', 'silver' ),
		'description'       => __( 'E.g. 123456789, -1001234567890 (group) or @channel.', 'silver' ),
		'section'           => 'product-reviews',
		'active_callback'   => $layout_on,
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_product_reviews_woocommerce',
		'section'  => 'product-reviews',
		'default'  => '<p class="description">' . sprintf(
			/* translators: %s: link to WooCommerce > Settings > Products */
			esc_html__( 'Star rating required, "verified owners" only and the "verified owner" label: %s.', 'silver' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=products' ) ) . '" target="_blank">' . esc_html__( 'WooCommerce > Settings > Products', 'silver' ) . '</a>'
		) . '</p>',
	) );
}
add_action( 'init', 'silver_customizer_shop_product_reviews_options' );
