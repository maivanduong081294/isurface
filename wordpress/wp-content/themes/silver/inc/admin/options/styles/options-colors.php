<?php
/**
 * Style & Colors
 */

Silver_Option::add_section( 'colors', array(
	'title' => __( 'Colors', 'silver' ),
	'panel' => 'style',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_colors_main',
	'label'    => __( '', 'silver' ),
	'section'  => 'colors',
	'default'  => '<div class="options-title-divider">Main Colors</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'color_primary',
	'label'       => __( 'Primary Color', 'silver' ),
	'description' => __( 'Change primary color.', 'silver' ),
	'section'     => 'colors',
	'default'     => Silver_Default::COLOR_PRIMARY,
	'transport'   => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'color_secondary',
	'transport'   => silver_customizer_transport(),
	'label'       => __( 'Secondary Color', 'silver' ),
	'description' => __( 'Change secondary color.', 'silver' ),
	'default'     => Silver_Default::COLOR_SECONDARY,
	'section'     => 'colors',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'color_success',
	'transport'   => silver_customizer_transport(),
	'label'       => __( 'Success Color', 'silver' ),
	'description' => __( 'Change the success color. Used for global success messages.', 'silver' ),
	'section'     => 'colors',
	'default'     => Silver_Default::COLOR_SUCCESS,
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'color_alert',
	'transport'   => silver_customizer_transport(),
	'label'       => __( 'Alert Color', 'silver' ),
	'description' => __( 'Change the alert color. Used for global error messages etc.', 'silver' ),
	'section'     => 'colors',
	'default'     => Silver_Default::COLOR_ALERT,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_color_type',
	'label'    => __( '', 'silver' ),
	'section'  => 'colors',
	'default'  => '<div class="options-title-divider">Type</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'color_texts',
	'label'       => __( 'Base Color', 'silver' ),
	'description' => __( 'Used for all normal texts.', 'silver' ),
	'section'     => 'colors',
	'default'     => Silver_Default::COLOR_BASE,
	'transport'   => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'type_headings_color',
	'label'       => __( 'Headline Color', 'silver' ),
	'description' => __( 'Used for all headlines on white backgrounds. (H1, H2, H3 etc.)', 'silver' ),
	'section'     => 'colors',
	'default'     => '#555',
	'transport'   => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color-alpha',
	'settings'    => 'color_divider',
	'label'       => __( 'Divider Color', 'silver' ),
	'description' => __( 'Used for dividers.', 'silver' ),
	'section'     => 'colors',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_type_links',
	'label'    => __( '', 'silver' ),
	'section'  => 'colors',
	'default'  => '<div class="options-title-divider">Links</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'color_links',
	'label'     => __( 'Link Colors', 'silver' ),
	'section'   => 'colors',
	'default'   => Silver_Default::LINK_COLOR,
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'color_links_hover',
	'label'     => __( 'Link Colors :hover', 'silver' ),
	'section'   => 'colors',
	'default'   => Silver_Default::LINK_COLOR_HOVER,
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'color_widget_links',
	'label'     => __( 'Widget Link Colors', 'silver' ),
	'section'   => 'colors',
	'default'   => '',
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'color_widget_links_hover',
	'label'     => __( 'Widget Link Colors :hover', 'silver' ),
	'section'   => 'colors',
	'default'   => '',
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_colors_tooltip',
	'label'    => '',
	'section'  => 'colors',
	'default'  => '<div class="options-title-divider">' . esc_html__( 'Tooltip colors', 'silver' ) . '</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'tooltip_color',
	'label'     => esc_html__( 'Tooltip color', 'silver' ),
	'section'   => 'colors',
	'default'   => Silver_Default::TOOLTIP_COLOR,
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color',
	'settings'  => 'tooltip_bg_color',
	'label'     => esc_html__( 'Tooltip background color', 'silver' ),
	'section'   => 'colors',
	'default'   => Silver_Default::TOOLTIP_BG_COLOR,
	'transport' => silver_customizer_transport(),
) );

if ( is_woocommerce_activated() ) {
	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_colors_shop',
		'label'    => __( '', 'silver' ),
		'section'  => 'colors',
		'default'  => '<div class="options-title-divider">Shop Colors</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_checkout',
		'label'       => __( 'Add to cart / Checkout buttons', 'silver' ),
		'description' => __( 'Change color for checkout buttons. Default is Secondary color', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_sale',
		'label'       => __( 'Sale bubble', 'silver' ),
		'description' => __( 'Change color of sale bubble. Default is Secondary color', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_new_bubble_auto',
		'label'       => __( 'New bubble (auto)', 'silver' ),
		'description' => __( 'Change color of the automatic "New" bubble.', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_new_bubble',
		'label'       => __( 'Custom bubble', 'silver' ),
		'description' => __( 'Change color of the custom bubble.', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_review',
		'label'       => __( 'Review Stars', 'silver' ),
		'description' => __( 'Change color of review stars', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_regular_price',
		'label'       => __( 'Regular Price', 'silver' ),
		'description' => __( 'Change color of the regular price of an on sale product.', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'color',
		'settings'    => 'color_sale_price',
		'label'       => __( 'Sale Price', 'silver' ),
		'description' => __( 'Change color of the sale price.', 'silver' ),
		'section'     => 'colors',
		'transport'   => silver_customizer_transport(),
	) );
}
