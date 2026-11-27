<?php
/**
 * Critical CSS (Advanced > Performance > Critical CSS).
 *
 * The theme CSS of the first screen is printed in the page and the full theme
 * stylesheets load without blocking the display: the page shows at once and
 * looks as before (the full files are not cut; once loaded they apply again,
 * in their order).
 *
 * Made in the browser of the admin (button "Generate critical CSS"): the
 * pages of the site open in hidden frames at 3 screen sizes, the rules of the
 * theme stylesheets that style (or hide) an element of the first screen are
 * kept, in the order of the file (assets/js/src/admin/silver-critical-css.js).
 * Saved in wp-content/cache/silver-critical/: no Node, any hosting.
 *
 * A stylesheet changed since (theme update) loads as usual until generated
 * again (its md5 is checked).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Folder of the critical CSS files.
 *
 * @return string
 */
function silver_critical_dir() {
	return WP_CONTENT_DIR . '/cache/silver-critical';
}

/**
 * Theme stylesheets handled: handle => file in the theme (assets/css/...).
 *
 * @return array
 */
function silver_critical_stylesheets() {
	$sheets = array(
		'silver-main'   => 'assets/css/silver.css',
		'silver-shop'   => 'assets/css/silver-shop.css',
		'silver-modern' => 'assets/css/silver-modern.css',
		'silver-vivid'  => 'assets/css/silver-vivid.css',
	);

	return array_filter( $sheets, static function ( $file ) {
		return is_file( get_template_directory() . '/' . $file );
	} );
}

/**
 * Manifest of the generated files (generated, urls, sheets: handle => md5 of
 * the stylesheet it was made from, size).
 *
 * @return array|null
 */
function silver_critical_manifest() {
	$file = silver_critical_dir() . '/manifest.json';
	$data = is_file( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions

	return is_array( $data ) && ! empty( $data['sheets'] ) ? $data : null;
}

/**
 * md5 of a file, computed again only when the file changes.
 *
 * @param string $file File.
 * @return string
 */
function silver_critical_file_md5( $file ) {
	$key = 'silver_md5_' . md5( $file . '|' . filemtime( $file ) . '|' . filesize( $file ) );
	$md5 = get_transient( $key );
	if ( false === $md5 ) {
		$md5 = (string) md5_file( $file );
		set_transient( $key, $md5, MONTH_IN_SECONDS );
	}
	return $md5;
}

/**
 * Critical files usable now: handle => file (made from the current version
 * of their stylesheet).
 *
 * @return array
 */
function silver_critical_fresh_sheets() {
	$manifest = silver_critical_manifest();
	$fresh    = array();

	if ( ! $manifest ) {
		return $fresh;
	}

	foreach ( silver_critical_stylesheets() as $handle => $source ) {
		$file = silver_critical_dir() . '/' . $handle . '.css';
		if ( isset( $manifest['sheets'][ $handle ]['md5'] ) && is_file( $file )
			&& silver_critical_file_md5( get_template_directory() . '/' . $source ) === $manifest['sheets'][ $handle ]['md5'] ) {
			$fresh[ $handle ] = $file;
		}
	}

	return $fresh;
}

/**
 * Whether the stylesheets changed since the critical CSS was generated.
 *
 * @return bool
 */
function silver_critical_is_stale() {
	$manifest = silver_critical_manifest();
	return $manifest && count( silver_critical_fresh_sheets() ) < count( array_intersect_key( silver_critical_stylesheets(), $manifest['sheets'] ) );
}

/**
 * A request of the generator (hidden frame of the admin): the page with the
 * full stylesheets as usual, no admin bar.
 *
 * @return bool
 */
function silver_critical_is_generating() {
	return isset( $_GET['silver_critical'] ) && is_user_logged_in() && current_user_can( 'manage_options' ); // phpcs:ignore WordPress.Security.NonceVerification
}

/**
 * No admin bar in the generator frames (same layout as visitors).
 *
 * @param bool $show Show the admin bar.
 * @return bool
 */
function silver_critical_admin_bar( $show ) {
	return silver_critical_is_generating() ? false : $show;
}
add_filter( 'show_admin_bar', 'silver_critical_admin_bar', 100 );

/*
 * On the site --------------------------------------------------------------
 */

/**
 * Critical rules where the stylesheet was, the full stylesheet without
 * blocking the display.
 *
 * @param string $html   Link tag.
 * @param string $handle Style handle.
 * @param string $href   Stylesheet URL.
 * @param string $media  Media attribute.
 * @return string
 */
function silver_critical_style_tag( $html, $handle, $href, $media ) {
	static $sheets = null;

	if ( null === $sheets ) {
		$sheets = silver_perf( 'perf_critical_css' ) && silver_perf_is_frontend() && ! is_rtl() && ! silver_critical_is_generating()
			? silver_critical_fresh_sheets()
			: array();
	}
	if ( ! isset( $sheets[ $handle ] ) || false !== strpos( $html, 'onload=' ) ) {
		return $html;
	}

	$css = (string) file_get_contents( $sheets[ $handle ] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( '' === trim( $css ) || false !== stripos( $css, '</style' ) ) {
		return $html;
	}

	// url() are relative to the full stylesheet.
	$dir = trailingslashit( dirname( strtok( html_entity_decode( $href ), '?' ) ) );
	$css = preg_replace_callback(
		'/url\(\s*([\'"]?)(?!data:|https?:|\/\/|\/|#|about:)([^\'")]+)\1\s*\)/i',
		static function ( $match ) use ( $dir ) {
			return 'url(' . $match[1] . $dir . $match[2] . $match[1] . ')';
		},
		$css
	);

	// Non-blocking full stylesheet; once loaded, scripts that measured the
	// page (sliders, sticky header) measure it again.
	$media = $media ? $media : 'all';
	$async = preg_replace( '/\smedia=([\'"])[^\'"]*\1/', '', $html );
	$async = preg_replace(
		'/<link\s/',
		'<link media="print" onload="this.onload=null;this.media=\'' . esc_attr( $media ) . '\';window.dispatchEvent(new Event(\'resize\'))" ',
		$async,
		1
	);

	return '<style id="' . esc_attr( $handle ) . '-critical-css">' . $css . "</style>\n"
		. $async . '<noscript>' . trim( $html ) . "</noscript>\n";
}
// Before the non-blocking (20) and inline (30) filters.
add_filter( 'style_loader_tag', 'silver_critical_style_tag', 10, 4 );

/*
 * Generator (wp-admin) -----------------------------------------------------
 */

/**
 * Pages the generator opens: homepage, a post, a page, shop, a product, a
 * product category, a post category (the ones that exist).
 *
 * @return string[]
 */
function silver_critical_sample_urls() {
	$urls = array( home_url( '/' ) );

	$post = get_posts( array( 'post_type' => 'post', 'numberposts' => 1, 'post_status' => 'publish', 'fields' => 'ids' ) );
	if ( $post ) {
		$urls[] = get_permalink( $post[0] );
	}

	$front = (int) get_option( 'page_on_front' );
	$page  = get_posts( array( 'post_type' => 'page', 'numberposts' => 1, 'post_status' => 'publish', 'fields' => 'ids', 'exclude' => array_filter( array( $front, function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'cart' ) : 0, function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'checkout' ) : 0, function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'myaccount' ) : 0 ) ) ) );
	if ( $page ) {
		$urls[] = get_permalink( $page[0] );
	}

	$category = get_terms( array( 'taxonomy' => 'category', 'number' => 1, 'hide_empty' => true ) );
	if ( $category && ! is_wp_error( $category ) ) {
		$urls[] = get_term_link( reset( $category ) );
	}

	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$urls[] = wc_get_page_permalink( 'shop' );

		$product = get_posts( array( 'post_type' => 'product', 'numberposts' => 1, 'post_status' => 'publish', 'fields' => 'ids' ) );
		if ( $product ) {
			$urls[] = get_permalink( $product[0] );
		}

		$product_cat = get_terms( array( 'taxonomy' => 'product_cat', 'number' => 1, 'hide_empty' => true ) );
		if ( $product_cat && ! is_wp_error( $product_cat ) ) {
			$urls[] = get_term_link( reset( $product_cat ) );
		}
	}

	$urls = array_values( array_unique( array_filter( $urls, 'is_string' ) ) );

	return (array) apply_filters( 'silver_critical_sample_urls', $urls );
}

/**
 * Status of the option (Advanced > Performance), HTML.
 *
 * @return string
 */
function silver_critical_status_html() {
	$manifest = silver_critical_manifest();

	if ( ! $manifest ) {
		return '<span style="color:#b32d2e">Not generated yet: click "Generate critical CSS" (the option does nothing until then).</span>';
	}

	$parts = array();
	$fresh = silver_critical_fresh_sheets();
	foreach ( $manifest['sheets'] as $handle => $info ) {
		$parts[] = esc_html( $handle ) . ' ' . size_format( (int) $info['size'], 1 )
			. ( isset( $fresh[ $handle ] ) ? '' : ' <strong style="color:#b32d2e">(theme CSS changed since: loads as usual until generated again)</strong>' );
	}

	return '<span style="color:#008a20">Generated ' . esc_html( wp_date( 'd/m/Y H:i', (int) $manifest['generated'] ) )
		. ' from ' . count( (array) $manifest['urls'] ) . ' page(s), ' . size_format( (int) $manifest['gzip'], 1 ) . ' compressed: ' . implode( ', ', $parts ) . '.</span>';
}

/**
 * Generator script on the Advanced options page.
 *
 * @param string $hook Admin page.
 */
function silver_critical_admin_script( $hook ) {
	if ( ! isset( $_GET['page'] ) || 'optionsframework' !== $_GET['page'] || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$file = '/assets/js/admin/silver-critical-css.js';
	wp_enqueue_script( 'silver-critical-css', get_template_directory_uri() . $file, array(), filemtime( get_template_directory() . $file ), true );

	$sheets = array();
	foreach ( silver_critical_stylesheets() as $handle => $source ) {
		$sheets[ $handle ] = '/' . $source;
	}

	wp_localize_script( 'silver-critical-css', 'silverCritical', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'silver_critical_css' ),
		'urls'      => silver_critical_sample_urls(),
		'sheets'    => $sheets,
		'viewports' => array(
			array( 'width' => 375, 'height' => 812 ),
			array( 'width' => 768, 'height' => 1024 ),
			array( 'width' => 1366, 'height' => 900 ),
		),
	) );
}
add_action( 'admin_enqueue_scripts', 'silver_critical_admin_script' );

/**
 * Saves the critical CSS sent by the generator.
 */
function silver_critical_save() {
	check_ajax_referer( 'silver_critical_css', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Not allowed.' ), 403 );
	}

	$posted = isset( $_POST['css'] ) && is_array( $_POST['css'] ) ? wp_unslash( $_POST['css'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- CSS, checked below.
	$urls   = isset( $_POST['urls'] ) && is_array( $_POST['urls'] ) ? array_map( 'esc_url_raw', wp_unslash( $_POST['urls'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$sheets = silver_critical_stylesheets();
	$dir    = silver_critical_dir();

	if ( ! wp_mkdir_p( $dir ) ) {
		wp_send_json_error( array( 'message' => 'wp-content/cache/silver-critical/ can not be created.' ) );
	}

	$manifest = array( 'generated' => time(), 'urls' => array_values( $urls ), 'sheets' => array(), 'gzip' => 0 );

	foreach ( $posted as $handle => $css ) {
		$handle = (string) $handle;
		$css    = (string) $css;
		// Known stylesheets only; CSS can not close the <style> it goes in.
		if ( ! isset( $sheets[ $handle ] ) || '' === trim( $css ) || strlen( $css ) > 300 * KB_IN_BYTES || false !== stripos( $css, '</style' ) || false !== strpos( $css, '<' ) ) {
			continue;
		}
		if ( false === file_put_contents( $dir . '/' . $handle . '.css', $css ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
			wp_send_json_error( array( 'message' => 'The files can not be written in wp-content/cache/silver-critical/.' ) );
		}
		$manifest['sheets'][ $handle ] = array(
			'md5'  => md5_file( get_template_directory() . '/' . $sheets[ $handle ] ),
			'size' => strlen( $css ),
		);
		$manifest['gzip'] += function_exists( 'gzencode' ) ? strlen( gzencode( $css, 6 ) ) : strlen( $css );
	}

	// Files of stylesheets not sent this time: removed.
	foreach ( array_keys( $sheets ) as $handle ) {
		if ( ! isset( $manifest['sheets'][ $handle ] ) && is_file( $dir . '/' . $handle . '.css' ) ) {
			wp_delete_file( $dir . '/' . $handle . '.css' );
		}
	}

	file_put_contents( $dir . '/manifest.json', wp_json_encode( $manifest ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( function_exists( 'silver_page_cache_clear' ) ) {
		silver_page_cache_clear();
	}

	wp_send_json_success( array( 'status' => silver_critical_status_html() ) );
}
add_action( 'wp_ajax_silver_save_critical_css', 'silver_critical_save' );

/**
 * Warning in wp-admin when the option is on and the theme CSS changed since
 * the critical CSS was made.
 */
function silver_critical_stale_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! silver_perf( 'perf_critical_css' ) || ! silver_critical_is_stale() ) {
		return;
	}
	$url = admin_url( 'admin.php?page=optionsframework&tab=of-option-performance' );
	echo '<div class="notice notice-warning"><p>' . wp_kses_post( sprintf(
		/* translators: %s: link to the Performance options */
		__( 'Critical CSS: the theme CSS changed since it was generated, the theme stylesheets load as usual (blocking). <a href="%s">Generate it again</a>.', 'silver' ),
		esc_url( $url )
	) ) . '</p></div>';
}
add_action( 'admin_notices', 'silver_critical_stale_notice' );
