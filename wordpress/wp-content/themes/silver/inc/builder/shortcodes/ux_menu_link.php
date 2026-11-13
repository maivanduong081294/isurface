<?php
/**
 * Registers the Menu link element in UX Builder.
 *
 * @package silver
 */

$silver_ux_menu_link_options = require __DIR__ . '/commons/links.php';

// Legacy `post` and `term` selectors: kept for existing content and only
// offered next to the custom link, right below the link type.
$silver_ux_menu_link_options['options'] = silver_array_insert(
	$silver_ux_menu_link_options['options'],
	array(
		'post' => array(
			'type'       => 'select',
			'full_width' => true,
			'conditions' => 'link_type === "custom" && !term && !link',
			'config'     => array(
				'placeholder' => __( 'Select post..', 'silver' ),
				'postSelect'  => array(),
			),
		),
		'term' => array(
			'type'       => 'select',
			'full_width' => true,
			'conditions' => 'link_type === "custom" && !post && !link',
			'config'     => array(
				'placeholder' => __( 'Select category..', 'silver' ),
				'termSelect'  => array(
					'taxonomies' => array(
						'post_tag',
						'category',
						'product_cat',
						'product_tag',
					),
				),
			),
		),
	),
	1
);

$silver_ux_menu_link_options['options']['link']['conditions'] = 'link_type === "custom" && !post && !term';

add_ux_builder_shortcode( 'ux_menu_link', array(
	'name'      => __( 'Menu link', 'silver' ),
	'category'  => __( 'Content', 'silver' ),
	'info'      => '{{ label }}',
	'require'   => array( 'ux_menu' ),
	'template'  => silver_ux_builder_template( 'ux_menu_link.html' ),
	'wrap'      => false,
	'presets'   => array(
		array(
			'name'    => __( 'Default', 'silver' ),
			'content' => '[ux_menu_link text="Menu link"]',
		),
	),
	'options'   => array(
		'text'            => array(
			'type'       => 'textfield',
			'heading'    => __( 'Text', 'silver' ),
			'default'    => '',
			'auto_focus' => true,
		),
		'icon'             => array(
			'type'    => 'icon',
			'heading' => __( 'Icon', 'silver' ),
		),
		'label'             => array(
			'type'    => 'select',
			'heading' => __( 'Label', 'silver' ),
			'options' => require __DIR__ . '/values/menu-labels.php',
		),
		'link_options'     => $silver_ux_menu_link_options,
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
