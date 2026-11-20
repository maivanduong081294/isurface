<?php
/**
 * Breadcrumbs: options shared by every breadcrumb of the theme (shop, blog,
 * portfolio), so they are available with or without WooCommerce.
 *
 * Where breadcrumbs show is set where they show: Blog > Blog Archive / Blog
 * Single Post > Breadcrumbs; the size of the shop ones in Shop > Product
 * Catalog > Breadcrumb Size.
 *
 * @package Silver
 */

Silver_Option::add_section( 'breadcrumbs', array(
	'title'       => __( 'Breadcrumbs', 'silver' ),
	'description' => __( 'Applies to the shop, blog and portfolio breadcrumbs.', 'silver' ),
) );

// Moved from Shop > Product Catalog: same setting, saved values are kept.
Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'breadcrumb_home',
	'transport'       => silver_customizer_transport(),
	'label'           => __( 'Show home link in breadcrumb', 'silver' ),
	'section'         => 'breadcrumbs',
	'active_callback' => function () {
		$wpseo     = class_exists( 'WPSEO_Options' ) && get_theme_mod( 'wpseo_breadcrumb' ) ? true : false;
		$rank_math = class_exists( 'RankMath' ) && get_theme_mod( 'rank_math_breadcrumb' ) ? true : false;

		return ! $wpseo && ! $rank_math;
	},
	'default'         => 1,
) );

// Moved from Shop > Product Catalog: same setting, saved values are kept.
Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'breadcrumb_case',
	'label'    => esc_attr__( 'Breadcrumbs Case', 'silver' ),
	'section'  => 'breadcrumbs',
	'default'  => 'uppercase',
	'choices'  => array(
		'uppercase' => 'UPPERCASE',
		''          => 'Normal',
	),
) );

// Icon picker of UX Builder (inc/admin/customizer/customizer-icon-control.php).
// Printed by silver_breadcrumb_divider().
Silver_Option::add_field( 'option', array(
	'type'              => 'silver-icon',
	'settings'          => 'breadcrumb_divider_icon',
	'label'             => __( 'Divider icon', 'silver' ),
	'description'       => __( 'Font Awesome icon between the breadcrumb links. Without an icon the divider is a slash ( / ). Rank Math and SEOPress breadcrumbs keep the separator set in the plugin.', 'silver' ),
	'section'           => 'breadcrumbs',
	'default'           => '',
	'empty_label'       => __( 'Slash ( / )', 'silver' ),
	'sanitize_callback' => 'silver_sanitize_icon_class',
) );

// Where the breadcrumbs go (silver_breadcrumbs_position(), silver_breadcrumbs_bar()).
// Blog breadcrumbs are turned on in Blog > Blog Archive / Blog Single Post.
Silver_Option::add_field( 'option', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'breadcrumbs_position',
	'label'       => __( 'Breadcrumbs position', 'silver' ),
	'description' => __( 'Default: above the blog title, in the shop toolbar and above the product title. Below header: in a bar under the header on blog, shop, product category and product pages. Pages with a transparent header and UX Builder product layouts keep the default.', 'silver' ),
	'section'     => 'breadcrumbs',
	// Until it is saved, show the value of the first, blog-only version of
	// this option (same fallback as silver_breadcrumbs_position()).
	'default'     => 'header' === get_theme_mod( 'blog_breadcrumbs_position', 'title' ) ? 'header' : 'title',
	'choices'     => array(
		'title'  => __( 'Default', 'silver' ),
		'header' => __( 'Below header', 'silver' ),
	),
) );
