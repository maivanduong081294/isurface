<?php

/**
 * Nhóm element "Post Item" trong UX Builder.
 *
 * Dùng bên trong một vòng lặp bài viết (hoặc trang chi tiết bài viết): mỗi
 * element lấy dữ liệu từ bài viết hiện tại. Phần render nằm ở
 * inc/shortcodes/post_item.php.
 */

$post_item_category = __('Post Item', 'silver');

// Các element Post Item dùng chung nhóm Advanced.
$post_item_advanced = require __DIR__ . '/commons/advanced.php';

// Post Title ------------------------------------------------------------------
add_ux_builder_shortcode('post_title', array(
	'name'      => __('Post Title', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('title'),
	'info'      => __('Post Title', 'silver'),
	'wrap'      => false,
	'options'   => array(
		'tag_name'         => array(
			'type'    => 'select',
			'heading' => esc_html__('Tag', 'silver'),
			'default' => 'div',
			'options' => array(
				'h1'  => esc_html__('Heading 1', 'silver'),
				'h2'  => esc_html__('Heading 2', 'silver'),
				'h3'  => esc_html__('Heading 3', 'silver'),
				'h4'  => esc_html__('Heading 4', 'silver'),
				'div' => esc_html__('Container', 'silver'),
				'p'   => esc_html__('Paragraph', 'silver'),
			),
		),
		'link'             => array(
			'type'    => 'radio-buttons',
			'heading' => __('Link to post', 'silver'),
			'default' => 'no',
			'options' => array(
				'no'  => array('title' => __('No', 'silver')),
				'yes' => array('title' => __('Yes', 'silver')),
			),
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Thumbnail --------------------------------------------------------------
add_ux_builder_shortcode('post_thumbnail', array(
	'name'      => __('Post Thumbnail', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('ux_image'),
	'info'      => __('Post Thumbnail', 'silver'),
	'wrap'      => false,
	'options'   => array(
		'size'             => array(
			'type'    => 'select',
			'heading' => __('Size', 'silver'),
			'default' => 'medium',
			'options' => silver_ux_builder_image_sizes(),
		),
		'link'             => array(
			'type'    => 'radio-buttons',
			'heading' => __('Link to post', 'silver'),
			'default' => 'no',
			'options' => array(
				'no'  => array('title' => __('No', 'silver')),
				'yes' => array('title' => __('Yes', 'silver')),
			),
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Excerpt ----------------------------------------------------------------
add_ux_builder_shortcode('post_excerpt', array(
	'name'      => __('Post Excerpt', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('text'),
	'info'      => __('Post Excerpt', 'silver'),
	'wrap'      => false,
	'options'   => array(
		'max_length'       => array(
			'type'        => 'scrubfield',
			'heading'     => __('Max Length', 'silver'),
			'description' => __('Số ký tự tối đa. Để trống hoặc 0 để hiển thị toàn bộ.', 'silver'),
			'default'     => '',
			'min'         => 0,
			'max'         => 1000,
			'step'        => 1,
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Content ----------------------------------------------------------------
add_ux_builder_shortcode('post_content', array(
	'name'      => __('Post Content', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('text'),
	'info'      => __('Post Content', 'silver'),
	'wrap'      => false,
	'options'   => array(
		'advanced_options' => $post_item_advanced,
	),
));

// Post Author -----------------------------------------------------------------
add_ux_builder_shortcode('post_author', array(
	'name'      => __('Post Author', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('woo_meta'),
	'info'      => '{{ label }}',
	'wrap'      => false,
	'options'   => array(
		'label'            => array(
			'type'        => 'textfield',
			'heading'     => __('Text', 'silver'),
			'description' => __('Nhãn hiển thị trước tên tác giả, ví dụ: Tác giả:', 'silver'),
			'default'     => '',
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Created ----------------------------------------------------------------
add_ux_builder_shortcode('post_created', array(
	'name'      => __('Post Created', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('woo_meta'),
	'info'      => '{{ label }}',
	'wrap'      => false,
	'options'   => array(
		'label'            => array(
			'type'        => 'textfield',
			'heading'     => __('Text', 'silver'),
			'description' => __('Nhãn hiển thị trước ngày đăng, ví dụ: Ngày tạo:', 'silver'),
			'default'     => '',
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Share ------------------------------------------------------------------
add_ux_builder_shortcode('post_share', array(
	'name'      => __('Post Share', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('share'),
	'info'      => __('Post Share', 'silver'),
	'wrap'      => false,
	'options'   => array(
		'networks'         => array(
			'type'        => 'select',
			'heading'     => __('Social share', 'silver'),
			'description' => __('Chọn mạng xã hội. Để trống để dùng cấu hình mặc định của theme.', 'silver'),
			'default'     => 'facebook,x,email,linkedin,pinterest,whatsapp',
			'full_width'  => true,
			'config'      => array(
				'placeholder' => __('Select...', 'silver'),
				'multiple'    => true,
				'sortable'    => true,
				'options'     => array(
					'facebook'  => 'Facebook',
					'x'         => 'X',
					'twitter'   => 'Twitter',
					'threads'   => 'Threads',
					'email'     => 'Email',
					'pinterest' => 'Pinterest',
					'linkedin'  => 'LinkedIn',
					'whatsapp'  => 'WhatsApp',
					'telegram'  => 'Telegram',
					'tumblr'    => 'Tumblr',
					'vk'        => 'VKontakte',
				),
			),
		),
		'advanced_options' => $post_item_advanced,
	),
));

// Post Meta -------------------------------------------------------------------
add_ux_builder_shortcode('post_meta', array(
	'name'      => __('Post Meta', 'silver'),
	'category'  => $post_item_category,
	'thumbnail' => silver_ux_builder_thumbnail('woo_meta'),
	'info'      => '{{ meta_key }}',
	'wrap'      => false,
	'options'   => array(
		'label'            => array(
			'type'        => 'textfield',
			'heading'     => __('Text', 'silver'),
			'description' => __('Nhãn hiển thị trước giá trị meta.', 'silver'),
			'default'     => '',
		),
		'meta_key'         => array(
			'type'    => 'textfield',
			'heading' => __('Meta key', 'silver'),
			'default' => '',
		),
		'advanced_options' => $post_item_advanced,
	),
));

unset($post_item_category, $post_item_advanced);
