<?php
/**
 * [ux_shortcode]...[/ux_shortcode] — runs the shortcode(s) of its content.
 * Builder element: inc/builder/shortcodes/ux_shortcode.php.
 */

// [ux_shortcode]
function silver_ux_shortcode_shortcode($atts, $content = null, $tag = 'ux_shortcode')
{
  static $depth = 0;

  $atts = ux_builder_shortcode_atts(array(
    '_id'        => '',
    'class'      => '',
    'visibility' => '',
    'label'      => '',
  ), $atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  $content = trim((string) $content);
  $builder = function_exists('ux_builder_is_active') && ux_builder_is_active();

  if ($content === '') {
    // Placeholder so the element can be selected in UX Builder.
    return $builder ? '<div class="uxb-no-content">' . esc_html__('Shortcode: nhập shortcode trong ô bên trái.', 'silver') . '</div>' : '';
  }

  // A shortcode element inside its own output: stop the loop.
  if ($depth >= 3) return '';

  $depth++;
  $html = do_shortcode(shortcode_unautop($content));
  $depth--;

  // Shortcode not registered (plugin disabled...): tell the editors only.
  if ($html === $content && preg_match('/^\[([\w-]+)/', $content, $m) && ! shortcode_exists($m[1])) {
    $html = current_user_can('edit_pages')
      ? '<p class="uxb-no-content">' . esc_html(sprintf(__('Shortcode [%s] chưa được đăng ký (plugin chưa bật?).', 'silver'), $m[1])) . '</p>'
      : '';
  }

  $classes = array('ux-shortcode');
  if ($atts['class']) $classes[] = $atts['class'];
  if ($atts['visibility']) $classes[] = $atts['visibility'];

  return '<div' . ($atts['_id'] ? ' id="' . esc_attr($atts['_id']) . '"' : '')
    . ' class="' . esc_attr(implode(' ', $classes)) . '">' . $html . '</div>';
}
add_shortcode('ux_shortcode', 'silver_ux_shortcode_shortcode');
