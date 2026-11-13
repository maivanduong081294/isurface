<?php
/**
 * Features main class.
 *
 * @package Silver\Admin
 */

namespace Silver\Admin;

/**
 * Features Class
 *
 * @package Silver\Admin
 */
final class Features {

	/**
	 * The single instance of the class.
	 *
	 * @var static
	 */
	protected static $instance = null;

	/**
	 * The constructor.
	 */
	private function __construct() {
		add_settings_section(
			'silver_features_section',
			__( 'Features', 'silver' ),
			[ $this, 'render_features_section' ],
			'silver-features'
		);

		add_settings_section(
			'silver_experimental_features_section',
			__( 'Experimental features', 'silver' ),
			[ $this, 'render_experimental_features_section' ],
			'silver-features'
		);

		$this->add_features(
			[
				// ...
			]
		);

		register_setting( 'silver-features', 'flatsome_features' );
	}

	/**
	 * Add settings for experimental features.
	 *
	 * @param array $features Experiments to add.
	 */
	private function add_features( array $features ) {
		foreach ( $features as $id => $args ) {
			add_settings_field(
				$id,
				$args['title'],
				[ $this,  'render_field' ],
				'silver-features',
				'silver_' . $args['section'] . '_section',
				array_merge( $args, [ 'id' => $id ] )
			);
		}
	}

	/**
	 * Renders above the «Features» settings.
	 */
	public function render_features_section() {}

	/**
	 * Renders above the «Experimental features» settings.
	 */
	public function render_experimental_features_section() {
		global $wp_settings_fields;

		if ( empty( $wp_settings_fields['silver-features']['silver_experimental_features_section'] ) ) {
			echo '<p><em>' . esc_html__( 'There are currently no experimental features in development.', 'silver' ) . '</em></p>';
		} else {
			echo '<p class="description">' . esc_html__( "Features that are testable while they're in development. These features are likely to change, so avoid using them in production.", 'silver' ) . '</p>';
		}
	}

	/**
	 * Renders a checkbox field for a feature.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_field( array $args ) {
		global $wp_version;

		$value = silver_is_feature_enabled( $args['id'] ) ? 1 : 0;
		$id    = 'silver-feature-' . $args['id'];

		$is_php_supported = isset( $args['requires_php'] ) ? version_compare( PHP_VERSION, $args['requires_php'], '>=' ) : true;
		$is_wp_supported  = isset( $args['requires_at_least'] ) ? version_compare( $wp_version, $args['requires_at_least'], '>=' ) : true;
		$disabled         = ! $is_php_supported || ! $is_wp_supported;

		echo '<label for="' . esc_attr( $id ) . '">';
		echo '<input type="checkbox" name="flatsome_features[' . esc_attr( $args['id'] ) . ']" id="' . esc_attr( $id ) . '" value="1"' . ( $value ? ' checked' : '' ) . ( $disabled ? ' disabled' : '' ) . '>';
		echo ' ' . esc_html( $args['description'] );
		echo '</label>';

		$styles = 'margin-top:6px;padding-left:calc(20px + 0.25rem);color:#a7aaad;font-size:13px;';

		if ( $value && ! empty( $args['links'] ) ) {
			echo '<div style="' . esc_attr( $styles ) . '">';
			for ( $i = 0; $i < count( $args['links'] ); $i++ ) { // phpcs:ignore
				if ( $i > 0 ) echo ' | ';
				echo '<a';
				echo ' href="' . esc_url_raw( $args['links'][ $i ]['url'] ) . '"';
				if ( ! empty( $args['links'][ $i ]['target'] ) ) {
					echo ' target="' . esc_attr( $args['links'][ $i ]['target'] ) . '"';
					if ( $args['links'][ $i ]['target'] === '_blank' ) {
						echo ' rel="noopener noreferrer"';
					}
				}
				echo '>';
				echo esc_html( $args['links'][ $i ]['title'] );
				echo '</a>';
			}
			echo '</div>';
		}

		if ( $disabled ) {
			echo '<div style="' . esc_attr( $styles ) . '">';
			if ( ! $is_php_supported ) {
				// translators: %s: PHP version.
				echo '<p class="description">' . sprintf( esc_html__( 'This feature requires PHP version %s or higher.', 'silver' ), $args['requires_php'] ) . '</p>';
			}
			if ( ! $is_wp_supported ) {
				// translators: %s: WordPress version.
				echo '<p class="description">' . sprintf( esc_html__( 'This feature requires WordPress version %s or higher.', 'silver' ), $args['requires_at_least'] ) . '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Get HTML for a badge.
	 *
	 * @param string $text The text (alpha, beta etc.).
	 */
	private function badge( $text ) {
		return '<span style="margin-left:6px;padding:3px 6px;font-size:75%;font-weight:normal;border-radius:3px;color:#777;background:rgba(0,0,0,0.05);">' . esc_html( $text ) . '</span>';
	}

	/**
	 * Main instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
