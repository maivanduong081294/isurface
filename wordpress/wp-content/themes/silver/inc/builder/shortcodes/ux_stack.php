<?php
/**
 * Registers the Stack element in UX Builder.
 *
 * @package flatsome
 */

add_ux_builder_shortcode( 'ux_stack', array(
	'type'      => 'container',
	'name'      => __( 'Stack', 'flatsome' ),
	'category'  => __( 'Layout', 'flatsome' ),
	'thumbnail' => flatsome_ux_builder_thumbnail( 'ux_stack' ),
	'template'  => flatsome_ux_builder_template( 'ux_stack.html' ),
	'directives' => array( 'ux-slider' ),
	'wrap'      => false,
	'nested'    => true,
	'options'   => array(
		'type'             => array(
			'type'    => 'select',
			'heading' => __( 'Layout', 'flatsome' ),
			'default' => 'stack',
			'options' => array(
				'stack'  => __( 'Stack', 'flatsome' ),
				'slider' => __( 'Slider', 'flatsome' ),
			),
		),
		'direction'        => array(
			'type'       => 'select',
			'heading'    => __( 'Direction', 'flatsome' ),
			'responsive' => true,
			'default'    => 'row',
			'options'    => array(
				'row' => __( 'Horizontal', 'flatsome' ),
				'col' => __( 'Vertical', 'flatsome' ),
			),
		),
		'distribute'       => array(
			'type'       => 'select',
			'heading'    => __( 'Distribute', 'flatsome' ),
			'responsive' => true,
			'default'    => 'start',
			'options'    => array(
				'start'   => __( 'Start', 'flatsome' ),
				'center'  => __( 'Center', 'flatsome' ),
				'end'     => __( 'End', 'flatsome' ),
				'between' => __( 'Space between', 'flatsome' ),
				'around'  => __( 'Space around', 'flatsome' ),
			),
		),
		'align'            => array(
			'type'       => 'select',
			'heading'    => __( 'Align', 'flatsome' ),
			'responsive' => true,
			'default'    => 'stretch',
			'options'    => array(
				'stretch'  => __( 'Stretch', 'flatsome' ),
				'start'    => __( 'Start', 'flatsome' ),
				'center'   => __( 'Center', 'flatsome' ),
				'end'      => __( 'End', 'flatsome' ),
				'baseline' => __( 'Baseline', 'flatsome' ),
			),
		),
		'gap'              => array(
			'type'       => 'slider',
			'heading'    => __( 'Gap', 'flatsome' ),
			'responsive' => true,
			'default'    => '0',
			'unit'       => 'rem',
			'max'        => '16',
			'min'        => '0',
			'step'       => '0.25',
		),
		'slider_style' => array(
			'type'       => 'select',
			'heading'    => __( 'Slider Style', 'flatsome' ),
			'default'    => 'normal',
			'conditions' => 'type === "slider"',
			'options'    => array(
				'normal'    => __( 'Default', 'flatsome' ),
				'container' => __( 'Container', 'flatsome' ),
				'focus'     => __( 'Focus', 'flatsome' ),
				'shadow'    => __( 'Shadow', 'flatsome' ),
			),
		),
		'slider_options' => require( __DIR__ . '/commons/repeater-slider.php' ),
		'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
	),
) );
