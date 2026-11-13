<?php

Silver_Option::add_section( 'blog-single', array(
	'title' => __( 'Blog Single Post', 'silver' ),
	'panel' => 'blog',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-image',
	'settings' => 'blog_post_layout',
	'label'    => __( 'Blog Post Single Layout', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 'right-sidebar',
	'choices'  => array(
		'right-sidebar' => $image_url . 'layout-right.svg',
		'left-sidebar'  => $image_url . 'layout-left.svg',
		'no-sidebar'    => $image_url . 'layout-no-sidebar.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-image',
	'settings' => 'blog_post_style',
	'label'    => __( 'Title Layout', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 'default',
	'choices'  => array(
		'default' => $image_url . 'blog-single.svg',
		'top'     => $image_url . 'blog-single-full.svg',
		'inline'  => $image_url . 'blog-single-inline.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_transparent',
	'label'    => __( 'Transparent Header', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 0,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_blog_single_header',
	'label'    => '',
	'section'  => 'blog-single',
	'default'  => '<div class="options-title-divider">Header</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_header_category',
	'label'    => __( 'Category', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_header_title',
	'label'    => __( 'Title', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_header_meta',
	'label'    => __( 'Meta', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_blog_single_post',
	'label'    => '',
	'section'  => 'blog-single',
	'default'  => '<div class="options-title-divider">Post</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_featured_image',
	'label'    => __( 'Featured image', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_blog_single_footer',
	'label'    => '',
	'section'  => 'blog-single',
	'default'  => '<div class="options-title-divider">Footer</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_share',
	'label'    => __( 'Share icons', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_footer_meta',
	'label'    => __( 'Meta', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_author_box',
	'label'    => __( 'Blog author box', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_single_next_prev_nav',
	'label'    => __( 'Next/Prev navigation', 'silver' ),
	'section'  => 'blog-single',
	'default'  => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'              => 'textarea',
	'settings'          => 'blog_after_post',
	'label'             => __( 'HTML after blog posts', 'silver' ),
	'section'           => 'blog-single',
	'description'       => 'Enter HTML or shortcodes that will be visible after blog posts. (Before comment box). Shortcodes are allowed',
	'sanitize_callback' => 'silver_custom_sanitize',
	'default'           => '',
) );
