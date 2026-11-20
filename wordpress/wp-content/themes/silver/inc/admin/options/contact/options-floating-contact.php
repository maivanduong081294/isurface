<?php
/**
 * Floating contact (absolute contact bar) options.
 *
 * @package silver
 */

require_once get_template_directory() . '/inc/extensions/silver-floating-contact/silver-floating-contact-items.php';

$silver_fc_icons = array( '' => __( 'Default icon', 'silver' ) )
	+ require get_template_directory() . '/inc/builder/shortcodes/values/icons.php';

Silver_Option::add_section( 'floating_contact', array(
	'title'    => __( 'Floating Contact', 'silver' ),
	'priority' => 159,
) );

$silver_fc_enabled = array(
	array(
		'setting'  => 'floating_contact',
		'operator' => '===',
		'value'    => true,
	),
);

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'floating_contact',
	'section'  => 'floating_contact',
	'label'    => esc_html__( 'Enable floating contact', 'silver' ),
	'default'  => false,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-buttonset',
	'settings'        => 'floating_contact_style',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Style', 'silver' ),
	'description'     => esc_html__( 'Style 1: vertical bar on the screen edge (desktop), bottom bar (mobile). Style 2: floating button in the corner that opens a stack of buttons.', 'silver' ),
	'default'         => 'style-1',
	'choices'         => array(
		'style-1' => __( 'Style 1', 'silver' ),
		'style-2' => __( 'Style 2', 'silver' ),
	),
	'active_callback' => $silver_fc_enabled,
) );

$silver_fc_style_1 = array(
	$silver_fc_enabled[0],
	array(
		'setting'  => 'floating_contact_style',
		'operator' => '===',
		'value'    => 'style-1',
	),
);

$silver_fc_style_2 = array(
	$silver_fc_enabled[0],
	array(
		'setting'  => 'floating_contact_style',
		'operator' => '===',
		'value'    => 'style-2',
	),
);

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-buttonset',
	'settings'        => 'floating_contact_position',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Position', 'silver' ),
	'description'     => esc_html__( 'Style 1: screen edge on desktop. Style 2: bottom corner on all devices.', 'silver' ),
	'default'         => 'right',
	'choices'         => array(
		'left'  => __( 'Left', 'silver' ),
		'right' => __( 'Right', 'silver' ),
	),
	'active_callback' => $silver_fc_enabled,
) );

$silver_fc_channels = silver_floating_contact_channels();

Silver_Option::add_field( 'option', array(
	'type'            => 'sortable',
	'settings'        => 'floating_contact_items',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Channels', 'silver' ),
	'description'     => esc_html__( 'Click the eye to turn a channel on, drag to sort. Its settings appear below. Tip: keep 5–6 channels on mobile.', 'silver' ),
	'multiple'        => 99,
	'default'         => array( 'messenger', 'zalo', 'tiktok', 'hotline' ),
	'choices'         => wp_list_pluck( $silver_fc_channels, 'label' ),
	'active_callback' => $silver_fc_enabled,
) );

$silver_fc_devices = array(
	'all'     => __( 'Desktop & mobile', 'silver' ),
	'desktop' => __( 'Desktop only', 'silver' ),
	'mobile'  => __( 'Mobile only', 'silver' ),
);

foreach ( $silver_fc_channels as $silver_fc_key => $silver_fc_channel ) {
	$silver_fc_prefix = 'floating_contact_' . $silver_fc_key;
	$silver_fc_multi  = ! empty( $silver_fc_channel['multi'] );
	$silver_fc_show   = array(
		$silver_fc_enabled[0],
		array(
			'setting'  => 'floating_contact_items',
			'operator' => 'contains',
			'value'    => $silver_fc_key,
		),
	);

	Silver_Option::add_field( 'option', array(
		'type'            => 'custom',
		'settings'        => $silver_fc_prefix . '_title',
		'section'         => 'floating_contact',
		'default'         => '<div class="options-title-divider">' . esc_html( $silver_fc_channel['label'] ) . '</div>',
		'active_callback' => $silver_fc_show,
	) );

	switch ( $silver_fc_channel['type'] ) {
		case 'phone':
		case 'sms':
		case 'whatsapp':
		case 'viber':
			$silver_fc_value_label = $silver_fc_multi ? __( 'Phone numbers', 'silver' ) : __( 'Phone number', 'silver' );
			break;
		case 'email':
			$silver_fc_value_label = __( 'Email addresses', 'silver' );
			break;
		default:
			$silver_fc_value_label = $silver_fc_multi ? __( 'Links / accounts', 'silver' ) : __( 'Link', 'silver' );
	}

	$silver_fc_description = $silver_fc_channel['hint'];
	if ( $silver_fc_multi ) {
		/* translators: %s: example value */
		$silver_fc_description = sprintf( __( 'One per line. Optional name: "Name | value". Several lines open a list. E.g. %s', 'silver' ), $silver_fc_channel['hint'] );
	}

	Silver_Option::add_field( 'option', array(
		'type'            => $silver_fc_multi ? 'textarea' : 'text',
		'settings'        => $silver_fc_prefix . '_link',
		'section'         => 'floating_contact',
		'label'           => esc_html( $silver_fc_value_label ),
		'description'     => esc_html( $silver_fc_description ),
		'default'         => '',
		'active_callback' => $silver_fc_show,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'text',
		'settings'        => $silver_fc_prefix . '_label',
		'section'         => 'floating_contact',
		'label'           => esc_html__( 'Label', 'silver' ),
		'default'         => $silver_fc_channel['label'],
		'active_callback' => $silver_fc_show,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => $silver_fc_prefix . '_device',
		'section'         => 'floating_contact',
		'label'           => esc_html__( 'Show on', 'silver' ),
		'default'         => isset( $silver_fc_channel['device'] ) ? $silver_fc_channel['device'] : 'all',
		'choices'         => $silver_fc_devices,
		'active_callback' => $silver_fc_show,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'select',
		'settings'        => $silver_fc_prefix . '_icon_class',
		'section'         => 'floating_contact',
		'label'           => esc_html__( 'Icon', 'silver' ),
		'description'     => esc_html__( 'Choose a theme icon or keep the default channel icon. A custom image takes priority.', 'silver' ),
		'default'         => '',
		'choices'         => $silver_fc_icons,
		'active_callback' => $silver_fc_show,
	) );

	Silver_Option::add_field( 'option', array(
		'type'            => 'image',
		'settings'        => $silver_fc_prefix . '_icon',
		'section'         => 'floating_contact',
		'label'           => esc_html__( 'Custom icon (optional)', 'silver' ),
		'default'         => '',
		'active_callback' => $silver_fc_show,
	) );
}

Silver_Option::add_field( 'option', array(
	'type'            => 'custom',
	'settings'        => 'floating_contact_toggle_title',
	'section'         => 'floating_contact',
	'default'         => '<div class="options-title-divider">' . esc_html__( 'Style 1: hide button & colors', 'silver' ) . '</div>',
	'active_callback' => $silver_fc_style_1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'floating_contact_toggle',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Show hide button', 'silver' ),
	'default'         => true,
	'active_callback' => $silver_fc_style_1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'text',
	'settings'        => 'floating_contact_toggle_label',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Hide button label', 'silver' ),
	'default'         => 'Ẩn',
	'active_callback' => $silver_fc_style_1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_bg',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Background color', 'silver' ),
	'default'         => '#ffffff',
	'active_callback' => $silver_fc_style_1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_color',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Label color', 'silver' ),
	'default'         => '#8a8a8a',
	'active_callback' => $silver_fc_style_1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_toggle_color',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Hide button color', 'silver' ),
	'default'         => '#2e8b3a',
	'active_callback' => $silver_fc_style_1,
) );

/* Style 2: floating button + stack of buttons */

Silver_Option::add_field( 'option', array(
	'type'            => 'custom',
	'settings'        => 'floating_contact_s2_title',
	'section'         => 'floating_contact',
	'default'         => '<div class="options-title-divider">' . esc_html__( 'Style 2: button & colors', 'silver' ) . '</div>',
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'floating_contact_launcher_icon',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Button icon', 'silver' ),
	'description'     => esc_html__( 'Choose a theme icon for the round button. A button image takes priority.', 'silver' ),
	'default'         => '',
	'choices'         => $silver_fc_icons,
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'image',
	'settings'        => 'floating_contact_launcher_image',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Button image', 'silver' ),
	'description'     => esc_html__( 'E.g. a mascot (transparent PNG/WebP/GIF). Empty: round button with the selected icon.', 'silver' ),
	'default'         => '',
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'floating_contact_launcher_size',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Button size (px)', 'silver' ),
	'default'         => 64,
	'choices'         => array(
		'min'  => 40,
		'max'  => 140,
		'step' => 1,
	),
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_launcher_color',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Round button color (no image)', 'silver' ),
	'default'         => '#2e8b3a',
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'floating_contact_launcher_animate',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Attention animation on the button', 'silver' ),
	'default'         => true,
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'floating_contact_open',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Open the buttons on page load', 'silver' ),
	'default'         => false,
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'floating_contact_offset_bottom',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Distance from bottom (px)', 'silver' ),
	'default'         => 20,
	'choices'         => array(
		'min'  => 0,
		'max'  => 200,
		'step' => 1,
	),
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'slider',
	'settings'        => 'floating_contact_offset_side',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Distance from side (px)', 'silver' ),
	'default'         => 20,
	'choices'         => array(
		'min'  => 0,
		'max'  => 200,
		'step' => 1,
	),
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_pill_bg',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Buttons background', 'silver' ),
	'default'         => '#000000',
	'active_callback' => $silver_fc_style_2,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'floating_contact_pill_color',
	'section'         => 'floating_contact',
	'label'           => esc_html__( 'Buttons text color', 'silver' ),
	'default'         => '#ffffff',
	'active_callback' => $silver_fc_style_2,
) );
