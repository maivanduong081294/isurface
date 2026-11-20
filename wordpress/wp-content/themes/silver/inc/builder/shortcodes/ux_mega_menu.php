<?php
/**
 * Elements "Mega Menu" + "Mega Menu Item".
 * Render: inc/shortcodes/ux_mega_menu.php. Server rendered (like Container),
 * so the preview uses the same markup, CSS and JS as the frontend.
 */

$mega_taxonomies = array('category' => __('Post categories', 'silver'));
if (taxonomy_exists('product_cat')) {
	$mega_taxonomies = array('product_cat' => __('Product categories', 'silver')) + $mega_taxonomies;
}

// Parent term per taxonomy (shown for the selected taxonomy).
$mega_parent_fields = array();
foreach (array_keys($mega_taxonomies) as $mega_taxonomy) {
	$mega_parent_fields['parent_' . $mega_taxonomy] = array(
		'type'       => 'select',
		'heading'    => __('Parent category', 'silver'),
		'full_width' => true,
		'conditions' => 'source === "categories" && taxonomy === "' . $mega_taxonomy . '"',
		'config'     => array(
			'placeholder' => __('Select...', 'silver'),
			'termSelect'  => array('taxonomies' => $mega_taxonomy),
		),
	);
}

// Mega Menu -----------------------------------------------------------------
add_ux_builder_shortcode('ux_mega_menu', array(
	'type'      => 'container',
	'name'      => __('Mega Menu', 'silver'),
	'category'  => __('Content'),
	'thumbnail' => silver_ux_builder_thumbnail('ux_menu'),
	'info'      => '{{ label || text }}',
	'wrap'      => false,
	'nested'    => true,
	'allow'     => array('ux_mega_menu_item'),
	'presets'   => array(
		array(
			'name'    => __('Default', 'silver'),
			'content' => '[ux_mega_menu][ux_mega_menu_item title="Danh mục 1"][/ux_mega_menu_item][ux_mega_menu_item title="Danh mục 2"][/ux_mega_menu_item][/ux_mega_menu]',
		),
	),
	'options'   => array(
		'label'         => array(
			'full_width'  => true,
			'type'        => 'textfield',
			'heading'     => 'Label',
			'placeholder' => 'Enter admin label here..',
		),
		'text'          => array(
			'type'    => 'textfield',
			'heading' => __('Button text', 'silver'),
			'default' => __('Danh mục sản phẩm', 'silver'),
		),
		'icon'          => array(
			'type'    => 'icon',
			'heading' => __('Button icon', 'silver'),
			'default' => 'fa-solid fa-bars',
		),
		'trigger'       => array(
			'type'        => 'radio-buttons',
			'heading'     => __('Open on', 'silver'),
			'description' => __('Desktop. On mobile the button / icon is always clicked.', 'silver'),
			'default'     => 'hover',
			'options'     => array(
				'hover' => array('title' => 'Hover'),
				'click' => array('title' => 'Click'),
			),
		),
		'trigger_bg'    => array(
			'type'     => 'colorpicker',
			'heading'  => __('Button background', 'silver'),
			'alpha'    => true,
			'format'   => 'rgb',
			'position' => 'bottom right',
			'helpers'  => require __DIR__ . '/helpers/colors.php',
		),
		'trigger_color' => array(
			'type'     => 'colorpicker',
			'heading'  => __('Button text color', 'silver'),
			'alpha'    => true,
			'format'   => 'rgb',
			'position' => 'bottom right',
			'helpers'  => require __DIR__ . '/helpers/colors-text.php',
		),
		'panel_options' => array(
			'type'    => 'group',
			'heading' => __('Panel', 'silver'),
			'options' => array(
				'panel_width'   => array(
					'type'        => 'scrubfield',
					'heading'     => __('Width', 'silver'),
					'responsive'  => true,
					'default'     => '1100px',
					'placeholder' => '1100px',
				),
				'panel_align'   => array(
					'type'        => 'radio-buttons',
					'heading'     => __('Align', 'silver'),
					'description' => __('Container: same left edge and width as the header / row container. The panel always stays on screen.', 'silver'),
					'default'     => 'left',
					'options'     => array(
						'left'      => array('title' => 'Left'),
						'center'    => array('title' => 'Center'),
						'right'     => array('title' => 'Right'),
						'container' => array('title' => 'Container'),
					),
				),
				'sidebar_width' => array(
					'type'        => 'scrubfield',
					'heading'     => __('List width', 'silver'),
					'responsive'  => true,
					'default'     => '260px',
					'placeholder' => '260px',
				),
				'panel_padding' => array(
					'type'        => 'scrubfield',
					'heading'     => __('Padding (left / right)', 'silver'),
					'description' => __('Inner side padding, like the column gutter of a row. Default 15px.', 'silver'),
					'responsive'  => true,
					'default'     => '',
					'placeholder' => '15px',
				),
				'min_height'    => array(
					'type'        => 'scrubfield',
					'heading'     => __('Min height', 'silver'),
					'responsive'  => true,
					'default'     => '',
					'placeholder' => '0px',
				),
			),
		),
		// Per screen: the button (desktop panel above 850px, accordion below)
		// or only the icon (hamburger) opening the items in a drawer.
		'display_options' => array(
			'type'    => 'group',
			'heading' => __('Display', 'silver'),
			'options' => array(
				'display_mode'      => array(
					'type'        => 'radio-buttons',
					'heading'     => __('Display', 'silver'),
					'description' => __('Per screen: switch the screen size in the toolbar.', 'silver'),
					'responsive'  => true,
					'default'     => 'button',
					'options'     => array(
						'button' => array('title' => 'Button'),
						'icon'   => array('title' => 'Icon + drawer'),
					),
				),
				'drawer_title'      => array(
					'type'        => 'textfield',
					'heading'     => __('Drawer title', 'silver'),
					'description' => __('Empty: the button text.', 'silver'),
					'default'     => '',
				),
				'drawer_layout'     => array(
					'type'        => 'radio-buttons',
					'heading'     => __('Drawer layout', 'silver'),
					'description' => __('Expanded: every item with its heading and grid. Accordion: one item open at a time.', 'silver'),
					'default'     => 'expanded',
					'options'     => array(
						'expanded'  => array('title' => 'Expanded'),
						'accordion' => array('title' => 'Accordion'),
					),
				),
				'drawer_columns'    => array(
					'type'    => 'slider',
					'heading' => __('Drawer grid columns', 'silver'),
					'default' => 3,
					'min'     => 2,
					'max'     => 5,
					'step'    => 1,
				),
				'drawer_pos'        => array(
					'type'    => 'radio-buttons',
					'heading' => __('Drawer opens from', 'silver'),
					'default' => 'left',
					'options' => array(
						'left'  => array('title' => 'Left'),
						'right' => array('title' => 'Right'),
					),
				),
				'drawer_icon_size'  => array(
					'type'       => 'slider',
					'heading'    => __('Icon size', 'silver'),
					'responsive' => true,
					'unit'       => 'px',
					'default'    => 22,
					'min'        => 14,
					'max'        => 40,
					'step'       => 1,
				),
				'drawer_icon_color' => array(
					'type'       => 'colorpicker',
					'heading'    => __('Icon color', 'silver'),
					'responsive' => true,
					'alpha'      => true,
					'format'     => 'rgb',
					'position'   => 'bottom right',
					'helpers'    => require __DIR__ . '/helpers/colors-text.php',
				),
			),
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));

// Mega Menu Item --------------------------------------------------------------
add_ux_builder_shortcode('ux_mega_menu_item', array(
	'type'      => 'container',
	'name'      => __('Mega Menu Item', 'silver'),
	'category'  => __('Content'),
	'thumbnail' => silver_ux_builder_thumbnail('ux_menu'),
	'info'      => '{{ title }}',
	'wrap'      => false,
	'nested'    => true,
	'require'   => array('ux_mega_menu'),
	'options'   => array(
		'title'        => array(
			'type'       => 'textfield',
			'heading'    => __('Title', 'silver'),
			'default'    => __('Danh mục', 'silver'),
			'auto_focus' => true,
		),
		'icon_type'    => array(
			'type'    => 'radio-buttons',
			'heading' => __('Media', 'silver'),
			'default' => 'none',
			'options' => array(
				'none'  => array('title' => 'None'),
				'icon'  => array('title' => 'Icon'),
				'image' => array('title' => 'Image'),
			),
		),
		'icon'         => array(
			'type'       => 'icon',
			'heading'    => __('Icon', 'silver'),
			'conditions' => 'icon_type === "icon"',
		),
		'img'          => array(
			'type'       => 'image',
			'heading'    => __('Image', 'silver'),
			'conditions' => 'icon_type === "image"',
		),
		'show_heading' => array(
			'type'    => 'radio-buttons',
			'heading' => __('Heading', 'silver'),
			'default' => 'yes',
			'options' => array(
				'yes' => array('title' => 'Show'),
				'no'  => array('title' => 'Hide'),
			),
		),
		'view_all'     => array(
			'type'        => 'textfield',
			'heading'     => __('"View all" text', 'silver'),
			'description' => __('Needs a link (Link options, or the parent category).', 'silver'),
			'default'     => __('Xem tất cả', 'silver'),
		),
		'source_options' => array(
			'type'    => 'group',
			'heading' => __('Content', 'silver'),
			'options' => array_merge(
				array(
					'source'   => array(
						'type'        => 'radio-buttons',
						'heading'     => __('Source', 'silver'),
						'description' => __('Elements added inside the item are shown in both cases (below the categories).', 'silver'),
						'default'     => 'content',
						'options'     => array(
							'content'    => array('title' => 'Elements'),
							'categories' => array('title' => 'Child categories'),
						),
					),
					'taxonomy' => array(
						'type'       => 'select',
						'heading'    => __('Taxonomy', 'silver'),
						'conditions' => 'source === "categories"',
						'default'    => key($mega_taxonomies),
						'options'    => $mega_taxonomies,
					),
				),
				$mega_parent_fields,
				array(
					'number'     => array(
						'type'       => 'slider',
						'heading'    => __('Number', 'silver'),
						'conditions' => 'source === "categories"',
						'default'    => 8,
						'min'        => 1,
						'max'        => 30,
					),
					'columns'    => array(
						'type'       => 'slider',
						'heading'    => __('Columns', 'silver'),
						'conditions' => 'source === "categories"',
						'responsive' => true,
						'default'    => 4,
						'min'        => 1,
						'max'        => 8,
					),
					'image_size' => array(
						'type'       => 'select',
						'heading'    => __('Image size', 'silver'),
						'conditions' => 'source === "categories"',
						'default'    => 'thumbnail',
						'options'    => silver_ux_builder_image_sizes(),
					),
					'hide_empty' => array(
						'type'       => 'checkbox',
						'heading'    => __('Hide empty', 'silver'),
						'conditions' => 'source === "categories"',
						'default'    => 'false',
					),
				)
			),
		),
		'link_options'     => require __DIR__ . '/commons/links.php',
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));

unset($mega_taxonomies, $mega_parent_fields, $mega_taxonomy);
