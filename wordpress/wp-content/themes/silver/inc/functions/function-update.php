<?php
/**
 * Disable update offers and automatic updates for the customized Silver theme.
 *
 * @package Silver/Functions
 */

/**
 * Remove Silver update data, including offers cached before updates were disabled.
 * Other themes and the checked versions remain unchanged.
 *
 * @param mixed $transient Theme update data.
 * @return mixed
 */
function silver_disable_theme_update_info( $transient ) {
	if ( is_object( $transient ) ) {
		unset( $transient->response['silver'], $transient->no_update['silver'] );
	}

	return $transient;
}
foreach ( array( 'site_transient_update_themes', 'pre_set_site_transient_update_themes', 'transient_update_themes', 'pre_set_transient_update_themes' ) as $silver_update_hook ) {
	add_filter( $silver_update_hook, 'silver_disable_theme_update_info', PHP_INT_MAX );
}
unset( $silver_update_hook );

/**
 * Exclude Silver from WordPress.org theme update checks.
 *
 * @param array  $request HTTP request arguments.
 * @param string $url Request URL.
 * @return array
 */
function flatsome_update_check_request_args( $request, $url ) {
	if ( false === strpos( $url, '//api.wordpress.org/themes/update-check/' ) || ! isset( $request['body']['themes'] ) || ! is_string( $request['body']['themes'] ) ) {
		return $request;
	}

	$data = json_decode( $request['body']['themes'] );
	if ( is_object( $data ) && isset( $data->themes->silver ) ) {
		unset( $data->themes->silver );
		$request['body']['themes'] = wp_json_encode( $data );
	}

	return $request;
}
add_filter( 'http_request_args', 'flatsome_update_check_request_args', 5, 2 );

/**
 * Disable background updates for Silver only.
 *
 * @param bool|null $update Whether to update automatically.
 * @param object    $item Theme update information.
 * @return bool|null
 */
function silver_disable_automatic_theme_update( $update, $item ) {
	return isset( $item->theme ) && $item->theme === 'silver' ? false : $update;
}
add_filter( 'auto_update_theme', 'silver_disable_automatic_theme_update', PHP_INT_MAX, 2 );

/**
 * Remove Silver update notices and auto-update controls from Appearance > Themes.
 *
 * @param array $themes Prepared theme information.
 * @return array
 */
function silver_disable_theme_update_controls( $themes ) {
	if ( isset( $themes['silver'] ) ) {
		$themes['silver']['hasUpdate']  = false;
		$themes['silver']['hasPackage'] = false;
		$themes['silver']['update']     = '';
		$themes['silver']['autoupdate'] = array(
			'enabled'   => false,
			'supported' => false,
			'forced'    => false,
		);
		$themes['silver']['actions']['autoupdate'] = null;
	}

	return $themes;
}
add_filter( 'wp_prepare_themes_for_js', 'silver_disable_theme_update_controls', PHP_INT_MAX );

final class Flatsome_WUpdates extends Flatsome_Base_Registration
{

    /**
	 * Setup instance.
	 *
	 * @param UxThemes_API $api The UX Themes API instance.
	 */
	public function __construct( UxThemes_API $api ) {
		parent::__construct( $api, 'flatsome_wupdates' );

	}

	/**
	 * Unregisters theme.
	 *
	 * @return array|WP_error
	 */
	public function unregister() {
		$this->delete_options();
		return array();
	}

	/**
	 * Get latest Flatsome version.
	 *
	 * @return string|WP_error
	 */
	public function get_latest_version() {
		$code = $this->get_code();

		if ( empty( $code ) ) {
			return new WP_Error( 'missing-purchase-code', __( 'Missing purchase code.', 'flatsome' ) );
		}

		$result = $this->send_request( "/update/check", 'wupdates-latest-version' );
		
		if ( is_wp_error( $result ) ) {
			$statuses = array( 400, 403, 404, 409, 410, 423 );
			if ( in_array( (int) $result->get_error_code(), $statuses, true ) ) {
				$this->set_errors( array( $result->get_error_message() ) );
			}
			return $result;
		} else {
			wp_clear_scheduled_hook( 'flatsome_scheduled_registration' );
			wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'flatsome_scheduled_registration' );
			$this->set_errors( array() );
		}

		if ( empty( $result['version'] ) ) {
			return new WP_Error( 'missing-version', __( 'No version received.', 'flatsome' ) );
		}

		if ( ! is_string( $result['version'] ) ) {
			return new WP_Error( 'invalid-version', __( 'Invalid version received.', 'flatsome' ) );
		}

		return $result['version'];
	}

	/**
	 * Get a temporary download URL.
	 *
	 * @param string $version Version number to download.
	 * @return string|WP_error
	 */
	public function get_download_url( $version ) {
		$code = $this->get_code();

		if ( empty( $code ) ) {
			return new WP_Error( 'missing-purchase-code', __( 'Missing purchase code.', 'flatsome' ) );
		}

		$result = $this->send_request( "/update/check", 'download-url' );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( empty( $result['url'] ) ) {
			return new WP_Error( 'missing-url', __( 'No URL received.', 'flatsome' ) );
		}

		if ( ! is_string( $result['url'] ) ) {
			return new WP_Error( 'invalid-url', __( 'Invalid URL received.', 'flatsome' ) );
		}

		return $result['url'];
	}
	public function get_code() {
		return true;
	}
	public function send_request( $path, $context = null, $args = array() ) {
		$args = array_merge_recursive( $args, array(
			'timeout' => 60,
			'headers' => array(
				'Referer' => $this->get_site_url(),
			),
		) );

		$url      = esc_url_raw( 'https://wupdates.net/wp-json/lic' . $path );
		$response = wp_remote_request( $url, $args );
		$status   = wp_remote_retrieve_response_code( $response );
		$headers  = wp_remote_retrieve_headers( $response );
		$body     = wp_remote_retrieve_body( $response );
		$data     = (array) json_decode( $body, true );

		if ( is_wp_error( $response ) ) {
			return $this->get_error( $response, $context, $data );
		}

		if ( $status === 429 ) {
			if ( isset( $headers['x-ratelimit-reset'] ) ) {
				$data['retry-after'] = (int) $headers['x-ratelimit-reset'];
			} elseif ( isset( $headers['retry-after'] ) ) {
				$data['retry-after'] = time() + ( (int) $headers['retry-after'] );
			}
		}

		if ( $status !== 200 ) {
			$error = isset( $data['message'] )
				? new WP_Error( $status, $data['message'], $data )
				// translators: %d: The status code.
				: new WP_Error( $status, sprintf( __( 'Sorry, an error occurred while accessing the API. Error %d', 'flatsome' ), $status ), $data );

			return $this->get_error( $error, $context, $data );
		}

		return $data;
	}
	protected function get_site_url() {
		global $wpdb;

		$row = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl' LIMIT 1" );

		if ( is_object( $row ) ) {
			return $row->option_value;
		}

		return '';
	}
	/**
	 * Returns a proper error for a HTTP status code.
	 *
	 * @param WP_Error $error   The original error.
	 * @param string   $context A context.
	 * @param array    $data    Optional data.
	 * @return WP_Error
	 */
	public function get_error( $error, $context = null, $data = array() ) {
		$status        = (int) $error->get_error_code();
		$account_attrs = ' href="' . esc_url_raw( UXTHEMES_ACCOUNT_URL ) . '" target="_blank" rel="noopener noreferrer"';

		switch ( $status ) {
			case 400:
				if ( $context === 'register' ) {
					return new WP_Error( $status, __( 'Your purchase code is malformed.', 'flatsome' ), $data );
				}
				if ( $context === 'envato-register' ) {
					return new WP_Error( $status, __( 'Sorry, an error occurred. Please try again.', 'flatsome' ), $data );
				}
				if ( $context === 'latest-version' ) {
					return new WP_Error( $status, __( 'Flatsome was unable to get the latest version. Your site might have changed domain after you registered it.', 'flatsome' ), $data );
				}
				return $error;
			case 403:
				if ( $context === 'latest-version' ) {
					return new WP_Error( $status, __( 'Flatsome was unable to get the latest version because the purchase code has not been verified yet. Please re-register it in order to receive updates.', 'flatsome' ), $data );
				}
				return $error;
			case 404:
				if ( $context === 'register' || $context === 'envato-register' || $context === 'wupdates-register' ) {
					return new WP_Error( $status, __( 'The purchase code is malformed or does not belong to a Flatsome sale.', 'flatsome' ), $data );
				}
				if ( $context === 'unregister' ) {
					// translators: %s: License manager link attributes.
					return new WP_Error( $status, sprintf( __( 'The registration was not found for <a%s>your account</a>. It was only deleted on this site.', 'flatsome' ), $account_attrs ), $data );
				}
				if ( $context === 'latest-version' ) {
					// translators: %s: License manager link attributes.
					return new WP_Error( $status, sprintf( __( 'Flatsome was unable to get the latest version. Your registration might have been deleted from <a%s>your account</a>.', 'flatsome' ), $account_attrs ), $data );
				}
				if ( $context === 'wupdates-latest-version' ) {
					return new WP_Error( $status, __( 'Flatsome was unable to get the latest version. Your purchase code is malformed.', 'flatsome' ), $data );
				}
				return $error;
			case 409:
				if ( $context === 'wupdates' ) {
					// translators: %s: License manager link attributes.
					return new WP_Error( $status, sprintf( __( 'Your purchase code has been used on too many sites. Please go to <a%s>your account</a> and manage your licenses.', 'flatsome' ), $account_attrs ), $data );
				}
				// translators: %s: License manager link attributes.
				return new WP_Error( $status, sprintf( __( 'The purchase code is already registered on another site. Please go to <a%s>your account</a> and manage your licenses.', 'flatsome' ), $account_attrs ), $data );
			case 410:
				if ( $context === 'register' || $context === 'envato-register' || $context === 'latest-version' ) {
					return new WP_Error( $status, __( 'Your purchase code has been blocked. Please contact support to resolve the issue.', 'flatsome' ), $data );
				}
				if ( $context === 'wupdates-register' ) {
					return new WP_Error( $status, __( 'The purchase code does not belong to a Flatsome sale.', 'flatsome' ), $data );
				}
				if ( $context === 'wupdates-latest-version' ) {
					return new WP_Error( $status, __( 'Flatsome was unable to get the latest version. The purchase code does not belong to a Flatsome sale.', 'flatsome' ), $data );
				}
				return new WP_Error( $status, __( 'The requested resource no longer exists.', 'flatsome' ), $data );
			case 417:
				return new WP_Error( $status, __( 'No domain was sent with the request.', 'flatsome' ), $data );
			case 422:
				return new WP_Error( $status, __( 'Unable to parse the domain for your site.', 'flatsome' ), $data );
			case 423:
				if ( $context === 'register' || $context === 'envato-register' || $context === 'latest-version' || $context === 'wupdates-latest-version' || $context === 'wupdates' ) {
					return new WP_Error( $status, __( 'Your purchase code has been locked. Please contact support to resolve the issue.', 'flatsome' ), $data );
				}
				return new WP_Error( $status, __( 'The requested resource has been locked.', 'flatsome' ), $data );
			case 429:
				return new WP_Error( $status, __( 'Sorry, the API is overloaded.', 'flatsome' ), $data );
			case 503:
				return new WP_Error( $status, __( 'Sorry, the API is unavailable at the moment.', 'flatsome' ), $data );
			default:
				return $error;
		}
	}
}

final class WUpdates_Flatsome_Envato {

	/**
	 * The single class instance.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * The registration instance.
	 *
	 * @var Flatsome_Base_Registration
	 */
	public $registration;

	/**
	 * Setup instance properties.
	 */
	private function __construct() {
		$api = new UxThemes_API();
		
		$this->registration = new Flatsome_WUpdates( $api );
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
	 * Main Flatsome_Envato instance
	 *
	 * @return Flatsome_Envato
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

function flatsome_envato_wupdates() {
	return WUpdates_Flatsome_Envato::get_instance();
}