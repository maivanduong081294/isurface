<?php 

Silver_Option::add_section( 'blog-archive', array(
	'title'       => __( 'Blog Archive', 'silver' ),
	'panel' => 'blog',
	'description' => __( 'Set custom layouts for archive pages like Categories, Search etc.', 'silver' ),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_archive_title',
	'label'       => __( 'Enable Blog Archive Title', 'silver' ),
	'section'     => 'blog-archive',
	'default'     => 1,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_style_archive',
	'label'       => __( 'Posts Layout', 'silver' ),
	'section'     => 'blog-archive',
	'default'     => '',
	'choices'     => array(
		'' => $image_url . 'blog-default.svg',
		'normal' => $image_url . 'blog-normal.svg',
		'inline' => $image_url . 'blog-inline.svg',
		'2-col' => $image_url . 'blog-two-col.svg',
		'3-col' =>$image_url . 'blog-three-col.svg',
		'list' => $image_url . 'blog-list.svg',
	),
));