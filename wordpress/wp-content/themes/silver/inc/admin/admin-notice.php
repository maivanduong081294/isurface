<?php

add_action( 'admin_notices', 'silver_status_check_admin_notice' );
add_action( 'admin_notices', 'silver_maintenance_admin_notice' );

function silver_maintenance_admin_notice() {
	$screen       = get_current_screen();
	$advanced_url = get_admin_url() . 'admin.php?page=optionsframework&tab=';
	$errors       = flatsome_envato()->registration->get_errors();

	if ( get_theme_mod( 'maintenance_mode', 0 ) && get_theme_mod( 'maintenance_mode_admin_notice', 1 ) ) {
		?>
		<div class="notice notice-info">
				<p><?php echo sprintf( __( 'Silver Maintenance Mode is <strong>active</strong>. Please don\'t forget to <a href="%s">deactivate</a> it as soon as you are done.', 'silver' ), $advanced_url . 'of-option-maintenancemode' ); ?></p>
		</div>
		<?php
	}

	if (
		count( $errors ) &&
		flatsome_envato()->registration->get_option( 'show_notice' ) &&
		$screen->id !== 'toplevel_page_silver-panel'
	) {
		?>
		<div id="silver-notice" class="notice notice-warning notice-alt is-dismissible">
			<h3 class="notice-title"><?php esc_html_e( 'Silver issues', 'silver' ); ?></h3>
			<?php foreach ( $errors as $error ) : ?>
				<?php echo wpautop( $error ); ?>
			<?php endforeach; ?>
			<p>
				<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=silver-panel' ) ); ?>">
					<?php esc_html_e( 'Manage registration', 'silver' ); ?>
				</a>
			</p>
			<p>
				<a href="<?php echo esc_url_raw( UXTHEMES_ACCOUNT_URL ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Manage your licenses', 'silver' ); ?>
					<span class="dashicons dashicons-external" style="vertical-align:middle;font-size:18px;text-decoration: none;"></span>
				</a>
			</p>
			<script>
				jQuery(function($){
					$('#silver-notice').on('click', '.notice-dismiss', function(){
						$.post('<?php echo admin_url( 'admin-ajax.php?action=silver_registration_dismiss_notice' ) ?>');
					});
				});
			</script>
		</div>
		<?php
	}
}

/**
 * Outdated template files notice.
 *
 * @return void
 */
function silver_status_check_admin_notice() {
	if ( ! is_child_theme() ) return;
	if ( in_array( get_current_screen()->id, array( 'dashboard', 'themes', 'theme-editor', 'update-core', 'update-core-network', 'site-health' ), true )
		&& Silver\Admin\status()->has_outdated_template() ) {
		?>
		<div class="notice notice-info">
			<h3>
				<img src="<?php echo esc_url( silver_theme_logo_url() ); ?>" alt="Silver" width="80" height="20" style="vertical-align:middle;">
				<?php esc_html_e( 'Silver', 'silver' ); ?>
			</h3>
			<p>
				<?php /* translators: %1$s: Theme name, %2$s: The URL to the status page. */ ?>
				<?php echo sprintf( __( '<strong>Your theme (%1$s) contains outdated copies of some Silver template files.</strong> These files may need updating to ensure they are compatible with the current version of Silver. Suggestions:', 'silver' ), esc_html( wp_get_theme()->name ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
			</p>
			<ol>
				<li><?php esc_html_e( 'If you copied over a template file to change something, then you will need to copy the new version of the template and apply your changes again.', 'silver' ); ?></li>
				<li><?php esc_html_e( 'If you are unfamiliar with code/templates and resolving potential conflicts, reach out to a developer for assistance.', 'silver' ); ?></li>
			</ol>
			<p class="submit">
				<a class="button button-large" href="https://docs.uxthemes.com/article/414-system-status#templates" target="_blank" rel="noopener"><?php esc_html_e( 'Learn more about templates', 'silver' ); ?>
					<span style="font-size:16px;width:auto;vertical-align:middle;" class="dashicons dashicons-external"></span>
				</a>
				<a class="button button-large button-primary" href="<?php echo esc_url_raw( network_admin_url( 'admin.php?page=silver-panel-status#templates' ) ); ?>"><?php esc_html_e( 'View affected templates', 'silver' ); ?></a>
			</p>
		</div>
		<?php
	}
}
