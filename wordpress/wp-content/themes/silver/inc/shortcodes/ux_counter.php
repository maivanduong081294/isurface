<?php
/**
 * [ux_counter] — Number that counts up when it scrolls into view.
 *
 * The value keeps its format ("5", "7/7", "10+", "≤5 min", "2-3 days",
 * "200%", "1,250", "4.8"): every number in it is animated, the rest of the
 * text stays as is (assets/js/silver-counter.js). The final value is in the
 * HTML, so it is shown without JS and read by search engines / screen readers.
 */

/** Counter assets (small, loaded on every page for the builder canvas / AJAX renders). */
function silver_counter_assets()
{
  $uri = get_template_directory_uri();
  $dir = get_template_directory();
  wp_enqueue_style('silver-counter', $uri . '/assets/css/silver-counter.css', array('silver-main'), filemtime($dir . '/assets/css/silver-counter.css'));
  wp_enqueue_script('silver-counter', $uri . '/assets/js/silver-counter.js', array('jquery', 'silver-js'), filemtime($dir . '/assets/js/silver-counter.js'), true);
}
add_action('wp_enqueue_scripts', 'silver_counter_assets', 110);

// [ux_counter]
function silver_counter_shortcode($atts, $content = null, $tag = 'ux_counter')
{
  $atts = ux_builder_shortcode_atts(array(
    '_id'         => '',
    'class'       => '',
    'visibility'  => '',
    'value'       => '100+',
    'label'       => '',
    'duration'    => '2',
    'align'       => 'center',
    'value_size'  => '',
    'value_color' => '',
    'label_size'  => '',
    'label_color' => '',
    'gap'         => '',
  ), $atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  $id       = $atts['_id'] ? sanitize_html_class($atts['_id']) : 'ux-counter-' . wp_rand();
  $value    = wp_strip_all_tags($atts['value']);
  $duration = max(0.2, min(10, (float) $atts['duration']));
  $align    = in_array($atts['align'], array('left', 'center', 'right'), true) ? $atts['align'] : 'center';

  $classes = array('ux-counter', 'text-' . $align);
  if ($atts['class']) $classes[] = $atts['class'];
  if ($atts['visibility']) $classes[] = $atts['visibility'];

  ob_start();
?>
  <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="ux-counter-value">
      <span class="ux-counter-number" aria-hidden="true" data-value="<?php echo esc_attr($value); ?>" data-duration="<?php echo esc_attr($duration); ?>"><?php echo esc_html($value); ?></span>
      <span class="screen-reader-text"><?php echo esc_html($value); ?></span>
    </div>
    <?php if ('' !== trim($atts['label'])) : ?>
      <div class="ux-counter-label"><?php echo wp_kses_post($atts['label']); ?></div>
    <?php endif; ?>
    <?php
    echo silver_responsive_style_tag($id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
      'align'       => array('selector' => '', 'only_overrides' => true, 'default' => 'center', 'map' => silver_responsive_map('text_align')),
      'value_size'  => array('selector' => '.ux-counter-value', 'property' => 'font-size'),
      'value_color' => array('selector' => '.ux-counter-value', 'property' => 'color'),
      'label_size'  => array('selector' => '.ux-counter-label', 'property' => 'font-size'),
      'label_color' => array('selector' => '.ux-counter-label', 'property' => 'color'),
      'gap'         => array('selector' => '.ux-counter-label', 'property' => 'margin-top'),
    ));
    ?>
  </div>
<?php
  return ob_get_clean();
}
add_shortcode('ux_counter', 'silver_counter_shortcode');
