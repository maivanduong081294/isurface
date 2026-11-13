<?php

/*************
 * HTML content
 *************/

// Add section immediately to keep sort order.
Silver_Option::add_section( 'header_content', array(
	'title'       => __( 'HTML', 'silver' ),
	'panel'       => 'header',
) );

function silver_customizer_header_content_options() {
	Silver_Option::add_field( 'option',  array(
		'type'        => 'select',
		'settings'    => 'header-block-1',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'Header Block 1', 'silver' ),
		'description' => __( 'Blocks can be edited in the page builder. Select a block, go to a page and open in the the Page Builder.', 'silver' ),
		'section'     => 'header_content',
		'choices'     => silver_customizer_blocks(),
	));

	Silver_Option::add_field( 'option',  array(
		'type'        => 'select',
		'settings'    => 'header-block-2',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'Header Block 2', 'silver' ),
		'description' => __( 'Blocks can be edited in the page builder. Select a block, go to a page and open in the the Page Builder.', 'silver' ),
		'section'     => 'header_content',
		'choices'     => silver_customizer_blocks(),
	));

	Silver_Option::add_field( 'option', array(
		'type'        => 'select',
		'settings'    => 'header-block-3',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'Header Block 3', 'silver' ),
		'description' => __( 'Blocks can be edited in the page builder. Select a block, go to a page and open in the the Page Builder.', 'silver' ),
		'section'     => 'header_content',
		'choices'     => silver_customizer_blocks(),
	) );

	Silver_Option::add_field( 'option', array(
		'type'        => 'select',
		'settings'    => 'header-block-4',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'Header Block 4', 'silver' ),
		'description' => __( 'Blocks can be edited in the page builder. Select a block, go to a page and open in the the Page Builder.', 'silver' ),
		'section'     => 'header_content',
		'choices'     => silver_customizer_blocks(),
	) );

	Silver_Option::add_field( 'option',  array(
		'type'        => 'textarea',
		'settings'    => 'topbar_left',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'HTML 1', 'silver' ),
		'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
		'section'     => 'header_content',
		'sanitize_callback' => 'silver_custom_sanitize',
		'default'     => '<strong class="uppercase">Add anything here or just remove it...</strong>',
	));



	Silver_Option::add_field( 'option',  array(
		'type'        => 'textarea',
		'settings'    => 'topbar_right',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'HTML 2', 'silver' ),
		'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
		'section'     => 'header_content',
		'sanitize_callback' => 'silver_custom_sanitize',
	));

	Silver_Option::add_field( 'option',  array(
		'type'        => 'textarea',
		'settings'    => 'top_right_text',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'HTML 3', 'silver' ),
		'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'     => 'header_content',
		'sanitize_callback' => 'silver_custom_sanitize',
	));

	Silver_Option::add_field( 'option',  array(
		'type'        => 'textarea',
		'settings'    => 'nav_position_text_top',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'HTML 4', 'silver' ),
		'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'     => 'header_content',
		'sanitize_callback' => 'silver_custom_sanitize',
	));

	Silver_Option::add_field( 'option',  array(
		'type'        => 'textarea',
		'settings'    => 'nav_position_text',
		'transport'   => silver_customizer_transport(),
		'label'       => __( 'HTML 5', 'silver' ),
		'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
		'section'     => 'header_content',
		'sanitize_callback' => 'silver_custom_sanitize',
	));
}
add_action( 'init', 'silver_customizer_header_content_options' );

function silver_refresh_header_content_partials( WP_Customize_Manager $wp_customize ) {

	if ( ! isset( $wp_customize->selective_refresh ) ) {
	      return;
	  }

	$wp_customize->selective_refresh->add_partial( 'top_right_text', array(
		'selector'        => '.html_top_right_text',
		'settings'        => array( 'top_right_text' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'top_right_text' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'nav_position_text_top', array(
		'selector'        => '.html_nav_position_text_top',
		'settings'        => array( 'nav_position_text_top' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'nav_position_text_top' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'topbar_left', array(
		'selector'        => '.html_topbar_left',
		'settings'        => array( 'topbar_left' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'topbar_left' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'topbar_right', array(
		'selector'        => '.html_topbar_right',
		'settings'        => array( 'topbar_right' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'topbar_right' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'nav_position_text', array(
		'selector'        => '.html_nav_position_text',
		'settings'        => array( 'nav_position_text' ),
		'render_callback' => function () {
			return do_shortcode( get_theme_mod( 'nav_position_text' ) );
		},
	) );


	// Header block
	$wp_customize->selective_refresh->add_partial( 'header-block-1', array(
	    'selector' => '.header-block-1',
	    'settings' => array('header-block-1'),
	    'render_callback' => function() {
			return do_shortcode( '[block id="' . get_theme_mod( 'header-block-1' ) . '"]' );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'header-block-2', array(
	    'selector' => '.header-block-2',
	    'settings' => array('header-block-2'),
	    'render_callback' => function() {
			return do_shortcode( '[block id="' . get_theme_mod( 'header-block-2' ) . '"]' );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'header-block-3', array(
		'selector'        => '.header-block-3',
		'settings'        => array( 'header-block-3' ),
		'render_callback' => function () {
			return do_shortcode( '[block id="' . get_theme_mod( 'header-block-3' ) . '"]' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'header-block-4', array(
		'selector'        => '.header-block-4',
		'settings'        => array( 'header-block-4' ),
		'render_callback' => function () {
			return do_shortcode( '[block id="' . get_theme_mod( 'header-block-4' ) . '"]' );
		},
	) );
}
add_action( 'customize_register', 'silver_refresh_header_content_partials' );
