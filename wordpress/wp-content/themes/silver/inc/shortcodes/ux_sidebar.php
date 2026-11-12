<?php


function flatsome_sidebar_shortcode( $atts ){

  extract( ux_builder_shortcode_atts( array(
    '_id' => '',
    'id' => 'sidebar-main',
    'class' => '',
    'visibility' => '',
    'style' => ''
  ), $atts ) );

  // Stop if visibility is hidden
  if($visibility == 'hidden') return;


	$classes	= array( 'sidebar-wrapper' , 'ul-reset' );
	if( $class ) $classes[] = $class;
	if( $visibility ) $classes[] = $visibility;
	if( $style ) $classes[] = 'widgets-'.$style;

	$classes = implode(' ', $classes);

	ob_start();
	dynamic_sidebar($id);
	$sidebar = trim( ob_get_clean() );

	return '<ul'.( $_id ? ' id="'.esc_attr( $_id ).'"' : '' ).' class="'.esc_attr($classes).'">'.$sidebar.'</ul>';

}
add_shortcode('ux_sidebar', 'flatsome_sidebar_shortcode');
