
<?php
// [logo img="" height="50" link=""]
function ux_logo($atts, $content = null)
{
  $atts = (array) $atts;

  $args = ux_builder_shortcode_atts(array(
    '_id'        => '',
    'class'      => '',
    'visibility' => '',
    'img'        => '',
    'image_size' => 'original',
    'padding'    => '',
    'title'      => '',
    'hover'      => '',
    'link'       => '',
    'target'     => '_self',
    'rel'        => '',
    'height'     => '50',
  ), $atts);

  $id         = $args['_id'];
  $height     = max(1, absint($args['height']));
  $padding    = $args['padding'];
  $title      = $args['title'];
  $link       = $args['link'];
  $image_size = $args['image_size'] === 'original'
    ? 'full'
    : $args['image_size'];

  // Wrapper classes.
  $classes = array(
    'ux-logo',
    'has-hover',
    'align-middle',
    'ux_logo',
    'inline-block',
  );

  if ($args['class']) {
    $classes[] = $args['class'];
  }

  if ($args['visibility']) {
    $classes[] = $args['visibility'];
  }

  // Resolve attachment ID.
  $img           = $args['img'];
  $attachment_id = 0;
  $image_url     = '';

  if (empty($img)) {
    $image_url = silver_theme_logo_url();
  } elseif (is_numeric($img)) {
    $attachment_id = absint($img);
  } else {
    $image_url = $img;
  }

  if (!$attachment_id && $image_url) {
    $attachment_id = attachment_url_to_postid($image_url);
  }

  // Calculate intrinsic image ratio.
  $ratio = null;

  if ($attachment_id) {
    $image_src = wp_get_attachment_image_src(
      $attachment_id,
      $image_size
    );

    if ($image_src && $image_src[1] && $image_src[2]) {
      $ratio = $image_src[1] / $image_src[2];
    }
  }

  if (!$ratio && empty($img)) {
    $ratio = 400 / 84;
  }

  // Build image attributes.
  $image_style = 'height:' . $height . 'px;width:auto;max-width:100%;';

  $image_atts = array(
    'class'    => 'ux-logo-image block',
    'alt'      => $title ?: get_bloginfo('name'),
    'loading'  => 'eager',
    'decoding' => 'async',
    'style'    => $image_style,
  );

  if ($title) {
    $image_atts['title'] = $title;
  }

  // Default wrapper style.
  $width = $ratio
    ? round($height * $ratio + intval($padding) * 2, 2) . 'px'
    : 'auto';

  $wrapper_style = 'max-width:100%!important;width:' . $width . '!important;';
  $responsive_style = '';

  // Responsive heights.
  if (silver_responsive_has_overrides($atts, 'height')) {
    if (!$id) {
      $id = 'logo-' . wp_rand();
    }

    $wrapper_values = array();
    $image_values   = array();

    foreach (
      silver_responsive_filled($atts, 'height', $height)
      as $index => $value
    ) {
      $value = max(1, absint($value));

      $responsive_width = $ratio
        ? round($value * $ratio + intval($padding) * 2, 2) . 'px'
        : 'auto';

      $wrapper_values[$index] =
        'max-width:100%;width:' . $responsive_width;

      $image_values[$index] =
        'height:' . $value . 'px;width:auto';
    }

    $wrapper_style = '';

    // Let responsive CSS control image height.
    unset($image_atts['style']);

    $responsive_style = silver_responsive_style_tag(
      $id,
      $atts,
      array(
        'height|wrapper' => array(
          'selector' => '',
          'values'   => $wrapper_values,
          'callback' => 'strval',
        ),
        'height|image' => array(
          'selector' => '.ux-logo-image',
          'values'   => $image_values,
          'callback' => 'strval',
        ),
      )
    );
  }

  // Render using native WordPress image HTML.
  $image_html = $attachment_id
    ? wp_get_attachment_image(
      $attachment_id,
      $image_size,
      false,
      $image_atts
    )
    : '';

  // Fallback for URLs not registered in Media Library.
  if (!$image_html && $image_url) {
    $fallback_atts = $image_atts;

    $fallback_atts['src'] = esc_url($image_url);

    $html_atts = '';

    foreach ($fallback_atts as $key => $value) {
      $html_atts .= ' ' . esc_attr($key)
        . '="' . esc_attr($value) . '"';
    }

    $image_html = '<img' . $html_atts . '>';
  }

  if (!$image_html) {
    return '';
  }

  // Inner link or container.
  $inner_tag = $link ? 'a' : 'div';

  $inner_atts = ' class="ux-logo-link block image-'
    . esc_attr($args['hover']) . '"';

  if ($title) {
    $inner_atts .= ' title="' . esc_attr($title) . '"';
  }

  if ($link) {
    $link_atts = array(
      'target' => $args['target'],
      'rel'    => array($args['rel']),
    );

    $inner_atts .= silver_parse_target_rel($link_atts);
    $inner_atts .= ' href="' . esc_url($link) . '"';
  }

  if ($padding !== '') {
    $inner_atts .= ' style="padding:' . esc_attr($padding) . ';"';
  }

  // Outer wrapper.
  $wrapper_atts = '';

  if ($id) {
    $wrapper_atts .= ' id="' . esc_attr($id) . '"';
  }

  $wrapper_atts .= ' class="'
    . esc_attr(implode(' ', $classes)) . '"';

  if ($wrapper_style) {
    $wrapper_atts .= ' style="' . esc_attr($wrapper_style) . '"';
  }

  return $responsive_style
    . '<div' . $wrapper_atts . '>'
    . '<' . $inner_tag . $inner_atts . '>'
    . $image_html
    . '</' . $inner_tag . '>'
    . '</div>';
}

add_shortcode('logo', 'ux_logo');
