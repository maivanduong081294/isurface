<?php

/*************
 * LOGO
 *************/

function silver_logo_name_customizer( $wp_customize ) {
  $wp_customize->get_setting('blogname')->transport        = silver_customizer_transport();
  $wp_customize->get_setting('blogdescription')->transport = silver_customizer_transport();
}
add_action( 'customize_register', 'silver_logo_name_customizer' );

Silver_Option::add_section( 'title_tagline', array(
	'title'       => __( 'Logo & Site Identity', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'site_logo_slogan',
	'label'       => __( 'Display below Logo', 'silver' ),
	'section'     => 'title_tagline',
	'transport' => silver_customizer_transport(),
	'default'     => 0,
));

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'logo_position',
	'label'       => __( 'Logo position', 'silver' ),
	'section'     => 'title_tagline',
	'transport' => silver_customizer_transport(),
	'default'     => 'left',
	'choices'     => array(
		'left' => $image_url . 'logo-left.svg',
		'center' => $image_url . 'logo-right.svg',
		//'vertical' => $image_url . 'logo-vertical.png'
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'image',
	'settings'     => 'site_logo',
	'label'       => __( 'Logo image', 'silver' ),
	'section'     => 'title_tagline',
	'transport' => silver_customizer_transport(),
	'default'     => silver_theme_logo_url(),
	'choices'   => array( 'save_as' => 'id' ),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'image',
	'settings'     => 'site_logo_dark',
	'label'       => __( 'Logo image - Light Version', 'silver' ),
	'description' => __( 'Upload an alternative light logo that will be used on Dark and Transparent Header templates', 'silver' ),
	'section'     => 'title_tagline',
	'transport' => silver_customizer_transport(),
	'choices'   => array( 'save_as' => 'id' ),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'logo_width',
	'label'       => __( 'Logo container width', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'title_tagline',
	'default'     => 200,
	'choices'     => array(
		'min'  => 30,
		'max'  => 700,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'text',
  'settings'     => 'logo_max_width',
  'label'       => __( 'Logo max width (px)', 'silver' ),
  'section'     => 'title_tagline',
  'description' => __( 'Set the logo max width in pixels. Leave it blank to make it auto fit inside the logo container.', 'silver' ),
  'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'logo_padding',
	'label'       => __( 'Logo Padding', 'silver' ),
	'section'     => 'title_tagline',
	'default'     => 0,
	'choices'     => array(
		'min'  => 0,
		'max'  => 30,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option', array(
	'type'        => 'link',
	'settings'    => 'logo_link',
	'label'       => __( 'Logo link', 'silver' ),
	'description' => __( 'Custom logo link (defaults to home page link if empty).', 'silver' ),
	'section'     => 'title_tagline',
	'default'     => '',
) );
