<?php

function silver_sidebar_classes(){

   echo implode(' ',  apply_filters( 'silver_sidebar_class', array() ) );
}


function silver_add_sidebar_class($classes){
	//$classes[] = 'col-divided';
	//$classes[] = 'widgets-boxed';

	return $classes;
}

add_filter('silver_sidebar_class','silver_add_sidebar_class', 10);

/**
 * Renders the sidebar menu header content.
 */
function silver_mobile_sidebar_top_content() {
	if ( $top_content = get_theme_mod( 'mobile_sidebar_top_content' ) ) {
		echo '<div class="sidebar-menu-top-content">';
		echo do_shortcode( $top_content );
		echo '</div>';
	}
}

add_action( 'silver_before_sidebar_menu_elements', 'silver_mobile_sidebar_top_content' );
