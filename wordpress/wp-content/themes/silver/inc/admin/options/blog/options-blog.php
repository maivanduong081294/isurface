<?php

Silver_Option::add_panel( 'blog', array(
	'title'       => __( 'Blog', 'silver' ),
) );

include_once( dirname( __FILE__ ) . '/options-blog-global.php' );
include_once(dirname( __FILE__ ).'/options-blog-layout.php');
include_once(dirname( __FILE__ ).'/options-blog-archive.php');
include_once(dirname( __FILE__ ).'/options-blog-single.php');
include_once(dirname( __FILE__ ).'/options-blog-featured.php');
include_once( dirname( __FILE__ ) . '/options-blog-promo.php' );


function silver_refresh_blog_partials( WP_Customize_Manager $wp_customize ) {

  // Abort if selective refresh is not available.
  if ( ! isset( $wp_customize->selective_refresh ) ) {
      return;
  }

	$wp_customize->selective_refresh->add_partial( 'blog-layout', array(
	    'selector' => '.blog-wrapper.blog-archive',
	    'settings' => array('blog_posts_depth_hover','blog_posts_depth','blog_layout','blog_layout_divider','blog_show_excerpt'),
	    'render_callback' => function() {
	        get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_layout','right-sidebar') );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'blog-layout-single', array(
	    'selector' => '.blog-wrapper.blog-single',
	    'settings' => array('blog_posts_depth_hover','blog_posts_depth','blog_post_layout','blog_single_layout_divider','blog_post_style','blog_single_header_category','blog_single_header_title','blog_single_header_meta','blog_single_footer_meta','blog_single_featured_image','blog_share','blog_author_box','blog_single_next_prev_nav'),
	    'render_callback' => function() {
	        get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_post_layout','right-sidebar') );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'blog-featured-posts', array(
	    'selector' => '.featured-posts',
	    'container_inclusive' => true,
	    'settings' => array('blog_featured_height'),
	    'render_callback' => function() {
	        get_template_part('template-parts/posts/featured-posts');
	    },
	) );

}
add_action( 'customize_register', 'silver_refresh_blog_partials' );

// UX Blocks before / after the content (inc/structure/structure-term-blocks.php):
// the default of the categories and of the posts, which can choose their own.
foreach ( array(
	'blog_archive' => array(
		'section' => 'blog-archive',
		'before'  => __( 'Blog categories: full width, under the page header. Each category can choose its own block.', 'silver' ),
		'after'   => __( 'Blog categories: full width, at the end of the page, before the footer. Each category can choose its own block.', 'silver' ),
	),
	'blog_single'  => array(
		'section' => 'blog-single',
		'before'  => __( 'Blog posts: full width, under the page header, above the post and the sidebar. A post, or its category, can choose its own block.', 'silver' ),
		'after'   => __( 'Blog posts: full width, at the end of the page, before the footer. A post, or its category, can choose its own block.', 'silver' ),
	),
) as $silver_page => $silver_field ) {
	foreach ( array( 'before', 'after' ) as $silver_position ) {
		Silver_Option::add_field( 'option', array(
			'type'        => 'select',
			'settings'    => $silver_page . '_' . $silver_position . '_block',
			'label'       => 'before' === $silver_position ? __( 'Before content (UX Block)', 'silver' ) : __( 'After content (UX Block)', 'silver' ),
			'description' => $silver_field[ $silver_position ],
			'section'     => $silver_field['section'],
			'default'     => 0,
			'choices'     => silver_get_block_list_by_id( array( 'option_none' => '-- None --' ) ),
		) );
	}
}
unset( $silver_page, $silver_field, $silver_position );
