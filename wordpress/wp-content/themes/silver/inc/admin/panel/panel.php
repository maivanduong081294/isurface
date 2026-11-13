<?php

/**
 * Silver Admin Panel
 */
class Silver_Admin {

	/**
	 * Sets up the welcome screen
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'silver_panel_register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'silver_panel_style' ) );
	}


	/**
	 * Load welcome screen css.
	 *
	 * @since  1.4.4
	 */
	public function silver_panel_style() {
		$uri     = get_template_directory_uri();
		$theme   = wp_get_theme( get_template() );
		$version = $theme->get( 'Version' );

		wp_enqueue_style( 'silver-panel-css', $uri . '/inc/admin/panel/panel.css', array(), $version . '.' . filemtime( get_template_directory() . '/inc/admin/panel/panel.css' ) );
	}

	/**
	 * Creates the dashboard page
	 * @see  add_theme_page()
	 * @since 1.0.0
	 */
	public function silver_panel_register_menu() {
		$silver_icon = silver_theme_square_logo_url();
		add_menu_page( 'Welcome to Silver', 'Silver', 'manage_options', 'silver-panel', array( $this, 'silver_panel_welcome' ), $silver_icon, '2' );
		add_submenu_page( 'silver-panel', 'Status', 'Status', 'manage_options', 'silver-panel-status', array( $this, 'silver_panel_status' ) );
		add_submenu_page( 'silver-panel', '', 'Theme Options', 'manage_options', 'customize.php' );
	}

	/**
	 * The welcome screen
	 * @since 1.0.0
	 */
	public function silver_panel_welcome() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-activate.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_getting_started() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-guide.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_tutorials() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-tutorials.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_support() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-support.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_status() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-status.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_features() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-features.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_changelog() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-changelog.php'; ?>
			</div>
		</div>
		<?php
	}

	public function silver_panel_license() {
		?>
		<div class="silver-panel">
			<div class="wrap about-wrap">
				<?php require get_template_directory() . '/inc/admin/panel/sections/top.php'; ?>
				<?php require get_template_directory() . '/inc/admin/panel/sections/tab-license.php'; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render content from txt files used in panels.
	 *
	 * @param array $filenames Filename(s) to include.
	 *
	 * @return void
	 */
	public static function render_panel_txt_content( array $filenames ): void {
		// Include the WP Filesystem API for reading safely txt files used in panels.
		global $wp_filesystem;
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();

		$content = '';

		if ( is_array( $filenames ) ) {
			foreach ( $filenames as $file ) {
				$file = get_template_directory() . '/' . $file;
				if ( $wp_filesystem->exists( $file ) ) {
					$file_content = $wp_filesystem->get_contents( $file );
					$content     .= '<div class="inner-panel">';
					$content     .= '<pre class="panel-tab-content-pre">';
					$content     .= esc_html( $file_content );
					$content     .= '</pre>';
					$content     .= '</div>';
				}
			}
		}

		echo wp_kses(
			$content,
			array(
				'div' => array(
					'class' => array(),
				),
				'pre' => array(
					'class' => array(),
				),
			)
		);
	}
}

$GLOBALS['Silver_Admin'] = new Silver_Admin();
