<?php

/*************
 * Search
 *************/

Silver_Option::add_section( 'header_search', array(
	'title'       => __( 'Search', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );



Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'search_icon_style',
	'label'       => __( 'Search Icon Style', 'silver' ),
	'section'     => 'header_search',
	'default'     => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'search-icon-plain.svg',
		'outline' => $image_url . 'search-icon-outline.svg',
		'fill' => $image_url . 'search-icon-fill.svg',
		'fill-round' => $image_url . 'search-icon-fill-round.svg',
		'outline-round' => $image_url . 'search-icon-outline-round.svg',
	),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'header_search_style',
	'transport' => silver_customizer_transport(),
	'label'       => __( 'Search Icon Type', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header_search',
	'default'     => 'dropdown',
	'choices'     => array(
		'dropdown' => __( 'Dropdown', 'silver' ),
		'lightbox' => __( 'Lightbox', 'silver' ),
	),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'header_search_form_style',
	'label'       => __( 'Search Form Style', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header_search',
	'default'     => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => __( 'Normal', 'silver' ),
		'flat' => __('Flat', 'silver' ),
	),
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'text',
  'settings'     => 'search_placeholder',
  'transport' => silver_customizer_transport(),
  'label'       => __( 'Placeholder text', 'silver' ),
  'section'     => 'header_search',
  'placeholder' => 'Search...',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'transport' => silver_customizer_transport(),
	'settings'     => 'header_search_categories',
	'label'       => __( 'Search Categories', 'silver' ),
	'help'        => __( 'Search categories', 'silver' ),
	'section'     => 'header_search',
	'default'     => 0,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_search_width',
	'label'       => __( 'Header Search form width', 'silver' ),
	'section'     => 'header_search',
	'default'     => '60',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'min'  => 10,
		'max'  => 100,
		'step' => 1
	)
));

function silver_refresh_header_search_partials( WP_Customize_Manager $wp_customize ) {

	if ( ! isset( $wp_customize->selective_refresh ) ) {
	      return;
	}

	$wp_customize->selective_refresh->add_partial( 'header_search', array(
	    'selector' => '.header-search',
	    'container_inclusive' => true,
	    'settings' => array('search_icon_style','header_search_style'),
	    'render_callback' => function() {
	        get_template_part('template-parts/header/partials/element','search');
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'header_search_form', array(
	    'selector' => '.search-form',
	    'container_inclusive' => true,
	    'settings' => array('header_search_categories'),
	    'render_callback' => function() {
	        get_template_part('template-parts/header/partials/element','search-form');
	    },
	) );
}
add_action( 'customize_register', 'silver_refresh_header_search_partials' );
