<?php
/** CSS length and order controls shared by Builder elements. */
add_action( 'ux_builder_setup', function () {
	add_ux_builder_option_type( 'css-value' );
} );

add_action( 'ux_builder_enqueue_scripts', function ( $context ) {
	$file = '/assets/js/builder/custom/css-value.js';
	wp_enqueue_script( 'silver-builder-css-value', get_template_directory_uri() . $file,
		array( 'ux-builder-core' ), filemtime( get_template_directory() . $file ), true );
}, 20 );
