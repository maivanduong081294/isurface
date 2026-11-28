<?php // [ux_video]

/**
 * CSS of the video "Height" option (silver_ratio_height_css()): --sv-video-h,
 * the aspect-ratio of .video-fit (silver.css).
 *
 * @param string $value Option value.
 * @return string Declarations, '' when empty or invalid.
 */
function silver_video_height_css($value)
{
  return silver_ratio_height_css($value, '--sv-video-h');
}

function silver_video($atts) {

    extract( ux_builder_shortcode_atts( array(
        '_id' => '',
        'class' => '',
        'visibility' => '',
        'url' => 'https://www.youtube.com/watch?v=AoPiLg8DZ3A',
        'height' => '56.25%',
        'depth' => '',
        'depth_hover' => ''
    ), $atts ) );


    $classes = array('video','video-fit','mb');
    if ( $class ) $classes[] = $class;
    if ( $visibility ) $classes[] = $visibility;

    $video = apply_filters('the_content', esc_url($url));

    if($depth) $classes[] = 'box-shadow-'.$depth;
    if($depth_hover) $classes[] = 'box-shadow-'.$depth_hover.'-hover';

    $height_css = silver_video_height_css( $height );

    // Responsive height (larger screens).
    $responsive_style = '';
    if ( silver_responsive_has_overrides( (array) $atts, 'height' ) ) {
      if ( ! $_id ) $_id = 'video-' . wp_rand();
      $responsive_style = silver_responsive_style_tag( $_id, (array) $atts, array(
        'height' => array( 'selector' => '', 'callback' => 'silver_video_height_css', 'only_overrides' => true, 'default' => '56.25%' ),
      ) );
    }

    return '<div'.( $_id ? ' id="'.esc_attr( $_id ).'"' : '' ).' class="'.esc_attr(implode(' ', $classes)).'"'.( $height_css ? ' style="'.esc_attr( $height_css ).'"' : '' ).'>'.$video.$responsive_style.'</div>';
}
add_shortcode("ux_video", "silver_video");
