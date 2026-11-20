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
	// Grid replaces Two Col / Three Col: a saved "2-col" / "3-col" still works
	// (a grid of 2 or 3 columns on desktop until the per row options are set).
	'choices'     => array(
		'normal' => $image_url . 'blog-normal.svg',
		'inline' => $image_url . 'blog-inline.svg',
		'list'   => $image_url . 'blog-list.svg',
		'grid'   => $image_url . 'blog-grid.svg',
	),
));

// Posts per row of the Grid layout (blog and Blog Archive), like Products per
// row: Desktop >= 1200px, Tablet landscape 850-1199px, Tablet portrait
// 550-849px, Mobile < 550px. Normal, Inline and List are one post per row.
Silver_Option::add_field( 'option', array(
	'type'        => 'slider',
	'settings'    => 'blog_columns',
	'label'       => __( 'Posts per row - Desktop', 'silver' ),
	'description' => __( 'Grid layout (blog and archives).', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 3,
	'choices'     => array(
		'min'  => 1,
		'max'  => 6,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'slider',
	'settings'    => 'blog_columns_tablet_landscape',
	'label'       => __( 'Posts per row - Tablet landscape', 'silver' ),
	'description' => __( '0 = same as Tablet portrait.', 'silver' ),
	'section'     => 'blog-layout',
	'default'     => 0,
	'choices'     => array(
		'min'  => 0,
		'max'  => 6,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'slider',
	'settings' => 'blog_columns_tablet',
	'label'    => __( 'Posts per row - Tablet portrait', 'silver' ),
	'section'  => 'blog-layout',
	'default'  => 2,
	'choices'  => array(
		'min'  => 1,
		'max'  => 4,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'slider',
	'settings' => 'blog_columns_mobile',
	'label'    => __( 'Posts per row - Mobile', 'silver' ),
	'section'  => 'blog-layout',
	'default'  => 1,
	'choices'  => array(
		'min'  => 1,
		'max'  => 3,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type' => 'select',
	'settings' => 'blog_post_item_style',
	'label' => __( 'Post Item Style', 'silver' ),
	'description' => __( 'Shared by all post layouts on the blog, category, archive and search pages. A selected style replaces the individual post presentation; the layout still controls the number of columns.', 'silver' ),
	'section' => 'blog-layout',
	'default' => '',
	'transport' => 'refresh',
	'choices' => array(
		'' => __( 'Use layout default', 'silver' ),
		'none' => __( 'None', 'silver' ),
		'default' => __( 'Default', 'silver' ),
		'normal' => __( 'Normal', 'silver' ),
		'overlay' => __( 'Overlay', 'silver' ),
		'shade' => __( 'Shade', 'silver' ),
		'vertical' => __( 'Vertical', 'silver' ),
		'label' => __( 'Label', 'silver' ),
		'push' => __( 'Push', 'silver' ),
		'badge' => __( 'Badge', 'silver' ),
		'bounce' => __( 'Bounce', 'silver' ),
	),
) );

$blog_item_options = require get_template_directory() . '/inc/helpers/blog-post-item-options.php';
foreach ( $blog_item_options as $attribute => $field ) {
	$field['settings'] = 'blog_item_' . $attribute;
	$field['section'] = 'blog-layout';
	$field['default'] = '';
	$field['transport'] = 'refresh';
	$field['active_callback'] = array( array( 'setting' => 'blog_post_item_style', 'operator' => '!=', 'value' => '' ) );
	if ( 'badge_style' === $attribute ) $field['active_callback'][] = array( 'setting' => 'blog_item_show_date', 'operator' => '==', 'value' => 'badge' );
	if ( 'excerpt_length' === $attribute ) $field['active_callback'][] = array( 'setting' => 'blog_item_excerpt', 'operator' => '!=', 'value' => 'false' );
	if ( strpos( $attribute, 'readmore_' ) === 0 ) $field['active_callback'][] = array( 'setting' => 'blog_item_readmore', 'operator' => '!=', 'value' => '' );
	Silver_Option::add_field( 'option', $field );
}
unset( $blog_item_options, $attribute, $field );

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
	'description' => __( 'Used by the Grid layout, including archive layouts.', 'silver' ),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'blog_show_excerpt',
	'active_callback' => array( array( 'setting' => 'blog_post_item_style', 'operator' => '==', 'value' => '' ) ),
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


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_posts_header_style',
	'label'       => __( 'Posts Title Style', 'silver' ),
	'section'     => 'blog-layout',
	'active_callback'    => array(
		array( 'setting' => 'blog_post_item_style', 'operator' => '==', 'value' => '' ),
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
