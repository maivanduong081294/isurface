<?php

/**
 * Add our controls.
 */


function silver_social_panels_sections( $wp_customize ) {


	$wp_customize->add_section( 'share', array(
		'title'       => __( 'Share', 'silver' ),
		'description' => __( 'This is the default settings for the [share] shortcode and various share icons on the website.', 'silver' ),
	) );

	$wp_customize->add_section( 'follow', array(
		'title'       => __( 'Follow Icons', 'silver' ),
		'panel'       => 'header',
		'description' => __( 'This is the default settings for the [follow] shortcode and Social Icons header element.', 'silver' ),
	) );
}
add_action( 'customize_register', 'silver_social_panels_sections' );


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'social_icons_style',
	'label'       => __( 'Share Icons Style', 'silver' ),
	'section'     => 'share',
	'default'     => 'outline',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'small' => $image_url . 'icon-plain.svg',
		'outline' => $image_url . 'icon-outline.svg',
		'fill' => $image_url . 'icon-fill.svg',
		'fill-round' => $image_url . 'icon-fill-round.svg',
		'outline-round' => $image_url . 'icon-outline-round.svg',
	),
	'active_callback' => array(
		array(
			'setting'  => 'custom_share_icons',
			'operator' => '===',
			'value'    => '',
		),
	),
));

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'social_icons_tooltip',
	'label'     => __( 'Show tooltips', 'silver' ),
	'section'   => 'share',
	'transport' => silver_customizer_transport(),
	'default'   => 1,
));

Silver_Option::add_field( 'option',  array(
		'type'        => 'multicheck',
		'settings'     => 'social_icons',
		'label'       => __( 'Share Icons', 'silver' ),
		//'description' => __( 'This is the control description', 'silver' ),
		//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
		'section'     => 'share',
		'transport' => silver_customizer_transport(),
		'active_callback' => array(
			array(
				'setting'  => 'custom_share_icons',
				'operator' => '===',
				'value'    => '',
			),
		),
		'default'     => array(
			'facebook',
			'twitter',
			'email',
			'pinterest',
			'whatsapp',
			'tumblr'
		),
		'choices'     => array(
			"facebook" => "Facebook",
			"linkedin" => "LinkedIn",
			"x" => esc_html_x( 'X', 'social media', 'silver' ),
			"twitter" => "Twitter",
			"threads" => "Threads",
			"email" => "Email",
			"pinterest" => "Pinterest",
			"vk" => "VKontakte",
			"tumblr" => "Tumblr",
			"telegram" => "Telegram",
			"whatsapp" => "WhatsApp (Only for Mobile)",
		),
	)
);

Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'custom_share_icons',
	'label'       => __( 'Share Replace', 'silver' ),
	'description'       => __( 'Replace Share Icons with Custom Scripts etc.', 'silver' ),
	'section'     => 'share',
	'default'     => '',
));



/*************
 * Social Icons
 *************/

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'follow_style',
	'label'       => __( 'Icons Style', 'silver' ),
	'section'     => 'follow',
	'default'     => 'small',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'small' => $image_url . 'icon-plain.svg',
		'outline' => $image_url . 'icon-outline.svg',
		'fill' => $image_url . 'icon-fill.svg',
		'fill-round' => $image_url . 'icon-fill-round.svg',
		'outline-round' => $image_url . 'icon-outline-round.svg',
	),
));

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'follow_tooltip',
	'label'     => __( 'Show tooltips', 'silver' ),
	'section'   => 'follow',
	'transport' => silver_customizer_transport(),
	'default'   => 1,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_facebook',
	'label'       => __( 'Facebook', 'silver' ),
	'transport' => silver_customizer_transport(),
	//'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_instagram',
	'label'       => __( 'Instagram', 'silver' ),
	'transport' => silver_customizer_transport(),
	//'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'follow_tiktok',
	'label'     => __( 'TikTok', 'silver' ),
	'transport' => silver_customizer_transport(),
	'section'   => 'follow',
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'follow_x',
	'label'     => esc_html_x( 'X', 'social media', 'silver' ),
	'transport' => silver_customizer_transport(),
	'section'   => 'follow',
	'default'   => '',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_twitter',
	'label'       => __( 'Twitter', 'silver' ),
	'transport' => silver_customizer_transport(),
	//'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'follow_threads',
	'label'     => esc_html__( 'Threads', 'silver' ),
	'transport' => silver_customizer_transport(),
	'section'   => 'follow',
	'default'   => '',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_email',
	'label'       => __( 'E-mail', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_phone',
	'label'       => __( 'Phone', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_pinterest',
	'label'       => __( 'Pinterest', 'silver' ),
	'transport' => silver_customizer_transport(),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_rss',
	'label'       => __( 'RSS', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_linkedin',
	'label'       => __( 'LinkedIn', 'silver' ),
	'transport' => silver_customizer_transport(),
	//'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_youtube',
	'label'       => __( 'YouTube', 'silver' ),
	'transport' => silver_customizer_transport(),
	//'description' => __( 'Add Any HTML or Shortcode here...', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'follow',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_flickr',
	'label'       => __( 'Flickr', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_500px',
	'label'       => __( '500px', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'follow_vk',
	'label'       => __( 'VKontakte', 'silver' ),
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
	'default'     => '',
));

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'follow_telegram',
	'label'     => __( 'Telegram', 'silver' ),
	'section'   => 'follow',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

Silver_Option::add_field( 'option',  array(
	'type'      => 'text',
	'settings'  => 'follow_twitch',
	'label'     => __( 'Twitch', 'silver' ),
	'section'   => 'follow',
	'transport' => silver_customizer_transport(),
	'default'   => '',
));

Silver_Option::add_field( 'option',  array(
	'type'      => 'text',
	'settings'  => 'follow_discord',
	'label'     => __( 'Discord', 'silver' ),
	'section'   => 'follow',
	'transport' => silver_customizer_transport(),
	'default'   => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'image',
	'settings'     => 'follow_snapchat',
	'label'       => __( 'SnapChat', 'silver' ),
	'description' => 'Upload a Snapcode image here. You can generate it here: https://accounts.snapchat.com/accounts/snapcodes',
	'section'     => 'follow',
	'transport' => silver_customizer_transport(),
));

function silver_refresh_social( WP_Customize_Manager $wp_customize ) {

  // Abort if selective refresh is not available.
  if ( ! isset( $wp_customize->selective_refresh ) ) {
      return;
  }

	  $wp_customize->selective_refresh->add_partial( 'follow_icons', array(
	    'selector' => '.header-social-icons > .follow-icons',
	    'settings' => array('follow_style','follow_tooltip','follow_linkedin','follow_flickr','follow_email','follow_phone','follow_facebook','follow_x','follow_twitter','follow_threads','follow_instagram','follow_tiktok','follow_rss','follow_vk','follow_youtube','follow_pinterest','follow_snapchat','follow_500px','follow_telegram','follow_twitch','follow_discord'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        return silver_apply_shortcode( 'follow', array(
				'style'    => get_theme_mod( 'follow_style', 'small' ),
				'tooltip'  => get_theme_mod( 'follow_tooltip', 1 ) ? 'true' : 'false',
			) );
	    },
	) );

	$wp_customize->selective_refresh->add_partial( 'social_icons', array(
	    'selector' => '.share-icons',
	    'settings' => array('social_icons','social_icons_style', 'social_icons_tooltip'),
	    'container_inclusive' => true,
	    'render_callback' => function() {
	        return silver_apply_shortcode( 'share', array(
				'style'   => get_theme_mod( 'social_icons_style', 'outline' ),
				'tooltip' => get_theme_mod( 'social_icons_tooltip', 1 ) ? 'true' : 'false',
			) );
	    },
	) );

}
add_action( 'customize_register', 'silver_refresh_social' );
