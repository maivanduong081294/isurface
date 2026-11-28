<?php
/**
 * Customizer layout: groups the sections in panels, orders them, names them
 * the same way, and splits the long sections (Floating contact, Shop page,
 * Product page, Blog page) into smaller sections.
 *
 * It runs after every section and control is registered and only changes
 * where they show: setting IDs stay the same, so saved values are kept. The
 * old section IDs stay (as the first part of a split section), so links that
 * open a section (header builder, wp.customize.section(), autofocus) still
 * work. A control registered later in a split section stays in that section.
 *
 * @package Silver
 */

/**
 * Root panels and sections: id => array( priority, title ).
 * The title is only changed when one is given.
 *
 * @return array
 */
function silver_customizer_layout_root() {
	return array(
		'silver_general' => array( 10, __( 'Layout & pages', 'silver' ) ),
		'header'         => array( 20, __( 'Header', 'silver' ) ),
		'style'          => array( 30, _x( 'Style', 'Customizer panel', 'silver' ) ),
		'blog'           => array( 40, __( 'Blog', 'silver' ) ),
		'woocommerce'    => array( 50, '' ),
		'fl-portfolio'   => array( 60, '' ),
		'silver_contact' => array( 70, __( 'Contact & social', 'silver' ) ),
		'advanced'       => array( 999, __( 'Reset options', 'silver' ) ),
	);
}

/**
 * Sections in panels: panel => array( section => title ), in display order.
 * An empty title keeps the title of the section.
 *
 * @return array
 */
function silver_customizer_layout_sections() {
	return array(
		'silver_general' => array(
			'layout'        => __( 'Site layout', 'silver' ),
			'pages'         => __( 'Pages', 'silver' ),
			'breadcrumbs'   => __( 'Breadcrumbs', 'silver' ),
			'footer'        => __( 'Footer', 'silver' ),
			'notifications' => __( 'Cookie notice', 'silver' ),
		),
		'header'         => array(
			'header-presets'      => __( 'Presets', 'silver' ),
			'header-layout'       => __( 'Elements', 'silver' ),
			'title_tagline'       => __( 'Logo & site identity', 'silver' ),
			'top_bar'             => __( 'Top bar', 'silver' ),
			'main_bar'            => __( 'Main bar', 'silver' ),
			'bottom_bar'          => __( 'Bottom bar', 'silver' ),
			'header_sticky'       => __( 'Sticky header', 'silver' ),
			'header_mobile'       => __( 'Mobile menu', 'silver' ),
			'header_dropdown'     => __( 'Dropdowns', 'silver' ),
			'header_nav_vertical' => __( 'Vertical menu', 'silver' ),
			'header_buttons'      => __( 'Buttons', 'silver' ),
			'header_account'      => __( 'Account', 'silver' ),
			'header_cart'         => __( 'Cart', 'silver' ),
			'header_search'       => __( 'Search', 'silver' ),
			'header_contact'      => __( 'Contact', 'silver' ),
			'header_newsletter'   => __( 'Newsletter', 'silver' ),
			'header_content'      => __( 'HTML', 'silver' ),
			'follow'              => __( 'Follow icons', 'silver' ),
		),
		'style'          => array(
			'colors'         => __( 'Colors', 'silver' ),
			'type'           => __( 'Typography', 'silver' ),
			'default-button' => __( 'Buttons', 'silver' ),
			'global-styles'  => __( 'Global styles', 'silver' ),
			'lightbox'       => __( 'Drawer & lightbox', 'silver' ),
			'custom-css'     => __( 'Custom CSS', 'silver' ),
		),
		'blog'           => array(
			'blog-global'       => __( 'General', 'silver' ),
			'blog-layout'       => __( 'Blog page', 'silver' ),
			'silver-blog-posts' => __( 'Posts layout', 'silver' ),
			'silver-blog-card'  => __( 'Post card', 'silver' ),
			'blog-archive'      => __( 'Archives', 'silver' ),
			'blog-single'       => __( 'Single post', 'silver' ),
			'blog-featured'     => __( 'Featured posts', 'silver' ),
			'blog-promo'        => __( 'Related products', 'silver' ),
		),
		'woocommerce'    => array(
			'woocommerce_product_catalog' => __( 'Shop page', 'silver' ),
			'silver-shop-layout'          => __( 'Shop layout', 'silver' ),
			'silver-shop-header'          => __( 'Category header', 'silver' ),
			'silver-shop-card'            => __( 'Product card', 'silver' ),
			'silver-shop-badges'          => __( 'Sale & new badges', 'silver' ),
			'product-page'                => __( 'Product layout', 'silver' ),
			'silver-product-gallery'      => __( 'Product gallery', 'silver' ),
			'silver-product-info'         => __( 'Product info & buy box', 'silver' ),
			'silver-product-tabs'         => __( 'Product tabs', 'silver' ),
			'silver-product-related'      => __( 'Related products', 'silver' ),
			'silver-product-extra'        => __( 'Extra content', 'silver' ),
			'product-reviews'             => __( 'Reviews', 'silver' ),
			'woocommerce_product_images'  => __( 'Product image sizes', 'silver' ),
			'cart-checkout'               => __( 'Cart', 'silver' ),
			'woocommerce_checkout'        => __( 'Checkout', 'silver' ),
			'fl-my-account'               => __( 'My account', 'silver' ),
			'payment-icons'               => __( 'Payment icons', 'silver' ),
			'woocommerce_store_notice'    => __( 'Store notice', 'silver' ),
		),
		'silver_contact' => array(
			'floating_contact' => __( 'Floating contact', 'silver' ),
			'silver-fc-button' => __( 'Floating contact: main button', 'silver' ),
			'silver-fc-chat'   => __( 'Floating contact: chat apps', 'silver' ),
			'silver-fc-social' => __( 'Floating contact: social networks', 'silver' ),
			'silver-fc-shops'  => __( 'Floating contact: marketplaces', 'silver' ),
			'silver-fc-direct' => __( 'Floating contact: call, email & map', 'silver' ),
			'share'            => __( 'Share icons', 'silver' ),
		),
	);
}

/**
 * Controls of the split sections: section => control IDs, in display order.
 * The controls of the old section that are not listed come after them.
 *
 * @return array
 */
function silver_customizer_layout_controls() {
	$fc = function ( $channels ) {
		$ids = array();
		foreach ( $channels as $channel ) {
			foreach ( array( 'title', 'link', 'label', 'device', 'icon_class', 'icon' ) as $field ) {
				$ids[] = 'floating_contact_' . $channel . '_' . $field;
			}
		}
		return $ids;
	};

	return array(
		// Blog.
		'blog-layout'                 => array( 'blog_header', 'blog_archive_transparent', 'blog_bg_color', 'blog_layout', 'blog_layout_divider', 'blog_sidebar_width', 'blog_sticky_sidebar', 'blog_sticky_sidebar_mode', 'blog_title_tag', 'blog_list_title', 'blog_list_title_display', 'blog_category_list', 'blog_category_list_level', 'blog_category_list_all', 'blog_category_list_number' ),
		'silver-blog-posts'           => array( 'blog_style', 'blog_style_type', 'blog_columns', 'blog_columns_tablet_landscape', 'blog_columns_tablet', 'blog_columns_mobile', 'blog_item_col_spacing', 'blog_post_item_style', 'blog_show_excerpt', 'blog_posts_header_style', 'blog_posts_title_align', 'blog_posts_depth', 'blog_posts_depth_hover' ),
		'silver-blog-card'            => array( 'blog_card_hover', 'blog_item_title_size', 'blog_item_title_style', 'blog_item_show_date', 'blog_item_badge_style', 'blog_item_show_category', 'blog_item_comments', 'blog_item_post_icon', 'blog_item_excerpt', 'blog_item_excerpt_length', 'blog_item_readmore', 'blog_item_readmore_color', 'blog_item_readmore_style', 'blog_item_readmore_size', 'blog_item_image_size', 'blog_item_image_height', 'blog_item_image_width', 'blog_item_image_radius', 'blog_item_image_overlay', 'blog_item_image_hover', 'blog_item_image_hover_alt', 'blog_item_text_pos', 'blog_item_text_size', 'blog_item_text_hover', 'blog_item_text_bg', 'blog_item_text_color', 'blog_item_text_padding' ),

		// Shop page (WooCommerce's own controls of the section come first).
		'woocommerce_product_catalog' => array( 'woocommerce_shop_page_display', 'woocommerce_category_archive_display', 'woocommerce_default_catalog_orderby', 'html_shop_page', 'html_shop_page_content', 'product_archive_before_block', 'product_archive_after_block' ),
		'silver-shop-layout'          => array( 'custom_title_category_layout', 'category_sidebar', 'category_sticky_sidebar', 'category_sticky_sidebar_mode', 'category_grid_style', 'products_pr_page', 'category_row_count', 'category_row_count_tablet_landscape', 'category_row_count_tablet', 'category_row_count_mobile', 'custom_html_woocommerce_image_shortcut_category', 'custom_title_category_category_box', 'cat_style', 'category_show_count' ),
		'silver-shop-header'          => array( 'category_title_style', 'category_show_title', 'category_header_transparent', 'header_shop_bg_featured', 'header_shop_bg_image', 'header_shop_bg_color', 'category_show_result_count', 'category_show_catalog_ordering', 'category_catalog_ordering_style', 'category_filter_text', 'category_filter_overlay_position', 'category_description_position', 'category_description_bg', 'category_description_padding', 'category_description_padding_mobile', 'category_description_toc', 'category_description_toc_width', 'category_description_readmore', 'category_description_readmore_height' ),
		'silver-shop-card'            => array( 'grid_style', 'product_box_card', 'product_hover', 'category_shadow', 'category_shadow_hover', 'equalize_product_box', 'add_to_cart_icon', 'add_to_cart_style', 'disable_quick_view', 'product_box_category', 'short_description_in_grid', 'product_box_rating', 'product_box_rating_style', 'product_box_empty_rating', 'product_box_review_count', 'product_box_attrs', 'product_box_attrs_style', 'product_box_attrs_bg', 'product_box_attrs_color', 'product_box_attrs_border', 'product_box_attrs_max' ),
		'silver-shop-badges'          => array( 'bubble_style', 'sale_bubble', 'sale_bubble_text', 'sale_bubble_percentage', 'sale_bubble_percentage_formatting', 'new_bubble_auto' ),

		// Product page.
		'product-page'                => array( 'product_layout', 'product_custom_layout', 'product_offcanvas_sidebar', 'product_offcanvas_sidebar_position', 'product_header', 'product_header_transparent', 'product_next_prev_nav' ),
		'silver-product-gallery'      => array( 'custom_section_gallery_message', 'product_image_width', 'product_image_style', 'product_gallery_grid_layout', 'product_gallery_slider_type', 'product_gallery_thumbs_desktop', 'product_gallery_thumbs_laptop', 'product_gallery_thumbs_tablet', 'product_gallery_thumbs_mobile', 'product_lightbox', 'product_zoom', 'product_sticky_gallery', 'product_sticky_gallery_mode', 'product_video_style', 'product_video_playback', 'custom_html_woocommerce_image_shortcut_product' ),
		'silver-product-info'         => array( 'product_info_align', 'product_info_form', 'product_title_divider', 'product_info_elements', 'product_brands', 'product_info_review_count', 'product_info_review_count_style', 'product_buy_box', 'product_buy_box_quantity', 'product_call_phone', 'product_buy_now', 'product_buy_now_redirect', 'product_sticky_cart', 'product_info_meta', 'product_info_share' ),
		'silver-product-tabs'         => array( 'product_display', 'product_tabs_align', 'product_tabs_order', 'product_tabs_readmore', 'product_tabs_readmore_height', 'product_sections_merge_specs', 'product_sections_merge_position', 'product_sections_merge_width', 'product_sections_merge_width_tablet_landscape', 'tab_title', 'tab_content' ),
		'silver-product-related'      => array( 'related_products', 'related_products_pr_row', 'related_products_pr_row_tablet_landscape', 'related_products_pr_row_tablet', 'related_products_pr_row_mobile', 'max_related_products', 'product_upsell' ),
		'silver-product-extra'        => array( 'product_single_before_block', 'product_single_after_block', 'html_before_add_to_cart', 'html_after_add_to_cart' ),

		// Breadcrumbs: the size of the shop breadcrumbs joins the other options.
		'breadcrumbs'                 => array( 'page_breadcrumbs', 'breadcrumb_home', 'breadcrumb_case', 'breadcrumb_divider_icon', 'breadcrumb_size', 'breadcrumbs_position' ),

		// Floating contact.
		'floating_contact'            => array( 'floating_contact', 'floating_contact_style', 'floating_contact_position', 'floating_contact_items' ),
		'silver-fc-button'            => array( 'floating_contact_s2_title', 'floating_contact_launcher_icon', 'floating_contact_launcher_image', 'floating_contact_launcher_size', 'floating_contact_launcher_color', 'floating_contact_launcher_animate', 'floating_contact_open', 'floating_contact_offset_bottom', 'floating_contact_offset_side', 'floating_contact_pill_bg', 'floating_contact_pill_color', 'floating_contact_toggle_title', 'floating_contact_toggle', 'floating_contact_toggle_label', 'floating_contact_bg', 'floating_contact_color', 'floating_contact_toggle_color' ),
		'silver-fc-chat'              => $fc( array( 'messenger', 'zalo', 'telegram', 'whatsapp', 'viber', 'line', 'wechat', 'kakaotalk', 'discord' ) ),
		'silver-fc-social'            => $fc( array( 'facebook', 'tiktok', 'instagram', 'threads', 'youtube', 'x', 'linkedin', 'pinterest' ) ),
		'silver-fc-shops'             => $fc( array( 'shopee', 'lazada', 'tiki' ) ),
		'silver-fc-direct'            => $fc( array( 'hotline', 'sms', 'email', 'maps', 'website' ) ),
	);
}

/**
 * Headings that only repeated the title of the new section they open, or
 * whose group moved away.
 *
 * @return array
 */
function silver_customizer_layout_removed_controls() {
	return array(
		'custom_title_category_homepage',
		'custom_title_category_header',
		'custom_title_category_breadcrumbs',
		'custom_title_category_product_box',
		'custom_title_product_layout',
		'custom_title_product_gallery',
		'custom_title_product_info',
		'custom_title_product_tabs',
		'custom_title_product_related',
		'custom_title_product_html',
	);
}

/**
 * Applies the layout.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function silver_customizer_layout( $wp_customize ) {
	$panels  = silver_customizer_layout_sections();
	$root    = silver_customizer_layout_root();
	$channel = __( 'Turn the channels on and order them in Floating contact > Channels.', 'silver' );
	$descs   = array(
		'silver-fc-chat'   => $channel,
		'silver-fc-social' => $channel,
		'silver-fc-shops'  => $channel,
		'silver-fc-direct' => $channel,
	);

	// Panels.
	foreach ( array( 'silver_general', 'silver_contact' ) as $id ) {
		if ( ! $wp_customize->get_panel( $id ) ) {
			$wp_customize->add_panel( $id, array( 'title' => $root[ $id ][1] ) );
		}
	}
	foreach ( $root as $id => $args ) {
		$box = $wp_customize->get_panel( $id );
		$box = $box ? $box : $wp_customize->get_section( $id );
		if ( ! $box ) {
			continue;
		}
		$box->priority = $args[0];
		if ( '' !== $args[1] ) {
			$box->title = $args[1];
		}
	}

	// Sections: moved, ordered, renamed; the new ones added. A panel that is
	// not registered (WooCommerce off) keeps its sections out.
	foreach ( $panels as $panel => $sections ) {
		if ( ! $wp_customize->get_panel( $panel ) ) {
			continue;
		}
		$priority = 0;
		foreach ( $sections as $id => $title ) {
			$priority += 10;
			$section   = $wp_customize->get_section( $id );
			if ( ! $section && 0 === strpos( $id, 'silver-' ) ) {
				$section = $wp_customize->add_section( $id, array(
					'title'       => $title,
					'description' => isset( $descs[ $id ] ) ? $descs[ $id ] : '',
				) );
			}
			if ( ! $section ) {
				continue;
			}
			$section->panel    = $panel;
			$section->priority = $priority;
			if ( '' !== $title ) {
				$section->title = $title;
			}
		}
	}

	foreach ( silver_customizer_layout_removed_controls() as $id ) {
		$wp_customize->remove_control( $id );
	}

	// Controls: listed ones in their order, then the others of the section.
	$controls = $wp_customize->controls();
	$order    = array();
	$i        = 0;
	foreach ( $controls as $id => $control ) {
		$order[ $id ] = array( (int) $control->priority, $i++ );
	}
	$moved = array();
	foreach ( silver_customizer_layout_controls() as $section => $ids ) {
		if ( ! $wp_customize->get_section( $section ) ) {
			continue;
		}
		foreach ( array_values( $ids ) as $n => $id ) {
			if ( isset( $controls[ $id ] ) ) {
				$controls[ $id ]->section  = $section;
				$controls[ $id ]->priority = ( $n + 1 ) * 10;
				$moved[ $id ]              = true;
			}
		}
	}
	// The controls left in a split section come after the listed ones, in
	// their order.
	$tail = array();
	foreach ( array_keys( silver_customizer_layout_controls() ) as $section ) {
		$tail[ $section ] = array();
	}
	foreach ( $controls as $id => $control ) {
		if ( ! isset( $moved[ $id ] ) && isset( $tail[ $control->section ] ) ) {
			$tail[ $control->section ][ $id ] = $order[ $id ];
		}
	}
	foreach ( $tail as $section => $ids ) {
		uasort( $ids, function ( $a, $b ) {
			return $a[0] === $b[0] ? $a[1] - $b[1] : $a[0] - $b[0];
		} );
		$n = 1000;
		foreach ( array_keys( $ids ) as $id ) {
			$controls[ $id ]->priority = $n;
			$n += 10;
		}
	}

	if ( isset( $controls['breadcrumb_size'] ) ) {
		$controls['breadcrumb_size']->label = __( 'Shop breadcrumbs size', 'silver' );
	}
}

add_action( 'customize_register', 'silver_customizer_layout', 1000 );

/**
 * Icons of the new root panels (the root items have a dashicon).
 */
function silver_customizer_layout_icons() {
	echo '<style>'
		. '#accordion-panel-silver_general > h3:before { content: "\f538"; }'
		. '#accordion-panel-silver_contact > h3:before { content: "\f525"; }'
		// Sections moved from the root into a panel (layout, pages, footer...)
		// keep their root icon rule but not its padding: no icon in a panel.
		. '#customize-theme-controls > ul.customize-pane-child > li > h3:before { content: none; }'
		. '</style>';
}

add_action( 'customize_controls_print_styles', 'silver_customizer_layout_icons', 20 );
