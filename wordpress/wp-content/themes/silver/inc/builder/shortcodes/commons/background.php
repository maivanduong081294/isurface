<?php

return array(
      'type' => 'group',
      'heading' => __( 'Background' ),
      'options' => array(
        'bg' => array(
          'type' => 'image',
          'heading' => __( 'Image' ),
          'responsive' => true,
          'thumb_size' => 'bg_size',
          'bg_position' => 'bg_pos',
        ),
        // Hide the image on a screen: an empty image there means "inherit".
        'bg_visible' => array(
          'type' => 'radio-buttons',
          'heading' => __( 'Image visibility' ),
          'responsive' => true,
          'default' => 'show',
          'conditions' => 'bg',
          'options' => array(
          	'show' => array( 'title' => 'Show' ),
          	'hide' => array( 'title' => 'Hide' ),
          ),
        ),
        'bg_size'=> array(
          'type' => 'select',
          'heading' => 'Size',
          'responsive' => true,
          'default' => 'large',
          'conditions' => 'bg',
          'options' => silver_ux_builder_image_sizes(),
        ),
        'bg_color' => array(
          'type' => 'colorpicker',
          'heading' => __('Color'),
          'responsive' => true,
          'format' => 'rgb',
          'position' => 'bottom right',
          'helpers' => require( __DIR__ . '/../helpers/colors.php' ),
        ),
        'bg_overlay' => array(
          'type' => 'colorpicker',
          'heading' => __('Overlay'),
          'responsive' => true,
          'alpha' => true,
          'format' => 'rgb',
          'position' => 'bottom right',
          'helpers' => require( __DIR__ . '/../helpers/colors-overlay.php' ),
        ),
        'bg_pos' => array(
          'conditions' => 'bg',
          'type' => 'textfield',
          'heading' => __('Position'),
          'responsive' => true,
        ),
        'hover' => array(
          'conditions' => 'bg',
          'type' => 'select',
          'heading' => 'Hover',
          'options' => require( __DIR__ . '/../values/image-hover.php' ),
        ),
        'hover_alt' => array(
          'conditions' => 'hover',
          'type' => 'select',
          'heading' => 'Hover Alt',
          'options' => require( __DIR__ . '/../values/image-hover.php' ),
        ),
        'parallax' => array(
            'conditions' => 'bg',
            'type' => 'slider',
            'heading' => 'Parallax',
            'unit' => '+',
            'default' => 0,
            'max' => 10,
            'min' => 0,
        ),
       'effect' => array(
            'type' => 'select',
            'heading' => 'Effects',
            'options' => array(
              '' => 'No effect',
              'snow' => 'Snow',
              'confetti' => 'Confetti',
              'sliding-glass' => 'Sliding Glass',
              'sparkle' => 'Sparkle',
              'rain' => 'Rain',
            ),
        ),
    ),
);
