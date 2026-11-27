<?php
/**
 * Settings > Media: the "Medium large" image size (medium_large_size_w /
 * medium_large_size_h), which WordPress uses for srcset (768px wide by
 * default) but does not show on the screen. The row goes between "Medium
 * size" and "Large size".
 *
 * A new size applies to the images uploaded after the change; the existing
 * ones keep their files until the thumbnails are regenerated.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the two options on the Media screen (saved by options.php) and
 * their row.
 */
function silver_media_medium_large_register() {
	foreach ( array( 'medium_large_size_w', 'medium_large_size_h' ) as $option ) {
		register_setting( 'media', $option, array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
		) );
	}

	add_settings_field(
		'silver_medium_large_size',
		__( 'Medium large size', 'silver' ),
		'silver_media_medium_large_field',
		'media',
		'default'
	);
}
add_action( 'admin_init', 'silver_media_medium_large_register' );

/**
 * The row: Max Width / Max Height, like the core "Medium size" row.
 */
function silver_media_medium_large_field() {
	?>
	<fieldset id="silver-medium-large-size"><legend class="screen-reader-text"><span><?php esc_html_e( 'Medium large size', 'silver' ); ?></span></legend>
		<label for="medium_large_size_w"><?php esc_html_e( 'Max Width' ); ?></label>
		<input name="medium_large_size_w" type="number" step="1" min="0" id="medium_large_size_w" value="<?php form_option( 'medium_large_size_w' ); ?>" class="small-text" />
		<br />
		<label for="medium_large_size_h"><?php esc_html_e( 'Max Height' ); ?></label>
		<input name="medium_large_size_h" type="number" step="1" min="0" id="medium_large_size_h" value="<?php form_option( 'medium_large_size_h' ); ?>" class="small-text" />
		<p class="description"><?php esc_html_e( 'Used for responsive images (srcset) and the blog post cards. Max Height 0 = no limit. Applies to new uploads; regenerate the thumbnails for the existing images.', 'silver' ); ?></p>
	</fieldset>
	<?php
}

/**
 * Moves the row up, after "Medium size": the sizes in order.
 */
function silver_media_medium_large_order() {
	?>
	<script>
	( function () {
		var row = document.getElementById( 'silver-medium-large-size' );
		var medium = document.getElementById( 'medium_size_w' );
		row = row && row.closest( 'tr' );
		medium = medium && medium.closest( 'tr' );
		if ( row && medium && medium.nextElementSibling !== row ) {
			medium.parentNode.insertBefore( row, medium.nextElementSibling );
		}
	} )();
	</script>
	<?php
}
add_action( 'admin_print_footer_scripts-options-media.php', 'silver_media_medium_large_order' );
