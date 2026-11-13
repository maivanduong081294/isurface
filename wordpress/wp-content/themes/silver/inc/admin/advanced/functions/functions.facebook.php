<?php

function silver_facebook_oauth_url() {
  $api_version = silver_facebook_api_version();
  $uri = get_template_directory_uri();
  $theme = wp_get_theme( get_template() );
  $version = $theme->get( 'Version' );
  $client_id = '380204239234502';
  $base_url = "https://www.facebook.com/$api_version/dialog/oauth";
  $redirect_uri = silver_api_url() . '/facebook/authorize/';
  $scope = 'pages_read_engagement,instagram_basic,public_profile';
  $state = urlencode( admin_url( "admin.php?page=optionsframework&tab=of-option-instagram&ver=$version" ) );
  $response_type = 'code';

  return "$base_url?client_id=$client_id&response_type=$response_type&scope=$scope&redirect_uri=$redirect_uri&state=$state";
}

function silver_facebook_login_button_html() {
  $url = silver_facebook_oauth_url();
  ob_start(); ?>
  <div class="notice notice-warning inline" style="margin-top: 0; margin-left: 0;">
    <p><?php echo sprintf( __( 'Connecting Instagram Business accounts is deprecated and will be removed in a future update. Please enter an access token instead. %1$sSee documentation%2$s.', 'silver' ), '<a href="https://docs.uxthemes.com/article/427-instagram-api" target="_blank" rel="noopener">', '</a>' ); ?></p>
  </div>
  <p><?php _e('Login with Facebook to connect an Instagram Business account:')  ?></p>
  <a class="button silver-facebook-login-button" href="<?php echo $url ?>">
    <span class="dashicons dashicons-facebook-alt"></span>
    <?php _e( 'Login with Facebook', 'silver' ) ?>
  </a>
  <?php return ob_get_clean();
}

function silver_facebook_cache_html() {
  ob_start(); ?>
  <p style="margin-top: 0;"><?php _e('Clear the cache to fetch fresh data from the Instagram API:')  ?></p>
  <button class="button" name="silver_instagram_clear_cache">
    <?php _e( 'Clear Instagram cache', 'silver' ) ?>
  </button>
  <?php return ob_get_clean();
}

function silver_facebook_accounts_html() {
  $accounts = silver_facebook_accounts();

  ob_start(); ?>

  <div id="silver-instagram-errors"></div>
  <input type="hidden" value="0" name="facebook_accounts[]">

  <div class="silver-instagram-accounts">
    <table class="widefat striped">
      <thead>
        <th><?php _e( 'Username' ); ?></th>
        <th><?php _e( 'Actions' ); ?></th>
      </thead>
      <tbody class="silver-instagram-accounts__body">
        <?php foreach ( $accounts as $username => $account ) : ?>
        <tr class="instagram-account" data-instagram-account="<?php echo esc_attr( $username ); ?>">
          <td>
            <?php foreach ( $account as $key => $value ) : ?>
              <?php if ( is_string( $value ) || is_numeric( $value ) ) : ?>
              <input type="hidden" name="facebook_accounts[<?php echo esc_attr( $username ) ?>][<?php echo esc_attr( $key ) ?>]" value="<?php echo esc_attr( $value ) ?>">
              <?php endif ?>
            <?php endforeach ?>
            <a target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $username ) ?>/" rel="noopener">
              <?php echo esc_html( $username ) ?>
            </a>
            <?php if ( empty( $account['type'] ) || $account['type'] !== 'instagram' ) : ?>
              <em><?php _e( 'deprecated', 'silver' ) ?></em>
            <?php endif; ?>
		    <?php if ( isset( $account['error'] ) ) : ?>
			<div class="notice notice-error inline">
			  <p>
				<?php
					// translators: %s is the error message.
					echo sprintf( __( 'An error occured while refreshing the access token: %s', 'silver' ), esc_html( $account['error'] ) );
			  	?>
			  </p>
			</div>
            <?php endif; ?>
          </td>
          <td align="right">
            <button type="button" class="button button-small" onclick="jQuery(this).closest('.instagram-account').remove()">
              <?php _e( 'Remove' ); ?>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      <tbody>
      <tfoot class="silver-instagram-accounts__foot">
        <tr>
          <td colspan="2">
			<div class="silver-instagram-accounts__access-token-form">
              <input id="silver-instagram-access-token-value" type="text" placeholder="<?php esc_attr_e( 'Enter an Instagram access token', 'silver' ) ?>">
              <button id="silver-instagram-access-token-submit" type="button" class="button" title="<?php esc_attr_e( 'Add access token', 'silver' ); ?>">
			    <span class="dashicons dashicons-plus-alt2"></span>
              </button>
			</div>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
  <p>
	<a href="<?php echo esc_url( 'https://docs.uxthemes.com/article/427-instagram-api' ) ?>" target="_blank" rel="noopener">
	  <?php esc_html_e( 'How to get an Instagram access token', 'silver' ); ?>
	</a>
  </p>
  <?php return ob_get_clean();
}

/**
 * Get accounts that is associated with an access token.
 *
 * @param string $access_token An access token for a Facebook user.
 *
 * @return WP_Error|array
 */
function silver_facebook_get_accounts( $access_token ) {
  $api_version = silver_facebook_api_version();
  $fields   = 'name,access_token,instagram_business_account{id,name,username,profile_picture_url}';
  $url      = "https://graph.facebook.com/$api_version/me/accounts?limit=100&fields=$fields&access_token=$access_token";
  $response = wp_remote_get( $url );

  if ( is_wp_error( $response ) ) {
    return new WP_Error( 'site_down', __( 'Unable to communicate with Instagram.', 'silver' ) );
  } else {
    $body = json_decode( $response['body'], true );

    if ( array_key_exists( 'error', $body ) ) {
      return new WP_Error( 'site_down', $body['error']['message'] );
    }

    return $body;
  }
}

/**
 * Renders the popup that shows the accounts that can be connected.
 */
function silver_facebook_connect_admin_footer() {
  if ( ! is_array( $_GET ) || ! isset( $_GET['flatsome_facebook_access_token'] ) ) {
    return;
  }

  $access_token = sanitize_text_field( $_GET['flatsome_facebook_access_token'] );
  $all_accounts = silver_facebook_get_accounts( $access_token );
  $current_accounts = silver_facebook_accounts();
  $accounts = array();

  if ( ! is_wp_error( $all_accounts ) ) {
    $accounts = array_filter( $all_accounts['data'], function( $account ) {
      return ! empty( $account['instagram_business_account'] );
    } );
  }

  ob_start();

  ?>

  <div class="silver-instagram-connect">
    <div class="silver-instagram-connect-body">
      <h2 class=""><?php _e( 'Connect Instagram Business accounts', 'silver' ); ?></h2>
      <?php if ( is_wp_error( $accounts ) ) : ?>
        <div class="notice notice-error inline" style="margin: 0;">
          <p><?php echo $accounts->get_error_message() ?></p>
        </div>
        <div class="tablenav bottom textright">
          <button type="button" class="button" onclick="jQuery(this).closest('.silver-instagram-connect').hide()">
            <?php esc_html_e( 'Okay', 'silver' ); ?>
          </button>
        </div>
      <?php elseif ( empty( $accounts ) ) : ?>
        <div class="notice notice-info inline" style="margin: 0;">
          <p><?php esc_html_e( 'No associated Instagram Business account was found for your Facebook user.', 'silver' ) ?></p>
        </div>
        <div class="tablenav bottom textright">
          <button type="button" class="button" onclick="jQuery(this).closest('.silver-instagram-connect').hide()">
            <?php esc_html_e( 'Okay', 'silver' ); ?>
          </button>
        </div>
      <?php else : ?>
      <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
        <input type="hidden" name="action" value="silver_instagram_connect" />
        <?php wp_nonce_field( 'silver_instagram_connect', 'silver_instagram_connect_nonce' ); ?>
        <table class="widefat striped">
          <tbody>
            <?php foreach ( $accounts as $account ) : ?>
            <tr>
              <th class="check-column">
                <?php if ( ! array_key_exists( $account['instagram_business_account']['username'], $current_accounts ) ) : ?>
                <input type="checkbox" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][id]" value="<?php echo esc_attr( $account['instagram_business_account']['id'] ) ?>">
                <?php else : ?>
                <input type="checkbox" disabled checked>
                <?php endif ?>
                <?php if ( ! empty( $account['instagram_business_account']['profile_picture_url'] ) ) : ?>
                <input type="hidden" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][profile_picture]" value="<?php echo esc_attr( $account['instagram_business_account']['profile_picture_url']) ?>">
                <?php endif ?>
                <input type="hidden" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][username]" value="<?php echo esc_attr( $account['instagram_business_account']['username'] ) ?>">
                <input type="hidden" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][account_id]" value="<?php echo esc_attr( $account['id'] ) ?>">
                <input type="hidden" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][account_access_token]" value="<?php echo esc_attr( $account['access_token'] ) ?>">
                <input type="hidden" name="instagram_accounts[<?php echo esc_attr( $account['id'] ) ?>][access_token]" value="<?php echo esc_attr( $access_token ) ?>">
              </th>
              <td width="38">
                <?php if ( ! empty( $account['instagram_business_account']['profile_picture_url'] ) ) : ?>
                <img src="<?php echo esc_attr( $account['instagram_business_account']['profile_picture_url'] ) ?>" width="38" style="border-radius: 100%" alt="<?php echo esc_attr( $account['instagram_business_account']['username'] ) ?>">
                <?php else : ?>
                <img src="<?php echo get_template_directory_uri() ?>/inc/admin/advanced/assets/images/instagram-profile.png" width="38" alt="<?php echo esc_attr( $account['instagram_business_account']['username'] ) ?>">
                <?php endif ?>
              </td>
              <td class="title">
                <strong class="row-title">
                <?php if ( ! empty( $account['instagram_business_account']['name'] ) ) : ?>
                <?php echo esc_html( $account['instagram_business_account']['name'] ) ?>
                <?php elseif ( ! empty( $account['name'] ) ) : ?>
                <?php echo esc_html( $account['name'] ) ?>
                <?php endif ?>
                </strong>
                <br>
                <a target="_blank" href="https://www.instagram.com/<?php echo esc_attr( $account['instagram_business_account']['username'] ) ?>/">
                  <?php echo '@' . esc_html( $account['instagram_business_account']['username'] ) ?>
                </a>
              </td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <div class="tablenav bottom textright">
          <button type="button" class="button" onclick="jQuery(this).closest('.silver-instagram-connect').hide()">
            <?php esc_html_e( 'Cancel', 'silver' ); ?>
          </button>
          <button name="silver_instagram_connect" class="button button-primary">
            <?php esc_html_e( 'Connect', 'silver' ); ?>
          </button>
        </div>
      </form>
      <?php endif ?>
    </div>
  </div>

  <?php
}
add_action( 'admin_footer-silver_page_optionsframework', 'silver_facebook_connect_admin_footer' );

/**
 * Saves the connected accounts data.
 */
function silver_facebook_connect_accounts() {
  check_admin_referer( 'silver_instagram_connect', 'silver_instagram_connect_nonce' );

  if ( ! empty( $_POST['instagram_accounts'] ) ) {
    $accounts = silver_facebook_accounts();

    foreach ( $_POST['instagram_accounts'] as $values ) {
      $account = array_map( 'sanitize_text_field', $values );
      if ( isset( $account['id'] ) ) {
        $accounts[ $account['username'] ] = $account;
      }
    }

    set_theme_mod( 'facebook_accounts', $accounts );
  }

  wp_safe_redirect( admin_url( 'admin.php?page=optionsframework&tab=of-option-instagram' ) );
}
add_action( 'admin_post_silver_instagram_connect', 'silver_facebook_connect_accounts' );

/**
 * Deletes the Instagram oEmbed cache and transients.
 *
 * @return void
 */
function silver_facebook_clear_cache() {
  global $wpdb;

  if ( isset( $_POST['silver_instagram_clear_cache'] ) ) {
    delete_option( 'flatsome_instagram_oembed_cache' );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('%\_transient\_silver\_instagram%');" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE ('%\_transient\_timeout\_silver\_instagram%');" );
  }
}
add_action( 'of_save_options_before', 'silver_facebook_clear_cache' );

/**
 * Validate an Instagram access token.
 *
 * @return void
 */
function silver_ajax_validate_instagram_access_token() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'silver_advanced' ) ) {
		wp_send_json_error( __( 'Invalid nonce.', 'silver' ) );
	}

	$access_token = isset( $_POST['access_token'] )
		? sanitize_text_field( wp_unslash( $_POST['access_token'] ) )
		: '';

	if ( empty( $access_token ) ) {
		wp_send_json_error( __( 'Invalid access token.', 'silver' ) );
	}

	$response = wp_remote_get(
		add_query_arg(
			array(
				'fields'       => 'id,username',
				'access_token' => $access_token,
			),
			'https://graph.instagram.com/me'
		),
		array(
			'timeout' => 30,
		)
	);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( $response->get_error_message() );
	} elseif ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
		wp_send_json_error( __( 'Invalid access token.', 'silver' ) );
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( ! isset( $data['username'] ) ) {
		wp_send_json_error( __( 'No username found for the access token', 'silver' ) );
	}

	// Set the expiration date to a week from now.
	$data['expires_at']   = time() + WEEK_IN_SECONDS;
	$data['access_token'] = $access_token;

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_silver_validate_instagram_access_token', 'silver_ajax_validate_instagram_access_token' );
