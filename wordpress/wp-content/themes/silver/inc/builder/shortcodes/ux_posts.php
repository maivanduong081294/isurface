<?php

/**
 * Element "Posts": danh sách bài viết của một post type bất kỳ (page, post,
 * CPT; trừ blocks), mỗi item render bằng một UX Block làm template.
 * Phần render nằm ở inc/shortcodes/ux_posts.php.
 */

$ux_posts_types = silver_ux_posts_post_types();

// Field chọn term cho từng taxonomy (hiện theo taxonomy đang chọn).
$ux_posts_tax_options = array('' => __('None', 'silver'));
$ux_posts_term_fields = array();
foreach (silver_ux_posts_taxonomies() as $ux_posts_tax) {
	$ux_posts_tax_options[$ux_posts_tax->name] = $ux_posts_tax->labels->name . ' (' . $ux_posts_tax->name . ')';

	$ux_posts_term_fields['tax_terms_' . $ux_posts_tax->name] = array(
		'type'       => 'select',
		'heading'    => $ux_posts_tax->labels->name,
		'full_width' => true,
		'conditions' => 'tax_taxonomy === "' . $ux_posts_tax->name . '" && tax_operator !== "EXISTS" && tax_operator !== "NOT EXISTS"',
		'config'     => array(
			'multiple'    => true,
			'placeholder' => __('Select...', 'silver'),
			'termSelect'  => array(
				'taxonomies' => $ux_posts_tax->name,
			),
		),
	);
}

// Biến mặc định cho commons/repeater-options.php.
$repeater_columns     = '4';
$repeater_type        = 'row';
$repeater_col_spacing = 'normal';

// Layout chung + căn chỉnh cột (giống Row), chèn sau "Column Spacing".
$ux_posts_layout = require __DIR__ . '/commons/repeater-options.php';
$ux_posts_layout['options'] = silver_ux_builder_array_insert_after($ux_posts_layout['options'], 'col_spacing', array(
	'v_align' => array(
		'type'       => 'radio-buttons',
		'heading'    => 'Align Vertical',
		'full_width' => true,
		'conditions' => 'type !== "slider-full"',
		'responsive' => true,
		'default'    => 'top',
		'options'    => array(
			'top'    => array('title' => 'Top'),
			'equal'  => array('title' => 'Equal'),
			'middle' => array('title' => 'Middle'),
			'bottom' => array('title' => 'Bottom'),
		),
	),
	'h_align' => array(
		'type'       => 'radio-buttons',
		'heading'    => 'Align Horizontal',
		'full_width' => true,
		'conditions' => 'type !== "slider-full"',
		'responsive' => true,
		'default'    => 'left',
		'options'    => array(
			'left'   => array('title' => 'Left'),
			'center' => array('title' => 'Center'),
			'right'  => array('title' => 'Right'),
		),
	),
));

add_ux_builder_shortcode('ux_posts', array(
	'name'      => __('Posts', 'silver'),
	'category'  => __('Content'),
	'thumbnail' => silver_ux_builder_thumbnail('blog_posts'),
	'info'      => '{{ post_type }}',
	'wrap'      => false,
	'scripts'   => array(
		'silver-masonry-js' => get_template_directory_uri() . '/assets/libs/packery.pkgd.min.js',
	),
	'options'   => array(
		'template_options' => array(
			'type'    => 'group',
			'heading' => __('Template', 'silver'),
			'options' => array(
				'template' => array(
					'type'        => 'select',
					'heading'     => __('UX Block', 'silver'),
					'description' => __('Block dùng làm template cho mỗi item. Dùng các element nhóm "Post Item" trong block.', 'silver'),
					'full_width'  => true,
					'config'      => array(
						'placeholder' => __('Select...', 'silver'),
						'postSelect'  => array(
							'post_type' => array('blocks'),
						),
					),
				),
			),
		),

		'query_options' => array(
			'type'    => 'group',
			'heading' => __('Query', 'silver'),
			'options' => array(
				'post_type' => array(
					'type'    => 'select',
					'heading' => __('Post type', 'silver'),
					'default' => 'post',
					'options' => $ux_posts_types,
				),
				'posts'     => array(
					'type'        => 'textfield',
					'heading'     => __('Total Posts', 'silver'),
					'description' => __('-1 để lấy tất cả.', 'silver'),
					'default'     => '8',
				),
				'offset'    => array(
					'type'    => 'textfield',
					'heading' => __('Offset', 'silver'),
					'default' => '',
				),
				'orderby'   => array(
					'type'    => 'select',
					'heading' => __('Order by', 'silver'),
					'default' => 'date',
					'options' => array(
						'ID'            => 'ID',
						'title'         => 'Title',
						'name'          => 'Name',
						'date'          => 'Published Date',
						'modified'      => 'Modified Date',
						'rand'          => 'Random',
						'comment_count' => 'Comment Count',
						'menu_order'    => 'Menu Order',
						'post__in'      => 'Posts In order',
					),
				),
				'order'     => array(
					'type'    => 'select',
					'heading' => __('Order', 'silver'),
					'default' => 'DESC',
					'options' => array(
						'ASC'  => 'ASC',
						'DESC' => 'DESC',
					),
				),
			),
		),

		'include_options' => array(
			'type'    => 'group',
			'heading' => __('Include / Exclude', 'silver'),
			'options' => array(
				'post_in'     => array(
					'type'       => 'select',
					'heading'    => __('Posts In', 'silver'),
					'full_width' => true,
					'config'     => array(
						'multiple'    => true,
						'sortable'    => true,
						'placeholder' => __('Select...', 'silver'),
						'postSelect'  => array(
							'post_type' => array_keys($ux_posts_types),
						),
					),
				),
				'post_not_in' => array(
					'type'       => 'select',
					'heading'    => __('Posts Not In', 'silver'),
					'full_width' => true,
					'config'     => array(
						'multiple'    => true,
						'placeholder' => __('Select...', 'silver'),
						'postSelect'  => array(
							'post_type' => array_keys($ux_posts_types),
						),
					),
				),
			),
		),

		'tax_query_options' => array(
			'type'    => 'group',
			'heading' => __('Tax Query', 'silver'),
			'options' => array_merge(
				array(
					'tax_taxonomy' => array(
						'type'    => 'select',
						'heading' => __('Taxonomy', 'silver'),
						'default' => '',
						'options' => $ux_posts_tax_options,
					),
					'tax_operator' => array(
						'type'       => 'select',
						'heading'    => __('Operator', 'silver'),
						'default'    => 'IN',
						'conditions' => 'tax_taxonomy',
						'options'    => array(
							'IN'         => 'IN',
							'NOT IN'     => 'NOT IN',
							'AND'        => 'AND',
							'EXISTS'     => 'EXISTS',
							'NOT EXISTS' => 'NOT EXISTS',
						),
					),
				),
				$ux_posts_term_fields,
				array(
					'tax_include_children' => array(
						'type'       => 'checkbox',
						'heading'    => __('Include children', 'silver'),
						'default'    => 'true',
						'conditions' => 'tax_taxonomy && tax_operator !== "EXISTS" && tax_operator !== "NOT EXISTS"',
					),
				)
			),
		),

		'meta_query_options' => array(
			'type'    => 'group',
			'heading' => __('Meta Query', 'silver'),
			'options' => array(
				'meta_key'     => array(
					'type'    => 'textfield',
					'heading' => __('Meta key', 'silver'),
					'default' => '',
				),
				'meta_compare' => array(
					'type'       => 'select',
					'heading'    => __('Compare', 'silver'),
					'default'    => '=',
					'conditions' => 'meta_key',
					'options'    => array(
						'='           => '=',
						'!='          => '!=',
						'>'           => '>',
						'>='          => '>=',
						'<'           => '<',
						'<='          => '<=',
						'LIKE'        => 'LIKE',
						'NOT LIKE'    => 'NOT LIKE',
						'IN'          => 'IN',
						'NOT IN'      => 'NOT IN',
						'BETWEEN'     => 'BETWEEN',
						'NOT BETWEEN' => 'NOT BETWEEN',
						'EXISTS'      => 'EXISTS',
						'NOT EXISTS'  => 'NOT EXISTS',
					),
				),
				'meta_value'   => array(
					'type'        => 'textfield',
					'heading'     => __('Meta value', 'silver'),
					'description' => __('IN / NOT IN / BETWEEN: các giá trị cách nhau bởi dấu phẩy.', 'silver'),
					'default'     => '',
					'conditions'  => 'meta_key && meta_compare !== "EXISTS" && meta_compare !== "NOT EXISTS"',
				),
				'meta_type'    => array(
					'type'       => 'select',
					'heading'    => __('Type', 'silver'),
					'default'    => 'CHAR',
					'conditions' => 'meta_key && meta_compare !== "EXISTS" && meta_compare !== "NOT EXISTS"',
					'options'    => array(
						'CHAR'     => 'CHAR',
						'NUMERIC'  => 'NUMERIC',
						'DECIMAL'  => 'DECIMAL',
						'DATE'     => 'DATE',
						'DATETIME' => 'DATETIME',
						'TIME'     => 'TIME',
						'SIGNED'   => 'SIGNED',
						'UNSIGNED' => 'UNSIGNED',
						'BINARY'   => 'BINARY',
					),
				),
			),
		),

		'layout_options'        => $ux_posts_layout,
		'layout_options_slider' => require __DIR__ . '/commons/repeater-slider.php',
		'relay_options'         => require __DIR__ . '/commons/relay.php',
		'advanced_options'      => require __DIR__ . '/commons/advanced.php',
	),
));

unset($ux_posts_layout, $ux_posts_types,$ux_posts_tax, $ux_posts_tax_options, $ux_posts_term_fields);
