<?php

/*************
 * Shop Panel
 *************/


Silver_Option::add_panel( 'woocommerce', array(
  'title'       => __( 'WooCommerce', 'silver' ),
) );

include_once(dirname( __FILE__ ).'/options-shop-category.php');
include_once(dirname( __FILE__ ).'/options-shop-product-page.php');
include_once(dirname( __FILE__ ).'/options-shop-product-reviews.php');
include_once(dirname( __FILE__ ).'/options-shop-my-account.php');
include_once(dirname( __FILE__ ).'/options-shop-cart-checkout.php');
include_once(dirname( __FILE__ ).'/options-shop-payments-icons.php');
include_once(dirname( __FILE__ ).'/options-shop-store-notice.php');

/**
 * UX Blocks before / after the content (inc/structure/structure-term-blocks.php):
 * the default of the shop, the product categories and the products, which can
 * choose their own. On init, after the Product Page section.
 */
function silver_customizer_shop_content_block_options() {
	foreach ( array(
		'product_archive' => array(
			'section' => 'woocommerce_product_catalog',
			'before'  => __( 'Shop and product categories: full width, before the products. The shop page and each category can choose their own block.', 'silver' ),
			'after'   => __( 'Shop and product categories: full width, at the end of the page, before the footer. The shop page and each category can choose their own block.', 'silver' ),
		),
		'product_single'  => array(
			'section' => 'product-page',
			'before'  => __( 'Products: full width, above the gallery and the summary. A product, or its category, can choose its own block.', 'silver' ),
			'after'   => __( 'Products: full width, after the tabs and the related products, before the footer. A product, or its category, can choose its own block.', 'silver' ),
		),
	) as $page => $field ) {
		foreach ( array( 'before', 'after' ) as $position ) {
			Silver_Option::add_field( 'option', array(
				'type'        => 'select',
				'settings'    => $page . '_' . $position . '_block',
				'label'       => 'before' === $position ? __( 'Before content (UX Block)', 'silver' ) : __( 'After content (UX Block)', 'silver' ),
				'description' => $field[ $position ],
				'section'     => $field['section'],
				'default'     => 0,
				'choices'     => silver_get_block_list_by_id( array( 'option_none' => '-- None --' ) ),
			) );
		}
	}
}
add_action( 'init', 'silver_customizer_shop_content_block_options', 20 );

function silver_refresh_shop_partials( WP_Customize_Manager $wp_customize ) {

  // Abort if selective refresh is not available.
  if ( ! isset( $wp_customize->selective_refresh ) ) {
      return;
  }

	$wp_customize->selective_refresh->add_partial( 'product-layout', array(
	    'selector' => '.product-container',
	    'settings' => array('product_layout'),
	    'fallback_refresh' => false,
	    'container_inclusive' => true,
	    'render_callback' => function() {
			wc_get_template_part( 'single-product/layouts/product', get_theme_mod( 'product_layout', silver_product_layout() ) );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'product-filter-button', array(
		'selector'            => '.product-filter-row',
		'settings'            => array( 'product_offcanvas_sidebar_position' ),
		'fallback_refresh'    => false,
		'container_inclusive' => true,
		'render_callback'     => function () {
			wc_get_template_part( 'single-product/filter-button' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'shop-header', array(
		'selector'            => '.woocommerce .category-page-title',
		'fallback_refresh'    => false,
		'settings'            => array(
			'html_shop_page',
			'category_title_style',
			'category_show_title',
			'category_show_result_count',
			'category_show_catalog_ordering',
			'breadcrumb_home',
			'category_filter_text',
			'category_filter_overlay_position',
		),
		'container_inclusive' => true,
		'render_callback'     => function () {
			silver_category_header();
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'shop-grid', array(
	    'selector' => '.category-page-row',
	    'fallback_refresh' => false,
	    'settings' => array('sale_bubble_text','category_grid_style','short_description_in_grid','cat_style','category_show_count','sale_bubble_percentage_formatting','new_bubble_auto','add_to_cart_style','add_to_cart_icon','product_box_category','product_box_rating','product_box_empty_rating','product_box_review_count','product_hover','bubble_style','sale_bubble','grid_style','category_sidebar','products_pr_page','category_row_count','category_row_count_mobile','category_row_count_tablet','category_shadow','category_shadow_hover','equalize_product_box','disable_quick_view'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
		    wc_get_template_part( 'layouts/category', get_theme_mod( 'category_sidebar', 'left-sidebar' ) );
	    },
	) );


	$wp_customize->selective_refresh->add_partial( 'account-header', array(
		'selector'            => '.my-account-header',
		'fallback_refresh'    => false,
		'settings'            => array( 'my_account_title_bg_color', 'facebook_login_bg', 'facebook_login_text', 'my_account_title_text_color', 'my_account_title_align' ),
		'container_inclusive' => true,
		'render_callback'     => function() {
			wc_get_template( 'myaccount/header.php' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'html_cart_footer', array(
	    'selector' => '.cart-footer-content',
	    'settings' => array('html_cart_footer'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        silver_html_cart_footer();
	    }
	) );

	$wp_customize->selective_refresh->add_partial( 'html_cart_sidebar', array(
	    'selector' => '.cart-sidebar-content',
	    'settings' => array('html_cart_sidebar'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        silver_html_cart_sidebar();
	     }
	) );

	$wp_customize->selective_refresh->add_partial( 'html_checkout_sidebar', array(
	    'selector' => '.html-checkout-sidebar',
	    'settings' => array('html_checkout_sidebar'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        silver_html_checkout_sidebar();
	     }
	) );

	$wp_customize->selective_refresh->add_partial( 'payment-icons', array(
	    'selector' => '.payment-icons',
	    'settings' => array('payment_icons', 'payment_icons_custom'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        return do_shortcode('[ux_payment_icons]');
	    }
	) );

	$wp_customize->selective_refresh->add_partial( 'refresh_css_shop', array(
	    'selector' => 'head > style#custom-css',
	    'container_inclusive' => true,
	    'settings' => array('product_image_ratio','product_image_height','product_image_fit','product_gallery_ratio','product_gallery_height','product_gallery_fit','color_new_bubble','color_new_bubble_auto','color_checkout','color_sale','color_review','color_regular_price','color_sale_price','color_price_badge'),
	    'render_callback' => function() {
	        silver_custom_css();
	    },
	) );
}
add_action( 'customize_register', 'silver_refresh_shop_partials' );
