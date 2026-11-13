<?php
/**
 * [ux_terms] — Terms of a taxonomy (product categories, tags, attributes...)
 * with thumbnail / title / count / description, each part optional.
 * Layout: same repeater as Blog posts / Posts (row, slider, masonry, grid).
 * Builder element: inc/builder/shortcodes/ux_terms.php.
 */

/** Terms CSS (small, loaded on every page for the builder canvas / AJAX renders). */
function silver_terms_assets()
{
  $file = '/assets/css/silver-terms.css';
  wp_enqueue_style('silver-terms', get_template_directory_uri() . $file, array('silver-main'), filemtime(get_template_directory() . $file));
}
add_action('wp_enqueue_scripts', 'silver_terms_assets', 110);

/**
 * Public taxonomies offered by the element (without post_format).
 *
 * @return WP_Taxonomy[] name => taxonomy
 */
function silver_terms_taxonomies()
{
  $taxonomies = get_taxonomies(array('public' => true), 'objects');
  unset($taxonomies['post_format']);
  return $taxonomies;
}

/**
 * Query arguments from the shortcode attributes.
 *
 * @param array $atts     Parsed attributes.
 * @param array $raw_atts Raw attributes (per-taxonomy fields parent_{tax}, include_{tax}, exclude_{tax}).
 * @return array
 */
function silver_terms_query_args($atts, $raw_atts)
{
  $taxonomies = silver_terms_taxonomies();
  $taxonomy   = isset($taxonomies[$atts['taxonomy']]) ? $atts['taxonomy'] : (isset($taxonomies['product_cat']) ? 'product_cat' : 'category');
  $ids        = function ($key) use ($raw_atts) {
    return isset($raw_atts[$key]) ? array_values(array_filter(array_map('absint', explode(',', (string) $raw_atts[$key])))) : array();
  };

  $args = array(
    'taxonomy'   => $taxonomy,
    'hide_empty' => 'true' === $atts['hide_empty'],
    'number'     => max(0, (int) $atts['number']),
    'order'      => 'DESC' === strtoupper($atts['order']) ? 'DESC' : 'ASC',
    'pad_counts' => is_taxonomy_hierarchical($taxonomy),
  );

  // Level: all / top level / children of a term.
  if ('top' === $atts['parent_mode']) {
    $args['parent'] = 0;
  } elseif ('children' === $atts['parent_mode']) {
    $parent         = $ids('parent_' . $taxonomy);
    $args['parent'] = $parent ? $parent[0] : 0;
  }

  $include = $ids('include_' . $taxonomy);
  $exclude = $ids('exclude_' . $taxonomy);
  if ($include) $args['include'] = $include;
  if ($exclude) $args['exclude'] = $exclude;

  switch ($atts['orderby']) {
    case 'count':
    case 'term_id':
    case 'slug':
      $args['orderby'] = $atts['orderby'];
      break;
    case 'include':
      $args['orderby'] = $include ? 'include' : 'name';
      break;
    case 'menu_order':
      // WooCommerce stores the drag & drop order of product terms in "order".
      $args['orderby']  = 'meta_value_num';
      $args['meta_key'] = 'order';
      break;
    default:
      $args['orderby'] = 'name';
  }

  return apply_filters('silver_terms_query_args', $args, $atts, $raw_atts);
}

/**
 * Thumbnail of a term: attachment ID (or URL) stored in a term meta.
 *
 * @param WP_Term $term
 * @param string  $meta_key
 * @param string  $size
 * @return string
 */
function silver_terms_thumbnail($term, $meta_key, $size)
{
  $value = $meta_key ? get_term_meta($term->term_id, $meta_key, true) : '';

  if (is_numeric($value) && (int) $value > 0) {
    $image = wp_get_attachment_image((int) $value, $size, false, array('loading' => 'lazy', 'alt' => $term->name));
    if ($image) return $image;
  } elseif (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
    return '<img src="' . esc_url($value) . '" alt="' . esc_attr($term->name) . '" loading="lazy" />';
  }

  // WooCommerce placeholder for product terms, a blank block otherwise.
  if (function_exists('wc_placeholder_img_src') && in_array($term->taxonomy, get_object_taxonomies('product'), true)) {
    return '<img src="' . esc_url(wc_placeholder_img_src($size)) . '" alt="' . esc_attr($term->name) . '" loading="lazy" />';
  }

  return '<span class="ux-terms-placeholder" aria-hidden="true"></span>';
}

// [ux_terms]
function silver_terms_shortcode($atts, $content = null, $tag = 'ux_terms')
{
  $raw_atts = (array) $atts;
  $atts     = ux_builder_shortcode_atts(array(
    '_id'        => 'ux-terms-' . wp_rand(),
    'class'      => '',
    'visibility' => '',

    // Query.
    'taxonomy'    => taxonomy_exists('product_cat') ? 'product_cat' : 'category',
    'parent_mode' => 'top', // all / top / children
    'number'      => '8',
    'orderby'     => 'name',
    'order'       => 'ASC',
    'hide_empty'  => 'false',

    // Display.
    'show_thumbnail'   => 'yes',
    'show_title'       => 'yes',
    'show_count'       => 'yes',
    'show_description' => 'no',
    'count_format'     => '{count}',
    'title_tag'        => 'h4',
    'image_meta'       => 'thumbnail_id',
    'image_size'       => 'medium',
    'image_height'     => '100%',
    'image_radius'     => '',
    'image_fit'        => 'cover',
    'text_align'       => 'center',

    // Layout (commons/repeater-options.php + repeater-slider.php).
    'type'        => 'row',
    'grid'        => '1',
    'grid_height' => '600px',
    'width'       => '',
    'col_spacing' => 'normal',
    'columns'     => '2',
    'columns__md' => null,
    'columns__ml' => null,
    'columns__lg' => '4',
    'depth'       => '',
    'depth_hover' => '',
    'animate'     => '',
    'slider_nav_style'    => 'reveal',
    'slider_nav_position' => '',
    'slider_nav_color'    => '',
    'slider_bullets'      => 'false',
    'auto_slide'          => 'false',
    'infinitive'          => 'true',
  ), $raw_atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  $terms = get_terms(silver_terms_query_args($atts, $raw_atts));
  if (is_wp_error($terms) || ! $terms) {
    return current_user_can('edit_pages') ? '<p class="text-center"><mark>' . esc_html__('Terms: không có term nào phù hợp.', 'silver') . '</mark></p>' : '';
  }

  $title_tag = in_array($atts['title_tag'], array('h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div'), true) ? $atts['title_tag'] : 'h4';
  $image_fit = 'contain' === $atts['image_fit'] ? 'contain' : 'cover';
  $grid      = 'grid' === $atts['type'] ? silver_get_grid($atts['grid']) : array();
  $animate   = $atts['animate'] ? ' data-animate="' . esc_attr($atts['animate']) . '"' : '';

  $repeater = array(
    'id'                  => $atts['_id'],
    'tag'                 => $tag,
    'type'                => $atts['type'],
    'class'               => trim('ux-terms ux-terms-fit-' . $image_fit . ' text-' . $atts['text_align'] . ' ' . $atts['class']),
    'visibility'          => $atts['visibility'],
    'style'               => '',
    'slider_style'        => $atts['slider_nav_style'],
    'slider_nav_position' => $atts['slider_nav_position'],
    'slider_nav_color'    => $atts['slider_nav_color'],
    'slider_bullets'      => $atts['slider_bullets'],
    'auto_slide'          => $atts['auto_slide'],
    'infinitive'          => $atts['infinitive'],
    'row_spacing'         => $atts['col_spacing'],
    'row_width'           => $atts['width'],
    'columns'             => $grid ? 0 : $atts['columns'],
    'columns__md'         => $atts['columns__md'],
    'columns__ml'         => $atts['columns__ml'],
    'columns__lg'         => $atts['columns__lg'],
    'depth'               => $atts['depth'],
    'depth_hover'         => $atts['depth_hover'],
    // Responsive Layout width only (image/text options are handled below;
    // their names would otherwise match the Box styles rules).
    'responsive_atts'     => array_intersect_key($atts, array_flip(array('col_spacing', 'width', 'width__md', 'width__ml', 'width__lg'))),
  );

  ob_start();

  if ($grid) silver_get_grid_height($atts['grid_height'], $atts['_id']);

  get_silver_repeater_start($repeater);

  foreach ($terms as $index => $term) {
    $url = get_term_link($term);
    if (is_wp_error($url)) continue;

    $col_class = array('col', 'ux-terms-item', 'ux-terms-item-' . $term->slug);
    if ($grid) {
      $current     = min($index, count($grid) - 1);
      $col_class[] = 'grid-col';
      if ($grid[$current]['height']) $col_class[] = 'grid-col-' . $grid[$current]['height'];
      if ($grid[$current]['span']) $col_class[] = 'xlarge-' . $grid[$current]['span'];
      if ($grid[$current]['md']) $col_class[] = 'medium-' . $grid[$current]['md'];
    }

    echo '<div class="' . esc_attr(implode(' ', $col_class)) . '"' . $animate . '><div class="col-inner">'; // phpcs:ignore WordPress.Security.EscapeOutput
    echo '<a class="ux-terms-link plain" href="' . esc_url($url) . '">';

    if ('yes' === $atts['show_thumbnail']) {
      echo '<div class="ux-terms-image"><div class="ux-terms-image-inner">' . silver_terms_thumbnail($term, $atts['image_meta'], $atts['image_size']) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
    }

    if ('yes' === $atts['show_title'] || 'yes' === $atts['show_count'] || 'yes' === $atts['show_description']) {
      echo '<div class="ux-terms-text">';
      if ('yes' === $atts['show_title']) {
        echo '<' . $title_tag . ' class="ux-terms-title">' . esc_html($term->name) . '</' . $title_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
      }
      if ('yes' === $atts['show_count']) {
        $count = str_replace('{count}', number_format_i18n($term->count), $atts['count_format'] ?: '{count}');
        echo '<span class="ux-terms-count">' . esc_html($count) . '</span>';
      }
      if ('yes' === $atts['show_description'] && '' !== trim($term->description)) {
        echo '<div class="ux-terms-description">' . wp_kses_post(wpautop($term->description)) . '</div>';
      }
      echo '</div>';
    }

    echo '</a></div></div>';
  }

  get_silver_repeater_end($atts);

  // Image ratio / radius and text align, per screen.
  echo silver_responsive_style_tag($atts['_id'], $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
    'image_height' => array('selector' => '.ux-terms-image-inner', 'property' => 'padding-top'),
    'image_radius' => array('selector' => '.ux-terms-image-inner', 'property' => 'border-radius', 'unit' => 'px'),
    'text_align'   => array('selector' => '.ux-terms-item', 'only_overrides' => true, 'default' => 'center', 'map' => silver_responsive_map('text_align')),
  ));

  return ob_get_clean();
}
add_shortcode('ux_terms', 'silver_terms_shortcode');
