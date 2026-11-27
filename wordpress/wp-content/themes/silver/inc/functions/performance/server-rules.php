<?php
/**
 * Browser cache and Gzip / Brotli (Advanced > Performance > Server).
 *
 * CSS, JS, images and fonts are sent by the web server itself, without
 * WordPress: their headers are set by server rules.
 * - Apache / LiteSpeed: the theme writes them in .htaccess, between
 *   "# BEGIN Silver Performance" and "# END Silver Performance" (WordPress
 *   insert_with_markers(), like the permalinks), when Theme Options are
 *   saved; the block is removed when the options are off.
 * - Nginx: .htaccess is ignored; the options show the lines to add to the
 *   site's Nginx configuration.
 *
 * The options show what the server really sends (a request to silver.css).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the server reads .htaccess (Apache, LiteSpeed).
 *
 * @return bool
 */
function silver_server_reads_htaccess() {
	global $is_apache;
	return (bool) $is_apache;
}

/**
 * Browser cache lifetime in seconds (0 = off).
 *
 * @return int
 */
function silver_browser_cache_lifetime() {
	return max( 0, (int) silver_perf( 'perf_browser_cache', 0 ) );
}

/**
 * Lines of the .htaccess block for the current options (empty = no block).
 *
 * @return string[]
 */
function silver_server_rules_htaccess() {
	$lines    = array();
	$lifetime = silver_browser_cache_lifetime();

	if ( $lifetime ) {
		$types = array(
			'text/css', 'application/javascript', 'text/javascript', 'application/x-javascript',
			'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/svg+xml',
			'image/x-icon', 'image/vnd.microsoft.icon',
			'font/woff2', 'font/woff', 'font/ttf', 'font/otf', 'application/font-woff', 'application/font-woff2',
			'application/vnd.ms-fontobject', 'video/mp4', 'video/webm', 'audio/mpeg',
		);
		$lines[] = '# Browser cache: static files kept ' . $lifetime . ' seconds (CSS / JS have ?ver= and reload when they change).';
		$lines[] = '<IfModule mod_expires.c>';
		$lines[] = '	ExpiresActive On';
		foreach ( $types as $type ) {
			$lines[] = '	ExpiresByType ' . $type . ' "access plus ' . $lifetime . ' seconds"';
		}
		$lines[] = '	ExpiresByType text/html "access plus 0 seconds"';
		$lines[] = '</IfModule>';
		$lines[] = '<IfModule mod_headers.c>';
		$lines[] = '	<FilesMatch "\.(css|js|mjs|jpe?g|png|gif|webp|avif|svg|ico|woff2?|ttf|otf|eot|mp4|webm|mp3)$">';
		$lines[] = '		Header set Cache-Control "public, max-age=' . $lifetime . '"';
		$lines[] = '	</FilesMatch>';
		$lines[] = '</IfModule>';
	}

	if ( silver_perf( 'perf_gzip' ) ) {
		$types   = 'text/html text/plain text/css text/xml text/javascript application/javascript application/x-javascript application/json application/ld+json application/xml application/rss+xml application/xhtml+xml image/svg+xml image/x-icon font/ttf font/otf application/vnd.ms-fontobject';
		$lines[] = '# Gzip / Brotli: text files compressed when sent (webp / jpg / png / woff2 are already compressed).';
		$lines[] = '<IfModule mod_brotli.c>';
		$lines[] = '	AddOutputFilterByType BROTLI_COMPRESS ' . $types;
		$lines[] = '</IfModule>';
		$lines[] = '<IfModule mod_deflate.c>';
		$lines[] = '	AddOutputFilterByType DEFLATE ' . $types;
		$lines[] = '	<IfModule mod_headers.c>';
		$lines[] = '		Header append Vary Accept-Encoding';
		$lines[] = '	</IfModule>';
		$lines[] = '</IfModule>';
	}

	return $lines;
}

/**
 * Nginx lines for the current options (shown in the options).
 *
 * @return string
 */
function silver_server_rules_nginx() {
	$out      = '';
	$lifetime = silver_browser_cache_lifetime();

	if ( $lifetime ) {
		$out .= "location ~* \\.(css|js|mjs|jpe?g|png|gif|webp|avif|svg|ico|woff2?|ttf|otf|eot|mp4|webm|mp3)$ {\n\texpires {$lifetime}s;\n\tadd_header Cache-Control \"public, max-age={$lifetime}\";\n\taccess_log off;\n\ttry_files \$uri =404;\n}\n";
	}
	if ( silver_perf( 'perf_gzip' ) ) {
		$out .= "gzip on;\ngzip_vary on;\ngzip_comp_level 6;\ngzip_min_length 1024;\ngzip_types text/plain text/css text/xml text/javascript application/javascript application/json application/xml application/rss+xml image/svg+xml font/ttf font/otf;\n";
	}

	return $out;
}

/**
 * Writes (or removes) the block in .htaccess. Only on a server that reads it.
 *
 * @return bool|null True written, false not writable, null nothing to do.
 */
function silver_server_rules_write() {
	if ( ! silver_server_reads_htaccess() ) {
		return null;
	}

	$file  = ABSPATH . '.htaccess';
	$lines = silver_server_rules_htaccess();

	// No block and none to write: the file is left alone (not created).
	if ( ! $lines && ( ! file_exists( $file ) || false === strpos( (string) @file_get_contents( $file ), '# BEGIN Silver Performance' ) ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions
		return null;
	}

	if ( ! function_exists( 'insert_with_markers' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}

	delete_transient( 'silver_server_rules_check' );

	return insert_with_markers( $file, 'Silver Performance', $lines );
}

/**
 * Theme Options saved: rules follow the options.
 */
function silver_server_rules_on_save() {
	// Only a save by the user (not the framework saving on its own when an
	// admin page loads).
	if ( ! doing_action( 'wp_ajax_of_ajax_post_action' ) ) {
		return;
	}

	$result = silver_server_rules_write();
	if ( false === $result ) {
		set_transient( 'silver_server_rules_error', 1, MINUTE_IN_SECONDS * 10 );
	} else {
		delete_transient( 'silver_server_rules_error' );
	}
}
add_action( 'of_save_options_after', 'silver_server_rules_on_save', 20 );

/**
 * Another theme: the rules go with this one.
 */
function silver_server_rules_remove() {
	if ( silver_server_reads_htaccess() && file_exists( ABSPATH . '.htaccess' ) ) {
		if ( ! function_exists( 'insert_with_markers' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}
		insert_with_markers( ABSPATH . '.htaccess', 'Silver Performance', array() );
	}
}
add_action( 'switch_theme', 'silver_server_rules_remove' );

/**
 * What the server sends for silver.css (cached 10 minutes): Cache-Control,
 * Expires, Content-Encoding.
 *
 * @return array|WP_Error
 */
function silver_server_rules_check() {
	$cached = get_transient( 'silver_server_rules_check' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$url      = get_template_directory_uri() . '/assets/css/silver.css';
	$response = wp_remote_get( add_query_arg( 'check', time(), $url ), array(
		'timeout'    => 5,
		'decompress' => false,
		'sslverify'  => apply_filters( 'https_local_ssl_verify', false ),
		'headers'    => array( 'Accept-Encoding' => 'br, gzip' ),
	) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$result = array(
		'cache_control' => (string) wp_remote_retrieve_header( $response, 'cache-control' ),
		'expires'       => (string) wp_remote_retrieve_header( $response, 'expires' ),
		'encoding'      => (string) wp_remote_retrieve_header( $response, 'content-encoding' ),
	);
	set_transient( 'silver_server_rules_check', $result, MINUTE_IN_SECONDS * 10 );

	return $result;
}

/**
 * Status lines of the options (Advanced > Performance), HTML.
 *
 * @param string $what browser_cache or gzip.
 * @return string
 */
function silver_server_rules_status( $what ) {
	$on = 'gzip' === $what ? (bool) silver_perf( 'perf_gzip' ) : (bool) silver_browser_cache_lifetime();

	$check = silver_server_rules_check();
	if ( is_wp_error( $check ) ) {
		$seen = '<span style="color:#996800">Could not check (' . esc_html( $check->get_error_message() ) . ').</span>';
	} elseif ( 'gzip' === $what ) {
		$seen = $check['encoding']
			? '<span style="color:#008a20">The server compresses: Content-Encoding: ' . esc_html( $check['encoding'] ) . '.</span>'
			: '<span style="color:#b32d2e">The server does not compress (no Content-Encoding).</span>';
	} else {
		$seen = $check['cache_control'] || $check['expires']
			? '<span style="color:#008a20">The server sends: ' . esc_html( trim( 'Cache-Control: ' . $check['cache_control'] . ( $check['expires'] ? ' / Expires: ' . $check['expires'] : '' ) ) ) . '.</span>'
			: '<span style="color:#b32d2e">The server sends no browser cache header.</span>';
	}

	$where = '';
	if ( silver_server_reads_htaccess() ) {
		if ( get_transient( 'silver_server_rules_error' ) ) {
			$where = ' <strong style="color:#b32d2e">.htaccess is not writable: add the rules yourself or make it writable, then save again.</strong>';
		} elseif ( $on ) {
			$where = ' Applied in .htaccess (Apache / LiteSpeed).';
		}
	} elseif ( $on ) {
		$where = ' <strong>This server (' . esc_html( isset( $_SERVER['SERVER_SOFTWARE'] ) ? (string) $_SERVER['SERVER_SOFTWARE'] : 'not Apache' ) . ') does not read .htaccess</strong>: add these lines to the site\'s Nginx configuration (aaPanel: Website > Conf), then reload Nginx:<pre style="white-space:pre-wrap;background:#f6f7f7;padding:8px">' . esc_html( silver_server_rules_nginx() ) . '</pre>'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	return '<br>' . $seen . $where;
}
