<?php
/**
 * Element "Counter": number that counts up, any format.
 * Render: inc/shortcodes/ux_counter.php.
 */

add_ux_builder_shortcode('ux_counter', array(
	'name'      => __('Counter', 'silver'),
	'category'  => __('Content'),
	'thumbnail' => silver_ux_builder_thumbnail('text'),
	'info'      => '{{ value }}',
	'wrap'      => false,
	'presets'   => array(
		array('name' => '5', 'content' => '[ux_counter value="5" label="locations across Vietnam"]'),
		array('name' => '7/7', 'content' => '[ux_counter value="7/7" label="days a week, 9AM–10PM"]'),
		array('name' => '≤5 min', 'content' => '[ux_counter value="≤5 min" label="average chat reply"]'),
		array('name' => '200%', 'content' => '[ux_counter value="200%" label="counterfeit refund"]'),
	),
	'options'   => array(
		'value'    => array(
			'type'        => 'textfield',
			'heading'     => __('Value', 'silver'),
			'description' => __('Mọi con số sẽ chạy, phần chữ giữ nguyên. VD: 5, 7/7, 10+, ≤5 min, 2-3 days, 200%, 1,250, 4.8', 'silver'),
			'default'     => '100+',
			'auto_focus'  => true,
		),
		'label'    => array(
			'type'    => 'textfield',
			'heading' => __('Label', 'silver'),
			'default' => '',
		),
		'duration' => array(
			'type'    => 'slider',
			'heading' => __('Duration', 'silver'),
			'unit'    => 's',
			'default' => 2,
			'min'     => 0.5,
			'max'     => 6,
			'step'    => 0.5,
		),
		'align'    => array(
			'type'       => 'radio-buttons',
			'heading'    => __('Align', 'silver'),
			'responsive' => true,
			'default'    => 'center',
			'options'    => require __DIR__ . '/values/align-radios.php',
		),
		'style_options' => array(
			'type'    => 'group',
			'heading' => __('Style', 'silver'),
			'options' => array(
				'value_size'  => array(
					'type'        => 'scrubfield',
					'heading'     => __('Value size', 'silver'),
					'responsive'  => true,
					'default'     => '',
					'placeholder' => '2.25em',
				),
				'value_color' => array(
					'type'       => 'colorpicker',
					'heading'    => __('Value color', 'silver'),
					'responsive' => true,
					'alpha'      => true,
					'format'     => 'rgb',
					'position'   => 'bottom right',
					'helpers'    => require __DIR__ . '/helpers/colors-text.php',
				),
				'label_size'  => array(
					'type'        => 'scrubfield',
					'heading'     => __('Label size', 'silver'),
					'responsive'  => true,
					'default'     => '',
					'placeholder' => '0.85em',
				),
				'label_color' => array(
					'type'       => 'colorpicker',
					'heading'    => __('Label color', 'silver'),
					'responsive' => true,
					'alpha'      => true,
					'format'     => 'rgb',
					'position'   => 'bottom right',
					'helpers'    => require __DIR__ . '/helpers/colors-text.php',
				),
				'gap'         => array(
					'type'        => 'scrubfield',
					'heading'     => __('Gap', 'silver'),
					'responsive'  => true,
					'default'     => '',
					'placeholder' => '4px',
				),
			),
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
