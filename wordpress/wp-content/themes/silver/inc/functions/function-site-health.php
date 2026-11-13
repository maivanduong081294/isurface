<?php

/**
 * Adds site status tests for Silver registration.
 *
 * @param array $tests An associative array of tests.
 * @return array
 */
function silver_site_status_tests( $tests ) {
	$tests['direct']['flatsome_registration'] = array(
		'label' => __( 'Silver registration', 'silver' ),
		'test'  => 'silver_site_health_registration_test',
	);

	return $tests;
}
add_filter( 'site_status_tests', 'silver_site_status_tests' );

/**
 * Performs the registration status test.
 *
 * @return array
 */
function silver_site_health_registration_test() {
	$errors = flatsome_envato()->registration->get_errors();
	$result = array(
		'test'        => 'flatsome_registration',
		'label'       => __( 'Silver is registered', 'silver' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Security', 'silver' ),
			'color' => 'blue',
		),
		'description' => sprintf( '<p>%s</p>', __( 'Register Silver to receive updates.', 'silver' ) ),
		'actions'     => '',
	);

	if ( ! flatsome_envato()->registration->is_registered() ) {
		$result['status']  = 'critical';
		$result['label']   = __( 'Silver is not registered', 'silver' );
		$result['actions'] = sprintf(
			'<p><a href="%s">%s</a></p>',
			esc_url( admin_url( 'admin.php?page=silver-panel' ) ),
			__( 'Register now', 'silver' )
		);
	} elseif ( ! empty( $errors ) ) {
		$result['status']      = 'critical';
		$result['label']       = __( 'Silver was unable to receive the latest update', 'silver' );
		$result['description'] = sprintf( '<p>%s</p>', implode( '</p><p>', $errors ) );
		?>
		<p>
			<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=silver-panel' ) ); ?>">
				<?php esc_html_e( 'Manage registration', 'silver' ); ?>
			</a>
		</p>
		<p>
			<a href="<?php echo esc_url_raw( UXTHEMES_ACCOUNT_URL ); ?>" target="_blank" rel="noopener">
				<?php esc_html_e( 'Manage your licenses', 'silver' ); ?>
			</a>
		</p>
		<?php
		$result['actions'] = ob_get_clean();
	}

	return $result;
}
