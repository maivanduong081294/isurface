<?php
/**
 * Absolute footer.
 *
 * @package          Silver\Templates
 * @silver-version 3.19.9
 */

$align = 'small-text-center';
if ( get_theme_mod( 'footer_bottom_align', '' ) == 'center' ) {
  $align = 'text-center';
}

ob_start();
do_action( 'silver_absolute_footer_secondary' );
$silver_absolute_footer_secondary = trim( ob_get_clean() );
$silver_footer_right_text = trim( get_theme_mod( 'footer_right_text', '' ) );

?>

<div class="absolute-footer <?php echo get_theme_mod( 'footer_bottom_text', 'dark' ); ?> medium-text-center <?php echo $align;?>">
  <div class="container clearfix">

    <?php if ( $silver_footer_right_text || $silver_absolute_footer_secondary ) : ?>
      <div class="footer-secondary pull-right">
        <?php if ( $silver_footer_right_text ) : ?>
          <div class="footer-text inline-block small-block">
            <?php echo do_shortcode($silver_footer_right_text); ?>
          </div>
        <?php endif; ?>
        <?php echo $silver_absolute_footer_secondary; ?>
      </div>
    <?php endif; ?>

    <div class="footer-primary pull-left">
      <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <?php
        wp_nav_menu( array(
          'theme_location' => 'footer',
          'menu_class' => 'links footer-nav uppercase',
          'depth' => 1,
          'fallback_cb' => false,
        ) );
        ?>
      <?php endif; ?>
      <div class="copyright-footer">
        <?php echo do_shortcode( get_theme_mod( 'footer_left_text', 'Copyright [ux_current_year] &copy; <strong>Silver Theme</strong>' ) ); ?>
      </div>
      <?php do_action( 'silver_absolute_footer_primary' ); ?>
    </div>
  </div>
</div>
