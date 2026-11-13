<?php

add_ux_builder_shortcode( 'ux_html', array(
	'type'      => 'container',
	'name'      => __( 'HTML', 'silver' ),
	'category'  => __( 'Content', 'silver' ),
	'compile'   => false,
	'overlay'   => true,
	'thumbnail' => silver_ux_builder_thumbnail( 'ux_html' ),
	'info'      => '{{ label }}',
	'template'  => silver_ux_builder_template( 'ux_html.html' ),
	'priority'  => 3,

	'options'   => array(
		'label'            => array(
			'full_width'  => true,
			'type'        => 'textfield',
			'heading'     => 'Label',
			'placeholder' => 'Enter admin label here...',
		),
		'$content'         => array(
			'type'       => 'text-editor',
			'full_width' => true,
			'height'     => 'calc(100vh - 470px)',
			'tinymce'    => false,
		),
		'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
	),
) );
