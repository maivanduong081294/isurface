<?php
/**
 * Registers the Stack element in UX Builder.
 *
 * @package silver
 */

add_ux_builder_shortcode( 'ux_stack', array(
	'type'      => 'container',
	'name'      => __( 'Stack', 'silver' ),
	'category'  => __( 'Layout', 'silver' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'ux_stack' ),
	'template'  => silver_ux_builder_template( 'ux_stack.html' ),
	'directives' => array( 'ux-slider' ),
	'wrap'      => false,
	'nested'    => true,
	'options'   => array(
		'type'             => array(
			'type'    => 'select',
			'heading' => __( 'Layout', 'silver' ),
			'default' => 'stack',
			'options' => array(
				'stack'  => __( 'Stack', 'silver' ),
				'slider' => __( 'Slider', 'silver' ),
			),
		),
		'direction'        => array(
			'type'       => 'select',
			'heading'    => __( 'Direction', 'silver' ),
			'responsive' => true,
			'default'    => 'row',
			'options'    => array(
				'row' => __( 'Horizontal', 'silver' ),
				'col' => __( 'Vertical', 'silver' ),
			),
		),
		'distribute'       => array(
			'type'       => 'select',
			'heading'    => __( 'Distribute', 'silver' ),
			'responsive' => true,
			'default'    => 'start',
			'options'    => array(
				'start'   => __( 'Start', 'silver' ),
				'center'  => __( 'Center', 'silver' ),
				'end'     => __( 'End', 'silver' ),
				'between' => __( 'Space between', 'silver' ),
				'around'  => __( 'Space around', 'silver' ),
			),
		),
		'align'            => array(
			'type'       => 'select',
			'heading'    => __( 'Align', 'silver' ),
			'responsive' => true,
			'default'    => 'stretch',
			'options'    => array(
				'stretch'  => __( 'Stretch', 'silver' ),
				'start'    => __( 'Start', 'silver' ),
				'center'   => __( 'Center', 'silver' ),
				'end'      => __( 'End', 'silver' ),
				'baseline' => __( 'Baseline', 'silver' ),
			),
		),
		'gap'              => array(
			'type'       => 'slider',
			'heading'    => __( 'Gap', 'silver' ),
			'responsive' => true,
			'default'    => '0',
			'unit'       => 'rem',
			'max'        => '16',
			'min'        => '0',
			'step'       => '0.25',
		),
		'slider_style' => array(
			'type'       => 'select',
			'heading'    => __( 'Slider Style', 'silver' ),
			'default'    => 'normal',
			'conditions' => 'type === "slider"',
			'options'    => array(
				'normal'    => __( 'Default', 'silver' ),
				'container' => __( 'Container', 'silver' ),
				'focus'     => __( 'Focus', 'silver' ),
				'shadow'    => __( 'Shadow', 'silver' ),
			),
		),
		'slider_options' => require( __DIR__ . '/commons/repeater-slider.php' ),
		'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
	),
) );
