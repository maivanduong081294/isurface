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

// Until saved, it follows the archive setting (Blog Layout > Enable Sidebar
// Divider), so existing sites keep their look. Used by
// template-parts/posts/layout-{left,right}-sidebar.php.
Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'blog_single_layout_divider',
	'label'           => __( 'Enable Sidebar Divider', 'silver' ),
	'section'         => 'blog-single',
	'transport'       => silver_customizer_transport(),
	'default'         => get_theme_mod( 'blog_layout_divider', 1 ),
	'active_callback' => array(
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '!==',
			'value'    => 'no-sidebar',
		),
	),
) );

// Until saved, it follows Blog Layout > Sidebar Width. Printed by
// silver_custom_css() in inc/functions/function-custom-css.php.
Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'blog_single_sidebar_width',
	'label'           => __( 'Sidebar Width (%)', 'silver' ),
	'description'     => __( 'Desktop (1200px and up). Default 25%.', 'silver' ),
	'section'         => 'blog-single',
	'default'         => (int) get_theme_mod( 'blog_sidebar_width', 25 ),
	'choices'         => array(
		'min'  => 15,
		'max'  => 50,
		'step' => 1,
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '!==',
			'value'    => 'no-sidebar',
		),
	),
) );

// Until saved, they follow Blog Layout > Sticky sidebar / Sticky mode. Used by
// template-parts/posts/layout-{left,right}-sidebar.php.
Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'blog_single_sticky_sidebar',
	'label'           => __( 'Sticky sidebar', 'silver' ),
	'section'         => 'blog-single',
	'default'         => get_theme_mod( 'blog_sticky_sidebar', 0 ),
	'active_callback' => array(
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '!==',
			'value'    => 'no-sidebar',
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'blog_single_sticky_sidebar_mode',
	'label'           => __( 'Sticky mode', 'silver' ),
	'section'         => 'blog-single',
	'default'         => get_theme_mod( 'blog_sticky_sidebar_mode', '' ),
	'choices'         => array(
		''           => __( 'CSS (native)', 'silver' ),
		'javascript' => __( 'JavaScript (enhanced)', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '!==',
			'value'    => 'no-sidebar',
		),
		array(
			'setting'  => 'blog_single_sticky_sidebar',
			'operator' => '==',
			'value'    => true,
		),
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

// See silver_blog_breadcrumbs() in inc/structure/structure-posts.php.
Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'blog_single_breadcrumbs',
	'label'       => __( 'Breadcrumbs', 'silver' ),
	'description' => __( 'Shown above the post title.', 'silver' ),
	'section'     => 'blog-single',
	'default'     => 0,
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

// Category, title and meta. Until saved, it follows Blog Layout > Posts Title
// Align. See silver_blog_title_align() in inc/structure/structure-posts.php.
Silver_Option::add_field( 'option', array(
	'type'     => 'radio-image',
	'settings' => 'blog_single_title_align',
	'label'    => __( 'Title align', 'silver' ),
	'section'  => 'blog-single',
	'default'  => get_theme_mod( 'blog_posts_title_align', 'center' ),
	'choices'  => array(
		'left'   => $image_url . 'align-left.svg',
		'center' => $image_url . 'align-center.svg',
		'right'  => $image_url . 'align-right.svg',
	),
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

// See silver_single_post_toc() in inc/structure/structure-posts.php.
Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'blog_single_toc',
	'label'       => __( 'Table of contents', 'silver' ),
	'description' => __( 'From the h2 / h3 headings (3 or more): after the first paragraph, or in the place of the sidebar with the No sidebar layout.', 'silver' ),
	'section'     => 'blog-single',
	'default'     => 1,
) );

// No sidebar layout: the side of the column of the table of contents
// (template-parts/posts/layout.php).
Silver_Option::add_field( 'option', array(
	'type'            => 'radio-buttonset',
	'settings'        => 'blog_single_toc_position',
	'label'           => __( 'Table of contents position', 'silver' ),
	'section'         => 'blog-single',
	'default'         => 'right',
	'choices'         => array(
		'left'  => __( 'Left', 'silver' ),
		'right' => __( 'Right', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_single_toc',
			'operator' => '==',
			'value'    => true,
		),
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '==',
			'value'    => 'no-sidebar',
		),
	),
) );

// No sidebar layout: the width of the column of the table of contents, from
// 850px. Until saved, it follows Sidebar Width (%). Printed by
// silver_custom_css() in inc/functions/function-custom-css.php.
Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'blog_single_toc_width',
	'label'           => __( 'Table of contents width (%)', 'silver' ),
	'description'     => __( 'From 850px. Default 25%.', 'silver' ),
	'section'         => 'blog-single',
	'default'         => (int) get_theme_mod( 'blog_single_sidebar_width', get_theme_mod( 'blog_sidebar_width', 25 ) ),
	'choices'         => array(
		'min'  => 15,
		'max'  => 40,
		'step' => 1,
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_single_toc',
			'operator' => '==',
			'value'    => true,
		),
		array(
			'setting'  => 'blog_post_layout',
			'operator' => '==',
			'value'    => 'no-sidebar',
		),
	),
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
	'type'        => 'checkbox',
	'settings'    => 'blog_single_comments',
	'label'       => __( 'Show comments', 'silver' ),
	'section'     => 'blog-single',
	'description' => __( 'Show the comment list and form on blog posts. WordPress discussion settings still apply. Product reviews are not affected.', 'silver' ),
	'default'     => 1,
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
