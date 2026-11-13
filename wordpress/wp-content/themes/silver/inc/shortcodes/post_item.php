<?php

/**
 * Render các shortcode thuộc nhóm "Post Item".
 *
 * Mỗi shortcode lấy dữ liệu từ bài viết hiện tại ($post toàn cục), nên dùng
 * được trong vòng lặp bài viết hoặc trang chi tiết bài viết.
 */

/**
 * Bọc nội dung của một element Post Item trong thẻ div có id/class/visibility.
 *
 * @param string $name       Tên element, dùng làm class `post-item-{name}`.
 * @param string $html       Nội dung bên trong (đã escape).
 * @param array  $atts       Attribute đã parse (_id, class, visibility).
 * @return string
 */
function silver_post_item_wrap($name, $html, $atts)
{
  if ($html === '') {
    // Trong UX Builder phải luôn có một phần tử DOM để chọn/kéo element,
    // nên khi không có dữ liệu (vd: đang sửa UX Block template) thì hiện placeholder.
    if (! silver_post_item_is_builder()) return '';
    $html = silver_post_item_placeholder($name);
  }

  $classes = array('post-item-element', 'post-item-' . $name);
  if (! empty($atts['class'])) $classes[] = $atts['class'];
  if (! empty($atts['visibility'])) $classes[] = $atts['visibility'];

  return '<div' . (! empty($atts['_id']) ? ' id="' . esc_attr($atts['_id']) . '"' : '')
    . ' class="' . esc_attr(implode(' ', $classes)) . '">' . $html . '</div>';
}

/** Đang render trong UX Builder (iframe/editor/do_shortcode)? */
function silver_post_item_is_builder()
{
  return function_exists('ux_builder_is_active') && ux_builder_is_active();
}

/** Đang sửa chính UX Block (template), nên chưa có bài viết thật. */
function silver_post_item_is_template()
{
  return 'blocks' === get_post_type();
}

/**
 * Placeholder hiển thị trong UX Builder khi element không có dữ liệu.
 *
 * @param string $name Tên element (title, thumbnail...).
 * @return string
 */
function silver_post_item_placeholder($name)
{
  $label = 'Post ' . ucfirst($name);
  $style = 'display:flex;align-items:center;justify-content:center;padding:10px;'
    . 'border:1px dashed rgba(0,0,0,.25);background:rgba(0,0,0,.04);color:#888;font-size:13px;';

  if ('thumbnail' === $name) {
    $style .= 'aspect-ratio:16/9;';
  }

  return '<div class="post-item-placeholder" style="' . esc_attr($style) . '">' . esc_html($label) . '</div>';
}

/**
 * Bọc $html bằng thẻ <a> trỏ tới permalink bài viết hiện tại khi bật Link (yes).
 *
 * @param string $html Nội dung bên trong.
 * @param array  $atts Attribute đã parse (link = yes|no).
 * @return string
 */
function silver_post_item_link($html, $atts)
{
  if ($html === '' || 'yes' !== $atts['link']) return $html;

  $url = get_permalink();
  if (! $url) return $html;

  return '<a href="' . esc_url($url) . '">' . $html . '</a>';
}

/** Danh sách attribute chung (Advanced + tuỳ chọn Link yes/no). */
function silver_post_item_common_atts($with_link = false)
{
  $atts = array('_id' => '', 'class' => '', 'visibility' => '');
  if ($with_link) {
    $atts['link'] = 'no';
  }
  return $atts;
}

/** Nhãn + giá trị (dùng cho author, created, meta). */
function silver_post_item_label_value($label, $value)
{
  if ($value === '' || $value === null) return '';

  return ($label !== '' ? '<span class="post-item-label">' . esc_html($label) . '</span> ' : '')
    . '<span class="post-item-value">' . $value . '</span>';
}

// [post_title]
function silver_post_title_shortcode($atts, $content = null, $tag = 'post_title')
{
  $atts = ux_builder_shortcode_atts(
    array('tag_name' => 'h3') + silver_post_item_common_atts(true),
    $atts,
    $tag
  );

  $tag_name = in_array($atts['tag_name'], array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p'), true) ? $atts['tag_name'] : 'h2';
  $title    = get_the_title();
  if ($title === '') return silver_post_item_wrap('title', '', $atts);

  $html = '<' . $tag_name . ' class="post-item-title-text">' . silver_post_item_link(wp_kses_post($title), $atts) . '</' . $tag_name . '>';

  return silver_post_item_wrap('title', $html, $atts);
}
add_shortcode('post_title', 'silver_post_title_shortcode');

// [post_thumbnail]
function silver_post_thumbnail_shortcode($atts, $content = null, $tag = 'post_thumbnail')
{
  $atts = ux_builder_shortcode_atts(
    array('size' => 'medium') + silver_post_item_common_atts(true),
    $atts,
    $tag
  );

  if (! has_post_thumbnail()) return silver_post_item_wrap('thumbnail', '', $atts);

  $html = get_the_post_thumbnail(null, $atts['size'] ? $atts['size'] : 'medium');

  return silver_post_item_wrap('thumbnail', silver_post_item_link($html, $atts), $atts);
}
add_shortcode('post_thumbnail', 'silver_post_thumbnail_shortcode');

// [post_excerpt]
function silver_post_excerpt_shortcode($atts, $content = null, $tag = 'post_excerpt')
{
  $atts = ux_builder_shortcode_atts(
    array('max_length' => '') + silver_post_item_common_atts(),
    $atts,
    $tag
  );

  $excerpt = trim(wp_strip_all_tags(get_the_excerpt()));
  $max     = absint($atts['max_length']);
  if ($max > 0) $excerpt = wp_html_excerpt($excerpt, $max, '&hellip;');

  return silver_post_item_wrap('excerpt', $excerpt !== '' ? '<p>' . ($max > 0 ? wp_kses_post($excerpt) : esc_html($excerpt)) . '</p>' : '', $atts);
}
add_shortcode('post_excerpt', 'silver_post_excerpt_shortcode');

// [post_content]
function silver_post_content_shortcode($atts, $content = null, $tag = 'post_content')
{
  static $rendering = false;

  $atts = ux_builder_shortcode_atts(silver_post_item_common_atts(), $atts, $tag);

  // Tránh vòng lặp vô hạn khi shortcode nằm trong chính nội dung bài viết.
  // Khi đang sửa UX Block template thì "nội dung" chính là block, nên chỉ hiện placeholder.
  if ($rendering || silver_post_item_is_template()) return silver_post_item_wrap('content', '', $atts);
  $rendering = true;
  $html      = apply_filters('the_content', get_the_content());
  $rendering = false;

  return silver_post_item_wrap('content', $html, $atts);
}
add_shortcode('post_content', 'silver_post_content_shortcode');

// [post_author]
function silver_post_author_shortcode($atts, $content = null, $tag = 'post_author')
{
  $atts = ux_builder_shortcode_atts(
    array('label' => '') + silver_post_item_common_atts(),
    $atts,
    $tag
  );

  $html = silver_post_item_label_value($atts['label'], esc_html(get_the_author()));

  return silver_post_item_wrap('author', $html, $atts);
}
add_shortcode('post_author', 'silver_post_author_shortcode');

// [post_created]
function silver_post_created_shortcode($atts, $content = null, $tag = 'post_created')
{
  $atts = ux_builder_shortcode_atts(
    array('label' => '') + silver_post_item_common_atts(),
    $atts,
    $tag
  );

  $html = silver_post_item_label_value(
    $atts['label'],
    '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>'
  );

  return silver_post_item_wrap('created', $html, $atts);
}
add_shortcode('post_created', 'silver_post_created_shortcode');

// [post_share]
function silver_post_share_shortcode($atts, $content = null, $tag = 'post_share')
{
  $atts = ux_builder_shortcode_atts(
    array('networks' => '') + silver_post_item_common_atts(),
    $atts,
    $tag
  );

  $networks = array_filter(array_map('trim', explode(',', (string) $atts['networks'])));

  // silver_share() đọc danh sách mạng xã hội từ theme_mod nên ghi đè tạm thời.
  $override = function () use ($networks) {
    return array_values($networks);
  };
  if ($networks) add_filter('theme_mod_social_icons', $override);
  $html = silver_share(array(
    '_id'        => $atts['_id'],
    'class'      => trim('post-item-share ' . $atts['class']),
    'visibility' => $atts['visibility'],
  ), null, 'share');
  if ($networks) remove_filter('theme_mod_social_icons', $override);

  return $html;
}
add_shortcode('post_share', 'silver_post_share_shortcode');

// [post_meta]
function silver_post_meta_shortcode($atts, $content = null, $tag = 'post_meta')
{
  $atts = ux_builder_shortcode_atts(
    array('label' => '', 'meta_key' => '') + silver_post_item_common_atts(),
    $atts,
    $tag
  );

  $key = trim($atts['meta_key']);
  if ($key === '') return silver_post_item_wrap('meta', '', $atts);

  $value = get_post_meta(get_the_ID(), $key, true);
  if (is_array($value)) $value = implode(', ', array_filter(array_map('strval', $value), 'strlen'));
  if (! is_scalar($value) || (string) $value === '') return silver_post_item_wrap('meta', '', $atts);

  return silver_post_item_wrap('meta', silver_post_item_label_value($atts['label'], wp_kses_post((string) $value)), $atts);
}
add_shortcode('post_meta', 'silver_post_meta_shortcode');
