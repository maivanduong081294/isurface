<?php

$title_link_options            = require __DIR__ . '/commons/links.php';
$title_link_options['options'] = array_merge(
	array(
		'link_text' => array(
			'type'    => 'textfield',
			'heading' => 'Text',
			'default' => '',
		),
	),
	$title_link_options['options']
);

add_ux_builder_shortcode('title', array(
	'name'      => __('Title', 'ux-builder'),
	'category'  => __('Content'),
	'thumbnail' => flatsome_ux_builder_thumbnail('title'),
	'template'  => flatsome_ux_builder_template('title.html'),
	'info'      => '{{ text }}',
	'wrap'      => false,
	'options' => array(
		'style'            => array(
			'type'    => 'select',
			'heading' => 'Style',
			'default' => 'normal',
			'options' => array(
				'normal'      => 'Normal',
				'center'      => 'Center',
				'bold'        => 'Left Bold',
				'bold-center' => 'Center Bold',
			),
		),
		'text'             => array(
			'type'       => 'textfield',
			'heading'    => 'Title',
			'default'    => 'Lorem ipsum dolor sit amet...',
			'auto_focus' => true,
		),
		'tag_name'         => array(
			'type'    => 'select',
			'heading' => esc_html__('Tag', 'silver'),
			'default' => 'h3',
			'options' => array(
				'h1'  => esc_html__('Heading 1', 'silver'),
				'h2'  => esc_html__('Heading 2', 'silver'),
				'h3'  => esc_html__('Heading 3', 'silver'),
				'h4'  => esc_html__('Heading 4', 'silver'),
				'div' => esc_html__('Container', 'silver'),
				'p'   => esc_html__('Paragraph', 'silver'),
			),
		),
		'color'            => array(
			'type'     => 'colorpicker',
			'heading'  => __('Color'),
			'alpha'    => true,
			'format'   => 'rgb',
			'position' => 'bottom right',
		),
		'icon'             => array(
			'type'    => 'icon',
			'heading' => 'Icon',
		),
		'width'            => array(
			'type'    => 'scrubfield',
			'heading' => __('Width'),
			'default' => '',
			'min'     => 0,
			'max'     => 1200,
			'step'    => 5,
		),
		'margin_top'       => array(
			'type'        => 'scrubfield',
			'heading'     => __('Margin Top'),
			'default'     => '',
			'placeholder' => __('0px'),
			'min'         => -100,
			'max'         => 300,
			'step'        => 1,
		),
		'margin_bottom'    => array(
			'type'        => 'scrubfield',
			'heading'     => __('Margin Bottom'),
			'default'     => '',
			'placeholder' => __('0px'),
			'min'         => -100,
			'max'         => 300,
			'step'        => 1,
		),
		'size'             => array(
			'type'    => 'slider',
			'heading' => __('Size'),
			'default' => 100,
			'unit'    => '%',
			'min'     => 20,
			'max'     => 300,
			'step'    => 1,
		),
		'link_options'     => $title_link_options,
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
