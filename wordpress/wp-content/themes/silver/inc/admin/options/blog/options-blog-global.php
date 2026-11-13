<?php

Silver_Option::add_section( 'blog-global', array(
	'title' => __( 'Blog Global', 'silver' ),
	'panel' => 'blog',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_badge',
	'label'    => __( 'Show Date Box', 'silver' ),
	'section'  => 'blog-global',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-image',
	'settings'        => 'blog_badge_style',
	'label'           => __( 'Date Box Style', 'silver' ),
	'section'         => 'blog-global',
	'default'         => 'outline',
	'active_callback' => array(
		array(
			'setting'  => 'blog_badge',
			'operator' => '==',
			'value'    => '1',
		),
	),
	'choices'         => array(
		'square'        => $image_url . 'badge-square.svg',
		'circle'        => $image_url . 'badge-circle.svg',
		'circle-inside' => $image_url . 'badge-circle-inside.svg',
		'outline'       => $image_url . 'badge-outline.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'text',
	'settings'    => 'blog_excerpt_suffix',
	'label'       => __( 'Blog Excerpt Suffix', 'silver' ),
	'description' => __( 'Choose custom post excerpt suffix. Default [...]', 'silver' ),
	'section'     => 'blog-global',
	'default'     => ' [...]',
) );
