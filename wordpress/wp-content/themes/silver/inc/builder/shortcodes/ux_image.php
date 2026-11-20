<?php

$position_options = require(__DIR__ . '/commons/position.php');
$position_options['options']['position_x']['on_change'] = array(
    'recompile' => false,
    'class' => 'x{{ value }} md-x{{ value }} xl-x{{ value }}'
);
$position_options['options']['position_y']['on_change'] = array(
    'recompile' => false,
    'class' => 'y{{ value }} md-y{{ value }} xl-y{{ value }}'
);

add_ux_builder_shortcode('ux_image', array(
    'name' => __('Image', 'ux-builder'),
    'category' => __('Content'),
    'toolbar_thumbnail' => 'id',
    'thumbnail' =>  silver_ux_builder_thumbnail('ux_image'),
    'wrap' => false,

    'presets' => array(
        array(
            'name' => __('Blank'),
            'content' => '[ux_image][/ux_image]',
        ),
    ),

    'options' => array(

        'image_id' => array(
            'type' => 'image',
            'heading' => __('Image'),
            'default' => ''
        ),
        'image_size' => array(
            'type' => 'select',
            'heading' => 'Image Size',
            'param_name' => 'image_size',
            'default' => 'large',
            'options' => silver_ux_builder_image_sizes(),
        ),
        // Typed value (drag to scrub): a number is %, or any unit
        // (300px, 20rem, 50vw, calc(...)). Old values (numbers) stay in %.
        'width' => array(
            'type' => 'scrubfield',
            'heading' => 'Width',
            'description' => 'Number = %. Or a unit: 300px, 20rem, 50vw, calc(...).',
            'responsive' => true,
            'default' => '100',
            'placeholder' => '100',
            'min' => 0,
            'step' => 1,
        ),
        'height' => array(
            'responsive' => true,
            'type' => 'scrubfield',
            'heading' => __('Height'),
            'default' => '',
            'placeholder' => __('Auto'),
            'min' => 0,
            'max' => 1000,
            'step' => 1,
            'helpers' => require(__DIR__ . '/helpers/image-heights.php'),
            'on_change' => array(
                'selector' => '.image-cover',
                'style' => 'padding-top: {{ value }}'
            )
        ),
        'margin' => array(
            'type' => 'margins',
            'heading' => __('Margin'),
            'value' => '',
            'full_width' => true,
            'min' => -100,
            'max' => 100,
            'step' => 1,
        ),
        // Space around the image (top / right / bottom / left), per screen.
        'padding' => array(
            'type' => 'margins',
            'heading' => __('Padding'),
            'full_width' => true,
            'responsive' => true,
            'min' => 0,
            'max' => 200,
            'step' => 1,
        ),
        // Rounded corners, per screen: a number is px, or a unit (8px, 1rem, 50%).
        'radius' => array(
            'type' => 'scrubfield',
            'heading' => __('Radius'),
            'description' => 'Number = px. Or a unit: 8px, 1rem, 50%.',
            'responsive' => true,
            'default' => '',
            'placeholder' => '0',
            'min' => 0,
            'step' => 1,
        ),
        // Background behind the image (seen through the padding or a
        // transparent image), per screen.
        'bg_color' => array(
            'type' => 'colorpicker',
            'heading' => __('Background'),
            'responsive' => true,
            'alpha' => true,
            'format' => 'rgb',
            'position' => 'bottom right',
            'helpers' => require(__DIR__ . '/helpers/colors.php'),
        ),
        'lightbox' => array(
            'type' => 'radio-buttons',
            'heading' => __('Lightbox'),
            'default' => '',
            'options' => array(
                ''  => array('title' => 'Off'),
                'true'  => array('title' => 'On'),
            ),
        ),

        'lightbox_image_size' => array(
            'type'       => 'select',
            'heading'    => __('Lightbox Image Size'),
            'conditions' => 'lightbox == "true"',
            'default'    => 'large',
            'options'    => silver_ux_builder_image_sizes(),
        ),

        'caption' => array(
            'type' => 'radio-buttons',
            'heading' => __('Caption'),
            'default' => '',
            'options' => array(
                ''  => array('title' => 'Off'),
                'true'  => array('title' => 'On'),
            ),
        ),

        'lightbox_caption'    => array(
            'type'       => 'radio-buttons',
            'heading'    => __('Caption on Lightbox'),
            'conditions' => 'lightbox == "true"',
            'default'    => '',
            'options'    => array(
                ''     => array('title' => 'Off'),
                'true' => array('title' => 'On'),
            ),
        ),

        'image_title' => array(
            'type'    => 'radio-buttons',
            'heading' => __('Title'),
            'default' => '',
            'options' => array(
                ''      => array('title' => 'Off'),
                'true'  => array('title' => 'On'),
            ),
        ),

        'image_overlay' => array(
            'type' => 'colorpicker',
            'heading' => __('Image Overlay'),
            'default' => '',
            'alpha' => true,
            'format' => 'rgb',
            'position' => 'bottom right',
            'on_change' => array(
                'selector' => '.overlay',
                'style' => 'background-color: {{ value }}',
            ),
        ),

        'image_hover' => array(
            'type' => 'select',
            'heading' => 'Image Hover',
            'default' => '',
            'options' => require(__DIR__ . '/values/image-hover.php'),
            'on_change' => array(
                'selector' => '.img-inner',
                'class' => 'image-{{ value }}'
            )
        ),

        'image_hover_alt' => array(
            'type' => 'select',
            'heading' => 'Image Hover Alt',
            'default' => '',
            'options' => require(__DIR__ . '/values/image-hover.php'),
            'on_change' => array(
                'selector' => '.img-inner',
                'class' => 'image-{{ value }}'
            )
        ),

        'depth' => array(
            'type' => 'slider',
            'heading' => 'Depth',
            'default' => '0',
            'max' => '5',
            'min' => '0',
            'on_change' => array(
                'selector' => '.img-inner',
                'class' => 'box-shadow-{{ value }}'
            )
        ),

        'depth_hover' => array(
            'type' => 'slider',
            'heading' => 'Depth :hover',
            'default' => '0',
            'max' => '5',
            'min' => '0',
            'on_change' => array(
                'selector' => '.img-inner',
                'class' => 'box-shadow-{{ value }}-hover'
            )
        ),
        'parallax' => array(
            'type' => 'slider',
            'heading' => 'Parallax',
            'default' => '0',
            'max' => '10',
            'min' => '-10',
        ),
        'animate' => array(
            'type' => 'select',
            'heading' => 'Animate',
            'default' => 'none',
            'options' => require(__DIR__ . '/values/animate.php'),
        ),
        'link_options' => require(__DIR__ . '/commons/links.php'),
        'position_options' => $position_options,
        'advanced_options' => require(__DIR__ . '/commons/advanced.php'),
    ),
));
