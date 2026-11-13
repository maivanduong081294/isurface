<?php
/**
 * [ux_quick_cart] — Cart icon with the mini cart (dropdown, off-canvas or
 * plain link) that can be placed anywhere with UX Builder.
 *
 * Reuses the theme behaviors: ".nav li.has-dropdown" (dropdown) and
 * "[data-open]" (off-canvas). The mini cart content
 * (.widget_shopping_cart_content), the count and the total are refreshed by
 * the WooCommerce cart fragments.
 *
 * Loaded only when WooCommerce is active (inc/init.php).
 */

/**
 * Font Awesome classes of the icon option (picker value, e.g.
 * "fa-solid fa-cart-shopping"). The values of an earlier version
 * ("basket", "cart", "bag", like the header cart icon) are mapped.
 *
 * @param string $value
 * @return string
 */
function silver_quick_cart_icon_class($value)
{
  $legacy = array(
    'basket' => 'fa-solid fa-basket-shopping',
    'cart'   => 'fa-solid fa-cart-shopping',
    'bag'    => 'fa-solid fa-bag-shopping',
  );
  $value = trim((string) $value);

  if (isset($legacy[$value])) return $legacy[$value];

  // Only CSS class names.
  $classes = array_filter(preg_split('/\s+/', $value), function ($class) {
    return (bool) preg_match('/^[a-z0-9-]+$/', $class);
  });

  return $classes ? implode(' ', $classes) : $legacy['basket'];
}

// [ux_quick_cart]
function silver_quick_cart_shortcode($atts, $content = null, $tag = 'ux_quick_cart')
{
  $atts = ux_builder_shortcode_atts(array(
    '_id'        => '',
    'class'      => '',
    'visibility' => '',
    'style'      => 'dropdown', // dropdown / off-canvas / link
    'icon'       => get_theme_mod('cart_icon', 'basket'),
    'icon_size'  => '',
    'icon_color' => '',
    'show_count' => 'yes',
    'show_title' => 'no',
    'show_total' => 'yes',
    'align'      => 'left',
  ), $atts, $tag);

  if ('hidden' === $atts['visibility']) return '';

  // Same conditions as the header cart.
  if (! function_exists('WC') || ! silver_is_wc_cart_available() || get_theme_mod('catalog_mode')) {
    return '';
  }

  $id    = $atts['_id'] ? sanitize_html_class($atts['_id']) : 'ux-quick-cart-' . wp_rand();
  $style = in_array($atts['style'], array('dropdown', 'off-canvas', 'link'), true) ? $atts['style'] : 'dropdown';
  $icon  = silver_quick_cart_icon_class($atts['icon']);
  $align = in_array($atts['align'], array('left', 'center', 'right'), true) ? $atts['align'] : 'left';

  // No mini cart on the cart and checkout pages (same as the header cart).
  if (apply_filters('silver_disable_mini_cart', is_cart() || is_checkout())) {
    $style = 'link';
  }

  // Cart fragments are not loaded by default since WooCommerce 7.8.
  if (fl_woocommerce_version_check('7.8.0') && ! wp_script_is('wc-cart-fragments')) {
    wp_enqueue_script('wc-cart-fragments');
  }

  $count   = WC()->cart->get_cart_contents_count();
  $popup   = $id . '-popup';
  $classes = array('ux-quick-cart', 'ux-quick-cart-style-' . $style);
  if ('yes' !== $atts['show_count']) $classes[] = 'ux-quick-cart-no-count';
  if ($atts['class']) $classes[] = $atts['class'];
  if ($atts['visibility']) $classes[] = $atts['visibility'];

  $link_atts = array(
    'href'       => is_customize_preview() ? '#' : esc_url(wc_get_cart_url()),
    'class'      => 'ux-quick-cart-link nav-top-link',
    'title'      => esc_attr__('Cart', 'woocommerce'),
    'aria-label' => esc_attr__('View cart', 'woocommerce'),
  );
  if ('dropdown' === $style) {
    $link_atts['role']          = 'button';
    $link_atts['aria-expanded'] = 'false';
    $link_atts['aria-haspopup'] = 'true';
  }
  if ('off-canvas' === $style) {
    $link_atts['class']        .= ' off-canvas-toggle';
    $link_atts['role']          = 'button';
    $link_atts['data-open']     = '#' . $popup;
    $link_atts['data-class']    = 'off-canvas-cart';
    $link_atts['data-pos']      = 'right';
    $link_atts['aria-haspopup'] = 'dialog';
    $link_atts['aria-controls'] = $popup;
  }

  ob_start();
  silver_dropdown_classes();
  $dropdown_classes = ob_get_clean();

  ob_start();
?>
  <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <ul class="nav ux-quick-cart-nav nav-<?php echo esc_attr($align); ?>">
      <li class="cart-item has-icon<?php echo 'dropdown' === $style ? ' has-dropdown' : ''; ?>">
        <a <?php echo silver_html_atts($link_atts); ?>>
          <?php if ('yes' === $atts['show_title'] || 'yes' === $atts['show_total']) : ?>
            <span class="ux-quick-cart-title">
              <?php if ('yes' === $atts['show_title']) esc_html_e('Cart', 'woocommerce'); ?>
              <?php if ('yes' === $atts['show_title'] && 'yes' === $atts['show_total']) echo '/'; ?>
              <?php if ('yes' === $atts['show_total']) : ?>
                <span class="ux-quick-cart-total"><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
              <?php endif; ?>
            </span>
          <?php endif; ?>
          <span class="ux-quick-cart-icon">
            <?php echo get_silver_icon($icon); // phpcs:ignore WordPress.Security.EscapeOutput ?>
            <?php // Count badge: own element so the cart fragment does not depend on the icon. ?>
            <span class="ux-quick-cart-count" data-icon-label="<?php echo esc_attr($count); ?>"></span>
          </span>
        </a>

        <?php if ('dropdown' === $style) : ?>
          <ul class="nav-dropdown <?php echo esc_attr($dropdown_classes); ?>">
            <li class="html widget_shopping_cart">
              <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
            </li>
          </ul>
        <?php endif; ?>
      </li>
    </ul>

    <?php if ('off-canvas' === $style) : ?>
      <div id="<?php echo esc_attr($popup); ?>" class="mfp-hide">
        <div class="cart-popup-inner inner-padding<?php echo get_theme_mod('header_cart_sticky_footer', 1) ? ' cart-popup-inner--sticky' : ''; ?>">
          <div class="cart-popup-title text-center">
            <span class="heading-font uppercase"><?php esc_html_e('Cart', 'woocommerce'); ?></span>
            <div class="is-divider"></div>
          </div>
          <div class="widget_shopping_cart">
            <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php
    // Icon size / color and responsive align.
    echo silver_responsive_style_tag($id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
      'align'      => array(
        'selector'       => '> .ux-quick-cart-nav',
        'only_overrides' => true,
        'default'        => 'left',
        'map'            => silver_responsive_map('nav_align'),
      ),
      'icon_size'  => array(
        'selector' => '.ux-quick-cart-icon > i',
        'property' => 'font-size',
        'unit'     => 'px',
      ),
      'icon_color' => array(
        'selector' => '.ux-quick-cart-icon > i',
        'property' => 'color',
      ),
    ));
    ?>
  </div>
<?php
  return ob_get_clean();
}
add_shortcode('ux_quick_cart', 'silver_quick_cart_shortcode');

/**
 * Cart fragments of the Quick Cart elements: count badge and total.
 * The mini cart itself (div.widget_shopping_cart_content) is refreshed by
 * WooCommerce.
 *
 * @param array $fragments
 * @return array
 */
function silver_quick_cart_fragments($fragments)
{
  if (! function_exists('WC') || ! WC()->cart) return $fragments;

  $count = WC()->cart->get_cart_contents_count();
  $fragments['.ux-quick-cart .ux-quick-cart-count'] = '<span class="ux-quick-cart-count" data-icon-label="' . esc_attr($count) . '"></span>';
  $fragments['.ux-quick-cart .ux-quick-cart-total'] = '<span class="ux-quick-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';

  return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'silver_quick_cart_fragments');
