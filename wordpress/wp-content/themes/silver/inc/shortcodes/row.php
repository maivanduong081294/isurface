<?php

// [row]
function ux_row($atts, $content = null)
{
  extract($atts = ux_builder_shortcode_atts(array(
    '_id' => 'row-' . rand(),
    'style' => '',
    'col_style' => '',
    'label' => '',
    'border_color' => '',
    'width' => '',
    'custom_width' => '',
    'class' => '',
    'visibility' => '',
    'v_align' => '',
    'h_align' => '',
    'flex_wrap' => 'wrap',
    'flex_reverse' => 'no',
    'depth' => '',
    'depth_hover' => '',
    // Paddings
    'padding' => '',
    'padding__sm' => null,
    'padding__md' => null,
    'col_bg' => '',
    'col_bg_radius' => ''
  ), $atts));

  // Stop if visibility is hidden
  if ($visibility == 'hidden') return;

  $classes[] = 'row';

  // Add Row style
  if ($style) $classes[] = 'row-' . $style;

  // Add Row Width
  if ($width == 'full-width') $classes[] = 'row-full-width';

  // Column Vertical Align ("top" is the default, no class).
  if ($v_align && $v_align !== 'top') $classes[] = 'align-' . $v_align;

  // Column Horizontal Align ("left" is the default, no class).
  if ($h_align && $h_align !== 'left') $classes[] = 'align-' . $h_align;

  // Column style
  if ($col_style) $classes[] = 'row-' . $col_style;

  // Custom Class
  if ($class) $classes[] = $class;
  if ($visibility) $classes[] = $visibility;


  // Depth
  if ($depth) $classes[] = 'row-box-shadow-' . $depth;
  if ($depth_hover) $classes[] = 'row-box-shadow-' . $depth_hover . '-hover';

  // Add Custom Widths
  if ($width !== 'custom') {
    $custom_width = '';
  } else {
    $custom_width = 'style="max-width:' . esc_attr($custom_width) . '"';
  }

  $args = array(
    'padding' => array(
      'selector' => '> .col > .col-inner',
      'property' => 'padding',
    ),
    'col_bg' => array(
      'selector' => '> .col > .col-inner',
      'property' => 'background-color',
    ),
    'col_bg_radius' => array(
      'selector' => '> .col > .col-inner',
      'property' => 'border-radius',
      'unit'      => 'px',
    ),
  );

  $classes =  implode(" ", $classes);

  return '<div class="' . esc_attr($classes) . '" ' . $custom_width . ' id="' . esc_attr($_id) . '">' . do_shortcode($content) . ux_builder_element_style_tag($_id, $args, $atts) . silver_row_responsive_style($_id, $atts) . '</div>';
}

/**
 * Responsive width / alignment of a row (values for larger screens).
 *
 * @param string $id
 * @param array  $atts
 * @return string
 */
function silver_row_responsive_style($id, $atts)
{
  $rules = silver_responsive_row_align_rules();

  // Width + custom width are combined per breakpoint.
  if (silver_responsive_any_overrides($atts, array('width', 'custom_width'))) {
    $widths        = silver_responsive_filled($atts, 'width', 'container');
    $custom_widths = silver_responsive_filled($atts, 'custom_width', '');
    $max_widths    = array();

    foreach ($widths as $index => $width) {
      if ($width === 'full-width') {
        $max_widths[$index] = '100%';
      } elseif ($width === 'custom' && $custom_widths[$index] !== '') {
        $max_widths[$index] = $custom_widths[$index];
      } else {
        $max_widths[$index] = silver_responsive_container_width(isset($atts['style']) ? $atts['style'] : '');
      }
    }

    $rules['width'] = array(
      'selector' => '',
      'property' => 'max-width',
      'values'   => $max_widths,
    );
  }

  // Flex wrap (wrap / nowrap) and Reverse (no / yes), per screen. Only
  // printed when a screen differs from the .row default (wrap, not reversed).
  $wraps    = silver_responsive_filled($atts, 'flex_wrap', 'wrap');
  $reverses = silver_responsive_filled($atts, 'flex_reverse', 'no');
  $wrap_css = array();
  $dir_css  = array();

  foreach ($wraps as $index => $wrap) {
    // Values of an earlier version: "wrap-reverse" / "nowrap-reverse".
    $nowrap  = in_array($wrap, array('nowrap', 'nowrap-reverse'), true);
    $reverse = $reverses[$index] === 'yes' || in_array($wrap, array('wrap-reverse', 'nowrap-reverse'), true);

    $wrap_css[$index] = $nowrap ? 'nowrap' : 'wrap';
    $dir_css[$index]  = $reverse ? 'row-reverse' : 'row';
  }

  if (array_diff($wrap_css, array('wrap'))) {
    $rules['flex_wrap'] = array('selector' => '', 'property' => 'flex-wrap', 'values' => $wrap_css);
  }
  if (array_diff($dir_css, array('row'))) {
    $rules['flex_reverse'] = array('selector' => '', 'property' => 'flex-direction', 'values' => $dir_css);
  }

  return silver_responsive_style_tag($id, $atts, $rules);
}


// [col]
function ux_col($atts, $content = null)
{
  extract($atts = ux_builder_shortcode_atts(array(
    '_id' => 'col-' . rand(),
    'label' => '',
    'span' => '12',
    'span__md' => '',
    'span__sm' => '',
    'small' => '12',
    // Width mode (grid / auto / custom) + custom width, both responsive.
    'width_mode' => 'grid',
    'custom_width' => '',
    'visibility' => '',
    'divider' => '',
    'animate' => '',
    'padding' => '',
    'padding__md' => '',
    'padding__sm' => '',
    'margin' => '',
    'margin__md' => '',
    'margin__sm' => '',
    'tooltip' => '',
    'max_width' => '',
    'max_width__md' => '',
    'max_width__sm' => '',
    'hover' => '',
    'class' => '',
    'align' => '',
    'color' => '',
    'sticky' => '',
    'sticky_mode' => '',
    'parallax' => '',
    'force_first' => '',
    'bg' => '',
    'bg_color' => '',
    'bg_radius' => '',
    'depth' => '',
    'depth_hover' => '',
    'text_depth' => '',
    // Border Control.
    'border'        => '',
    'border_margin' => '',
    'border_style'  => '',
    'border_radius' => '',
    'border_color'  => '',
    'border_hover'  => '',
  ), $atts));

  // Hide if visibility is hidden
  if ($visibility == 'hidden') return;

  $classes[] = 'col';
  $classes_inner[] = 'col-inner';

  // Fix old cols
  if (strpos($span, '/')) $span = silver_fix_span($span);

  // add custom class
  if ($class) $classes[] = $class;
  if ($visibility) $classes[] = $visibility;

  if ($span__md) $classes[] = 'medium-' . $span__md;
  if ($span__ml) $classes[] = 'large-' . $span__ml;
  if ($span__lg) $classes[] = 'xlarge-' . $span__lg;
  if ($span) $classes[] = 'small-' . $span;
  if ($border_hover) $classes[] = 'has-hover';

  // Force first position
  if ($force_first) $classes[] = $force_first . '-col-first';

  // Add divider
  if ($divider) $classes[] = 'col-divided';

  // Add Animation Class
  if ($animate) {
    $animate = 'data-animate="' . esc_attr($animate) . '"';
  }

  // Add Align Class
  if ($align) $classes_inner[] = 'text-' . $align;

  // Add Hover Class
  if ($hover) $classes[] = 'col-hover-' . $hover;

  // Add Depth Class
  if ($depth) $classes_inner[] = 'box-shadow-' . $depth;
  if ($depth_hover) $classes_inner[] = 'box-shadow-' . $depth_hover . '-hover';
  if ($text_depth) $classes_inner[] = 'text-shadow-' . $text_depth;

  // Add Color class
  if ($color == 'light') $classes_inner[] = 'dark';

  // Add Toolip Html
  $tooltip_class = '';
  if ($tooltip) {
    $tooltip = 'title="' . esc_attr($tooltip) . '"';
    $classes[] = 'tip-top';
  }

  // Parallax
  if ($parallax) $parallax = 'data-parallax-fade="true" data-parallax="' . esc_attr($parallax) . '"';

  // Inline CSS
  $css_args = array(
    'bg_color' => array(
      'attribute' => 'background-color',
      'value'     => $bg_color,
    ),
  );

  $col_inner = $sticky ? '> .is-sticky-column > .is-sticky-column__inner > .col-inner' : '> .col-inner';

  $args = array(
    'padding'   => array(
      'selector' => $col_inner,
      'property' => 'padding',
    ),
    'margin'    => array(
      'selector' => $col_inner,
      'property' => 'margin',
    ),
    'bg_radius' => array(
      'selector' => $col_inner,
      'property' => 'border-radius',
      'unit'     => 'px',
    ),
    'max_width' => array(
      'selector' => $col_inner,
      'property' => 'max-width',
    ),
  );

  $classes          = implode(' ', $classes);
  $classes_inner    = implode(' ', $classes_inner);
  $attributes       = implode(' ', array($tooltip, $animate));
  $attributes_inner = $parallax;

  ob_start();
?>

  <div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr($classes); ?>" <?php echo $attributes; ?>>
    <?php if ($sticky) silver_sticky_column_open('', $sticky_mode); ?>
    <div class="<?php echo esc_attr($classes_inner); ?>" <?php echo get_shortcode_inline_css($css_args); ?> <?php echo $attributes_inner; ?>>
      <?php require __DIR__ . '/commons/border.php'; ?>
      <?php echo do_shortcode($content); ?>
    </div>
    <?php if ($sticky) silver_sticky_column_close(); ?>
    <?php echo ux_builder_element_style_tag($_id, $args, $atts); ?>
    <?php
    // Responsive text align / text color / background (larger screens).
    echo silver_responsive_style_tag($_id, $atts, array_merge(
      array(
        'align'    => array(
          'selector'       => $col_inner,
          'only_overrides' => true,
          'map'            => silver_responsive_map('text_align'),
        ),
        'bg_color' => array(
          'selector'       => $col_inner,
          'only_overrides' => true,
          'property'       => 'background-color',
        ),
      ),
      silver_responsive_scheme_rules('color', $col_inner, 'light', 'dark', array('only_overrides' => true, 'default' => 'dark'))
    ));

    // Width mode: auto / custom width per screen (overrides the grid span).
    echo silver_col_width_style($_id, $atts);
    ?>
  </div>

<?php
  return ob_get_clean();
}

/**
 * Column width per screen when "Width mode" is Auto or Custom on any screen.
 *
 * Screens left on "Grid" get the width of their span (1-12) back, so a
 * custom width on one screen does not leak to the others.
 *
 * @param string $id   Column ID.
 * @param array  $atts Parsed column attributes.
 * @return string
 */
function silver_col_width_style($id, $atts)
{
  $modes = silver_responsive_filled($atts, 'width_mode', 'grid');
  if (! array_diff($modes, array('grid'))) {
    return '';
  }

  $customs = silver_responsive_filled($atts, 'custom_width', '');
  $spans   = silver_responsive_filled($atts, 'span', '12');
  $values  = array();

  foreach ($modes as $index => $mode) {
    $custom = trim((string) $customs[$index]);
    if (is_numeric($custom)) {
      $custom .= 'px';
    }

    if ('auto' === $mode) {
      $values[$index] = 'flex: 0 1 auto; max-width: none; width: auto';
    } elseif ('custom' === $mode && '' !== $custom) {
      $values[$index] = 'flex: 0 0 ' . $custom . '; max-width: ' . $custom . '; width: ' . $custom;
    } else {
      // Grid: the span of this screen (legacy "1/3" values included).
      $span = (string) $spans[$index];
      if (strpos($span, '/')) {
        $span = silver_fix_span($span);
      }
      $percent          = round(max(1, min(12, (float) $span)) / 12 * 100, 6) . '%';
      $values[$index] = 'flex: 0 1 ' . $percent . '; max-width: ' . $percent . '; width: 100%';
    }
  }

  return silver_responsive_style_tag($id, $atts, array(
    'width_mode' => array(
      'selector' => '',
      'values'   => $values,
      'callback' => 'strval',
    ),
  ));
}

add_shortcode('col', 'ux_col');
add_shortcode('col_inner', 'ux_col');
add_shortcode('col_inner_1', 'ux_col');
add_shortcode('col_inner_2', 'ux_col');
add_shortcode('col_inner_3', 'ux_col');
add_shortcode('row', 'ux_row');
add_shortcode('row_inner', 'ux_row');
add_shortcode('row_inner_1', 'ux_row');
add_shortcode('row_inner_2', 'ux_row');
add_shortcode('row_inner_3', 'ux_row');
add_shortcode('background', 'ux_section');
add_shortcode('section', 'ux_section');
add_shortcode('section_inner', 'ux_section');
