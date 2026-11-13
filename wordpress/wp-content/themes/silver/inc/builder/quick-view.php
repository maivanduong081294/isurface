<?php
/**
 * "Quick view" action for the UX Builder toolbar.
 *
 * Adds a button to the right toolbar that shows the layout being edited
 * without the sidebar and the editing tools.
 *
 * @package silver
 */

/**
 * Enqueue the quick view assets in the UX Builder editor and preview iframe.
 *
 * @param string $context Context is «editor» or «content».
 */
function silver_ux_builder_quick_view_enqueue( $context ) {
	$uri     = get_template_directory_uri();
	$theme   = wp_get_theme( get_template() );
	$version = $theme->get( 'Version' );
	$style   = $uri . '/assets/css/builder/custom/quick-view.css';

	if ( 'editor' === $context ) {
		wp_enqueue_style( 'silver-ux-builder-quick-view', $style, array( 'ux-builder-core' ), $version );

		wp_enqueue_script(
			'silver-ux-builder-quick-view',
			$uri . '/assets/js/builder/custom/quick-view.js',
			array( 'ux-builder-core' ),
			$version,
			true
		);
	}

	if ( 'content' === $context ) {
		wp_enqueue_style( 'silver-ux-builder-quick-view', $style, array(), $version );
	}
}

// Priority 20: the button is added after the theme's own toolbar actions.
add_action( 'ux_builder_enqueue_scripts', 'silver_ux_builder_quick_view_enqueue', 20 );
