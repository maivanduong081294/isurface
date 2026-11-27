<?php
/**
 * [ux_mega_menu] + [ux_mega_menu_item] — Mega menu: a trigger button that
 * opens a panel with a list of items on the left (tabs) and the content of
 * the active item on the right. Below 850px the items become an accordion.
 *
 * Markup: every item renders its tab and its pane next to each other, so the
 * DOM order also works as an accordion. On desktop the items use
 * `display: contents` and the panel is a 2-column grid (tabs | panes).
 *
 * Assets: assets/css/src/silver-mega-menu.css and
 * assets/js/src/silver-mega-menu.js, merged into silver.css / silver.js by
 * build/build.mjs.
 */

/**
 * Counts the items of the mega menu being rendered.
 *
 * @param string $action "reset" (before the items), "next" (an item: returns
 *                       its index) or "get" (after the items: the count).
 * @return int
 */
function silver_mega_menu_counter($action = 'next')
{
  static $count = 0;

  if ('reset' === $action) {
    $count = 0;
    return 0;
  }
  if ('get' === $action) return $count;

  return $count++;
}

// [ux_mega_menu]
function silver_mega_menu_shortcode($atts, $content = null, $tag = 'ux_mega_menu')
{
  $atts = ux_builder_shortcode_atts(array(
    '_id'           => '',
    'class'         => '',
    'visibility'    => '',
    'text'          => __('Danh mục sản phẩm', 'silver'),
    'icon'          => 'fa-solid fa-bars',
    'trigger'       => 'hover', // hover / click (desktop; mobile always uses click)
    'panel_width'   => '1100px',
    'panel_align'   => 'left',
    'sidebar_width' => '260px',
    'panel_padding' => '', // Side padding of the panel inner (default: row gutter, 15px).
    'min_height'    => '',
    'trigger_bg'    => '',
    'trigger_color' => '',
    // Per screen (responsive): button / icon (hamburger + drawer).
    'display_mode'      => 'button',
    'drawer_title'      => '',
    'drawer_layout'     => 'expanded', // expanded / accordion
    'drawer_columns'    => 3,
    'drawer_pos'        => 'left',
    'drawer_icon_size'  => 22,
    'drawer_icon_color' => '',
  ), $atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  $id      = $atts['_id'] ? sanitize_html_class($atts['_id']) : 'ux-mega-menu-' . wp_rand();
  $trigger = 'click' === $atts['trigger'] ? 'click' : 'hover';
  $align   = in_array($atts['panel_align'], array('left', 'center', 'right', 'container'), true) ? $atts['panel_align'] : 'left';
  // Only the edited element's AJAX preview, not the surrounding header/footer.
  $builder = function_exists('ux_builder_is_active') && ux_builder_is_active()
    && function_exists('ux_builder_is_doing_shortcode') && ux_builder_is_doing_shortcode();

  // Items render their own tab + pane; count them for the grid rows.
  silver_mega_menu_counter('reset');
  $items = do_shortcode((string) $content);
  $count = max(1, silver_mega_menu_counter('get'));

  // Screens (UX Builder breakpoints: sm, md, ml, lg) that show the icon +
  // drawer. Mobile-first: a screen without its own value inherits the
  // next smaller one. CSS styles the icon per screen (no flash before JS);
  // silver-mega-menu.js adds .is-drawer on those screens.
  $drawer_screens = array();
  $screen_names   = array_keys(get_ux_builder_breakpoints());
  foreach (silver_responsive_filled($atts, 'display_mode', 'button') as $index => $mode) {
    if ('icon' === $mode && isset($screen_names[$index])) $drawer_screens[] = $screen_names[$index];
  }
  $mobile_icon = ! empty($drawer_screens);

  $classes = array('ux-mega-menu', 'ux-mega-menu-trigger-' . $trigger, 'ux-mega-menu-align-' . $align);
  if ($mobile_icon) {
    $classes[] = 'ux-mega-menu-has-drawer';
    $classes[] = 'ux-mega-menu-drawer-' . ('right' === $atts['drawer_pos'] ? 'right' : 'left');
    $classes[] = 'ux-mega-menu-drawer-' . ('accordion' === $atts['drawer_layout'] ? 'accordion' : 'expanded');
    foreach ($drawer_screens as $screen) $classes[] = 'ux-mega-menu-icon-' . sanitize_html_class($screen);
  }
  // UX Builder: closed like on the site, opened by a click on the button /
  // icon; silver-mega-menu.js keeps it open (and its item) between renders.
  if ($builder) $classes[] = 'ux-mega-menu-builder';
  if ($atts['class']) $classes[] = $atts['class'];
  if ($atts['visibility']) $classes[] = $atts['visibility'];

  $icon = trim(implode(' ', array_filter(preg_split('/\s+/', (string) $atts['icon']), function ($class) {
    return (bool) preg_match('/^[a-z0-9-]+$/', $class);
  })));

  ob_start();
?>
  <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-trigger="<?php echo esc_attr($trigger); ?>"<?php if ($mobile_icon) echo ' data-drawer="' . esc_attr(implode(' ', $drawer_screens)) . '"'; ?>>
    <button type="button" class="ux-mega-menu-trigger" aria-expanded="false" aria-controls="<?php echo esc_attr($id); ?>-panel">
      <?php if ($icon) echo get_silver_icon($icon); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <span class="ux-mega-menu-trigger-text"><?php echo esc_html($atts['text']); ?></span>
    </button>
    <div id="<?php echo esc_attr($id); ?>-panel" class="ux-mega-menu-panel">
      <?php if ($mobile_icon) : ?>
        <?php /* Drawer header: drawer only, outside the inner (full width). */ ?>
        <div class="ux-mega-menu-drawer-head">
          <span class="ux-mega-menu-drawer-title"><?php echo esc_html($atts['drawer_title'] ? $atts['drawer_title'] : $atts['text']); ?></span>
          <button type="button" class="ux-mega-menu-close" aria-label="<?php esc_attr_e('Close', 'silver'); ?>">
            <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      <?php endif; ?>
      <?php /* Inner: side padding like a row's columns (Panel > Padding); tabs | panes grid on desktop. */ ?>
      <div class="ux-mega-menu-panel-inner" style="grid-template-rows: repeat(<?php echo (int) $count; ?>, min-content) 1fr;">
        <?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput ?>
      </div>
    </div>
    <?php
    // Trigger colours are variables, so the mobile icon can drop the background.
    echo silver_responsive_style_tag($id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
      'panel_width'       => array('selector' => '', 'property' => '--ux-mega-width', 'important' => false),
      'sidebar_width'     => array('selector' => '', 'property' => '--ux-mega-sidebar', 'important' => false),
      'panel_padding'     => array('selector' => '', 'property' => '--ux-mega-gutter', 'important' => false),
      'min_height'        => array('selector' => '', 'property' => '--ux-mega-min-height', 'important' => false),
      'trigger_bg'        => array('selector' => '', 'property' => '--ux-mega-trigger-bg', 'important' => false),
      'trigger_color'     => array('selector' => '', 'property' => '--ux-mega-trigger-color', 'important' => false),
      'drawer_columns'    => array('selector' => '', 'property' => '--ux-mega-drawer-columns', 'important' => false),
      'drawer_icon_size'  => array('selector' => '', 'property' => '--ux-mega-drawer-icon-size', 'unit' => 'px', 'important' => false),
      'drawer_icon_color' => array('selector' => '', 'property' => '--ux-mega-drawer-icon-color', 'important' => false),
    ));
    ?>
  </div>
<?php
  return ob_get_clean();
}
add_shortcode('ux_mega_menu', 'silver_mega_menu_shortcode');

/**
 * Child terms shown by an item with the "Child categories" source.
 *
 * @param string $taxonomy
 * @param int    $parent
 * @param int    $number
 * @param bool   $hide_empty
 * @return WP_Term[]
 */
function silver_mega_menu_child_terms($taxonomy, $parent, $number, $hide_empty)
{
  $key   = 'silver_mega_' . md5(wp_json_encode(func_get_args()) . wp_cache_get_last_changed('terms'));
  $terms = get_transient($key);

  if (false === $terms) {
    $terms = get_terms(array(
      'taxonomy'   => $taxonomy,
      'parent'     => $parent,
      'number'     => $number,
      'hide_empty' => $hide_empty,
      'orderby'    => 'product_cat' === $taxonomy ? 'meta_value_num' : 'name',
      'meta_key'   => 'product_cat' === $taxonomy ? 'order' : '',
    ));
    if (is_wp_error($terms)) $terms = array();
    set_transient($key, $terms, HOUR_IN_SECONDS);
  }

  return $terms;
}

// [ux_mega_menu_item]
function silver_mega_menu_item_shortcode($atts, $content = null, $tag = 'ux_mega_menu_item')
{
  $raw_atts = (array) $atts;
  $atts     = ux_builder_shortcode_atts(array(
    '_id'          => '',
    'class'        => '',
    'visibility'   => '',
    'title'        => __('Danh mục', 'silver'),
    'icon_type'    => 'none', // none / icon / image
    'icon'         => '',
    'img'          => '',
    'show_heading' => 'yes',
    'view_all'     => __('Xem tất cả', 'silver'),
    'link'         => '',
    'target'       => '',
    'rel'          => '',
    // Source of the pane: free content or the child categories of a term.
    'source'       => 'content',
    'taxonomy'     => 'product_cat',
    'number'       => '8',
    'hide_empty'   => 'false',
    'columns'      => '4',
    'image_size'   => 'thumbnail',
  ), $raw_atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  $index    = silver_mega_menu_counter();
  $id       = $atts['_id'] ? sanitize_html_class($atts['_id']) : 'ux-mega-item-' . wp_rand();
  $title    = wp_strip_all_tags($atts['title']);
  $link     = trim((string) $atts['link']);
  $classes  = array('ux-mega-menu-item');
  // UX Builder renders every item on its own (the counter starts again at 0
  // for each one): there silver-mega-menu.js picks the open item.
  $builder  = function_exists('ux_builder_is_active') && ux_builder_is_active()
    && function_exists('ux_builder_is_doing_shortcode') && ux_builder_is_doing_shortcode();
  $active   = 0 === $index && ! $builder;
  if ($active) $classes[] = 'is-active';
  if ($atts['class']) $classes[] = $atts['class'];
  if ($atts['visibility']) $classes[] = $atts['visibility'];

  // Tab media: Font Awesome icon or image.
  $media = '';
  if ('icon' === $atts['icon_type'] && $atts['icon']) {
    $icon  = trim(implode(' ', array_filter(preg_split('/\s+/', (string) $atts['icon']), function ($class) {
      return (bool) preg_match('/^[a-z0-9-]+$/', $class);
    })));
    $media = $icon ? get_silver_icon($icon) : '';
  } elseif ('image' === $atts['icon_type'] && $atts['img']) {
    $media = silver_get_image($atts['img'], 'thumbnail', $title);
  }

  // Pane: child categories.
  $grid = '';
  if ('categories' === $atts['source']) {
    $taxonomy = 'category' === $atts['taxonomy'] || ! taxonomy_exists('product_cat') ? 'category' : 'product_cat';
    $parent   = isset($raw_atts['parent_' . $taxonomy]) ? absint($raw_atts['parent_' . $taxonomy]) : 0;
    $terms    = silver_mega_menu_child_terms($taxonomy, $parent, max(1, absint($atts['number'])), 'true' === $atts['hide_empty']);

    // "Xem tất cả" defaults to the parent term.
    if (! $link && $parent) {
      $parent_link = get_term_link($parent, $taxonomy);
      if (! is_wp_error($parent_link)) $link = $parent_link;
    }

    if ($terms) {
      $grid .= '<ul class="ux-mega-menu-grid">';
      foreach ($terms as $term) {
        $thumb_id = 'product_cat' === $taxonomy ? (int) get_term_meta($term->term_id, 'thumbnail_id', true) : 0;
        $image    = $thumb_id ? wp_get_attachment_image($thumb_id, $atts['image_size'], false, array('loading' => 'lazy', 'alt' => $term->name)) : '';
        $url      = get_term_link($term);
        if (is_wp_error($url)) continue;

        $grid .= '<li class="ux-mega-menu-grid-item"><a href="' . esc_url($url) . '">'
          . '<span class="ux-mega-menu-grid-image">' . ($image ?: '<span class="ux-mega-menu-grid-placeholder" aria-hidden="true"></span>') . '</span>'
          . '<span class="ux-mega-menu-grid-name">' . esc_html($term->name) . '</span>'
          . '</a></li>';
      }
      $grid .= '</ul>';
    } elseif (current_user_can('edit_pages')) {
      $grid = '<p class="ux-mega-menu-empty">' . esc_html__('Không có danh mục con.', 'silver') . '</p>';
    }
  }

  $link_atts = '';
  if ($link) {
    $rel = trim((string) $atts['rel']);
    if ('_blank' === $atts['target'] && false === strpos($rel, 'noopener')) $rel = trim($rel . ' noopener');
    $link_atts = ' href="' . esc_url($link) . '"'
      . ($atts['target'] ? ' target="' . esc_attr($atts['target']) . '"' : '')
      . ($rel ? ' rel="' . esc_attr($rel) . '"' : '');
  }

  ob_start();
?>
  <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <button type="button" class="ux-mega-menu-tab" aria-expanded="<?php echo $active ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($id); ?>-pane"<?php echo $link ? ' data-href="' . esc_url($link) . '"' : ''; ?>>
      <?php if ($media) : ?><span class="ux-mega-menu-tab-media"><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php endif; ?>
      <span class="ux-mega-menu-tab-text"><?php echo esc_html($title); ?></span>
      <?php echo get_silver_icon('fa-solid fa-chevron-right', null, array('class' => 'ux-mega-menu-tab-arrow')); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </button>
    <div id="<?php echo esc_attr($id); ?>-pane" class="ux-mega-menu-pane">
      <?php
      // Always rendered: the expanded drawer (tabs hidden) needs the heading
      // even when "Show heading" is off; then it is hidden on the desktop panel.
      // The media (icon / image) only shows in the drawer.
      ?>
      <div class="ux-mega-menu-heading<?php echo 'yes' === $atts['show_heading'] ? '' : ' ux-mega-menu-heading--drawer-only'; ?>">
        <span class="ux-mega-menu-title">
          <?php if ($media) : ?><span class="ux-mega-menu-heading-media"><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php endif; ?>
          <span class="ux-mega-menu-title-text"><?php echo esc_html($title); ?></span>
        </span>
        <?php if ($link && $atts['view_all']) : ?>
          <a class="ux-mega-menu-view-all"<?php echo $link_atts; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html($atts['view_all']); ?></a>
        <?php endif; ?>
      </div>
      <?php echo $grid; // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <?php if (trim((string) $content) !== '') : ?>
        <div class="ux-mega-menu-content"><?php echo do_shortcode($content); ?></div>
      <?php endif; ?>
    </div>
    <?php
    echo silver_responsive_style_tag($id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
      'columns' => array('selector' => '', 'property' => '--ux-mega-columns', 'important' => false),
    ));
    ?>
  </div>
<?php
  return ob_get_clean();
}
add_shortcode('ux_mega_menu_item', 'silver_mega_menu_item_shortcode');
