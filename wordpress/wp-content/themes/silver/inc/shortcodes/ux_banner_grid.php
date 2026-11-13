<?php
// [ux_banner_grid]
function silver_banner_grid($atts, $content = null) {
    extract( ux_builder_shortcode_atts( array(
    '_id' => 'banner-grid-'.rand(),
    'class' => '',
    'visibility' => '',
    'width' => '',
    'height' => '',
    'height__lg' => '600px',
    'height__sm' => '',
    'height__md' => '',
    'spacing' => 'small',
    'depth' => '',
    'depth_hover' => '',
    // Depricated
    'padding' => '',
    'grid' => '',
    ), $atts ) );

    $classes = array('row','row-grid');

    if ( $class ) $classes[] = $class;
    if ( $visibility ) $classes[] = $visibility;
	if ( silver_is_multi_gallery( 'banner-grid' ) ) $classes[] = 'lightbox-multi-gallery';

    // Fix old
    if($padding == '0px'){
      $spacing = 'collapse';
    }
    if($padding == '15px'){
      $spacing = 'small';
    }

    if($spacing !== 'normal') $classes[] = 'row-'.$spacing;
    if($depth) $classes[] = 'row-box-shadow-'.$depth;
    if($depth_hover) $classes[] = 'row-box-shadow-'.$depth_hover.'-hover';
    if($width == 'full-width') $classes[] = 'row-full-width';

    // Run masonry script
    wp_enqueue_script( 'silver-masonry-js');
    ob_start();
  ?>
  <div class="banner-grid-wrapper">
  <div id="<?php echo esc_attr( $_id ); ?>" class="banner-grid <?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-packery-options="">
          <?php if(has_shortcode( $content, 'col_grid' ) || has_shortcode( $content, 'col' )) { ?>
            <?php echo do_shortcode( $content ) ?>
          <?php } else {

              // Fix old content
              $pattern = get_shortcode_regex();
              $columns = 0;
              $current_grid = 0;
              $grid = silver_get_grid($grid);
              $grid_total = count($grid);
              silver_get_grid_height(array('sm' => $height, 'md' => $height__md, 'ml' => $height__ml, 'lg' => $height__lg), $_id);

              if (preg_match_all( '/'. $pattern .'/s', $content, $matches )
                  && array_key_exists( 2, $matches )
                  && in_array( 'ux_banner', $matches[2] ) ){
                foreach ($matches[0] as $shortcode) {
                    if($grid_total > $current_grid) $current_grid++;
                    $current = $current_grid-1;
                    echo do_shortcode('[col_grid span__lg="'.$grid[$current]['span'].'" span__md="'.$grid[$current]['md'].'" height="'.$grid[$current]['height'].'"]'.$shortcode.'[/col_grid]');
                }
              }
            }
          ?>
  </div>
  <?php silver_get_grid_height(array('sm' => $height, 'md' => $height__md, 'ml' => $height__ml, 'lg' => $height__lg), $_id); ?>
  <?php
  // Responsive width (larger screens).
  echo silver_responsive_style_tag( $_id, (array) $atts, array(
    'width' => array(
      'selector'       => '',
      'only_overrides' => true,
      'default'        => 'container',
      'map'            => array(
        'container'  => 'max-width: ' . silver_responsive_container_width( in_array( $spacing, array( 'collapse', 'small', 'large' ), true ) ? $spacing : '' ),
        'full-width' => 'max-width: 100%',
      ),
    ),
  ) );
  ?>
  </div>
  <?php
  // Get banner grid styles
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}
add_shortcode('ux_banner_grid', 'silver_banner_grid');


// [col_grid]
function ux_grid_col($atts, $content = null) {
  extract( ux_builder_shortcode_atts( array(
    'span' => '12',
    'span__md' => '',
    'span__sm' => '',
    'animate' => '',
    'height' => '',
    'class' => '',
    'visibility' => '',
    'depth' => '',
    'depth_hover' => '',
    ), $atts ) );

  $classes[] = 'col grid-col';
  $classes_inner[] = 'col-inner';

  if($span__md) $classes[] = 'medium-'.$span__md;
  if($span__ml) $classes[] = 'large-'.$span__ml;
  if($span__lg) $classes[] = 'xlarge-'.$span__lg;
  if($span) $classes[] = 'small-'.$span;

  $classes[] = $height ? 'grid-col-'.$height : 'grid-col-1';

  // Add Animation Class
  if($animate) { $animate = 'data-animate="' . esc_attr( $animate ) . '"'; }

  if ( $class ) $classes[] = $class;
  if ( $visibility ) $classes[] = $visibility;

  // Add Depth Class
  if($depth) $classes_inner[] = 'box-shadow-'.$depth;
  if($depth_hover) $classes_inner[] = 'box-shadow-'.$depth_hover.'-hover';

  $classes =  implode(" ", $classes);
  $classes_inner =  implode(" ", $classes_inner);

  $column = '<div class="' . esc_attr( $classes ) . '" ' . $animate . '><div class="' . esc_attr( $classes_inner ) . '">' . $content . '</div></div>';

  return do_shortcode( $column );
}
add_shortcode('col_grid', 'ux_grid_col');
