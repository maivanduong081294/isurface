<?php
/**
 * Registers the Menu link element in UX Builder.
 *
 * @package silver
 */

add_ux_builder_shortcode( 'ux_menu_title', array(
	'name'      => __( 'Menu title', 'silver' ),
	'category'  => __( 'Content', 'silver' ),
	'require'   => array( 'ux_menu' ),
	'template'  => silver_ux_builder_template( 'ux_menu_title.html' ),
	'wrap'      => false,
	'presets'   => array(
		array(
			'name'    => __( 'Default', 'silver' ),
			'content' => '[ux_menu_title text="Menu title"]',
		),
	),
	'options'   => array(
		'text'             => array(
			'type'       => 'textfield',
			'heading'    => __( 'Text', 'silver' ),
			'default'    => '',
			'auto_focus' => true,
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
