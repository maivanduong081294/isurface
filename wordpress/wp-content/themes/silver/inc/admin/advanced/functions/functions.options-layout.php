<?php
/**
 * Layout of the Advanced Theme Options page: tabs, groups, labels.
 *
 * of_options() (functions.options.php) declares the options; this file puts
 * them in 9 tabs with titled groups, one style of group title, and labels in
 * the same form (translatable, "silver" domain). The option IDs never change:
 * the saved values are kept.
 *
 * Each tab has a fixed slug (the #of-option-… link of the tab, used by the
 * admin bar): it does not change with the language of the labels.
 *
 * @package Silver/Admin/Options/Advanced
 */

defined( 'ABSPATH' ) || exit;

/**
 * Old tab links (#of-option-…) and the tab that holds their options now.
 *
 * @return array<string, string>
 */
function silver_of_options_tab_redirects() {
	return array(
		'globalsettings'  => 'code',
		'customcss'       => 'code',
		'contentdelivery' => 'loading',
		'siteloader'      => 'loading',
		'sitesearch'      => 'search',
		'googleapis'      => 'general',
		'404page'         => 'general',
		'portfolio'       => 'general',
		'mobile'          => 'general',
		'catalogmode'     => 'woocommerce',
		'integrations'    => 'system',
		'updates'         => 'system',
		'backupandimport' => 'system',
	);
}

/**
 * A group title: the same markup everywhere.
 *
 * @param string $title Title (escaped HTML allowed: a tag).
 * @param string $desc  Description (HTML).
 * @return array Option of type "info".
 */
function silver_of_group( $title, $desc = '' ) {
	return array(
		'name'  => '',
		'type'  => 'info',
		'class' => 'silver-of-group',
		'desc'  => '<h2 class="silver-of-group__title" style="font-size:1rem;margin:40px 0 4px;">' . $title . '</h2>'
			. ( '' !== trim( $desc ) ? '<div class="silver-of-group__desc" style="font-size:14px;">' . $desc . '</div>' : '' ),
	);
}

/**
 * Labels, in the same form: sentence case, the name of the setting (the
 * description says what it does).
 *
 * @return array<string, string> Option ID => label.
 */
function silver_of_options_labels() {
	return array(
		// General.
		'fl_portfolio'                              => __( 'Portfolio', 'silver' ),
		'parallax_mobile'                           => __( 'Parallax on mobile', 'silver' ),
		'flatsome_fallback'                         => __( 'Silver 2.0 content support', 'silver' ),
		'404_block'                                 => __( '404 page block', 'silver' ),
		'google_map_api'                            => __( 'Google Maps API key', 'silver' ),
		// Custom code.
		'html_scripts_header'                       => __( 'Header scripts', 'silver' ),
		'html_scripts_after_body'                   => __( 'Body scripts: top', 'silver' ),
		'html_scripts_before_body'                  => __( 'Body scripts: bottom', 'silver' ),
		'html_scripts_footer'                       => __( 'Footer scripts', 'silver' ),
		'html_custom_css'                           => __( 'All screens', 'silver' ),
		'html_custom_css_tablet'                    => __( 'Tablets and smaller', 'silver' ),
		'html_custom_css_tablet_landscape'          => __( 'Tablet landscape only', 'silver' ),
		'html_custom_css_tablet_portrait'           => __( 'Tablet portrait only', 'silver' ),
		'html_custom_css_mobile'                    => __( 'Mobile only', 'silver' ),
		// Performance.
		'perf_enabled'                              => __( 'Performance optimizations', 'silver' ),
		'perf_page_cache'                           => __( 'Page cache', 'silver' ),
		'perf_page_cache_exclude'                   => __( 'Do not cache these URLs', 'silver' ),
		'perf_instant_page'                         => __( 'Preload pages', 'silver' ),
		'lazy_load_images'                          => __( 'Lazy load images', 'silver' ),
		'lazy_load_iframes'                         => __( 'Lazy load iframes', 'silver' ),
		'lazy_load_video'                           => __( 'Lazy load videos', 'silver' ),
		'lazy_load_audio'                           => __( 'Lazy load audio', 'silver' ),
		'flatsome_disable_style_css'                => __( 'Disable theme style.css', 'silver' ),
		'disable_emoji'                             => __( 'Disable emoji script', 'silver' ),
		'perf_icon_font_display'                    => __( 'Icon font display', 'silver' ),
		'disable_blockcss'                          => __( 'Disable block library CSS', 'silver' ),
		'disable_mejs'                              => __( 'Disable MediaElement script and CSS', 'silver' ),
		'jquery_migrate'                            => __( 'Disable jQuery Migrate', 'silver' ),
		'perf_disable_dashicons'                    => __( 'Disable Dashicons for visitors', 'silver' ),
		'perf_disable_global_styles'                => __( 'Disable global styles', 'silver' ),
		'perf_disable_embeds'                       => __( 'Disable embeds', 'silver' ),
		'perf_clean_head'                           => __( 'Clean up <head>', 'silver' ),
		'perf_defer_js'                             => __( 'Defer JavaScript', 'silver' ),
		'perf_defer_js_exclude'                     => __( 'Do not defer these scripts', 'silver' ),
		'perf_critical_css'                         => __( 'Critical CSS', 'silver' ),
		'perf_inline_css'                           => __( 'Inline small CSS', 'silver' ),
		'perf_async_css'                            => __( 'Non-blocking CSS', 'silver' ),
		'perf_preconnect'                           => __( 'Preconnect to domains', 'silver' ),
		'perf_preload'                              => __( 'Preload files (all pages)', 'silver' ),
		'perf_preload_home'                         => __( 'Preload files (homepage)', 'silver' ),
		'perf_browser_cache'                        => __( 'Browser cache', 'silver' ),
		'perf_gzip'                                 => __( 'Gzip / Brotli compression', 'silver' ),
		// Page loading.
		'site_loader'                               => __( 'Site loader', 'silver' ),
		'site_loader_color'                         => __( 'Loader color', 'silver' ),
		'site_loader_bg'                            => __( 'Loader background color', 'silver' ),
		'blog_pagination'                           => __( 'Blog pagination', 'silver' ),
		'shop_pagination'                           => __( 'Shop pagination', 'silver' ),
		'shop_filter_widgets_pjax'                  => __( 'AJAX shop filters', 'silver' ),
		'pjax_scroll_to_top'                        => __( 'Scroll to top after loading', 'silver' ),
		'infinite_scroll_loader_type'               => __( 'Loader type', 'silver' ),
		'infinite_scroll_loader_img'                => __( 'Custom loader image', 'silver' ),
		// Search.
		'live_search'                               => __( 'Live search', 'silver' ),
		'search_placeholder'                        => __( 'Search placeholder', 'silver' ),
		'search_result_latency'                     => __( 'Results delay', 'silver' ),
		'search_result_posts'                       => __( 'Blog posts in results', 'silver' ),
		'search_result_pages'                       => __( 'Pages in results', 'silver' ),
		'search_result_style'                       => __( 'Posts and pages list style', 'silver' ),
		'search_products_order_by'                  => __( 'Order products by', 'silver' ),
		'search_products_order'                     => __( 'Products order', 'silver' ),
		'search_by_sku'                             => __( 'Search by product SKU', 'silver' ),
		'search_by_product_tag'                     => __( 'Search by product tag', 'silver' ),
		// WooCommerce.
		'swatches'                                  => __( 'Variation swatches', 'silver' ),
		'additional_variation_images'               => __( 'Additional variation images', 'silver' ),
		'product_gallery_woocommerce'               => __( 'WooCommerce product gallery', 'silver' ),
		'ajax_add_to_cart'                          => __( 'AJAX add to cart (product page)', 'silver' ),
		'disable_reviews'                           => __( 'Disable reviews', 'silver' ),
		'catalog_mode'                              => __( 'Catalog mode', 'silver' ),
		'catalog_mode_prices'                       => __( 'Hide prices', 'silver' ),
		'catalog_mode_sale_badge'                   => __( 'Hide sale badge', 'silver' ),
		'catalog_mode_header'                       => __( 'Header: cart / account replacement', 'silver' ),
		'catalog_mode_product'                      => __( 'Product page: add to cart replacement', 'silver' ),
		'catalog_mode_lightbox'                     => __( 'Quick view: add to cart replacement', 'silver' ),
		'html_shop_page'                            => __( 'Shop header', 'silver' ),
		'tab_title'                                 => __( 'Global product tab: title', 'silver' ),
		'tab_content'                               => __( 'Global product tab: content', 'silver' ),
		'html_before_add_to_cart'                   => __( 'Before the add to cart button', 'silver' ),
		'html_after_add_to_cart'                    => __( 'After the add to cart button', 'silver' ),
		'html_thank_you'                            => __( 'Thank you page content / scripts', 'silver' ),
		// Instagram.
		'instagram_lazy_load'                       => __( 'Lazy load feeds', 'silver' ),
		// Maintenance mode.
		'maintenance_mode'                          => __( 'Maintenance mode', 'silver' ),
		'maintenance_mode_admin_notice'             => __( 'Admin notice', 'silver' ),
		'maintenance_mode_page'                     => __( 'Maintenance page', 'silver' ),
		'maintenance_mode_text'                     => __( 'Maintenance text', 'silver' ),
		'maintenance_mode_excluded_roles'           => __( 'Roles with access', 'silver' ),
		'maintenance_mode_bypass_key'               => __( 'Bypass key', 'silver' ),
		// System.
		'flatsome_studio'                           => __( 'Silver Studio', 'silver' ),
		'rank_math_primary_term'                    => __( 'Rank Math primary category', 'silver' ),
		'rank_math_manages_product_layout_priority' => __( 'Rank Math product layout priority', 'silver' ),
		'rank_math_breadcrumb'                      => __( 'Rank Math breadcrumbs', 'silver' ),
		'release_channel'                           => __( 'Release channel', 'silver' ),
		'of_backup'                                 => __( 'Backup and restore', 'silver' ),
		'of_transfer'                               => __( 'Transfer options', 'silver' ),
	);
}

/**
 * Group titles already in of_options() (Performance, AJAX loading...): their
 * translated title.
 *
 * @return array<string, string> English title => label.
 */
function silver_of_options_group_titles() {
	return array(
		'Page cache'               => __( 'Page cache', 'silver' ),
		'Optimizations'            => __( 'Optimizations', 'silver' ),
		'JavaScript & CSS'         => __( 'JavaScript & CSS', 'silver' ),
		'Generate critical CSS'    => __( 'Generate critical CSS', 'silver' ),
		'Resource hints'           => __( 'Resource hints', 'silver' ),
		'Server'                   => __( 'Server', 'silver' ),
		'AJAX loading'             => __( 'AJAX loading', 'silver' ),
		'Infinite scroll settings' => __( 'Infinite scroll', 'silver' ),
		'Accounts'                 => __( 'Accounts', 'silver' ),
		'Cache'                    => __( 'Cache', 'silver' ),
		'Business Account'         => __( 'Business account', 'silver' ),
	);
}

/**
 * A group title of of_options() (an "info" or untyped item starting with an
 * <h2>) in the common markup, other items as they are.
 *
 * @param array $option Option.
 * @return array
 */
function silver_of_options_normalize_group( $option ) {
	$type = isset( $option['type'] ) ? $option['type'] : '';
	if ( ! in_array( $type, array( 'info', '' ), true ) || ! isset( $option['desc'] ) || ! is_string( $option['desc'] ) ) {
		return $option;
	}
	$map = silver_of_options_group_titles();

	// A note with its own heading (name), as the Instagram ones: same title markup.
	if ( 'info' === $type && ! empty( $option['name'] ) && empty( $option['id'] ) ) {
		$plain = trim( html_entity_decode( wp_strip_all_tags( $option['name'] ) ) );
		$group = silver_of_group( esc_html( isset( $map[ $plain ] ) ? $map[ $plain ] : $plain ), $option['desc'] );
		return array_merge( $option, array(
			'name'  => '',
			'class' => trim( ( isset( $option['class'] ) ? $option['class'] : '' ) . ' silver-of-group-note' ),
			'desc'  => $group['desc'],
		) );
	}

	if ( empty( $option['desc'] ) || ! preg_match( '#^\s*<h2[^>]*>(.*?)</h2>(.*)$#s', $option['desc'], $match ) ) {
		return $option;
	}

	// Title text, and its tag (<span class="of-tag">…</span>) if any.
	$tag   = preg_match( '#<span class="of-tag">.*?</span>#s', $match[1], $tag_match ) ? ' ' . $tag_match[0] : '';
	$plain = trim( html_entity_decode( wp_strip_all_tags( str_replace( $tag ? trim( $tag ) : '', '', $match[1] ) ) ) );
	$title = isset( $map[ $plain ] ) ? $map[ $plain ] : $plain;
	$desc  = preg_replace( '#<p style="font-size:14px">(.*?)</p>#s', '<p>$1</p>', $match[2] );

	return silver_of_group( esc_html( $title ) . $tag, $desc );
}

/**
 * Puts the options of of_options() in the tabs below.
 *
 * Items of a tab: an option ID; array( 'group', title, desc ) for a group
 * title; array( 'tab', old tab name ) for every item of an old tab (its
 * group titles included); array( 'notes', old tab name ) for the items
 * without ID of an old tab (notes, warnings). Missing options (WooCommerce
 * off, plugin not active) are skipped. An option no tab lists stays in the
 * tab that took over its old tab, at the end: nothing ever disappears.
 *
 * @param array $options of_options() list.
 * @return array
 */
function silver_of_options_reorganize( $options ) {
	$by_id  = array();
	$by_tab = array();
	$tab    = '';
	foreach ( $options as $option ) {
		if ( isset( $option['type'] ) && 'heading' === $option['type'] ) {
			$tab            = $option['name'];
			$by_tab[ $tab ] = array();
			continue;
		}
		$by_tab[ $tab ][] = $option;
		if ( ! empty( $option['id'] ) ) {
			$by_id[ $option['id'] ] = $option;
		}
	}

	$notes = static function ( $name ) use ( $by_tab ) {
		return array_values( array_filter( isset( $by_tab[ $name ] ) ? $by_tab[ $name ] : array(), static function ( $option ) {
			return empty( $option['id'] );
		} ) );
	};

	// Maintenance mode: the "Access mode" note goes under the Access title.
	$access_note = '';
	foreach ( $notes( 'Maintenance Mode' ) as $note ) {
		$access_note .= isset( $note['desc'] ) ? $note['desc'] : '';
	}
	$integrations_note = '';
	foreach ( $notes( 'Integrations' ) as $note ) {
		$integrations_note .= isset( $note['desc'] ) ? $note['desc'] : '';
	}

	$layout = array(
		'general'         => array(
			'title' => __( 'General', 'silver' ),
			'items' => array(
				array( 'group', __( 'Features', 'silver' ) ),
				'fl_portfolio',
				'parallax_mobile',
				'flatsome_fallback',
				array( 'group', __( '404 page', 'silver' ) ),
				'404_block',
				array( 'group', __( 'APIs', 'silver' ) ),
				'google_map_api',
			),
		),
		'code'            => array(
			'title' => __( 'Custom code', 'silver' ),
			'items' => array(
				array( 'group', __( 'Scripts', 'silver' ), __( 'Tracking codes, chat widgets... printed on every page.', 'silver' ) ),
				'html_scripts_header',
				'html_scripts_after_body',
				'html_scripts_before_body',
				'html_scripts_footer',
				array( 'group', __( 'Custom CSS', 'silver' ) ),
				'html_custom_css',
				'html_custom_css_tablet',
				'html_custom_css_tablet_landscape',
				'html_custom_css_tablet_portrait',
				'html_custom_css_mobile',
			),
		),
		'performance'     => array(
			'title' => __( 'Performance', 'silver' ),
			'items' => array( array( 'tab', 'Performance' ) ),
		),
		'loading'         => array(
			'title' => __( 'Page loading', 'silver' ),
			'items' => array(
				array( 'group', __( 'Site loader', 'silver' ) ),
				'site_loader',
				'site_loader_color',
				'site_loader_bg',
				array( 'tab', 'Content Delivery' ),
			),
		),
		'search'          => array(
			'title' => __( 'Search', 'silver' ),
			'items' => array(
				array( 'group', __( 'Search field', 'silver' ) ),
				'live_search',
				'search_placeholder',
				'search_result_latency',
				array( 'group', __( 'Results', 'silver' ) ),
				'search_result_posts',
				'search_result_pages',
				'search_result_style',
				array( 'group', __( 'Products', 'silver' ) ),
				'search_products_order_by',
				'search_products_order',
				'search_by_sku',
				'search_by_product_tag',
			),
		),
		'woocommerce'     => array(
			'title' => __( 'WooCommerce', 'silver' ),
			'items' => array(
				array( 'group', __( 'Product features', 'silver' ) ),
				'swatches',
				'additional_variation_images',
				'product_gallery_woocommerce',
				'ajax_add_to_cart',
				'disable_reviews',
				array( 'group', __( 'Catalog mode', 'silver' ), __( 'Turns the shop into a catalog: no cart, no checkout.', 'silver' ) ),
				'catalog_mode',
				'catalog_mode_prices',
				'catalog_mode_sale_badge',
				'catalog_mode_header',
				'catalog_mode_product',
				'catalog_mode_lightbox',
				array( 'group', __( 'Extra content', 'silver' ), __( 'HTML or shortcodes added to every product / shop page.', 'silver' ) ),
				'html_shop_page',
				'tab_title',
				'tab_content',
				'html_before_add_to_cart',
				'html_after_add_to_cart',
				'html_thank_you',
			),
		),
		'instagram'       => array(
			'title' => __( 'Instagram', 'silver' ),
			'items' => array( array( 'tab', 'Instagram' ) ),
		),
		'maintenancemode' => array(
			'title' => __( 'Maintenance mode', 'silver' ),
			'items' => array(
				'maintenance_mode',
				'maintenance_mode_admin_notice',
				'maintenance_mode_page',
				'maintenance_mode_text',
				array( 'group', __( 'Access', 'silver' ), $access_note ),
				'maintenance_mode_excluded_roles',
				'maintenance_mode_bypass_key',
			),
		),
		'system'          => array(
			'title' => __( 'System', 'silver' ),
			'items' => array(
				array( 'group', __( 'Integrations', 'silver' ), $integrations_note ),
				'flatsome_studio',
				'rank_math_primary_term',
				'rank_math_manages_product_layout_priority',
				'rank_math_breadcrumb',
				array( 'group', __( 'Updates', 'silver' ) ),
				'release_channel',
				array( 'notes', 'Updates' ),
				array( 'group', __( 'Backup and import', 'silver' ) ),
				'of_backup',
				'of_transfer',
			),
		),
	);

	// Old tab => new tab, for the options no tab lists.
	$old_to_new = array(
		'Global Settings'   => 'general',
		'Custom CSS'        => 'code',
		'Performance'       => 'performance',
		'Content Delivery'  => 'loading',
		'Site Loader'       => 'loading',
		'Site Search'       => 'search',
		'Instagram'         => 'instagram',
		'Google APIs'       => 'general',
		'Maintenance Mode'  => 'maintenancemode',
		'404 Page'          => 'general',
		'WooCommerce'       => 'woocommerce',
		'Catalog Mode'      => 'woocommerce',
		'Portfolio'         => 'general',
		'Mobile'            => 'general',
		'Integrations'      => 'system',
		'Updates'           => 'system',
		'Backup and Import' => 'system',
	);

	$labels = silver_of_options_labels();
	$used   = array();
	$result = array();
	$tabs   = array();

	foreach ( $layout as $slug => $tab_layout ) {
		$items = array();
		foreach ( $tab_layout['items'] as $item ) {
			if ( is_string( $item ) ) {
				if ( isset( $by_id[ $item ] ) ) {
					$items[]        = $by_id[ $item ];
					$used[ $item ]  = true;
				}
			} elseif ( 'group' === $item[0] ) {
				$items[] = silver_of_group( esc_html( $item[1] ), isset( $item[2] ) ? $item[2] : '' );
			} elseif ( 'tab' === $item[0] ) {
				foreach ( isset( $by_tab[ $item[1] ] ) ? $by_tab[ $item[1] ] : array() as $option ) {
					$items[] = $option;
					if ( ! empty( $option['id'] ) ) {
						$used[ $option['id'] ] = true;
					}
				}
			} elseif ( 'notes' === $item[0] ) {
				$items = array_merge( $items, $notes( $item[1] ) );
			}
		}
		$tabs[ $slug ] = $items;
	}

	// Options no tab lists (new options, plugin options): never lost.
	foreach ( $by_tab as $old_tab => $old_items ) {
		$slug = isset( $old_to_new[ $old_tab ] ) ? $old_to_new[ $old_tab ] : 'system';
		foreach ( $old_items as $option ) {
			if ( ! empty( $option['id'] ) && empty( $used[ $option['id'] ] ) ) {
				$tabs[ $slug ][]         = $option;
				$used[ $option['id'] ]   = true;
			}
		}
	}

	foreach ( $layout as $slug => $tab_layout ) {
		// A group title followed by nothing (missing options) is dropped.
		$items = array();
		$count = count( $tabs[ $slug ] );
		foreach ( $tabs[ $slug ] as $index => $option ) {
			$is_group = isset( $option['class'] ) && 'silver-of-group' === $option['class'];
			if ( $is_group ) {
				$next = $index + 1 < $count ? $tabs[ $slug ][ $index + 1 ] : null;
				if ( ! $next || ( isset( $next['class'] ) && 'silver-of-group' === $next['class'] ) ) {
					continue;
				}
			}
			$option = silver_of_options_normalize_group( $option );
			if ( ! empty( $option['id'] ) && isset( $labels[ $option['id'] ] ) ) {
				$option['name'] = esc_html( $labels[ $option['id'] ] );
			}
			$items[] = $option;
		}
		if ( ! $items ) {
			continue;
		}

		$result[] = array(
			'name' => $tab_layout['title'],
			'slug' => $slug,
			'type' => 'heading',
		);
		$result = array_merge( $result, $items );
	}

	return $result;
}
