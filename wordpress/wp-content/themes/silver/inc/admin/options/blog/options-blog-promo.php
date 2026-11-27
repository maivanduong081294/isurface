<?php
/**
 * Blog > Blog Related Products: the section of related products under the posts of
 * the blog page and post categories (inc/woocommerce/structure-wc-blog-promo.php).
 * Per post category (inherited by subcategories): heading, product category
 * and products, on the Edit category screen.
 *
 * @package Silver
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

Silver_Option::add_section( 'blog-promo', array(
	'title' => __( 'Blog Related Products', 'silver' ),
	'panel' => 'blog',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'blog_promo',
	'label'       => __( 'Related products section', 'silver' ),
	'description' => __( 'A section of products under the posts of the blog page and post categories. Products chosen on the category, then its product category (on sale, best sellers), then the fallback below.', 'silver' ),
	'section'     => 'blog-promo',
	'default'     => 1,
) );

$silver_blog_promo_on = array(
	array(
		'setting'  => 'blog_promo',
		'operator' => '==',
		'value'    => true,
	),
);

Silver_Option::add_field( 'option', array(
	'type'              => 'text',
	'settings'          => 'blog_promo_title',
	'label'             => __( 'Related products heading', 'silver' ),
	'description'       => __( 'H2 of the section, %s = the name of the category. Each category can set its own.', 'silver' ),
	'section'           => 'blog-promo',
	'default'           => __( 'Related products', 'silver' ),
	'sanitize_callback' => 'sanitize_text_field',
	'active_callback'   => $silver_blog_promo_on,
) );

// Colors of the section (silver_blog_promo_section(): CSS variables on it).
Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'blog_promo_bg',
	'label'           => __( 'Related products: background color', 'silver' ),
	'description'     => __( 'Empty: white.', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => '',
	'active_callback' => $silver_blog_promo_on,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'blog_promo_text',
	'label'           => __( 'Related products: text color', 'silver' ),
	'description'     => __( 'The heading and "View all". The product cards keep their own colors.', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => '',
	'active_callback' => $silver_blog_promo_on,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'blog_promo_title_style',
	'label'           => __( 'Related products heading style', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => 'simple',
	'choices'         => array(
		'simple'      => 'Simple',
		'normal'      => 'Normal',
		'center'      => 'Center',
		'bold'        => 'Left Bold',
		'bold-center' => 'Center Bold',
	),
	'active_callback' => $silver_blog_promo_on,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'blog_promo_number',
	'label'           => __( 'Related products: number', 'silver' ),
	'description'     => __( '0 = one row (Products per row - Desktop below).', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => 0,
	'choices'         => array(
		'min'  => 0,
		'max'  => 12,
		'step' => 1,
	),
	'active_callback' => $silver_blog_promo_on,
) );

// Products per row on each screen (silver_blog_promo_columns()): Desktop
// >= 1200px, Tablet landscape 850-1199px, Tablet portrait 550-849px, Mobile
// < 550px. 0 = Shop > Product Catalog > Products per row.
foreach ( array(
	'blog_promo_columns'                  => array( __( 'Products per row - Desktop', 'silver' ), 6 ),
	'blog_promo_columns_tablet_landscape' => array( __( 'Products per row - Tablet landscape', 'silver' ), 6 ),
	'blog_promo_columns_tablet'           => array( __( 'Products per row - Tablet portrait', 'silver' ), 4 ),
	'blog_promo_columns_mobile'           => array( __( 'Products per row - Mobile', 'silver' ), 3 ),
) as $silver_blog_promo_setting => $silver_blog_promo_field ) {
	Silver_Option::add_field( 'option', array(
		'type'            => 'slider',
		'settings'        => $silver_blog_promo_setting,
		'label'           => $silver_blog_promo_field[0],
		'description'     => __( '0 = as the shop (Shop > Product Catalog > Products per row).', 'silver' ),
		'section'         => 'blog-promo',
		'default'         => 0,
		'choices'         => array(
			'min'  => 0,
			'max'  => $silver_blog_promo_field[1],
			'step' => 1,
		),
		'active_callback' => $silver_blog_promo_on,
	) );
}
unset( $silver_blog_promo_setting, $silver_blog_promo_field );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-buttonset',
	'settings'        => 'blog_promo_type',
	'label'           => __( 'Related products layout', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => 'row',
	'choices'         => array(
		'row'    => __( 'Row', 'silver' ),
		'slider' => __( 'Slider', 'silver' ),
	),
	'active_callback' => $silver_blog_promo_on,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'blog_promo_fallback',
	'label'           => __( 'Related products: fallback', 'silver' ),
	'description'     => __( 'When the category does not set enough products.', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => 'featured',
	'choices'         => array(
		'featured'     => __( 'Featured products', 'silver' ),
		'onsale'       => __( 'On sale products', 'silver' ),
		'best_selling' => __( 'Best sellers', 'silver' ),
		'latest'       => __( 'Latest products (all categories)', 'silver' ),
		'none'         => __( 'None', 'silver' ),
	),
	'active_callback' => $silver_blog_promo_on,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'blog_promo_first_page',
	'label'           => __( 'Related products: first page only', 'silver' ),
	'section'         => 'blog-promo',
	'default'         => 1,
	'active_callback' => $silver_blog_promo_on,
) );

unset( $silver_blog_promo_on );
