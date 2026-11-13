<?php
/**
 * [ux_posts] — Hiển thị danh sách bài viết của một post type bất kỳ
 * (page, post, CPT; trừ blocks), mỗi item render bằng một UX Block làm template.
 *
 * Trong block template dùng các element nhóm "Post Item" (Post Title,
 * Post Thumbnail...) để lấy dữ liệu của item hiện tại.
 */

/**
 * Các post type được phép chọn: public, trừ attachment và blocks.
 *
 * @return array name => label
 */
function silver_ux_posts_post_types()
{
  $types = array();
  foreach (get_post_types(array('public' => true), 'objects') as $type) {
    if (in_array($type->name, array('attachment', 'blocks'), true)) continue;
    $types[$type->name] = $type->labels->name;
  }
  return $types;
}

/**
 * Các taxonomy public gắn với post type được phép (trừ post_format).
 *
 * @return WP_Taxonomy[]
 */
function silver_ux_posts_taxonomies()
{
  $post_types = array_keys(silver_ux_posts_post_types());
  $taxonomies = array();
  foreach (get_taxonomies(array('public' => true), 'objects') as $tax) {
    if ('post_format' === $tax->name) continue;
    if (! array_intersect((array) $tax->object_type, $post_types)) continue;
    $taxonomies[$tax->name] = $tax;
  }
  return $taxonomies;
}

/** Tách chuỗi "1, 2,3" thành mảng ID. */
function silver_ux_posts_ids($value)
{
  return array_values(array_filter(array_map('absint', explode(',', (string) $value))));
}

/**
 * Dựng tham số WP_Query từ attribute của shortcode.
 *
 * @param array $atts     Attribute đã parse.
 * @param array $raw_atts Attribute gốc (chứa các field động tax_terms_{taxonomy}).
 * @return array
 */
function silver_ux_posts_query_args($atts, $raw_atts)
{
  $allowed_types = silver_ux_posts_post_types();
  $post_type     = isset($allowed_types[$atts['post_type']]) ? $atts['post_type'] : 'post';
  $posts         = (int) $atts['posts'];
  if ($posts === 0) $posts = 8;
  $page_number   = max(1, (int) $atts['page_number']);
  $offset        = absint($atts['offset']);

  $orderby = in_array($atts['orderby'], array('ID', 'title', 'name', 'date', 'modified', 'rand', 'comment_count', 'menu_order', 'post__in'), true) ? $atts['orderby'] : 'date';
  $order   = 'ASC' === strtoupper($atts['order']) ? 'ASC' : 'DESC';

  $args = array(
    'post_type'           => $post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $posts,
    'paged'               => $page_number,
    'ignore_sticky_posts' => true,
    'orderby'             => $orderby,
    'order'               => $order,
  );

  if ($offset || $page_number > 1) {
    $args['offset'] = $posts > 0 ? $offset + ($page_number - 1) * $posts : $offset;
  }

  // post__in / post__not_in.
  $post_in     = silver_ux_posts_ids($atts['post_in']);
  $post_not_in = silver_ux_posts_ids($atts['post_not_in']);
  if ($post_in) $args['post__in'] = $post_in;
  if ($post_not_in) $args['post__not_in'] = $post_not_in;
  if ('post__in' === $orderby && ! $post_in) $args['orderby'] = 'date';

  // tax_query.
  $taxonomies = silver_ux_posts_taxonomies();
  $taxonomy   = $atts['tax_taxonomy'];
  if ($taxonomy && isset($taxonomies[$taxonomy])) {
    $terms    = silver_ux_posts_ids(isset($raw_atts['tax_terms_' . $taxonomy]) ? $raw_atts['tax_terms_' . $taxonomy] : '');
    $operator = in_array($atts['tax_operator'], array('IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS'), true) ? $atts['tax_operator'] : 'IN';
    $clause   = array('taxonomy' => $taxonomy, 'operator' => $operator);

    if (in_array($operator, array('EXISTS', 'NOT EXISTS'), true)) {
      $args['tax_query'] = array($clause);
    } elseif ($terms) {
      $clause['field']            = 'term_id';
      $clause['terms']            = $terms;
      $clause['include_children'] = 'true' === $atts['tax_include_children'];
      $args['tax_query']          = array($clause);
    }
  }

  // meta_query.
  $meta_key = trim((string) $atts['meta_key']);
  // Qua AJAX (phân trang), không cho lọc theo meta ẩn (bắt đầu bằng "_")
  // để tránh dò giá trị meta nội bộ từ request công khai.
  if ($meta_key !== '' && ! (wp_doing_ajax() && is_protected_meta($meta_key, 'post'))) {
    $compares = array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS');
    $types    = array('CHAR', 'NUMERIC', 'DECIMAL', 'DATE', 'DATETIME', 'TIME', 'SIGNED', 'UNSIGNED', 'BINARY');
    $compare  = in_array($atts['meta_compare'], $compares, true) ? $atts['meta_compare'] : '=';
    $clause   = array(
      'key'     => $meta_key,
      'compare' => $compare,
      'type'    => in_array($atts['meta_type'], $types, true) ? $atts['meta_type'] : 'CHAR',
    );

    if (! in_array($compare, array('EXISTS', 'NOT EXISTS'), true)) {
      $value = (string) $atts['meta_value'];
      if (in_array($compare, array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'), true)) {
        $value = array_map('trim', explode(',', $value));
      }
      $clause['value'] = $value;
    }

    $args['meta_query'] = array($clause);
  }

  /**
   * Cho phép tuỳ biến query (thêm nhiều mệnh đề tax_query/meta_query...).
   *
   * @param array $args     Tham số WP_Query.
   * @param array $atts     Attribute đã parse.
   * @param array $raw_atts Attribute gốc.
   */
  return apply_filters('silver_ux_posts_query_args', $args, $atts, $raw_atts);
}

// [ux_posts]
function silver_ux_posts_shortcode($atts, $content = null, $tag = 'ux_posts')
{
  static $depth = 0;

  $raw_atts = (array) $atts;
  $atts     = ux_builder_shortcode_atts(array(
    '_id'        => 'ux-posts-' . wp_rand(),
    'class'      => '',
    'visibility' => '',

    // Query.
    'post_type'            => 'post',
    'posts'                => '8',
    'offset'               => '',
    'orderby'              => 'date',
    'order'                => 'DESC',
    'page_number'          => '1',
    'post_in'              => '',
    'post_not_in'          => '',
    'tax_taxonomy'         => '',
    'tax_operator'         => 'IN',
    'tax_include_children' => 'true',
    'meta_key'             => '',
    'meta_compare'         => '=',
    'meta_value'           => '',
    'meta_type'            => 'CHAR',

    // Template.
    'template' => '',

    // Layout.
    'type'        => 'row',
    'grid'        => '1',
    'grid_height' => '600px',
    'width'       => '',
    'col_spacing' => 'normal',
    'v_align'     => '',
    'h_align'     => '',
    'columns'     => '2',
    'columns__md' => null,
    'columns__ml' => null,
    'columns__lg' => '4',
    'depth'       => '',
    'depth_hover' => '',
    'animate'     => '',

    // Slider.
    'slider_nav_style'    => 'reveal',
    'slider_nav_position' => '',
    'slider_nav_color'    => '',
    'slider_bullets'      => 'false',
    'slider_arrows'       => 'true',
    'auto_slide'          => 'false',
    'infinitive'          => 'true',

    // Relay (pagination).
    'relay'                      => '',
    'relay_control_result_count' => 'true',
    'relay_control_position'     => 'bottom',
    'relay_control_align'        => 'center',
    'relay_id'                   => '',
    'relay_class'                => '',
  ), $raw_atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  // Template: UX Block đã publish.
  $template_id = silver_get_block_id($atts['template']);
  $template    = $template_id ? get_post($template_id) : null;
  if (! $template || 'blocks' !== $template->post_type || 'publish' !== $template->post_status) {
    return current_user_can('edit_pages') ? '<p class="text-center"><mark>' . esc_html__('Posts: chưa chọn template (UX Block).', 'silver') . '</mark></p>' : '';
  }

  // Tránh lặp vô hạn khi template lại chứa [ux_posts].
  if ($depth >= 3) return '';
  $depth++;

  $args  = silver_ux_posts_query_args($atts, $raw_atts);
  $query = new WP_Query($args);

  // Trừ offset khỏi tổng số bài để phân trang đúng (giống blog_posts).
  if (! empty($args['offset']) && absint($atts['offset'])) {
    $query->found_posts   = max(0, $query->found_posts - absint($atts['offset']));
    $query->max_num_pages = $args['posts_per_page'] > 0 ? (int) ceil($query->found_posts / $args['posts_per_page']) : 1;
  }

  $grid = array();
  if ('grid' === $atts['type']) {
    $grid = silver_get_grid($atts['grid']);
  }

  // Căn chỉnh cột giống Row: align-{top|equal|middle|bottom|center|right}.
  $row_class = array('ux-posts');
  if (in_array($atts['v_align'], array('equal', 'middle', 'bottom'), true)) $row_class[] = 'align-' . $atts['v_align'];
  if (in_array($atts['h_align'], array('center', 'right'), true)) $row_class[] = 'align-' . $atts['h_align'];
  if ($atts['class']) $row_class[] = $atts['class'];

  $repeater = array(
    'id'                  => $atts['_id'],
    'tag'                 => $tag,
    'type'                => $atts['type'],
    'class'               => implode(' ', $row_class),
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
    'columns'             => 'grid' === $atts['type'] ? 0 : $atts['columns'],
    'columns__md'         => $atts['columns__md'],
    'columns__ml'         => $atts['columns__ml'],
    'columns__lg'         => $atts['columns__lg'],
    'depth'               => $atts['depth'],
    'depth_hover'         => $atts['depth_hover'],
    // Responsive width / alignment (see get_silver_repeater_start()).
    'responsive_atts'     => $atts,
  );

  $template_html = $template->post_content;
  $animate       = $atts['animate'] ? ' data-animate="' . esc_attr($atts['animate']) . '"' : '';

  ob_start();

  Silver_Relay::render_container_open($query, $tag, $raw_atts, $atts);

  if ('grid' === $atts['type']) {
    silver_get_grid_height($atts['grid_height'], $atts['_id']);
  }

  get_silver_repeater_start($repeater);

  $index = 0;
  while ($query->have_posts()) {
    $query->the_post();

    $col_class = array('col', 'post-item', 'ux-posts-item', 'ux-posts-item-' . get_post_type());

    if ($grid) {
      $current     = min($index, count($grid) - 1);
      $col_class[] = 'grid-col';
      if ($grid[$current]['height']) $col_class[] = 'grid-col-' . $grid[$current]['height'];
      if ($grid[$current]['span']) $col_class[] = 'xlarge-' . $grid[$current]['span'];
      if ($grid[$current]['md']) $col_class[] = 'medium-' . $grid[$current]['md'];
    }

    echo '<div class="' . esc_attr(implode(' ', $col_class)) . '"' . $animate . '><div class="col-inner">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo do_shortcode($template_html); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</div></div>';

    $index++;
  }
  wp_reset_postdata();

  get_silver_repeater_end($atts);

  Silver_Relay::render_container_close();

  $depth--;

  return ob_get_clean();
}
add_shortcode('ux_posts', 'silver_ux_posts_shortcode');
