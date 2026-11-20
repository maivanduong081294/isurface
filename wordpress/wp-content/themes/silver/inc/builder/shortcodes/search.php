<?php


add_ux_builder_shortcode( 'search', array(
  'name' => __( 'Search Box' ),
  'category' => __( 'Content' ),
  'thumbnail' => silver_ux_builder_thumbnail( 'search' ),
  'wrap' => false,
  'allow_in' => array('text_box'),
  'presets' => array(
    array(
      'name' => __( 'Default' ),
      'content' => '[search]',
      ),
    array(
      'name' => __( 'Pill, icon left' ),
      'content' => '[search style="flat" icon_pos="left" radius="99" grow="true"]',
      ),
    array(
      'name' => __( 'Icon (mobile)' ),
      'content' => '[search display="icon-lightbox" visibility="show-for-medium"]',
      ),
    ),
    'options' => array(
      // Box: the search field. Icon: a search icon that opens the field in a
      // dropdown or a full-screen lightbox (compact headers, mobile).
      'display' => array(
          'type' => 'radio-buttons',
          'heading' => __( 'Display' ),
          'default' => '',
          'options' => array(
              '' => array( 'title' => 'Box' ),
              'icon-dropdown' => array( 'title' => 'Icon + dropdown' ),
              'icon-lightbox' => array( 'title' => 'Icon + lightbox' ),
          ),
        ),

      'style' => array(
          'type' => 'select',
          'heading' => __( 'Style' ),
          'options' => array(
              '' => 'Normal',
              'flat' => 'Flat',
          )
        ),

      'size' => array(
              'type' => 'radio-buttons',
              'heading' => __( 'Size' ),
              'default' => 'medium',
              'options' => require( __DIR__ . '/values/text-sizes.php' ),
              'on_change' => array(
                  'class' => 'is-{{ value }}'
              )
        ),

      'placeholder' => array(
          'type' => 'textfield',
          'heading' => __( 'Placeholder' ),
          'description' => __( 'Empty: Header > Search > Placeholder.' ),
          'default' => '',
        ),

      'box_options' => array(
          'type' => 'group',
          'heading' => __( 'Box' ),
          'conditions' => 'display === ""',
          'options' => array(
              // Per screen: only a search icon; a click opens the field as a
              // full-width bar under the header row (or the parent block).
              'collapse' => array(
                  'type' => 'radio-buttons',
                  'heading' => __( 'Collapse to icon' ),
                  'description' => __( 'Per screen. The icon opens the field in a bar below.' ),
                  'responsive' => true,
                  'default' => 'false',
                  'options' => array(
                      'false' => array( 'title' => 'No' ),
                      'true' => array( 'title' => 'Yes' ),
                  ),
                ),
              'icon_pos' => array(
                  'type' => 'radio-buttons',
                  'heading' => __( 'Search icon' ),
                  'default' => '',
                  'options' => array(
                      '' => array( 'title' => 'Button right' ),
                      'left' => array( 'title' => 'Icon left' ),
                  ),
                ),
              'width' => array(
                  'type' => 'slider',
                  'heading' => __( 'Max width' ),
                  'responsive' => true,
                  'unit' => 'px',
                  'default' => '',
                  'min' => 120,
                  'max' => 900,
                  'step' => 10,
                ),
              'grow' => array(
                  'type' => 'radio-buttons',
                  'heading' => __( 'Fill free space (in a Stack)' ),
                  'default' => '',
                  'options' => array(
                      '' => array( 'title' => 'No' ),
                      'true' => array( 'title' => 'Yes' ),
                  ),
                ),
              'radius' => array(
                  'type' => 'slider',
                  'heading' => __( 'Radius' ),
                  'unit' => 'px',
                  'default' => '',
                  'min' => 0,
                  'max' => 99,
                  'step' => 1,
                ),
              'bg_color' => array(
                  'type' => 'colorpicker',
                  'heading' => __( 'Field background' ),
                  'alpha' => true,
                  'format' => 'rgb',
                  'position' => 'bottom right',
                ),
              'border_color' => array(
                  'type' => 'colorpicker',
                  'heading' => __( 'Field border' ),
                  'alpha' => true,
                  'format' => 'rgb',
                  'position' => 'bottom right',
                ),
          ),
        ),

      'icon_options' => array(
          'type' => 'group',
          'heading' => __( 'Icon' ),
          'conditions' => 'display !== ""',
          'options' => array(
              'icon_size' => array(
                  'type' => 'slider',
                  'heading' => __( 'Icon size' ),
                  'responsive' => true,
                  'unit' => 'px',
                  'default' => '',
                  'min' => 12,
                  'max' => 48,
                  'step' => 1,
                ),
              'icon_color' => array(
                  'type' => 'colorpicker',
                  'heading' => __( 'Icon color' ),
                  'responsive' => true,
                  'alpha' => true,
                  'format' => 'rgb',
                  'position' => 'bottom right',
                  'helpers' => require __DIR__ . '/helpers/colors-text.php',
                ),
          ),
        ),

      'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
    )
) );
