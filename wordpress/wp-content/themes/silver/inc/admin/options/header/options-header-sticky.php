<?php
/**
 * Header Sticky
 *
 * @package Silver/Admin/Options/Header
 */

Silver_Option::add_section( 'header_sticky', array(
	'title' => __( 'Sticky Header', 'silver' ),
	'panel' => 'header',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'header_height_sticky',
	'label'     => __( 'Header Height on Sticky', 'silver' ),
	'section'   => 'header_sticky',
	'transport' => silver_customizer_transport(),
	'default'   => 70,
	'choices'   => array(
		'min'  => 30,
		'max'  => 300,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'sticky_logo_padding',
	'label'     => __( 'Sticky Logo Padding', 'silver' ),
	'section'   => 'header_sticky',
	'default'   => 0,
	'choices'   => array(
		'min'  => 0,
		'max'  => 30,
		'step' => 1,
	),
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'nav_height_sticky',
	'label'     => __( 'Nav Height on Sticky', 'silver' ),
	'section'   => 'header_sticky',
	'default'   => '',
	'choices'   => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'topbar_sticky',
	'label'    => __( 'Top Bar - Sticky on Scroll', 'silver' ),
	'section'  => 'header_sticky',
	'default'  => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'header_sticky',
	'label'    => __( 'Header Main - Sticky on Scroll', 'silver' ),
	'section'  => 'header_sticky',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'bottombar_sticky',
	'label'    => __( 'Header Bottom - Sticky on Scroll', 'silver' ),
	'section'  => 'header_sticky',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'sticky_style',
	'label'    => __( 'Sticky Style', 'silver' ),
	'section'  => 'header_sticky',
	'default'  => 'jump',
	'choices'  => array(
		'jump'   => __( 'Jump Down', 'silver' ),
		'fade'   => __( 'Fade', 'silver' ),
		'shrink' => __( 'Shrink', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'sticky_hide_on_scroll',
	'label'    => __( 'Hide sticky when scrolling down', 'silver' ),
	'section'  => 'header_sticky',
	'default'  => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'image',
	'settings'  => 'site_logo_sticky',
	'label'     => __( 'Custom Sticky Logo', 'silver' ),
	'section'   => 'header_sticky',
	'transport' => silver_customizer_transport(),
	'default'   => '',
	'choices'   => array( 'save_as' => 'id' ),
) );
