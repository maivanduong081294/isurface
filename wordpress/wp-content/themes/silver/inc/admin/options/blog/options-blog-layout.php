<?php

Silver_Option::add_section( 'blog-layout', array(
	'title'       => __( 'Blog Layout', 'silver' ),
	'panel' => 'blog',
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'blog_header',
	'label'       => __( 'Blog Homepage Header', 'silver' ),
	'description' => __( 'Enter HTML for blog header here. Will be placed above content and sidebar. Shortcodes are allowed. F.ex [block id="blog-header"]', 'silver' ),
	'section'     => 'blog-layout',
	'sanitize_callback' => 'silver_custom_sanitize',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_archive_transparent',
	'label'       => __( 'Transparent Header', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 0,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_layout',
	'label'       => __( 'Blog Sidebar', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 'right-sidebar',
	'transport'	  => silver_customizer_transport(),
	'choices'     => array(
		'right-sidebar' => $image_url . 'layout-right.svg',
		'left-sidebar' => $image_url . 'layout-left.svg',
		'no-sidebar' => $image_url . 'layout-no-sidebar.svg',
	),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_layout_divider',
	'label'       => __( 'Enable Sidebar Divider', 'silver' ),
	'section'     => 'blog-layout',
	'transport'	  => silver_customizer_transport(),
	'default'     => 1,
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'blog_sticky_sidebar',
	'label'    => __( 'Sticky sidebar', 'silver' ),
	'section'  => 'blog-layout',
	'default'  => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'blog_sticky_sidebar_mode',
	'label'           => __( 'Sticky mode', 'silver' ),
	'section'         => 'blog-layout',
	'default'         => '',
	'choices'         => array(
		''           => __( 'CSS (native)', 'silver' ),
		'javascript' => __( 'JavaScript (enhanced)', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_sticky_sidebar',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_style',
	'label'       => __( 'Posts Layout', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 'normal',
	'choices'     => array(
		'normal' => $image_url . 'blog-normal.svg',
		'inline' => $image_url . 'blog-inline.svg',
		'2-col' => $image_url . 'blog-two-col.svg',
		'3-col' =>$image_url . 'blog-three-col.svg',
		'list' => $image_url . 'blog-list.svg',
	),
));

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-buttonset',
	'settings'        => 'blog_style_type',
	'label'           => __( 'Posts Layout Type', 'silver' ),
	'section'         => 'blog-layout',
	'default'         => 'masonry',
	'choices'         => array(
		'row'     => __( 'Row', 'silver' ),
		'masonry' => __( 'Masonry', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'blog_style',
			'operator' => 'contains',
			'value'    => array( '2-col', '3-col' ),
		),
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_show_excerpt',
	'transport'	  => silver_customizer_transport(),
	'label'       => __( 'Show Excerpts Only', 'silver' ),
	'help'        => __( 'Show Excerpts only of the Blog posts. You can manually add a Read More link by adding a More Tag to the Post Content.', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 1,
));

Silver_Option::add_field( 'option',  array(
'type'        => 'color-alpha',
'settings'     => 'blog_bg_color',
'label'       => __( 'Blog Background Color', 'silver' ),
'section'     => 'blog-layout',
'default'     => '',
'transport' => silver_customizer_transport(),
'js_vars'   => array(
	array(
		'element'  => '.blog-wrapper',
		'function' => 'css',
		'property' => 'background-color'
	),
)
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'blog_posts_depth',
	'label'       => __( 'Depth', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 0,
	'choices'     => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1
	),
	'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'blog_posts_depth_hover',
	'label'       => __( 'Depth :hover', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 0,
	'choices'     => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1
	),
	'transport' => silver_customizer_transport()
));


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_post_layout',
    'label'       => __( '', 'silver' ),
	'section'     => 'blog-layout',
    'default'     => '<div class="options-title-divider">Post layout</div>',
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_posts_header_style',
	'label'       => __( 'Posts Title Style', 'silver' ),
	'section'     => 'blog-layout',
	'active_callback'    => array(
		array(
			'setting'  => 'blog_style',
			'operator' => '===',
			'value'    => 'normal',
		),
	),
	'default'     => 'normal',
	'choices'     => array(
		'normal' => $image_url . 'text-top.svg',
		'bottom' => $image_url . 'text-bottom.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_posts_title_align',
	'label'       => __( 'Posts Title Align', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 'center',
	'choices'     => array(
		'left' =>	$image_url . 'align-left.svg',
		'center' => $image_url . 'align-center.svg',
		'right' => 	$image_url . 'align-right.svg',
	),
));
