<?php 
// [message_box]
function silver_message_box($atts, $content = null) {
	extract(ux_builder_shortcode_atts(array(
		'_id' => '',
        'bg'  => '',
        'class' => '',
        'visibility' => '',
        'bg_color' => '',
        'text_color'  => 'dark',
        'padding' => '15',
	), $atts));

  if($visibility == 'hidden') return;

  $classes = array('message-box','relative');
  
  if( $class ) $classes[] = $class;
  if( $visibility ) $classes[] = $visibility;

  if($bg) {
    $bg = silver_get_image_url($bg);
  }

  if($text_color == 'dark') $classes[] = 'dark';

  $css_args = array(
      array( 'attribute' => 'padding-top', 'value' => $padding, 'unit' => 'px'),
      array( 'attribute' => 'padding-bottom', 'value' => $padding, 'unit' => 'px'),
   );
   $css_bg = array();
   if($bg) {
        $css_bg = array(
            array( 'attribute' => 'background-image', 'value' => 'url('.$bg.')'),
        );
    }
   $css_bg_overlay = array(
      array( 'attribute' => 'background-color', 'value' => $bg_color ),
   );

   // Responsive background image / color / text color (larger screens).
   $responsive_style = '';
   $bg_responsive = silver_responsive_background_is_responsive( $atts );
   if ( $bg_responsive || silver_responsive_any_overrides( (array) $atts, array( 'bg_color', 'text_color' ) ) ) {
     if ( ! $_id ) $_id = 'message-box-' . wp_rand();

     // Background image per breakpoint ('' = hidden by "Image visibility").
     $bg_values = array();
     if ( $bg_responsive ) {
       foreach ( silver_responsive_background_values( (array) $atts ) as $index => $image ) {
         $url                 = $image ? silver_get_image_url( $image ) : '';
         $bg_values[ $index ] = $url ? 'background-image: url(' . esc_url_raw( $url ) . ')' : 'background-image: none';
       }
     }

     $responsive_style = silver_responsive_style_tag( $_id, (array) $atts, array_merge(
       array(
         'bg'       => array(
           'selector' => '.message-box-bg-image',
           'values'   => $bg_values,
           'callback' => 'strval',
         ),
         'bg_color' => array(
           'selector'       => '.message-box-bg-overlay',
           'property'       => 'background-color',
           'only_overrides' => true,
         ),
       ),
       // "dark" adds the `dark` class (light text).
       silver_responsive_scheme_rules( 'text_color', '', 'dark', 'light', array( 'only_overrides' => true, 'default' => 'dark' ) )
     ) );
   }

	return '<div'.( $_id ? ' id="'.esc_attr( $_id ).'"' : '' ).' class="' . esc_attr( implode( ' ', $classes ) ) . '" '.get_shortcode_inline_css($css_args).'><div class="message-box-bg-image bg-fill fill" '.get_shortcode_inline_css($css_bg).'></div><div class="message-box-bg-overlay bg-fill fill" '.get_shortcode_inline_css($css_bg_overlay).'></div><div class="container relative"><div class="inner last-reset">'.do_shortcode($content).'</div></div>'.$responsive_style.'</div>';
}

add_shortcode("message_box", "silver_message_box");


