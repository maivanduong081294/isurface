<?php
/**
 * Silver_Theme_JSON class.
 *
 * @author  UX Themes
 * @package Silver
 * @since   3.18.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Silver_Theme_JSON
 *
 * @package Silver
 */
final class Silver_Theme_JSON {

	/**
	 * Initialize.
	 */
	public function init() {
		add_filter( 'wp_theme_json_data_user', [ $this, 'update_data' ] );
	}

	/**
	 * Modifies the `theme.json` values.
	 *
	 * @param WP_Theme_JSON_Data $theme_json Theme JSON data instance.
	 */
	public function update_data( $theme_json ) {
		return $theme_json->update_with(
			array(
				'version'  => 2,
				'settings' => array(
					'color' => array(
						'palette' => array(
							'theme' => $this->get_color_palette(),
						),
					),
				),
			)
		);
	}

	/**
	 * Get color values.
	 */
	private function get_color_palette() {
		return array(
			array(
				'slug'  => 'primary',
				'color' => get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ),
			),
			array(
				'slug'  => 'secondary',
				'color' => get_theme_mod( 'color_secondary', Silver_Default::COLOR_SECONDARY ),
			),
			array(
				'slug'  => 'success',
				'color' => get_theme_mod( 'color_success', Silver_Default::COLOR_SUCCESS ),
			),
			array(
				'slug'  => 'alert',
				'color' => get_theme_mod( 'color_alert', Silver_Default::COLOR_ALERT ),
			),
		);
	}
}
