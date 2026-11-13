<?php
/**
 * Footer panel.
 */

// Add section immediately to keep sort order.
Silver_Option::add_section( 'footer', array(
	'title' => __( 'Footer', 'silver' ),
) );

function silver_customizer_footer_options() {
	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_custom_footer',
		'label'    => '',
		'section'  => 'footer',
		'default'  => '<div class="options-title-divider"  style="margin-bottom:15px">Custom Footer</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'select',
		'settings'    => 'footer_block',
		'label'       => __( 'Custom Footer Block', 'silver' ),
		'section'     => 'footer',
		'description' => __( 'You can replace the Footer with a Custom Block that you can edit in the Page Builder.', 'silver' ),
		'default'     => false,
		'choices'     => silver_customizer_blocks(),
	) );

	$hide_on_custom_footer_block = array(
		array( 'setting' => 'footer_block', 'operator' => '==', 'value' => false ),
	);

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_html_footer_widgets',
		'label'           => '',
		'section'         => 'footer',
		'active_callback' => $hide_on_custom_footer_block,
		'default'         => '<div class="options-title-divider" style="margin-bottom:15px">Widgets</div><p>Click the button to go to Footer Widgets</p><div><button style="margin-bottom:15px" class="button button-primary" data-to-panel="widgets">Edit Footer Widgets</button></div>',
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_footer_1',
		'label'           => '',
		'active_callback' => $hide_on_custom_footer_block,
		'section'         => 'footer',
		'default'         => '<div class="options-title-divider">Footer 1</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'footer_1',
		'active_callback' => $hide_on_custom_footer_block,
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Enable Footer 1', 'silver' ),
		'section'         => 'footer',
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'footer_1_columns',
		'label'           => __( 'Columns', 'silver' ),
		'section'         => 'footer',
		'active_callback' => $hide_on_custom_footer_block,
		'default'         => '4',
		'transport'       => silver_customizer_transport(),
		'choices'         => array(
			'6' => __( '6', 'silver' ),
			'4' => __( '4', 'silver' ),
			'3' => __( '3', 'silver' ),
			'2' => __( '2', 'silver' ),
			'1' => __( '1', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'footer_1_color',
		'label'           => __( 'Text color', 'silver' ),
		'active_callback' => $hide_on_custom_footer_block,
		'section'         => 'footer',
		'default'         => 'light',
		'transport'       => silver_customizer_transport(),
		'choices'         => array(
			'dark'  => silver_customizer_images_uri() . '/text-light.svg',
			'light' => silver_customizer_images_uri() . '/text-dark.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'color-alpha',
		'alpha'           => true,
		'settings'        => 'footer_1_bg_color',
		'label'           => __( 'Background Color', 'silver' ),
		'section'         => 'footer',
		'default'         => '#fff',
		'transport'       => silver_customizer_transport(),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'image',
		'settings'        => 'footer_1_bg_image',
		'label'           => __( 'Background Image', 'silver' ),
		'section'         => 'footer',
		'default'         => '',
		'transport'       => silver_customizer_transport(),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_footer_2',
		'label'           => '',
		'section'         => 'footer',
		'default'         => '<div class="options-title-divider">Footer 2</div>',
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'footer_2',
		'transport'       => silver_customizer_transport(),
		'active_callback' => $hide_on_custom_footer_block,
		'label'           => __( 'Enable Footer 2', 'silver' ),
		'section'         => 'footer',
		'default'         => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'footer_2_columns',
		'label'           => __( 'Columns', 'silver' ),
		'section'         => 'footer',
		'default'         => '4',
		'transport'       => silver_customizer_transport(),
		'choices'         => array(
			'6' => __( '6', 'silver' ),
			'4' => __( '4', 'silver' ),
			'3' => __( '3', 'silver' ),
			'2' => __( '2', 'silver' ),
			'1' => __( '1', 'silver' ),
		),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'footer_2_color',
		'label'           => __( 'Text color', 'silver' ),
		'section'         => 'footer',
		'default'         => 'dark',
		'transport'       => silver_customizer_transport(),
		'choices'         => array(
			'dark'  => silver_customizer_images_uri() . '/text-light.svg',
			'light' => silver_customizer_images_uri() . '/text-dark.svg',
		),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'color-alpha',
		'alpha'           => true,
		'settings'        => 'footer_2_bg_color',
		'label'           => __( 'Background Color', 'silver' ),
		'section'         => 'footer',
		'default'         => '#777',
		'transport'       => silver_customizer_transport(),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'image',
		'settings'        => 'footer_2_bg_image',
		'label'           => __( 'Background Image', 'silver' ),
		'section'         => 'footer',
		'default'         => '',
		'transport'       => silver_customizer_transport(),
		'active_callback' => $hide_on_custom_footer_block,
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_footer_absolute',
		'label'    => '',
		'section'  => 'footer',
		'default'  => '<div class="options-title-divider">Absolute Footer</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'radio-image',
		'settings'  => 'footer_bottom_text',
		'label'     => __( 'Text color', 'silver' ),
		'section'   => 'footer',
		'default'   => 'dark',
		'transport' => silver_customizer_transport(),
		'choices'   => array(
			'dark'  => silver_customizer_images_uri() . '/text-light.svg',
			'light' => silver_customizer_images_uri() . '/text-dark.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'radio-buttonset',
		'settings'  => 'footer_bottom_align',
		'label'     => __( 'Align', 'silver' ),
		'section'   => 'footer',
		'default'   => '',
		'transport' => silver_customizer_transport(),
		'choices'   => array(
			''       => __( 'Left/Right', 'silver' ),
			'center' => __( 'Center', 'silver' ),
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'footer_bottom_color',
		'label'     => __( 'Background Color', 'silver' ),
		'section'   => 'footer',
		'default'   => '',
		'transport' => silver_customizer_transport(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'footer_left_text',
		'label'             => __( 'Bottom Text - Primary', 'silver' ),
		'description'       => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'           => 'footer',
		'transport'         => silver_customizer_transport(),
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => 'Copyright [ux_current_year] &copy; <strong>Silver Theme</strong>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'footer_right_text',
		'label'             => __( 'Bottom Text - Secondary', 'silver' ),
		'description'       => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'           => 'footer',
		'transport'         => silver_customizer_transport(),
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );

	$hide_off_back_to_top = array(
		array( 'setting' => 'back_to_top', 'operator' => '==', 'value' => true ),
	);

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'back_to_top_button_title',
		'label'           => '',
		'section'         => 'footer',
		'default'         => '<div class="options-title-divider">Back To Top Button</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'checkbox',
		'settings'  => 'back_to_top',
		'label'     => __( 'Enable Back To Top Button', 'silver' ),
		'section'   => 'footer',
		'default'   => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-image',
		'settings'        => 'back_to_top_shape',
		'label'           => __( 'Button Shape', 'silver' ),
		'section'         => 'footer',
		'transport'       => silver_customizer_transport(),
		'default'         => 'circle',
		'choices'         => array(
			'circle'  => silver_customizer_images_uri() . '/back-to-top-outline-circle.svg',
			'square'  => silver_customizer_images_uri() . '/back-to-top-outline-square.svg',
		),
		'active_callback' => $hide_off_back_to_top,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'radio-buttonset',
		'settings'        => 'back_to_top_position',
		'label'           => __( 'Position', 'silver' ),
		'section'         => 'footer',
		'transport'       => silver_customizer_transport(),
		'default'         => 'right',
		'choices'         => array(
			'left'  => __( 'Left', 'silver' ),
			'right' => __( 'Right', 'silver' ),
		),
		'active_callback' => $hide_off_back_to_top,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'checkbox',
		'settings'        => 'back_to_top_mobile',
		'label'           => __( 'Show on Mobile', 'silver' ),
		'section'         => 'footer',
		'transport'       => silver_customizer_transport(),
		'default'         => 0,
		'active_callback' => $hide_off_back_to_top,
	) );

	Silver_Option::add_field( '', array(
		'type'            => 'custom',
		'settings'        => 'custom_title_footer_html',
		'label'           => '',
		'section'         => 'footer',
		'active_callback' => $hide_on_custom_footer_block,
		'default'         => '<div class="options-title-divider">Footer HTML</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'html_before_footer',
		'label'             => __( 'HTML before footer', 'silver' ),
		'description'       => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'           => 'footer',
		'transport'         => silver_customizer_transport(),
		'active_callback'   => $hide_on_custom_footer_block,
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'              => 'textarea',
		'settings'          => 'html_after_footer',
		'label'             => __( 'HTML after footer', 'silver' ),
		'description'       => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'           => 'footer',
		'transport'         => silver_customizer_transport(),
		'active_callback'   => $hide_on_custom_footer_block,
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'           => '',
	) );
}
add_action( 'init', 'silver_customizer_footer_options' );

function silver_refresh_footer_partials( WP_Customize_Manager $wp_customize ) {

	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'footer-layout', array(
		'selector'        => '.footer-wrapper',
		'settings'        => array(
			'footer_2',
			'footer_1',
			'html_before_footer',
			'html_after_footer',
			'footer_2_color',
			'footer_1_color',
			'footer_2_columns',
			'footer_1_columns',
		),
		'render_callback' => function () {
			get_template_part( 'template-parts/footer/footer' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'footer-left-text', array(
		'selector'        => '.copyright-footer',
		'settings'        => array( 'footer_left_text' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'footer_left_text', 'Copyright [ux_current_year] &copy; <strong>Silver Theme</strong>' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'footer-right-text', array(
		'selector'        => '.footer-secondary .footer-text',
		'settings'        => array( 'footer_right_text' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'footer_right_text', '' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'footer-absolute', array(
		'selector'            => '.absolute-footer',
		'settings'            => array( 'footer_bottom_align', 'footer_bottom_text' ),
		'container_inclusive' => true,
		'render_callback'     => function () {
			get_template_part( 'template-parts/footer/footer-absolute' );
		},
	) );

	// Refresh custom styling / Colors etc.
	$wp_customize->selective_refresh->add_partial( 'refresh_css_footer', array(
		'selector'            => 'head > style#custom-css',
		'container_inclusive' => true,
		'settings'            => array( 'footer_1_bg_image', 'footer_2_bg_image' ),
		'render_callback'     => function () {
			silver_custom_css();
		},
	) );

}

add_action( 'customize_register', 'silver_refresh_footer_partials' );
