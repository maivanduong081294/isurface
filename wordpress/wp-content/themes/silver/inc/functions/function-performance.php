<?php
/**
 * Performance options (Advanced > Performance).
 *
 * "Enable performance optimizations" (perf_enabled) is the main switch: when
 * it is off, every option of the tab is ignored (their values are kept, so
 * turning it on again restores them). Use it to check quickly whether an
 * optimization breaks something.
 *
 * Read the options with silver_perf(), never get_theme_mod(), so the main
 * switch applies to them.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the performance optimizations are on (main switch).
 *
 * @return bool
 */
function silver_perf_enabled() {
	return (bool) apply_filters( 'silver_performance_enabled', (bool) get_theme_mod( 'perf_enabled', 1 ) );
}

/**
 * A performance option, or false when the main switch is off.
 *
 * @param string $name    Option (theme mod) name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function silver_perf( $name, $default = 0 ) {
	return silver_perf_enabled() ? get_theme_mod( $name, $default ) : false;
}

/**
 * Whether the request is a page of the site for visitors (not wp-admin, the
 * Customizer preview, UX Builder, a feed...): where the optimizations that
 * change the markup of the assets apply.
 *
 * @return bool
 */
function silver_perf_is_frontend() {
	if ( is_admin() || wp_doing_ajax() || is_customize_preview() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}
	if ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() ) {
		return false;
	}

	return true;
}

/**
 * A list option (comma or new line separated) as an array of trimmed items.
 *
 * @param string $name    Option name.
 * @param string $default Value while the option was never saved.
 * @return string[]
 */
function silver_perf_list( $name, $default = '' ) {
	$value = (string) silver_perf( $name, $default );

	return array_values( array_filter( array_map( 'trim', preg_split( '/[\s,]+/', $value ) ) ) );
}

/**
 * Whether a handle matches one of the patterns ("*" is any text).
 *
 * @param string   $handle   Script or style handle.
 * @param string[] $patterns Handles, with * wildcards.
 * @return bool
 */
function silver_perf_handle_matches( $handle, $patterns ) {
	foreach ( $patterns as $pattern ) {
		$regex = '/^' . str_replace( '\*', '.*', preg_quote( $pattern, '/' ) ) . '$/i';
		if ( preg_match( $regex, $handle ) ) {
			return true;
		}
	}

	return false;
}

/*
 * Defer JavaScript ----------------------------------------------------------
 *
 * Gives every script the "defer" loading strategy of WordPress (6.3+), which
 * keeps a script blocking when deferring it is not safe (a blocking script
 * depends on it, an inline script runs right after it...). Scripts that are
 * already async keep it. Excluded handles stay as they are: jquery-core by
 * default, for jQuery code written directly in the page.
 */

/**
 * Adds the defer strategy to the registered scripts (before the head scripts,
 * then again before the footer ones for scripts registered later).
 */
function silver_perf_defer_scripts() {
	if ( ! silver_perf( 'perf_defer_js' ) || ! silver_perf_is_frontend() ) {
		return;
	}

	$exclude = silver_perf_list( 'perf_defer_js_exclude', 'jquery-core' );
	$scripts = wp_scripts();

	foreach ( $scripts->registered as $handle => $script ) {
		// Aliases (jquery) have no file; async scripts stay async.
		if ( empty( $script->src ) || $scripts->get_data( $handle, 'strategy' ) ) {
			continue;
		}
		if ( $exclude && silver_perf_handle_matches( $handle, $exclude ) ) {
			continue;
		}
		$scripts->add_data( $handle, 'strategy', 'defer' );
	}
}
add_action( 'wp_print_scripts', 'silver_perf_defer_scripts', PHP_INT_MAX );
add_action( 'wp_print_footer_scripts', 'silver_perf_defer_scripts', 1 );

/*
 * Non-blocking CSS ---------------------------------------------------------
 */

/**
 * Loads the listed stylesheets without blocking the rendering: media="print"
 * until loaded, then the original media (with a <noscript> fallback).
 *
 * @param string $html   Link tag.
 * @param string $handle Style handle.
 * @param string $href   Stylesheet URL.
 * @param string $media  Media attribute.
 * @return string
 */
function silver_perf_async_style( $html, $handle, $href, $media ) {
	static $handles = null;

	if ( null === $handles ) {
		$handles = silver_perf_is_frontend() ? silver_perf_list( 'perf_async_css' ) : array();
	}
	if ( ! $handles || ! silver_perf_handle_matches( $handle, $handles ) || false !== strpos( $html, 'onload=' ) ) {
		return $html;
	}

	return silver_perf_async_link( $html, $media );
}
add_filter( 'style_loader_tag', 'silver_perf_async_style', 20, 4 );

/**
 * A stylesheet link that does not block the display: media="print" until
 * loaded, then the original media; <noscript> fallback.
 *
 * @param string $html  Link tag.
 * @param string $media Media attribute.
 * @return string
 */
function silver_perf_async_link( $html, $media ) {
	$media = $media ? $media : 'all';
	if ( 'print' === $media ) {
		return $html;
	}

	$async = preg_replace( '/\smedia=([\'"])[^\'"]*\1/', '', $html );
	$async = preg_replace(
		'/<link\s/',
		'<link media="print" onload="this.onload=null;this.media=\'' . esc_attr( $media ) . '\'" ',
		$async,
		1
	);

	return $async . '<noscript>' . trim( $html ) . "</noscript>\n";
}

/*
 * Inline small CSS ---------------------------------------------------------
 */

/**
 * Local file of a stylesheet URL of this site, or '' (other site, missing).
 *
 * @param string $href Stylesheet URL.
 * @return string
 */
function silver_perf_local_css_file( $href ) {
	$href = html_entity_decode( (string) $href );
	if ( 0 === strpos( $href, '//' ) ) {
		$href = ( is_ssl() ? 'https:' : 'http:' ) . $href;
	}

	$url  = wp_parse_url( $href );
	$site = wp_parse_url( site_url( '/' ) );
	if ( empty( $url['path'] ) || ( ! empty( $url['host'] ) && strtolower( $url['host'] ) !== strtolower( (string) $site['host'] ) ) ) {
		return '';
	}

	$base = isset( $site['path'] ) ? trailingslashit( $site['path'] ) : '/';
	if ( 0 !== strpos( $url['path'], $base ) || '.css' !== strtolower( substr( $url['path'], -4 ) ) ) {
		return '';
	}

	$file = realpath( ABSPATH . rawurldecode( substr( $url['path'], strlen( $base ) ) ) );
	$root = realpath( ABSPATH );

	return $file && $root && 0 === strpos( $file, $root ) && is_file( $file ) ? $file : '';
}

/**
 * Prints the small local stylesheets in the page (<style>) instead of a
 * <link>: one request less each, which blocks the display. Relative url()
 * (fonts, images) become absolute. Files with @import are left as they are.
 *
 * @param string $html   Link tag.
 * @param string $handle Style handle.
 * @param string $href   Stylesheet URL.
 * @param string $media  Media attribute.
 * @return string
 */
function silver_perf_inline_style( $html, $handle, $href, $media ) {
	static $limit = null;

	if ( null === $limit ) {
		$limit = silver_perf_is_frontend() ? max( 0, (int) silver_perf( 'perf_inline_css', 0 ) ) : 0;
	}
	// Not the non-blocking ones (onload) or an alternate stylesheet.
	if ( ! $limit || false !== strpos( $html, 'onload=' ) || false !== strpos( $html, 'alternate' ) ) {
		return $html;
	}

	$file = silver_perf_local_css_file( $href );
	$size = $file ? filesize( $file ) : 0;
	if ( ! $size || $size > $limit ) {
		return $html;
	}

	$css = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( '' === trim( $css ) || false !== stripos( $css, '@import' ) || false !== stripos( $css, '</style' ) ) {
		return $html;
	}

	// url(../fonts/x.woff2) is relative to the CSS file: make it absolute.
	$dir = trailingslashit( dirname( strtok( html_entity_decode( $href ), '?' ) ) );
	$css = preg_replace_callback(
		'/url\(\s*([\'"]?)(?!data:|https?:|\/\/|\/|#|about:)([^\'")]+)\1\s*\)/i',
		static function ( $match ) use ( $dir ) {
			return 'url(' . $match[1] . $dir . $match[2] . $match[1] . ')';
		},
		$css
	);
	$css = preg_replace( '/@charset\s+[^;]+;/i', '', $css );

	$media_attr = $media && 'all' !== $media ? ' media="' . esc_attr( $media ) . '"' : '';

	return '<style id="' . esc_attr( $handle ) . '-css"' . $media_attr . '>' . trim( $css ) . "</style>\n";
}
// After the non-blocking CSS (20).
add_filter( 'style_loader_tag', 'silver_perf_inline_style', 30, 4 );

/*
 * Icon font display ---------------------------------------------------------
 *
 * Font Awesome declares its fonts with `font-display: block`: the icons stay
 * hidden until the font is downloaded (PageSpeed: "Font display"). Its file
 * is not changed: a copy with the chosen font-display (and absolute font
 * URLs) is written once in wp-content/cache/silver-assets/ and loaded
 * instead. (Declaring the @font-face rules again after the file is not
 * enough: Chrome loads both, the "block" one too.) A new copy is made when
 * the Font Awesome file changes; the original is loaded when the copy can
 * not be written.
 */

/**
 * URL of the Font Awesome copy with this font-display, '' when it can not be
 * written.
 *
 * @param string $display swap or fallback.
 * @return string
 */
function silver_perf_icon_font_css_url( $display ) {
	$source = get_template_directory() . '/assets/css/icons/fontawesome/css/all.min.css';
	$base   = get_template_directory_uri() . '/assets/css/icons/fontawesome/css/';
	if ( ! is_file( $source ) ) {
		return '';
	}

	$dir  = WP_CONTENT_DIR . '/cache/silver-assets';
	$name = 'fontawesome-' . $display . '-' . substr( md5( filemtime( $source ) . '|' . $base ), 0, 12 ) . '.css';
	$file = $dir . '/' . $name;

	if ( ! is_file( $file ) ) {
		if ( ! wp_mkdir_p( $dir ) ) {
			return '';
		}

		$css = (string) file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		// url(../webfonts/x.woff2) is relative to the original file.
		$css = preg_replace_callback(
			'/url\(\s*([\'"]?)(?!data:|https?:|\/\/|\/)([^\'")]+)\1\s*\)/i',
			static function ( $match ) use ( $base ) {
				return 'url(' . $match[1] . $base . $match[2] . $match[1] . ')';
			},
			$css
		);
		$css = preg_replace( '/font-display\s*:\s*[a-z-]+/i', 'font-display:' . $display, $css );

		// Written aside then moved: never half a file.
		$tmp = $file . '.' . uniqid( '', true ) . '.tmp';
		if ( false === @file_put_contents( $tmp, $css ) || ! @rename( $tmp, $file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
			return '';
		}
	}

	return content_url( '/cache/silver-assets/' . $name );
}

/**
 * Loads the Font Awesome copy with the chosen font-display.
 */
function silver_perf_icon_font_display() {
	$display = (string) silver_perf( 'perf_icon_font_display', '' );
	if ( ! in_array( $display, array( 'swap', 'fallback' ), true ) || ! silver_perf_is_frontend() ) {
		return;
	}

	$styles = wp_styles();
	if ( empty( $styles->registered['silver-fontawesome'] ) ) {
		return;
	}

	$url = silver_perf_icon_font_css_url( $display );
	if ( $url ) {
		// Same handle: dependencies, non-blocking CSS... keep working.
		$styles->registered['silver-fontawesome']->src = $url;
	}
}
// After silver_add_icons_css() (90), which registers Font Awesome.
add_action( 'wp_enqueue_scripts', 'silver_perf_icon_font_display', 91 );

/*
 * Resource hints: preconnect and preload -----------------------------------
 */

/**
 * Preconnect (and DNS prefetch) to the listed origins. A line may end with
 * " crossorigin" (fonts).
 *
 * @param array  $urls          Hints.
 * @param string $relation_type preconnect, dns-prefetch...
 * @return array
 */
function silver_perf_resource_hints( $urls, $relation_type ) {
	if ( ( 'preconnect' !== $relation_type && 'dns-prefetch' !== $relation_type ) || ! silver_perf_is_frontend() ) {
		return $urls;
	}

	$lines = preg_split( '/\r\n|\r|\n|,/', (string) silver_perf( 'perf_preconnect', '' ) );

	foreach ( $lines as $line ) {
		$line        = trim( $line );
		$crossorigin = (bool) preg_match( '/\s+crossorigin$/i', $line );
		$url         = esc_url_raw( trim( preg_replace( '/\s+crossorigin$/i', '', $line ) ) );
		$parts       = $url ? wp_parse_url( $url ) : false;

		if ( empty( $parts['host'] ) ) {
			continue;
		}

		$origin = ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'https' ) . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );

		if ( 'preconnect' === $relation_type ) {
			$urls[] = $crossorigin ? array( 'href' => $origin, 'crossorigin' => 'anonymous' ) : $origin;
		} else {
			$urls[] = '//' . $parts['host'];
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'silver_perf_resource_hints', 10, 2 );

/**
 * Preload link tag of a URL; the type comes from the file extension.
 *
 * @param string $url URL.
 * @return string Empty for an unknown type.
 */
function silver_perf_preload_tag( $url ) {
	$url  = esc_url( $url );
	$path = $url ? (string) wp_parse_url( html_entity_decode( $url ), PHP_URL_PATH ) : '';
	$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

	$fonts  = array( 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf', 'otf' => 'font/otf' );
	$images = array( 'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg' );

	if ( isset( $fonts[ $ext ] ) ) {
		return '<link rel="preload" href="' . $url . '" as="font" type="' . $fonts[ $ext ] . '" crossorigin>' . "\n";
	}
	if ( 'css' === $ext ) {
		return '<link rel="preload" href="' . $url . '" as="style">' . "\n";
	}
	if ( 'js' === $ext ) {
		return '<link rel="preload" href="' . $url . '" as="script">' . "\n";
	}
	if ( in_array( $ext, $images, true ) ) {
		// The image that is the largest on the first screen (LCP). An image of
		// the Media Library gets the srcset / sizes the <img> uses: without
		// them the browser downloads the href file (often the full size) and
		// then, for the <img>, the srcset file: twice.
		$responsive = silver_perf_preload_image_srcset( html_entity_decode( $url ) );
		$extra      = $responsive
			? ' imagesrcset="' . esc_attr( $responsive['srcset'] ) . '" imagesizes="' . esc_attr( $responsive['sizes'] ) . '"'
			: '';

		return '<link rel="preload" href="' . $url . '" as="image"' . $extra . ' fetchpriority="high">' . "\n";
	}

	return '';
}

/**
 * srcset / sizes of a Media Library image URL (full size or one of its sizes,
 * e.g. photo-768x432.jpg), as wp_get_attachment_image() prints them for that
 * size. Cached a day per URL.
 *
 * @param string $url Image URL.
 * @return array|null srcset, sizes; null when not a Media Library image or
 *                    when it has a single file.
 */
function silver_perf_preload_image_srcset( $url ) {
	$key    = 'silver_preload_img_' . md5( $url );
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return $cached ? $cached : null;
	}

	$result = '';
	$id     = attachment_url_to_postid( $url );
	if ( ! $id ) {
		// A size of the image (-768x432 before the extension): the original file.
		$id = attachment_url_to_postid( preg_replace( '/-\d+x\d+(?=(\.[a-z0-9]+)+$)/i', '', $url ) );
	}

	$meta = $id ? wp_get_attachment_metadata( $id ) : false;
	if ( $meta && ! empty( $meta['width'] ) ) {
		// Width / height of the file of this URL (a size or the original).
		$file   = wp_basename( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		$width  = (int) $meta['width'];
		$height = (int) $meta['height'];
		foreach ( isset( $meta['sizes'] ) ? (array) $meta['sizes'] : array() as $size ) {
			if ( isset( $size['file'] ) && $size['file'] === $file ) {
				$width  = (int) $size['width'];
				$height = (int) $size['height'];
				break;
			}
		}

		$srcset = wp_calculate_image_srcset( array( $width, $height ), $url, $meta, $id );
		$sizes  = wp_calculate_image_sizes( array( $width, $height ), $url, $meta, $id );
		if ( $srcset && $sizes ) {
			$result = array( 'srcset' => $srcset, 'sizes' => $sizes );
		}
	}

	set_transient( $key, $result, DAY_IN_SECONDS );

	return $result ? $result : null;
}

/**
 * Preload: the files of every page, and those of the homepage.
 */
function silver_perf_preload() {
	if ( ! silver_perf_is_frontend() ) {
		return;
	}

	$urls = preg_split( '/\r\n|\r|\n/', (string) silver_perf( 'perf_preload', '' ) );
	if ( is_front_page() ) {
		$urls = array_merge( $urls, preg_split( '/\r\n|\r|\n/', (string) silver_perf( 'perf_preload_home', '' ) ) );
	}

	foreach ( array_unique( array_filter( array_map( 'trim', $urls ) ) ) as $url ) {
		echo silver_perf_preload_tag( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
	}
}
add_action( 'wp_head', 'silver_perf_preload', 2 );

/*
 * Removals -----------------------------------------------------------------
 */

/**
 * Head clean up, embeds, emoji: core actions removed before they run.
 */
function silver_perf_remove_core_output() {
	if ( is_admin() ) {
		return;
	}

	// Emoji: the detection script and the styles. Since WordPress 6.4 the
	// styles are enqueued (wp_enqueue_emoji_styles), print_emoji_styles is
	// only kept for old code.
	if ( silver_perf( 'disable_emoji' ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		add_filter( 'emoji_svg_url', '__return_false' );
	}

	// <head>: RSD (XML-RPC clients), generator (WordPress / WooCommerce
	// versions), shortlink, previous / next post links.
	if ( silver_perf( 'perf_clean_head' ) ) {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
	}

	// Embeds of this site's posts in other sites (discovery links, script).
	if ( silver_perf( 'perf_disable_embeds' ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 4 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}
}
add_action( 'init', 'silver_perf_remove_core_output' );

/**
 * Styles and scripts removed on the site.
 */
function silver_perf_dequeue_assets() {
	if ( ! silver_perf_is_frontend() ) {
		return;
	}

	if ( silver_perf( 'perf_disable_embeds' ) ) {
		wp_dequeue_script( 'wp-embed' );
	}

	// Dashicons: only the admin bar needs them (logged in). Dequeued only:
	// a stylesheet that depends on them still loads them.
	if ( silver_perf( 'perf_disable_dashicons' ) && ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}

	if ( silver_perf( 'perf_disable_global_styles' ) ) {
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'silver_perf_dequeue_assets', PHP_INT_MAX );

/**
 * Global styles are enqueued again in the footer (wp_footer, 1).
 */
function silver_perf_dequeue_footer_global_styles() {
	if ( silver_perf( 'perf_disable_global_styles' ) && silver_perf_is_frontend() ) {
		wp_dequeue_style( 'global-styles' );
	}
}
add_action( 'wp_footer', 'silver_perf_dequeue_footer_global_styles', 2 );

// Page cache (served as soon as this file loads) and server rules.
require __DIR__ . '/performance/server-rules.php';
require __DIR__ . '/performance/page-cache.php';

require __DIR__ . '/performance/critical-css.php';
