<?php

add_ux_builder_shortcode( 'testimonial', array(
    'type' => 'container',
    'name' => __( 'Testimonial' ),
    'category' => __( 'Content' ),
    'wrapper' => false,
    'thumbnail' =>  silver_ux_builder_thumbnail( 'testimonials' ),
    'allow_in' => array('text_box'),
    'presets' => array(
        array(
          'name' => __( 'Default' ),
          'content' => '[testimonial]<h3>Lorem ipsum dolor sit amet</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat....</p>[/testimonial]'
        ),
        array(
          'name' => __( 'Row - Image Left' ),
          'content' => '[row] [col span__lg="6" span="12"] [testimonial image="9147" image_width="121" name="Mark Jance" company="Facebook"] <p>The overall use of Silver is very VERY useful. It lacks very few, if any, things! I loved it and have created my first ever website Punsteronline.com! Best yet, Silver gets free updates that are great! (and the support is amazing as well!:)</p> [/testimonial] [/col] [col span__lg="6" span="12"] [testimonial image="9149" image_width="121" name="Mark Jance" company="Facebook"] <p>This is a FANTASTIC Theme. Do you think that in the next version you could try and have it Multilanguage. Because I have nothing bad to say about this theme. Thank a million!</p> [/testimonial] [/col] [/row]'
        ),
         array(
          'name' => __( 'Row - Image On Top' ),
          'content' => '[row] [col span__lg="4" span="12"] [testimonial image="9147" image_width="121" pos="center" name="Mark Jance" company="Facebook"] <p>The overall use of Silver is very VERY useful. It lacks very few, if any, things! I loved it and have created my first ever website Punsteronline.com! Best yet, Silver gets free updates that are great! (and the support is amazing as well!:)</p> [/testimonial] [/col] [col span__lg="4" span="12"] [testimonial image="9149" image_width="121" pos="center" name="Mark Jance" company="Facebook"] <div class="e-box h-p2 -stacked -radius-none"> <p class="t-body h-my1">This theme is amazing, you can customize EVERYTHING! The theme is a game changer for the Envato Market, cant wait for the future with Silver. Soo many good experiences from this, THANKS!</p> </div> [/testimonial] [/col] [col span__lg="4" span="12"] [testimonial image="9150" image_width="121" pos="center" name="MIRORIM"] <p>Excellent work. Very good theme, No need support, works perfectly. Congratulations !! <br />Waiting for version 3.0. Very excited.</p> [/testimonial] [/col] [/row]'
        ),
        array(
          'name' => __( 'Row - Boxed' ),
          'content' => '[row v_align="equal" padding="30px 30px 30px 30px" depth="2" depth_hover="5"] [col span__lg="4" span="12"] [testimonial image="9147" image_width="121" pos="center" name="Mark Jance" company="Facebook"] <p>The overall use of Silver is very VERY useful. It lacks very few, if any, things! I loved it and have created my first ever website Punsteronline.com! Best yet, Silver gets free updates that are great! (and the support is amazing as well!:)</p> [/testimonial] [/col] [col span__lg="4" span="12"] [testimonial image="9149" image_width="121" pos="center" name="Mark Jance" company="Facebook"] <div class="e-box h-p2 -stacked -radius-none"> <p class="t-body h-my1">This theme is amazing, you can customize EVERYTHING! The theme is a game changer for the Envato Market, cant wait for the future with Silver. Soo many good experiences from this, THANKS!</p> </div> [/testimonial] [/col] [col span__lg="4" span="12"] [testimonial image="9150" image_width="121" pos="center" name="MIRORIM"] <p>Excellent work. Very good theme, No need support, works perfectly. Congratulations !! <br />Waiting for version 3.0. Very excited.</p> [/testimonial] [/col] [/row]'
        ),
         array(
          'name' => __( 'In a slider' ),
          'content' => '[ux_slider] [ux_banner height="378px" bg="9147" bg_overlay="rgba(0, 0, 0, 0.68)" bg_pos="79% 68%"] [text_box width__lg="78" width="100"] [testimonial image="9147" image_width="142" name="Mark Jance" company="Facebook"] <p class="lead">The overall use of Silver is very VERY useful. It lacks very few, if any, things! I loved it and have created my first ever website Punsteronline.com! Best yet, Silver gets free updates that are great! (and the support is amazing as well!:)</p> [/testimonial] [/text_box] [/ux_banner] [ux_banner height="378px" bg="9148" bg_overlay="rgba(0, 0, 0, 0.68)" bg_pos="79% 68%"] [text_box width__lg="78" width="100"] [testimonial image="9148" image_width="142" name="Mark Jance" company="Facebook"] <p class="lead">The overall use of Silver is very VERY useful. It lacks very few, if any, things! I loved it and have created my first ever website Punsteronline.com! Best yet, Silver gets free updates that are great! (and the support is amazing as well!:)</p> [/testimonial] [/text_box] [/ux_banner] [/ux_slider]'
        ),
    ),
    'options' => array(
        // Avatar: an image or a circle with the short name (initials).
        'avatar' => array(
            'type' => 'radio-buttons',
            'heading' => 'Avatar',
            'default' => 'image',
            'options' => array(
                'image' => array( 'title' => 'Image' ),
                'initials' => array( 'title' => 'Short name' ),
            ),
        ),
        'image' => array(
            "type" => "image",
            "heading" => "Image",
            "value" => "",
            'conditions' => 'avatar !== "initials"',
        ),
        'initials' => array(
            'type' => 'textfield',
            'heading' => 'Short name',
            'description' => 'Để trống: tự lấy chữ đầu của Name (Minh Anh → MA).',
            'default' => '',
            'conditions' => 'avatar === "initials"',
        ),
        'initials_bg' => array(
            'type' => 'colorpicker',
            'heading' => 'Short name background',
            'alpha' => true,
            'format' => 'rgb',
            'position' => 'bottom right',
            'conditions' => 'avatar === "initials"',
            'helpers' => require( __DIR__ . '/helpers/colors.php' ),
        ),
        'initials_color' => array(
            'type' => 'colorpicker',
            'heading' => 'Short name color',
            'alpha' => true,
            'format' => 'rgb',
            'position' => 'bottom right',
            'conditions' => 'avatar === "initials"',
            'helpers' => require( __DIR__ . '/helpers/colors-text.php' ),
        ),
        'image_width' => array(
          'responsive' => true,
          "type" => "slider",
          "heading" => "Image Width",
          "unit" => "px",
          "default" => 80,
          "max" => 300,
          "min" => 20,
          'on_change' => array(
            'selector' => '.icon-box-img',
            'style' => 'width: {{ value }}px'
          ),
        ),
       'name' => array(
            "type" => "textfield",
            "heading" => "Name",
            "default" => ""
        ),
        'company' => array(
            "type" => "textfield",
            "heading" => "Company",
            "default" => ""
        ),

        'font_size' => array(
            'type' => 'radio-buttons',
            'heading' => __( 'Text Size' ),
            'default' => 'medium',
            'options' => require( __DIR__ . '/values/text-sizes.php' ),
        ),
        'stars' => array(
               'type' => 'slider',
               'heading' => __( 'Stars'),
               'default' => 5,
               'max' => 5,
               'min' => 0,
        ),
        'advanced_options' => require( __DIR__ . '/commons/advanced.php'),
    )
) );
