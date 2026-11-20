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

// Blog page, categories, tags, author, date and search results.
// See silver_blog_breadcrumbs() in inc/structure/structure-posts.php.
Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'    => 'blog_archive_breadcrumbs',
	'label'       => __( 'Breadcrumbs', 'silver' ),
	'description' => __( 'Shown above the archive title on the blog page, categories, tags, author, date and search pages.', 'silver' ),
	'section'     => 'blog-archive',
	'default'     => 0,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'blog_style_archive',
	'label'       => __( 'Posts Layout', 'silver' ),
	'section'     => 'blog-archive',
	'default'     => '',
	// Grid uses the Posts per row options of Blog Layout. A saved "2-col" /
	// "3-col" still works (see template-parts/posts/archive-grid.php).
	'choices'     => array(
		''       => $image_url . 'blog-default.svg',
		'normal' => $image_url . 'blog-normal.svg',
		'inline' => $image_url . 'blog-inline.svg',
		'list'   => $image_url . 'blog-list.svg',
		'grid'   => $image_url . 'blog-grid.svg',
	),
));