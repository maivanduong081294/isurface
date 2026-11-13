<?php
/**
 * Silver class.
 *
 * @author  UX Themes
 * @package Silver
 * @since   3.18.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Silver
 *
 * @package Silver
 */
final class Silver {

	/**
	 * Parent theme version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The Silver_Theme_JSON instance.
	 *
	 * @var Silver_Theme_JSON
	 */
	private $theme_json;

	/**
	 * The single instance of the class.
	 *
	 * @var Silver
	 */
	protected static $instance = null;

	/**
	 * Silver constructor.
	 */
	private function __construct() {
		$this->version = wp_get_theme( get_template() )->get( 'Version' );

		$this->theme_json = new Silver_Theme_JSON();
	}

	/**
	 * Initialize Silver.
	 */
	public function init() {
		$this->theme_json->init();

		if ( is_woocommerce_activated() ) {
			Silver\WooCommerce\MiniCart::get_instance();
			Silver\WooCommerce\Shipping::get_instance();
			Silver\WooCommerce\BuyNow::get_instance();
			Silver\WooCommerce\Brands::get_instance();
		}
	}

	/**
	 * Get parent theme version.
	 *
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Main instance.
	 *
	 * @deprecated in favor of get_instance()
	 * @return Silver
	 */
	public static function instance() {
		_deprecated_function( __METHOD__, '3.19.0', 'get_instance()' );
		return self::get_instance();
	}

	/**
	 * Main instance.
	 *
	 * @return Silver
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
