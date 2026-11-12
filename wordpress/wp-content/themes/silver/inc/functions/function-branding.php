<?php
/**
 * Silver display branding, preserving the theme's technical identifiers.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Replace the brand in display text without changing URLs or identifiers.
 *
 * @param mixed $text Display text.
 * @return mixed
 */
function silver_theme_brand_text( $text ) {
	if ( ! is_string( $text ) || stripos( $text, 'flatsome' ) === false ) {
		return $text;
	}

	return preg_replace( '~(?<![A-Za-z0-9_\\\\/.@-])flatsome(?![A-Za-z0-9_\\\\/@-]|\.[A-Za-z0-9_])~i', 'Silver', $text );
}

// Apply branding after translation so existing language files continue to work.
foreach ( array( 'flatsome', 'flatsome-admin', 'flatsome_admin', 'ux-builder', 'envato_setup', 'default' ) as $silver_brand_domain ) {
	foreach ( array( 'gettext', 'gettext_with_context', 'ngettext', 'ngettext_with_context' ) as $silver_brand_hook ) {
		add_filter( $silver_brand_hook . '_' . $silver_brand_domain, 'silver_theme_brand_text' );
	}
}
unset( $silver_brand_domain, $silver_brand_hook );

// Also update previously saved default footer text without changing the database.
add_filter( 'theme_mod_footer_left_text', 'silver_theme_brand_text' );
add_filter( 'theme_mod_footer_right_text', 'silver_theme_brand_text' );

/**
 * Get the supplied horizontal Silver logo, refreshing cached images after changes.
 *
 * @return string
 */
function silver_theme_logo_url() {
	$path = get_template_directory() . '/assets/img/silver-logo.png';
	$url  = get_template_directory_uri() . '/assets/img/silver-logo.png';

	return is_file( $path ) ? $url . '?ver=' . filemtime( $path ) : $url;
}

/**
 * Get the supplied square Silver logo for icons and admin branding.
 *
 * @return string
 */
function silver_theme_square_logo_url() {
	$path = get_template_directory() . '/assets/img/silver-icon.png';
	$url  = get_template_directory_uri() . '/assets/img/silver-icon.png';

	return is_file( $path ) ? $url . '?ver=' . filemtime( $path ) : $url;
}

/**
 * Keep admin logos aligned in the dashboard and the frontend toolbar.
 */
function silver_theme_admin_logo_styles() {
	if ( ! is_admin() && ! is_admin_bar_showing() ) {
		return;
	}

	wp_enqueue_style(
		'silver-admin-branding',
		get_template_directory_uri() . '/inc/admin/admin-branding.css',
		array(),
		filemtime( get_template_directory() . '/inc/admin/admin-branding.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'silver_theme_admin_logo_styles', 20 );
add_action( 'wp_enqueue_scripts', 'silver_theme_admin_logo_styles', 110 );

/**
 * Supply a default site icon while allowing a custom Site Identity icon.
 *
 * @param string $url Site icon URL.
 * @param int    $size Requested size.
 * @param int    $blog_id Requested site ID.
 * @return string
 */
function silver_theme_default_site_icon( $url, $size, $blog_id ) {
	if ( $blog_id && (int) $blog_id !== get_current_blog_id() ) {
		return $url;
	}

	return $url && get_option( 'site_icon' ) ? $url : silver_theme_square_logo_url();
}
add_filter( 'get_site_icon_url', 'silver_theme_default_site_icon', 10, 3 );

/**
 * Use the square logo in the Customizer preview loading overlay.
 */
function silver_theme_customizer_loading_logo() {
	?>
	<style>
		.kirki-customizer-loading-wrapper {
			background-image: url("<?php echo esc_url( silver_theme_square_logo_url() ); ?>") !important;
			background-size: 80px 80px;
		}
	</style>
	<?php
}
function silver_theme_customizer_loading_logo_setup() {
	add_action( 'wp_head', 'silver_theme_customizer_loading_logo', 100 );
}
add_action( 'customize_preview_init', 'silver_theme_customizer_loading_logo_setup' );

/**
 * Replace bundled logos while keeping custom uploaded logos.
 *
 * @param mixed $logo Logo setting.
 * @return mixed
 */
function silver_theme_brand_logo( $logo ) {
	if ( is_string( $logo ) && preg_match( '~/themes/(?:flatsome|silver)/assets/img/(?:logo(?:-light)?\.png|silver-logo(?:-light)?\.(?:svg|png))(?:\?.*)?$~i', $logo ) ) {
		return silver_theme_logo_url();
	}

	return $logo;
}

foreach ( array( 'site_logo', 'site_logo_dark', 'site_logo_sticky' ) as $silver_logo_setting ) {
	add_filter( 'theme_mod_' . $silver_logo_setting, 'silver_theme_brand_logo' );
}
unset( $silver_logo_setting );

/**
 * Use the uploaded theme screenshot and refresh it when the file changes.
 *
 * @param array $themes Prepared theme information.
 * @return array
 */
function silver_theme_brand_preview( $themes ) {
	$screenshot = get_template_directory() . '/screenshot.png';
	if ( isset( $themes['silver'] ) && is_file( $screenshot ) ) {
		$themes['silver']['screenshot'] = array(
			get_template_directory_uri() . '/screenshot.png?ver=' . filemtime( $screenshot ),
		);
	}

	return $themes;
}
add_filter( 'wp_prepare_themes_for_js', 'silver_theme_brand_preview' );

/**
 * Retain parent-theme settings until settings are saved under the new folder.
 *
 * @param mixed $default Default value when the Silver settings do not exist.
 * @return mixed
 */
function silver_theme_legacy_mods( $default ) {
	return get_option( 'theme_mods_flatsome', $default );
}
add_filter( 'default_option_theme_mods_silver', 'silver_theme_legacy_mods' );
