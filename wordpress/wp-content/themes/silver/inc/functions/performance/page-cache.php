<?php
/**
 * Page cache (Advanced > Performance > Page cache).
 *
 * The HTML of the pages seen by visitors who are not logged in is saved in
 * wp-content/cache/silver-page/ and sent back as soon as the theme loads, so
 * WordPress does not run the query, the template and the shortcodes again.
 * (A cache served before WordPress loads needs advanced-cache.php and
 * WP_CACHE in wp-config.php: out of what a theme should change.)
 *
 * Not cached: logged-in users, a cart that is not empty or a WooCommerce
 * session, comment authors, password-protected pages, URLs with parameters
 * WordPress does not use to choose the page (tracking ones, utm_*, fbclid...,
 * are ignored; ?p=, ?page_id= are part of the key), POST, 404, search, feeds, cart /
 * checkout / account pages, responses that set a cookie, the URLs of
 * "Do not cache these URLs".
 *
 * Emptied when content changes (posts, pages, blocks, products and their
 * stock, menus, widgets, comments, terms, Customizer, theme options), with
 * the "Clear page cache" button of the admin bar, and after its lifetime
 * (12 hours at most: the security codes (nonces) of the forms stay valid).
 *
 * Check: response header X-Silver-Cache: MISS (saved), then HIT.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Folder of the page cache.
 *
 * @return string
 */
function silver_page_cache_dir() {
	return WP_CONTENT_DIR . '/cache/silver-page';
}

/**
 * Lifetime in seconds, 0 when the page cache is off.
 *
 * @return int
 */
function silver_page_cache_lifetime() {
	return max( 0, min( 12 * HOUR_IN_SECONDS, (int) silver_perf( 'perf_page_cache', 0 ) ) );
}

/**
 * Query parameters that do not change the page (ads, analytics): ignored.
 *
 * @return string[]
 */
function silver_page_cache_ignored_params() {
	return (array) apply_filters( 'silver_page_cache_ignored_params', array(
		'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_id',
		'fbclid', 'gclid', 'gbraid', 'wbraid', 'msclkid', 'ttclid', 'twclid', 'srsltid',
		'_ga', '_gl', 'mc_cid', 'mc_eid', 'ref',
	) );
}

/**
 * Why the current request can not use the cache ('' = it can), from the
 * request only (it runs before WordPress parses it).
 *
 * @return string
 */
function silver_page_cache_request_bypass() {
	if ( PHP_SAPI === 'cli' ) {
		return 'cli';
	}

	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : 'GET'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( 'GET' !== $method && 'HEAD' !== $method ) {
		return 'method';
	}

	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return 'context';
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );

	if ( preg_match( '#(^|/)(wp-admin|wp-json|wp-login\.php|wp-cron\.php|xmlrpc\.php|wp-comments-post\.php)(/|$)#', $path ) || preg_match( '#\.(xml|xsl|txt|php)$#i', $path ) ) {
		return 'url';
	}

	// Parameters: checked when the page is saved (silver_page_cache_page_allowed()),
	// a URL with other parameters never gets a file.

	// Visitors with their own content: logged in, cart / session, comment
	// author, password of a protected post.
	foreach ( array_keys( $_COOKIE ) as $cookie ) {
		if ( preg_match( '/^(wordpress_logged_in_|wp-postpass_|comment_author_|woocommerce_items_in_cart|wp_woocommerce_session_|woocommerce_recently_viewed|wordpress_no_cache)/', (string) $cookie ) ) {
			return 'cookie';
		}
	}

	$exclude = silver_perf_list( 'perf_page_cache_exclude' );
	if ( $exclude ) {
		foreach ( $exclude as $pattern ) {
			$pattern = '/' . ltrim( $pattern, '/' );
			$regex   = '#^' . str_replace( '\*', '.*', preg_quote( untrailingslashit( $pattern ), '#' ) ) . '/?$#i';
			if ( preg_match( $regex, $path ) ) {
				return 'excluded';
			}
		}
	}

	if ( apply_filters( 'silver_page_cache_bypass', false ) ) {
		return 'filter';
	}

	return '';
}

/**
 * Query parameters of the request that change the page (the ignored ones
 * left out), sorted.
 *
 * @return array
 */
function silver_page_cache_params() {
	$params = array();
	foreach ( $_GET as $name => $value ) { // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! in_array( strtolower( (string) $name ), silver_page_cache_ignored_params(), true ) ) {
			$params[ (string) $name ] = is_array( $value ) ? wp_json_encode( $value ) : (string) $value;
		}
	}
	ksort( $params );

	return $params;
}

/**
 * Cache file of the current URL (scheme, host, path and the parameters that
 * change the page: ?p=, ?page_id= of plain permalinks...).
 *
 * @return string
 */
function silver_page_cache_file() {
	$host   = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( (string) $_SERVER['HTTP_HOST'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$path   = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$params = silver_page_cache_params();
	$key    = ( is_ssl() ? 'https' : 'http' ) . '://' . preg_replace( '/[^a-z0-9.:-]/', '', $host ) . $path . ( $params ? '?' . http_build_query( $params ) : '' );
	$key  = (string) apply_filters( 'silver_page_cache_key', $key );

	return silver_page_cache_dir() . '/' . md5( $key ) . '.html';
}

/**
 * Sends the cached page when there is a fresh one, and stops. Runs when the
 * theme loads, before WordPress parses the request.
 */
function silver_page_cache_serve() {
	$lifetime = silver_page_cache_lifetime();
	if ( ! $lifetime || silver_page_cache_request_bypass() ) {
		return;
	}

	$file  = silver_page_cache_file();
	$mtime = @filemtime( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- no file: false.

	if ( ! $mtime ) {
		return;
	}
	if ( $mtime + $lifetime < time() ) {
		@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
		return;
	}

	if ( ! headers_sent() ) {
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset', 'UTF-8' ) );
		header( 'X-Silver-Cache: HIT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $mtime ) . ' GMT' );
	}

	if ( 'HEAD' !== strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	exit;
}

/**
 * Starts saving the page (output buffer) for a request that can be cached.
 */
function silver_page_cache_start() {
	if ( ! silver_page_cache_lifetime() || silver_page_cache_request_bypass() || is_user_logged_in() ) {
		return;
	}
	if ( 'GET' !== strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		return;
	}

	if ( ! headers_sent() ) {
		header( 'X-Silver-Cache: MISS' );
	}
	ob_start( 'silver_page_cache_save' );
}
// Before the other output buffers: this one gets the final HTML.
add_action( 'template_redirect', 'silver_page_cache_start', -9999 );

/**
 * Whether the page that was built can be cached (WordPress knows the page
 * now).
 *
 * @return bool
 */
function silver_page_cache_page_allowed() {
	if ( http_response_code() !== 200 || ( defined( 'DONOTCACHEPAGE' ) && DONOTCACHEPAGE ) || is_user_logged_in() ) {
		return false;
	}
	if ( is_404() || is_search() || is_feed() || is_preview() || is_trackback() || is_robots() || is_customize_preview() || is_embed() ) {
		return false;
	}
	if ( is_singular() && post_password_required() ) {
		return false;
	}
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		return false;
	}

	// Only parameters that WordPress uses to choose the page (?p=, ?page_id=,
	// ?product=, ?paged=...): other ones (add-to-cart, replytocom, made-up
	// ones) are not saved, so their URLs never come from the cache.
	global $wp;
	$public = isset( $wp->public_query_vars ) ? (array) $wp->public_query_vars : array();
	$public = array_diff( $public, array( 's', 'preview', 'replytocom', 'feed', 'embed', 'customize_changeset_uuid' ) );
	foreach ( array_keys( silver_page_cache_params() ) as $param ) {
		if ( ! in_array( $param, $public, true ) ) {
			return false;
		}
	}

	// A response that sets a cookie belongs to this visitor; another type
	// than HTML (sitemaps...) is not a page.
	foreach ( headers_list() as $header ) {
		if ( stripos( $header, 'Set-Cookie:' ) === 0 ) {
			return false;
		}
		if ( stripos( $header, 'Content-Type:' ) === 0 && stripos( $header, 'text/html' ) === false ) {
			return false;
		}
	}

	return (bool) apply_filters( 'silver_page_cache_page_allowed', true );
}

/**
 * Output buffer callback: saves the page, sends it unchanged.
 *
 * @param string $html Page.
 * @return string
 */
function silver_page_cache_save( $html ) {
	if ( ! is_string( $html ) || false === stripos( $html, '</html>' ) || ! silver_page_cache_page_allowed() ) {
		return $html;
	}

	$dir = silver_page_cache_dir();
	if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
		return $html;
	}
	if ( ! file_exists( $dir . '/index.php' ) ) {
		@file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
	}

	$file = silver_page_cache_file();
	$tmp  = $file . '.' . uniqid( '', true ) . '.tmp';
	$copy = $html . "\n<!-- Silver page cache: " . gmdate( 'Y-m-d H:i:s' ) . " UTC -->\n";

	// Written aside then moved: a visitor never gets half a file.
	if ( false !== @file_put_contents( $tmp, $copy ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
		if ( ! @rename( $tmp, $file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
		}
	}

	return $html;
}

/**
 * Number of cached pages.
 *
 * @return int
 */
function silver_page_cache_count() {
	$files = glob( silver_page_cache_dir() . '/*.html' );
	return is_array( $files ) ? count( $files ) : 0;
}

/**
 * Empties the page cache.
 */
function silver_page_cache_clear() {
	// No GLOB_BRACE: missing on some systems (musl).
	$files = array_merge( (array) glob( silver_page_cache_dir() . '/*.html' ), (array) glob( silver_page_cache_dir() . '/*.tmp' ) );
	foreach ( array_filter( $files ) as $file ) {
		@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
	}
	do_action( 'silver_page_cache_cleared' );
}

/**
 * Empties the cache when a post that shows on the site changes (not the
 * orders, revisions...). Blocks (header, footer...) are not public but show
 * on every page.
 *
 * @param int $post_id Post ID.
 */
function silver_page_cache_clear_post( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	$type   = get_post_type( $post_id );
	$object = $type ? get_post_type_object( $type ) : null;
	if ( ! $object || ( ! $object->public && ! in_array( $type, array( 'blocks', 'wp_block', 'wp_navigation' ), true ) ) ) {
		return;
	}
	silver_page_cache_clear();
}

/**
 * A post changes status: cleared when it is or was published.
 *
 * @param string  $new  New status.
 * @param string  $old  Old status.
 * @param WP_Post $post Post.
 */
function silver_page_cache_transition( $new, $old, $post ) {
	if ( 'publish' === $new || 'publish' === $old ) {
		silver_page_cache_clear_post( $post->ID );
	}
}
// On every save (also publish -> publish: an update).
add_action( 'transition_post_status', 'silver_page_cache_transition', 10, 3 );
// Before: the post type is still known.
add_action( 'before_delete_post', 'silver_page_cache_clear_post' );

/**
 * Theme Options (Advanced) saved by the user: Save, Reset, Import, Restore
 * (the of_ajax_post_action request). The options framework also saves on
 * its own when an admin page loads; that is not a change of the site.
 */
function silver_page_cache_clear_options_saved() {
	if ( doing_action( 'wp_ajax_of_ajax_post_action' ) ) {
		silver_page_cache_clear();
	}
}
add_action( 'of_save_options_after', 'silver_page_cache_clear_options_saved' );

// Everything else that shows on many pages: empties the whole cache.
foreach ( array(
	'wp_update_nav_menu', 'customize_save_after', 'switch_theme',
	'update_option_sidebars_widgets', 'created_term', 'edited_term', 'delete_term',
	'wp_set_comment_status', 'edit_comment', 'deleted_comment',
	'woocommerce_product_set_stock', 'woocommerce_variation_set_stock', 'woocommerce_product_set_stock_status',
	'activated_plugin', 'deactivated_plugin', 'upgrader_process_complete',
	'update_option_blogname', 'update_option_blogdescription', 'update_option_page_on_front', 'update_option_show_on_front',
) as $silver_page_cache_hook ) {
	add_action( $silver_page_cache_hook, 'silver_page_cache_clear' );
}
unset( $silver_page_cache_hook );

/**
 * A new approved comment shows on its post.
 *
 * @param int        $comment_id Comment ID.
 * @param int|string $approved   1, 0 or spam.
 */
function silver_page_cache_comment_post( $comment_id, $approved ) {
	if ( 1 === (int) $approved ) {
		silver_page_cache_clear();
	}
}
add_action( 'comment_post', 'silver_page_cache_comment_post', 10, 2 );

/**
 * "Clear page cache" in the admin bar.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar.
 */
function silver_page_cache_admin_bar( $admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) || ! silver_page_cache_lifetime() ) {
		return;
	}
	$admin_bar->add_node( array(
		'id'    => 'silver_clear_page_cache',
		'title' => esc_html__( 'Clear page cache', 'silver' ) . ' (' . silver_page_cache_count() . ')',
		'href'  => wp_nonce_url( admin_url( 'admin-post.php?action=silver_clear_page_cache' ), 'silver_clear_page_cache' ),
	) );
}
add_action( 'admin_bar_menu', 'silver_page_cache_admin_bar', 100 );

/**
 * Clears the cache (admin bar button), back to the page.
 */
function silver_page_cache_clear_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'silver' ), 403 );
	}
	check_admin_referer( 'silver_clear_page_cache' );
	silver_page_cache_clear();

	$back = wp_get_referer();
	wp_safe_redirect( add_query_arg( 'silver_cache_cleared', '1', $back ? $back : admin_url() ) );
	exit;
}
add_action( 'admin_post_silver_clear_page_cache', 'silver_page_cache_clear_action' );

/**
 * Notice after the button (wp-admin).
 */
function silver_page_cache_cleared_notice() {
	if ( isset( $_GET['silver_cache_cleared'] ) && current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Page cache cleared.', 'silver' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'silver_page_cache_cleared_notice' );

// Send the cached page now (the theme is loading, WordPress has not parsed
// the request yet).
silver_page_cache_serve();
