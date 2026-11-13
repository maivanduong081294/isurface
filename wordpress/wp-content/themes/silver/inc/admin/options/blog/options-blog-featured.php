<?php

Silver_Option::add_section( 'blog-featured', array(
	'title'       => __( 'Blog Featured Posts', 'silver' ),
	'panel' => 'blog',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_featured',
	'label'       => __( 'Featured Posts', 'silver' ),
	'description' => __( 'Show Featured posts in a slider above content. You need to make a post "Sticky" to show it here.', 'silver' ),
	'section'     => 'blog-featured',
	'default'     => '',
	'choices'     => array(
		'' => $image_url . 'disabled.svg',
		'content' => $image_url . 'featured-posts.svg',
		'top' =>$image_url . 'featured-posts-top.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_hide_sticky',
	'label'       => __( 'Hide Featured Posts from Default Blog feed.', 'silver' ),
	'section'     => 'blog-featured',
	'default'     => 0,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'blog_featured_height',
	'label'       => __( 'Featured Posts Height', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'blog-featured',
	'default'     => 500,
	'choices'     => array(
	'min'  => 200,
	'max'  => 1000,
	'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'blog_featured_image_size',
	'label'    => esc_attr__( 'Image Size', 'silver' ),
	'section'  => 'blog-featured',
	'default'  => 'medium',
	'choices'  => array(
		'thumbnail' => 'Thumbnail',
		'medium'    => 'Medium',
		'large'     => 'Large',
		'original'  => 'Original',
	),
) );
