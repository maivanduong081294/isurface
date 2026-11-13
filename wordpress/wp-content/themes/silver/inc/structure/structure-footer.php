<?php

// Get Mobile Sidebar Menu
function silver_mobile_menu(){
   get_template_part('template-parts/overlays/overlay','menu');
}
add_action('wp_footer', 'silver_mobile_menu', 7);


function silver_footer_row_style($footer){
	$classes = array('row');
	if($footer == 'footer-1'){
		$columns = get_theme_mod('footer_1_columns', 4);

		if(in_array( $columns, array( 'large-3', 'xlarge-3' ), true )){
			$columns = '4';
		}
		if(in_array( $columns, array( 'large-2', 'xlarge-2' ), true )){
			$columns = '6';
		}
		if(in_array( $columns, array( 'large-12', 'xlarge-12' ), true )){
			$columns = '1';
		}
		if(in_array( $columns, array( 'large-4', 'xlarge-4' ), true )){
			$columns = '3';
		}

		if(get_theme_mod('footer_1_color') == 'dark') $classes[] = 'dark';
		 $classes[] = 'xlarge-columns-'.$columns;
	}
	if($footer == 'footer-2'){
		$columns = get_theme_mod('footer_2_columns', 4);
		if(in_array( $columns, array( 'large-3', 'xlarge-3' ), true )){
			$columns = '4';
		}
		if(in_array( $columns, array( 'large-2', 'xlarge-2' ), true )){
			$columns = '6';
		}
		if(in_array( $columns, array( 'large-12', 'xlarge-12' ), true )){
			$columns = '1';
		}
		if(in_array( $columns, array( 'large-4', 'xlarge-4' ), true )){
			$columns = '3';
		}
		if(get_theme_mod('footer_2_color', 'dark') == 'dark') $classes[] = 'dark';
		$classes[] = 'xlarge-columns-'.$columns;
	}

	return implode(' ', $classes);
}

function silver_page_footer(){
	$block = get_theme_mod( 'footer_block' );

	if ( is_page() ) {
		// Custom Page footers.
		$page_footer = get_post_meta( get_the_ID(), '_footer', true );
		$default     = empty( $page_footer ) || $page_footer == 'normal';

		if ( $page_footer !== 'disabled' ) {
			if ( ! $block ) {
				if ( $default ) {
					get_template_part( 'template-parts/footer/footer' );
				} elseif ( ! empty( $page_footer ) ) {
					get_template_part( 'template-parts/footer/footer', $page_footer );
				}
			} else {
				echo do_shortcode( '[block id="' . $block . '"]' );
				get_template_part( 'template-parts/footer/footer-absolute' );
			}
		}
	} else {
		// Global footer.
		if ( $block ) {
			echo do_shortcode( '[block id="' . $block . '"]' );
			get_template_part( 'template-parts/footer/footer-absolute' );
		} else {
			get_template_part( 'template-parts/footer/footer' );
		}
	}
}

add_action( 'silver_footer', 'silver_page_footer', 10 );


// Add Top Link
function silver_go_to_top(){
	if(!get_theme_mod('back_to_top', 1)) return;
	get_template_part('template-parts/footer/back-to-top');
}
add_action( 'silver_footer', 'silver_go_to_top');


/* Custom footer scripts */
function silver_footer_scripts(){
  echo do_shortcode(get_theme_mod('html_scripts_footer'));
}
add_action('wp_footer', 'silver_footer_scripts');

/* Insert custom body bottom script */
function silver_before_body_close() {
	if ( get_theme_mod( 'html_scripts_before_body' ) && ! is_admin() ) {
		echo get_theme_mod( 'html_scripts_before_body' ); // WPCS: XSS ok.
	}
}

add_action( 'wp_footer', 'silver_before_body_close', apply_filters( 'silver_before_body_close_priority', 9999 ) );

/**
 * Adds body-overlay element if enabled for one of the header positions.
 */
function silver_body_overlay() {
	if ( get_theme_mod( 'nav_top_body_overlay' ) || get_theme_mod( 'nav_body_overlay' ) || get_theme_mod( 'nav_bottom_body_overlay' ) ) {
		echo '<div class="ux-body-overlay"></div>';
	}
}

add_action( 'wp_footer', 'silver_body_overlay' );


// Custom HTML Before footer
function silver_html_before_footer(){
  $html_before = get_theme_mod('html_before_footer');
  if($html_before){
    echo do_shortcode($html_before);
  }
}
add_action('silver_before_footer', 'silver_html_before_footer');

// Custom HMTL After footer
function silver_html_after_footer(){
	$html_after = get_theme_mod('html_after_footer');
	if($html_after){
	 echo do_shortcode($html_after);
	}
}
add_action('silver_after_footer', 'silver_html_after_footer');

