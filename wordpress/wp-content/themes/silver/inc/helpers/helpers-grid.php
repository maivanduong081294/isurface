<?php

function flatsome_get_grid_height( $height, $id ) {
  // Mobile-first heights keyed by breakpoint. A plain value is a desktop height.
  if ( ! is_array( $height ) ) $height = array( 'lg' => $height );
  $desktop   = isset( $height['lg'] ) ? $height['lg'] : '';
  $mobile    = isset( $height['sm'] ) ? $height['sm'] : '';
  $portrait  = isset( $height['md'] ) ? $height['md'] : '';
  $landscape = isset( $height['ml'] ) ? $height['ml'] : '';
  // Without a mobile height, scale the desktop height down as before.
  if ( ! strlen( (string) $mobile ) ) $mobile = strlen( (string) $desktop ) ? intval( $desktop ) / 1.5 : 400;
  $rules = array();
  $atts = array();

  foreach ( array( '1' => 1, '1-2' => 1 / 2, '1-3' => 1 / 3, '2-3' => 2 / 3, '1-4' => 1 / 4, '3-4' => 3 / 4 ) as $suffix => $ratio ) {
    $name = 'grid_' . $suffix;
    $rules[$name] = array( 'selector' => '.grid-col-' . $suffix, 'property' => 'height', 'unit' => 'px' );
    $atts[$name] = floatval( $mobile ) * $ratio;
    $atts[$name . '__lg'] = strlen( (string) $desktop ) ? floatval( $desktop ) * $ratio : null;
    $atts[$name . '__md'] = strlen( (string) $portrait ) ? floatval( $portrait ) * $ratio : null;
    $atts[$name . '__ml'] = strlen( (string) $landscape ) ? floatval( $landscape ) * $ratio : null;
  }

  echo ux_builder_element_style_tag( esc_attr( $id ), $rules, $atts );
}

if( ! function_exists( 'flatsome_get_grid' ) ) {
  function flatsome_get_grid($grid = 1){

    $g = array();

    if($grid == '1'){
      $g = array(
        array('height' => '1','span' => '6', 'size' => 'medium','md' => '12'),
        array('height' => '1','span' => '3', 'size' => 'medium','md' => '6'),
        array('height' => '1-2','span' => '3', 'size' => 'thumbnail','md' => '6'),
        array('height' => '1-2','span' => '3', 'size' => 'thumbnail','md' => '6'),
      );
    }
    if($grid == '2'){
      $g = array(
          array('height' => '1','span' => '12', 'size' => 'large','md' => '12'),
          array('height' => '1-3','span' => '4','size' => 'thumbnail','md' => '12'),
          array('height' => '1-3','span' => '4','size' => 'thumbnail','md' => '12'),
          array('height' => '1-3','span' => '4','size' => 'thumbnail','md' => '12'),
        );
    }

    if($grid == '3'){
      $g = array(
          array('height' => '1','span' => '6','size' => 'large','md' => '12'),
          array('height' => '1-2','span' => '6','size' => 'medium','md' => '6'),
          array('height' => '1-2','span' => '3','size' => 'thumbnail','md' => '6'),
        );
    }

    if($grid == '4'){
      $g = array(
         array('height' => '1','span' => '3','size' => 'large','md' => '6'),
      );
    }

    if($grid == '5'){
      $g = array(
         array('height' => '1','span' => '4','size' => 'large','md' => '6'),
      );
    }

    if($grid == '6'){
      $g = array(
          array('height' => '1','span' => '9','size' => 'large','md' => '12'),
          array('height' => '1-2','span' => '3','size' => 'thumbnail','md' => '6'),
          array('height' => '1-2','span' => '3','size' => 'thumbnail','md' => '6'),
        );
    }


    if($grid == '7'){
      $g = array(
         array('height' => '1','span' => '3','size' => 'medium', 'md' => '12'),
         array('height' => '1','span' => '6','size' => 'large','md' => '6'),
         array('height' => '1-2','span' => '3','size' => 'thumbnail','md' => '6'),
        );
    }

  if($grid == '8'){
      $g = array(
         array('height' => '1','span' => '3','size' => 'medium','md' => '12'),
         array('height' => '1','span' => '6','size' => 'large','md' => '6'),
         array('height' => '1','span' => '3','size' => 'medium','md' => '6'),
         array('height' => '1','span' => '3','size' => 'medium','md' => '6'),
        );
    }

  if($grid == '9'){
      $g = array(
         array('height' => '1','span' => '6','size' => 'large','md' => '12'),
         array('height' => '1','span' => '3','size' => 'medium','md' => '6'),
        );
    }

  if($grid == '10'){
      $g = array(
          array('height' => '1','span' => '6','size' => 'large','md' => '12'),
          array('height' => '1-3','span' => '6','size' => 'medium','md' => '6'),
          array('height' => '1-3','span' => '3','size' => 'thumbnail','md' => '6'),
          array('height' => '1-3','span' => '3','size' => 'thumbnail','md' => '6'),
          array('height' => '1-3','span' => '6','size' => 'medium','md' => '6'),
        );
    }

   if($grid == '11'){
      $g = array(
          array('height' => '2-3','span' => '6','md' => '12','size' => 'large'),
          array('height' => '2-3','span' => '3','md' => '6','size' => 'medium'),
          array('height' => '1','span' => '3','md' => '6','size' => 'medium'),
          array('height' => '2-3','span' => '3','md' => '6','size' => 'medium'),
          array('height' => '1-3','span' => '6','md' => '12','size' => 'medium'),

        );
    }

    if($grid == '12'){
      $g = array(
          array('height' => '1-2','span' => '8','md' => '12','size' => 'large'),
          array('height' => '1','span' => '4','md' => '6','size' => 'large',),
          array('height' => '1','span' => '4','md' => '6','size' => 'large',),
          array('height' => '1-2','span' => '8','md' => '6','size' => 'large'),
          array('height' => '1-2','span' => '8','md' => '6','size' => 'large'),
          array('height' => '1','span' => '4','md' => '6','size' => 'large'),
        );
    }


    if($grid == '13'){
      $g = array(
          array('height' => '2-3','span' => '6','md' => '12','size' => 'large'),
          array('height' => '1-2','span' => '3','md' => '6','size' => 'thumbnail'),
          array('height' => '1','span' => '3','md' => '6','size' => 'medium'),
          array('height' => '1-2','span' => '3','md' => '6','size' => 'thumbnail'),
          array('height' => '1-3','span' => '6','md' => '12','size' => 'medium'),
        );
    }

    if($grid == '14'){
      $g = array(
          array('height' => '1-2','span' => '8', 'md' => '12','size' => 'medium'),
          array('height' => '1','span' => '4', 'md' => '6','size' => 'medium'),
          array('height' => '1','span' => '4', 'md' => '6','size' => 'medium'),
          array('height' => '1','span' => '2','md' => '6','size' => 'medium'),
          array('height' => '1','span' => '2','md' => '6','size' => 'medium'),
          array('height' => '1-2','span' => '4','md' => '6','size' => 'medium'),
        );
     }

     return $g;
  }
}
