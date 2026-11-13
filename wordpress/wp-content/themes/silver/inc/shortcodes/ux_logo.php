<?php
// [logo img=""]
function ux_logo( $atts, $content = null ){
    extract( ux_builder_shortcode_atts( array(
      '_id' => '',
      'class' => '',
      'visibility' => '',
      'img' => '',
      'image_size' => 'original',
      'padding' => '15px',
      'title' => '',
      'hover' => '',
      'link' => '',
      'target' => '_self',
      'rel' => '',
      'height' => '50',
    ), $atts ) );

    $classes = array('ux-logo', 'has-hover', 'align-middle', 'ux_logo', 'inline-block');
	$org_img = '';

    if ( $class ) $classes[] = $class;
    if ( $visibility ) $classes[] = $visibility;

  	$height    = intval( $height );
  	$width     = 'auto';
  	$link_atts = array(
  		'target' => $target,
  		'rel'    => array( $rel ),
  	);

    if(!$img){
      $org_img = silver_theme_logo_url();
      $width = ($height / 84) * 400 + (intval($padding)*2).'px';
    }

	if ( $img && ! is_numeric( $img ) ) {
		$org_img = $img;
	} elseif ( $img ) {
		$img_src = wp_get_attachment_image_src( $img, $image_size );

		if ( $img_src ) {
			$org_img    = $img_src[0];
			$org_height = $img_src[2];
			// Check if width and height is set, because svg images has no size.
			if ( $img_src[1] > 0 && $img_src[2] > 0 ) {
				$width = $img_src[1];
				$width = ( intval( $height ) / intval( $org_height ) ) * intval( $width ) + ( intval( $padding ) * 2 ) . 'px';
			} else {
				$width = 'auto';
			}
		}
	}

    // Set inner tag
    $inner_tag = $link ? 'a' : 'div';

    $wrapper_style    = 'max-width: 100%!important; width: ' . esc_attr( $width ) . '!important';
    $image_style      = 'height:' . esc_attr( $height ) . 'px;';
    $responsive_style = '';

    // Responsive height: the width follows the height (same ratio as above),
    // so both are printed per breakpoint instead of inline.
    if ( silver_responsive_has_overrides( (array) $atts, 'height' ) ) {
      if ( ! $_id ) $_id = 'logo-' . wp_rand();

      $ratio = null; // Width / height of the image, null when unknown (svg).
      if ( ! $img ) {
        $ratio = 400 / 84;
      } elseif ( isset( $img_src ) && $img_src && $img_src[1] > 0 && $img_src[2] > 0 ) {
        $ratio = $img_src[1] / $img_src[2];
      }

      $wrapper_values = array();
      $image_values   = array();
      foreach ( silver_responsive_filled( (array) $atts, 'height', $height ) as $index => $value ) {
        $value                    = intval( $value );
        $image_values[ $index ]   = 'height: ' . $value . 'px';
        $wrapper_values[ $index ] = 'max-width: 100%; width: ' . ( $ratio ? round( $value * $ratio + intval( $padding ) * 2, 2 ) . 'px' : 'auto' );
      }

      $wrapper_style    = '';
      $image_style      = '';
      $responsive_style = silver_responsive_style_tag( $_id, (array) $atts, array(
        'height|wrapper' => array( 'selector' => '', 'values' => $wrapper_values, 'callback' => 'strval' ),
        'height|image'   => array( 'selector' => '.ux-logo-image', 'values' => $image_values, 'callback' => 'strval' ),
      ) );
    }

    $content = $responsive_style . '<div' . ( $_id ? ' id="' . esc_attr( $_id ) . '"' : '' ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( $wrapper_style ? ' style="' . $wrapper_style . '"' : '' ) . '><' . $inner_tag . ' class="ux-logo-link block image-' . esc_attr( $hover ) . '" title="' . esc_attr( $title ) . '"' . silver_parse_target_rel( $link_atts ) . 'href="' . esc_url( $link ) . '" style="padding: ' . esc_attr( $padding ) . ';"><img src="' . esc_url( $org_img ) . '" title="' . esc_attr( $title ) . '" alt="' . esc_attr( $title ) . '" class="ux-logo-image block"' . ( $image_style ? ' style="' . $image_style . '"' : '' ) . ' /></' . $inner_tag . '></div>';

    return $content;
}
add_shortcode('logo', 'ux_logo');
