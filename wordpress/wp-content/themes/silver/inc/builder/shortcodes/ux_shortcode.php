<?php
/**
 * Element "Shortcode": runs any shortcode (plugins, theme...).
 *
 * The shortcode is stored as the element content ([ux_shortcode][foo][/ux_shortcode]),
 * so brackets and quotes are safe. No template: the preview is rendered on the
 * server (like Block), so the real output of the shortcode is shown.
 * Render: inc/shortcodes/ux_shortcode.php.
 */

add_ux_builder_shortcode('ux_shortcode', array(
	'type'      => 'container',
	'name'      => __('Shortcode', 'silver'),
	'category'  => __('Content'),
	'compile'   => false, // Keep the content raw (not parsed into child elements).
	'overlay'   => true,  // Clicks in the canvas select the element, not its content.
	'wrap'      => false,
	'thumbnail' => silver_ux_builder_thumbnail('ux_html'),
	'info'      => '{{ label }}',
	'presets'   => array(
		array(
			'name'    => __('Default', 'silver'),
			'content' => '[ux_shortcode][/ux_shortcode]',
		),
	),
	'options'   => array(
		'label'            => array(
			'full_width'  => true,
			'type'        => 'textfield',
			'heading'     => 'Label',
			'placeholder' => 'Enter admin label here...',
		),
		'$content'         => array(
			'type'        => 'text-editor',
			'heading'     => __('Shortcode', 'silver'),
			'description' => __('Ví dụ: [contact-form-7 id="123"] hoặc [products limit="4"].', 'silver'),
			'full_width'  => true,
			'height'      => '200px',
			'tinymce'     => false,
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
