<?php
/**
 * Adds Pages Panel and options to the Customizer for Silver.
 *
 * @package Silver
 */

Silver_Option::add_section( 'pages', array(
	'title'       => __( 'Pages', 'silver' ),
	'description' => __( 'Change the default page layout for all pages. You can also override some of these options per page in the page editor.', 'silver' ),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'pages_template',
	'label'    => __( 'Default - Page Template', 'silver' ),
	'section'  => 'pages',
	'default'  => 'default',
	'choices'  => array(
		'default'                           => __( 'Container (Default)', 'silver' ),
		'blank-title-center'                => __( 'Container - Center Title', 'silver' ),
		'blank'                             => __( 'Full-Width', 'silver' ),
		'header-on-scroll'                  => __( 'Full-Width - Header On Scroll', 'silver' ),
		'blank-featured'                    => __( 'Full-Width - Parallax Title', 'silver' ),
		'transparent-header'                => __( 'Full-Width - Transparent Header', 'silver' ),
		'transparent-header-light'          => __( 'Full-Width - Transparent Header Light', 'silver' ),
		'left-sidebar'                      => __( 'Sidebar Left', 'silver' ),
		'blank-landingpage'                 => __( 'No Header / No Footer', 'silver' ),
		'right-sidebar'                     => __( 'Sidebar Right', 'silver' ),
		'single-page-nav'                   => __( 'Single Page Navigation', 'silver' ),
		'single-page-nav-transparent'       => __( 'Single Page Navigation - Transparent Header', 'silver' ),
		'single-page-nav-transparent-light' => __( 'Single Page Navigation - Transparent Header - Light', 'silver' ),
		'blank-sub-nav-vertical'            => __( 'Vertical Sub Navigation', 'silver' ),
	),
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'default_title',
	'label'    => __( 'Show H1 Page title on the container (default), left sidebar and right sidebar templates.', 'silver' ),
	'section'  => 'pages',
	'default'  => 0,
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'page_top_excerpt',
	'label'    => __( 'Add excerpt content to top of pages.', 'silver' ),
	'section'  => 'pages',
	'default'  => 1,
));
