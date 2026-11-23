<?php
/**
 * Element "Table of Contents": wraps content and lists its headings.
 * Render: inc/shortcodes/ux_toc.php.
 */

add_ux_builder_shortcode( 'ux_toc', array(
	'type'      => 'container',
	'name'      => __( 'Table of Contents', 'silver' ),
	'category'  => __( 'Content' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'text' ),
	'template'  => silver_ux_builder_template( 'ux_toc.html' ),
	'info'      => '{{ title }}',
	'wrap'      => false,
	'presets'   => array(
		array(
			'name'    => __( 'Default' ),
			'content' => '[ux_toc][ux_text]<h2>Heading 1</h2><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p><h2>Heading 2</h2><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p><h3>Sub heading</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>[/ux_text][/ux_toc]',
		),
	),
	'options'   => array(
		'title'    => array(
			'type'    => 'textfield',
			'heading' => __( 'Title', 'silver' ),
			'default' => __( 'Table of contents', 'silver' ),
		),
		'levels'   => array(
			'type'    => 'select',
			'heading' => __( 'Headings', 'silver' ),
			'default' => 'h2,h3',
			'options' => array(
				'h2,h3' => 'H2 + H3',
				'h2'    => 'H2',
				'h3,h4' => 'H3 + H4',
				'h3'    => 'H3',
			),
		),
		'numbered' => array(
			'type'    => 'checkbox',
			'heading' => __( 'Numbers', 'silver' ),
			'default' => 'true',
		),
		'subs'     => array(
			'type'        => 'radio-buttons',
			'heading'     => __( 'Sub-headings', 'silver' ),
			'description' => __( 'Section being read: the second level shows under the current section only, so a long list stays short.', 'silver' ),
			'default'     => 'all',
			'options'     => array(
				'all'    => array( 'title' => __( 'Always', 'silver' ) ),
				'active' => array( 'title' => __( 'Section being read', 'silver' ) ),
			),
		),
		'position' => array(
			'type'        => 'radio-buttons',
			'heading'     => __( 'Position', 'silver' ),
			'description' => __( 'From 850px.', 'silver' ),
			'default'     => 'right',
			'options'     => array(
				'left'  => array( 'title' => __( 'Left', 'silver' ) ),
				'right' => array( 'title' => __( 'Right', 'silver' ) ),
				'top'   => array( 'title' => __( 'Top', 'silver' ) ),
			),
		),
		'width'    => array(
			'type'        => 'scrubfield',
			'heading'     => __( 'Width', 'silver' ),
			'conditions'  => 'position !== "top"',
			'default'     => '',
			'placeholder' => '320px',
			'min'         => 200,
			'max'         => 600,
		),
		'sticky'   => array(
			'type'        => 'checkbox',
			'heading'     => __( 'Sticky', 'silver' ),
			'description' => __( 'Stays in view while the content scrolls.', 'silver' ),
			'conditions'  => 'position !== "top"',
			'default'     => 'true',
		),
		'mobile'   => array(
			'type'        => 'radio-buttons',
			'heading'     => __( 'Mobile', 'silver' ),
			'description' => __( 'Below 850px. Bar: fixed under the header while the content is read, it opens the list.', 'silver' ),
			'default'     => 'bar',
			'options'     => array(
				'bar'    => array( 'title' => __( 'Bar', 'silver' ) ),
				'box'    => array( 'title' => __( 'Box', 'silver' ) ),
				'hidden' => array( 'title' => __( 'Hidden', 'silver' ) ),
			),
		),
		'collapse' => array(
			'type'        => 'radio-buttons',
			'heading'     => __( 'Collapsible', 'silver' ),
			'description' => __( 'The list in a box that opens and closes, the title as its button.', 'silver' ),
			'default'     => '',
			'options'     => array(
				''       => array( 'title' => __( 'No', 'silver' ) ),
				'closed' => array( 'title' => __( 'Closed', 'silver' ) ),
				'open'   => array( 'title' => __( 'Open', 'silver' ) ),
			),
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
