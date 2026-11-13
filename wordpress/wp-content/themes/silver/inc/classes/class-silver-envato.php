<?php
/**
 * Silver_Envato class.
 *
 * @package Silver
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The Silver Envato.
 */
final class Silver_Envato {

	/**
	 * The single class instance.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * The registration instance.
	 *
	 * @var Silver_Base_Registration
	 */
	public $registration;

	/**
	 * The admin instance.
	 *
	 * @var Silver_Envato_Admin
	 */
	public $admin;

	/**
	 * Setup instance properties.
	 */
	private function __construct() {
		$api = new UxThemes_API();

		if ( get_option( silver_theme_key() . '_wup_purchase_code' ) ) {
			$this->registration = new Silver_WUpdates_Registration( $api );
		} else {
			$this->registration = new Silver_Registration( $api );
		}

		if ( is_admin() ) {
			$this->admin = new Silver_Envato_Admin( $this->registration );
		}
	}

	/**
	 * Checks whether this site is registered or not.
	 *
	 * @return boolean
	 */
	public function is_registered() {
		return $this->registration->is_registered();
	}

	/**
	 * Checks whether the purchase code has been verified or not.
	 *
	 * @return boolean
	 */
	public function is_verified() {
		return $this->registration->is_verified();
	}

	/**
	 * Main Silver_Envato instance
	 *
	 * @return Silver_Envato
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
