<?php

Silver_Option::add_section( 'fl-portfolio', array(
'title'       => __( 'Portfolio', 'silver' ),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'featured_items_page',
	'label'       => __( 'Custom Portfolio Page', 'silver' ),
	'section'     => 'fl-portfolio',
	'default'     => false,
	'choices'     => $list_pages
));

Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_save_permalinks',
    'label'       => __( '', 'silver' ),
	'section'     => 'fl-portfolio',
    'default'     => 'You need to Click <strong>"Save & Publish"</strong> and then <strong>"Update Permalinks"</strong> button to make sure it works!<br><br> <a class="button" href="'.admin_url().'options-permalink.php?settings-updated=true" target="_blank">Update permalinks</a>',
) );


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_portfolio_single',
    'label'       => __( '', 'silver' ),
	'section'     => 'fl-portfolio',
    'default'     => '<div class="options-title-divider">Single Page</div>',
) );

// Single Posts
Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'portfolio_layout',
	'label'       => __( 'Single Portfolio Layout', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' 	  => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'portfolio.svg',
		'sidebar-right' => $image_url . 'portfolio-sidebar-right.svg',
		'top' => $image_url . 'portfolio-top.svg',
		'top-full' => $image_url . 'portfolio-top-full.svg',
		'bottom' => $image_url . 'portfolio-bottom.svg',
		'bottom-full' => $image_url . 'portfolio-bottom-full.svg',
	),
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'portfolio_title_transparent',
  'label'       => __( 'Transparent Header', 'silver' ),
  'section'     => 'fl-portfolio',
  'default' => 0
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'portfolio_title',
	'label'       => __( 'Single Portfolio Title', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' 	  => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'portfolio-title.svg',
		'featured' => $image_url . 'portfolio-title-featured.svg',
		'breadcrumbs' => $image_url . 'portfolio-title-breadcrumbs.svg',
	),
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'portfolio_share',
	'label'    => __( 'Show share icons', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 1,
) );

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'portfolio_related',
  'label'       => __( 'Show related items', 'silver' ),
  'section'     => 'fl-portfolio',
  'default' => 1
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'portfolio_next_prev',
  'label'       => __( 'Show Next/Prev navigation', 'silver' ),
  'section'     => 'fl-portfolio',
  'default' => 1
));



Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_portfolio_archive',
    'label'       => __( '', 'silver' ),
	'section'     => 'fl-portfolio',
    'default'     => '<div class="options-title-divider">Archive Page</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'portfolio_archive_orderby',
	'label'    => __( 'Portfolio Items Orderby', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 'menu_order',
	'choices'  => array(
		'title'      => 'Title',
		'name'       => 'Name',
		'date'       => 'Date',
		'menu_order' => 'Menu Order',
	),
));

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'portfolio_archive_order',
	'label'    => __( 'Portfolio Items Order', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 'desc',
	'choices'  => array(
		'desc' => 'DESC',
		'asc'  => 'ASC',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'portfolio_style',
	'label'       => __( 'Portfolio Style', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' 	  => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'portfolio-simple.svg',
		'overlay' => $image_url . 'portfolio-overlay.svg',
		'shade' => $image_url . 'portfolio-shade.svg',
	),
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'select',
  'settings'     => 'portfolio_height',
  'label'       => __( 'Image Height', 'silver' ),
  'section'     => 'fl-portfolio',
  'default'     => 0,
  'choices'     => array(
     0   => 'Auto',
    '50%' => '1:2 (Wide)',
    '75%' => '4:3 (Rectangular)',
    '56%' => '16:9 (Widescreen)',
    '100%' => '1:1 (Square)',
    '125%' => 'Portrait',
    '200%' => '2:1 (Tall)',
  ),
));

Silver_Option::add_field( 'option',  array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_image_radius',
	'label'    => __( 'Image Radius (%)', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 0,
	'choices'  => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
));

Silver_Option::add_field( 'option',  array(
	'type'     => 'select',
	'settings' => 'portfolio_archive_image_size',
	'label'    => __( 'Image Size', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 'medium',
	'choices'  => array(
		'large'     => 'Large',
		'medium'    => 'Medium',
		'thumbnail' => 'Thumbnail',
		'original'  => 'Original',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_depth',
	'label'    => __( 'Item Depth', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 0,
	'choices'  => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1,
	),
));

Silver_Option::add_field( 'option',  array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_depth_hover',
	'label'    => __( 'Item Depth :hover', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 0,
	'choices'  => array(
		'min'  => 0,
		'max'  => 5,
		'step' => 1,
	),
));

Silver_Option::add_field( 'option',  array(
	'type'     => 'radio-buttonset',
	'settings' => 'portfolio_archive_spacing',
	'label'    => __( 'Column Spacing', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 'small',
	'choices'  => array(
		'collapse' => 'Collapse',
		'xsmall'   => 'X Small',
		'small'    => 'Small',
		'normal'   => 'Normal',
		'large'    => 'Large',
	),
));

Silver_Option::add_field( 'option', array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_columns',
	'label'    => __( 'Items per row - Desktop', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 4,
	'choices'  => array(
		'min'  => 1,
		'max'  => 6,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_columns_tablet',
	'label'    => __( 'Items per row - Tablet', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 3,
	'choices'  => array(
		'min'  => 1,
		'max'  => 4,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'slider',
	'settings' => 'portfolio_archive_columns_mobile',
	'label'    => __( 'Items per row - Mobile', 'silver' ),
	'section'  => 'fl-portfolio',
	'default'  => 2,
	'choices'  => array(
		'min'  => 1,
		'max'  => 3,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'portfolio_archive_title',
	'label'       => __( 'Archive Portfolio Title', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' 	  => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'portfolio-title.svg',
		'featured' => $image_url . 'portfolio-title-featured.svg',
		'breadcrumbs' => $image_url . 'portfolio-title-breadcrumbs.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'portfolio_archive_title_transparent',
	'label'       => __( 'Transparent Header', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' => 0
));

Silver_Option::add_field( 'option',  array(
    'type'        => 'image',
    'settings'     => 'portfolio_archive_bg',
    'label'       => __( 'Portfolio Header Background', 'silver' ),
	'section'     => 'fl-portfolio',
	'default'     => "",
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'portfolio_archive_filter',
	'label'       => __( 'Filter Navigation', 'silver' ),
	'section'     => 'fl-portfolio',
	'default'     => 'left',
	'choices'     => array(
		'left' => 'Left',
		'center' => 'Center',
		'disabled' => 'Disabled'
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'portfolio_archive_filter_style',
	'label'       => __( 'Filter Nav style', 'silver' ),
	'section'     => 'fl-portfolio',
	'default' 	  => 'line-grow',
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_styles_img
));




function silver_refresh_portfolio_partials( WP_Customize_Manager $wp_customize ) {

  // Abort if selective refresh is not available.
  if ( ! isset( $wp_customize->selective_refresh ) ) {
      return;
  }
	$wp_customize->selective_refresh->add_partial( 'portfolio-single-layout', array(
		'selector' => '.portfolio-single-page',
		'settings' => array('portfolio_style','portfolio_layout','portfolio_title'),
		'render_callback' => function() {
			get_template_part( 'template-parts/portfolio/single-portfolio', get_theme_mod( 'portfolio_layout', '' ) );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'portfolio-archive-layout', array(
		'selector' => '.portfolio-archive',
		'settings' => array('portfolio_archive_title','portfolio_archive_filter','portfolio_style','portfolio_archive_filter_style'),
		'render_callback' => function() {
			get_template_part( 'template-parts/portfolio/archive-portfolio' );
		},
	) );


}
add_action( 'customize_register', 'silver_refresh_portfolio_partials' );
