<?php
// [title]
function title_shortcode($atts, $content = null)
{
  extract(ux_builder_shortcode_atts(array(
    '_id' => 'title-' . rand(),
    'text' => 'Lorem ipsum dolor sit amet...',
    'tag_name' => 'h2',
    'style' => 'normal',
    'size' => '100',
    'margin_top' => '',
    'margin_bottom' => '',
    'color' => '',
    'width' => '',
    'icon' => '',
    'link_text' => '',
    'link' => '',
    'target' => '',
    'rel' => '',
    'class' => '',
    'visibility' => '',
    // Deprecated.
    'letter_case' => '',
    'sub_text' => '',
  ), $atts));

  // Cho phép cả div và p (h5, h6 giữ lại để tương thích dữ liệu cũ).
  $allowed_tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p');
  $tag_name     = trim($tag_name);
  if (! in_array($tag_name, $allowed_tags, true)) $tag_name = 'h3';

  $classes = array('container', 'section-title-container');
  if ($class) $classes[] = $class;
  if ($visibility) $classes[] = $visibility;
  if ($link_text) $classes[] = 'section-title-has-link';
  $classes = implode(' ', $classes);

  $link_atts   = array(
    'class'  => 'section-title-link',
    'href'   => esc_url($link),
    'target' => esc_attr($target),
    'rel'    => array(esc_attr($rel)),
  );
  $link_output = '';
  if ($link_text) {
    $link_output = sprintf(
      '<a %s>%s%s</a>',
      silver_html_atts($link_atts),
      wp_kses_post($link_text),
      get_silver_icon('icon-angle-right')
    );
  }

  $small_text = '';
  if ($sub_text) $small_text = '<small class="sub-title">' . wp_kses_post($atts['sub_text']) . '</small>';

  if ($icon) $icon = get_silver_icon($icon);

  // fix old
  if ($style == 'bold_center') $style = 'bold-center';

  $css_args = array(
    array('attribute' => 'margin-top', 'value' => $margin_top),
    array('attribute' => 'margin-bottom', 'value' => $margin_bottom),
  );

  if ($width) {
    $css_args[] = array('attribute' => 'max-width', 'value' => $width);
  }

  $css_args_title = array();

  if ($size !== '100') {
    $css_args_title[] = array('attribute' => 'font-size', 'value' => $size, 'unit' => '%');
  }
  if ($color) {
    $css_args_title[] = array('attribute' => 'color', 'value' => $color);
  }

  // Phần tử 1: tiêu đề
  $title_output = '<' . $tag_name . ' class="section-title section-title-' . esc_attr($style) . '">'
    . '<b aria-hidden="true"></b>'
    . '<span class="section-title-main" ' . get_shortcode_inline_css($css_args_title) . '>'
    . $icon . wp_kses_post($text) . $small_text
    . '</span>'
    . '<b aria-hidden="true"></b>'
    . '</' . $tag_name . '>';

  // Responsive color / width (larger screens).
  $responsive_style = silver_responsive_style_tag($_id, $atts, array(
    'color' => array('selector' => '.section-title-main', 'property' => 'color', 'only_overrides' => true),
    'width' => array('selector' => '', 'property' => 'max-width', 'only_overrides' => true),
  ));

  // Phần tử 2: link ($link_output) nằm ngoài tiêu đề
  return '<div' . ($_id ? ' id="' . esc_attr($_id) . '"' : '') . ' class="' . esc_attr($classes) . '" ' . get_shortcode_inline_css($css_args) . '>'
    . $title_output
    . $link_output
    . $responsive_style
    . '</div>';
}
add_shortcode('title', 'title_shortcode');


// [divider]
function divider_shortcode($atts, $content = null)
{
  extract(ux_builder_shortcode_atts(array(
    'width' => '',
    'height' => '',
    'margin' => '',
    'align' => '',
    'color' => '',
  ), $atts));

  $align_end = '';
  $align_start = '';


  // Fallback
  if ($width == 'full') $width = '100%';

  $css_args = array(
    array('attribute' => 'margin-top', 'value' => $margin),
    array('attribute' => 'margin-bottom', 'value' => $margin),
    array('attribute' => 'max-width', 'value' => $width),
    array('attribute' => 'height', 'value' => $height),
    array('attribute' => 'background-color', 'value' => $color),
  );

  if ($align === 'center') {
    $align_start = '<div class="text-center">';
    $align_end = '</div>';
  }
  if ($align === 'right') {
    $align_start = '<div class="text-right">';
    $align_end = '</div>';
  }

  // Responsive align / width / height / color (larger screens).
  $id_attr          = '';
  $responsive_style = '';
  if (silver_responsive_any_overrides((array) $atts, array('align', 'width', 'height', 'color'))) {
    $id               = 'divider-' . wp_rand();
    $id_attr          = ' id="' . esc_attr($id) . '"';
    $responsive_style = silver_responsive_style_tag($id, (array) $atts, array(
      'align'  => array(
        'selector'       => '',
        'only_overrides' => true,
        'map'            => array(
          'left'   => 'margin-left: 0; margin-right: auto',
          'center' => 'margin-left: auto; margin-right: auto',
          'right'  => 'margin-left: auto; margin-right: 0',
        ),
      ),
      'width'  => array('selector' => '', 'property' => 'max-width', 'only_overrides' => true, 'default' => '30px'),
      'height' => array('selector' => '', 'property' => 'height', 'only_overrides' => true, 'default' => '3px'),
      'color'  => array('selector' => '', 'property' => 'background-color', 'only_overrides' => true),
    ));
  }

  return $align_start . '<div' . $id_attr . ' class="is-divider divider clearfix" ' . get_shortcode_inline_css($css_args) . '></div>' . $align_end . $responsive_style;
}
add_shortcode('divider', 'divider_shortcode');
