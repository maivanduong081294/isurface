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
	'thumbnail' => silver_ux_builder_thumbnail('title'),
	// No client template: the preview is rendered by the shortcode itself, so
	// the dynamic title, the tag and the link always match the front end.
	'info'      => '{{ text }}',
	'wrap'      => false,
	'options' => array(
		'style'            => array(
			'type'    => 'select',
			'heading' => 'Style',
			// Not saved to the shortcode: must match the default in title_shortcode().
			'default' => 'simple',
			'options' => array(
				'simple'      => 'Simple',
				'normal'      => 'Normal',
				'center'      => 'Center',
				'bold'        => 'Left Bold',
				'bold-center' => 'Center Bold',
			),
		),
		// Only offered when the Link options point to a post, a term or an archive.
		'text_source'      => array(
			'type'       => 'select',
			'heading'    => 'Title source',
			'default'    => 'custom',
			'conditions' => 'link_type === "post" || link_type === "term" || link_type === "archive"',
			'options'    => array(
				'custom' => 'Custom text',
				'link'   => 'Name of the linked item',
			),
		),
		'text'             => array(
			'type'       => 'textfield',
			'heading'    => 'Title',
			'default'    => 'Lorem ipsum dolor sit amet...',
			'auto_focus' => true,
			'conditions' => 'link_type === "custom" || text_source === "custom"',
		),
		'tag_name'         => array(
			'type'    => 'select',
			'heading' => esc_html__('Tag', 'silver'),
			'default' => 'h2',
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
			'helpers' => require(__DIR__ . '/helpers/colors-text.php'),
			'heading'  => __('Color'),
			'responsive' => true,
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
			'responsive' => true,
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
