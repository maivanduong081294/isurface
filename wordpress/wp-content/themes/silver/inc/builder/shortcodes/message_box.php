<?php

add_ux_builder_shortcode( 'message_box', array(
    'name' => __( 'Message Box' ),
    'type' => 'container',
    'category' => __( 'Content' ),
    'thumbnail' =>  silver_ux_builder_thumbnail( 'message_box' ),
    'template' => silver_ux_builder_template( 'message_box.html' ),
     'presets' => array(
        array(
            'name' => __( 'Default' ),
            'content' => '[message_box] [row_inner v_align="middle" h_align="center"] [col_inner span__lg="9" span="12"] <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p> [/col_inner] [col_inner span__lg="2" span="12" align="center"] [button text="Click me" color="white" style="outline" radius="99"] [/col_inner] [/row_inner] [/message_box]',
        ),
    ),
    'options' => array(
        'bg' => array(
          'responsive' => true,
          'type' => 'image',
          'heading' => __( 'BG Image' ),
          'thumb_size' => 'bg_size',
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
        'bg_color' => array(
          'responsive' => true,
          'type' => 'colorpicker',
          'helpers' => require( __DIR__ . '/helpers/colors.php' ),
          'heading' => __('Bg Color'),
          'alpha' => true,
          'format' => 'rgb',
          'position' => 'bottom right',
        ),
        'text_color' => array(
            'responsive' => true,
            'type' => 'radio-buttons',
            'heading' => __( 'Color' ),
            'default' => 'dark',
            'options' => array(
                'light' => array( 'title' => 'Dark' ),
                'dark' => array( 'title' => 'Light' ),
            ),
        ),
        'padding' => array(
          'type' => 'slider',
          'heading' => __('Padding'),
          'max' => 200,
          'min' => 0,
          'default' => 15
        ),
        'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
    ),
) );
