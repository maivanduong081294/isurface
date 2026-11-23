<?php
/**
 * Element "Read More": long content cut to a height, expand / collapse.
 * Render: inc/shortcodes/ux_readmore.php.
 */

add_ux_builder_shortcode( 'ux_readmore', array(
	'type'      => 'container',
	'name'      => __( 'Read More', 'silver' ),
	'category'  => __( 'Content' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'text' ),
	'template'  => silver_ux_builder_template( 'ux_readmore.html' ),
	'info'      => '{{ height }}px',
	'wrap'      => false,
	'presets'   => array(
		array(
			'name'    => __( 'Default' ),
			'content' => '[ux_readmore height="200"][ux_text]<h2>Heading</h2><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p><p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p><p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat.</p>[/ux_text][/ux_readmore]',
		),
	),
	'options'   => array(
		'height'    => array(
			'type'        => 'slider',
			'heading'     => __( 'Collapsed height', 'silver' ),
			'description' => __( 'Content taller than this (plus 80px) is cut, with a button to read it all.', 'silver' ),
			'unit'        => 'px',
			'default'     => 400,
			'min'         => 80,
			'max'         => 1200,
			'step'        => 20,
		),
		'more_text' => array(
			'type'    => 'textfield',
			'heading' => __( 'Read more text', 'silver' ),
			'default' => __( 'Read more', 'silver' ),
		),
		'less_text' => array(
			'type'    => 'textfield',
			'heading' => __( 'Read less text', 'silver' ),
			'default' => __( 'Read less', 'silver' ),
		),
		'align'     => array(
			'type'    => 'radio-buttons',
			'heading' => __( 'Button align', 'silver' ),
			'default' => 'center',
			'options' => require __DIR__ . '/values/align-radios.php',
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
