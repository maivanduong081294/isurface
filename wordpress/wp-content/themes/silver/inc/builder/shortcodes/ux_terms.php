<?php
/**
 * Element "Terms": terms of a taxonomy with thumbnail / title / count /
 * description (each one Yes/No). Render: inc/shortcodes/ux_terms.php.
 */

$terms_taxonomy_options = array();
$terms_term_fields      = array();

foreach (silver_terms_taxonomies() as $terms_tax) {
	$terms_taxonomy_options[$terms_tax->name] = $terms_tax->labels->name . ' (' . $terms_tax->name . ')';

	$terms_select = array('termSelect' => array('taxonomies' => $terms_tax->name));

	$terms_term_fields['parent_' . $terms_tax->name] = array(
		'type'       => 'select',
		'heading'    => __('Parent', 'silver') . ' — ' . $terms_tax->labels->singular_name,
		'full_width' => true,
		'conditions' => 'taxonomy === "' . $terms_tax->name . '" && parent_mode === "children"',
		'config'     => $terms_select + array('placeholder' => __('Select...', 'silver')),
	);
	$terms_term_fields['include_' . $terms_tax->name] = array(
		'type'        => 'select',
		'heading'     => __('Include', 'silver'),
		'description' => __('Chỉ hiện các term này (kéo thả để sắp xếp khi Order by = Include order).', 'silver'),
		'full_width'  => true,
		'conditions'  => 'taxonomy === "' . $terms_tax->name . '"',
		'config'      => $terms_select + array('placeholder' => __('Select...', 'silver'), 'multiple' => true, 'sortable' => true),
	);
	$terms_term_fields['exclude_' . $terms_tax->name] = array(
		'type'       => 'select',
		'heading'    => __('Exclude', 'silver'),
		'full_width' => true,
		'conditions' => 'taxonomy === "' . $terms_tax->name . '"',
		'config'     => $terms_select + array('placeholder' => __('Select...', 'silver'), 'multiple' => true),
	);
}

$terms_yes_no = array(
	'yes' => array('title' => 'Yes'),
	'no'  => array('title' => 'No'),
);

// Defaults of commons/repeater-options.php.
$repeater_columns     = '4';
$repeater_type        = 'row';
$repeater_col_spacing = 'normal';

add_ux_builder_shortcode('ux_terms', array(
	'name'      => __('Terms', 'silver'),
	'category'  => __('Content'),
	'thumbnail' => silver_ux_builder_thumbnail('blog_posts'),
	'info'      => '{{ taxonomy }}',
	'wrap'      => false,
	'scripts'   => array(
		'silver-masonry-js' => get_template_directory_uri() . '/assets/libs/packery.pkgd.min.js',
	),
	'options'   => array(
		'query_options' => array(
			'type'    => 'group',
			'heading' => __('Terms', 'silver'),
			'options' => array_merge(
				array(
					'taxonomy'    => array(
						'type'    => 'select',
						'heading' => __('Taxonomy', 'silver'),
						'default' => taxonomy_exists('product_cat') ? 'product_cat' : 'category',
						'options' => $terms_taxonomy_options,
					),
					'parent_mode' => array(
						'type'    => 'radio-buttons',
						'heading' => __('Level', 'silver'),
						'default' => 'top',
						'options' => array(
							'all'      => array('title' => 'All'),
							'top'      => array('title' => 'Top level'),
							'children' => array('title' => 'Children of'),
						),
					),
				),
				$terms_term_fields,
				array(
					'number'     => array(
						'type'        => 'textfield',
						'heading'     => __('Number', 'silver'),
						'description' => __('0 = tất cả.', 'silver'),
						'default'     => '8',
					),
					'orderby'    => array(
						'type'    => 'select',
						'heading' => __('Order by', 'silver'),
						'default' => 'name',
						'options' => array(
							'name'       => 'Name',
							'count'      => 'Count',
							'term_id'    => 'ID',
							'slug'       => 'Slug',
							'menu_order' => 'Menu order (WooCommerce)',
							'include'    => 'Include order',
						),
					),
					'order'      => array(
						'type'    => 'radio-buttons',
						'heading' => __('Order', 'silver'),
						'default' => 'ASC',
						'options' => array(
							'ASC'  => array('title' => 'ASC'),
							'DESC' => array('title' => 'DESC'),
						),
					),
					'hide_empty' => array(
						'type'    => 'checkbox',
						'heading' => __('Hide empty', 'silver'),
						'default' => 'false',
					),
				)
			),
		),

		'display_options' => array(
			'type'    => 'group',
			'heading' => __('Display', 'silver'),
			'options' => array(
				'show_thumbnail'   => array('type' => 'radio-buttons', 'heading' => __('Thumbnail', 'silver'), 'default' => 'yes', 'options' => $terms_yes_no),
				'show_title'       => array('type' => 'radio-buttons', 'heading' => __('Title', 'silver'), 'default' => 'yes', 'options' => $terms_yes_no),
				'show_count'       => array('type' => 'radio-buttons', 'heading' => __('Count', 'silver'), 'default' => 'yes', 'options' => $terms_yes_no),
				'count_format'     => array(
					'type'        => 'textfield',
					'heading'     => __('Count format', 'silver'),
					'description' => __('{count} = số lượng. VD: "{count} sản phẩm", "({count})".', 'silver'),
					'default'     => '{count}',
					'conditions'  => 'show_count === "yes"',
				),
				'show_description' => array('type' => 'radio-buttons', 'heading' => __('Description', 'silver'), 'default' => 'no', 'options' => $terms_yes_no),
				'title_tag'        => array(
					'type'       => 'select',
					'heading'    => __('Title tag', 'silver'),
					'default'    => 'h4',
					'conditions' => 'show_title === "yes"',
					'options'    => array('h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p' => 'Paragraph', 'div' => 'Div'),
				),
				'text_align'       => array(
					'type'       => 'radio-buttons',
					'heading'    => __('Text align', 'silver'),
					'responsive' => true,
					'default'    => 'center',
					'options'    => require __DIR__ . '/values/align-radios.php',
				),
			),
		),

		'image_options' => array(
			'type'       => 'group',
			'heading'    => __('Image', 'silver'),
			'conditions' => 'show_thumbnail === "yes"',
			'options'    => array(
				'image_size'   => array(
					'type'    => 'select',
					'heading' => __('Size', 'silver'),
					'default' => 'medium',
					'options' => silver_ux_builder_image_sizes(),
				),
				'image_height' => array(
					'type'        => 'scrubfield',
					'heading'     => __('Height (ratio)', 'silver'),
					'description' => __('100% = vuông, 56.25% = 16:9.', 'silver'),
					'responsive'  => true,
					'default'     => '100%',
					'helpers'     => require __DIR__ . '/helpers/image-heights.php',
				),
				'image_radius' => array(
					'type'       => 'slider',
					'heading'    => __('Radius', 'silver'),
					'responsive' => true,
					'unit'       => 'px',
					'default'    => 0,
					'min'        => 0,
					'max'        => 100,
				),
				'image_fit'    => array(
					'type'    => 'radio-buttons',
					'heading' => __('Fit', 'silver'),
					'default' => 'cover',
					'options' => array(
						'cover'   => array('title' => 'Cover'),
						'contain' => array('title' => 'Contain'),
					),
				),
				'image_meta'   => array(
					'type'        => 'textfield',
					'heading'     => __('Image meta key', 'silver'),
					'description' => __('Term meta chứa ID/URL ảnh. WooCommerce: thumbnail_id.', 'silver'),
					'default'     => 'thumbnail_id',
				),
			),
		),

		'layout_options'        => require __DIR__ . '/commons/repeater-options.php',
		'layout_options_slider' => require __DIR__ . '/commons/repeater-slider.php',
		'advanced_options'      => require __DIR__ . '/commons/advanced.php',
	),
));

unset($terms_taxonomy_options, $terms_term_fields, $terms_tax, $terms_select, $terms_yes_no);
