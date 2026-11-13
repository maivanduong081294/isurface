<?php

add_ux_builder_shortcode( 'block', array(
  'name' => __( 'Block', 'silver' ),
  'category' => __( 'Layout', 'silver' ),
  'compile' => false,
  'thumbnail' =>  silver_ux_builder_thumbnail( 'block' ),
  'template_shortcode' => "[{tag}{options}]\n\n",
  'external' => true,

  'options' => array(
    'id' => array(
      'type' => 'select',
      'heading' => __( 'Block', 'silver' ),
      'full_width' => true,
      'config' => array(
        'placeholder' => __( 'Select', 'silver' ),
        'postSelect' => array(
          'post_type' => array( 'blocks' )
        ),
      )
    ),
  ),
) );
