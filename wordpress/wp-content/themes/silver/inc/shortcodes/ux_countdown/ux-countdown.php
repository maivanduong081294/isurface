<?php

// Register scripts
function silver_countdown_shortcode_scripts() {
    wp_register_style( 'silver-countdown-style', get_template_directory_uri() . '/inc/shortcodes/ux_countdown/ux-countdown.css', 'silver-style');
    wp_register_script( 'silver-countdown-script', get_template_directory_uri() . '/inc/shortcodes/ux_countdown/countdown-script-min.js', 'silver-countdown-script');
    wp_register_script( 'silver-countdown-theme-js', get_template_directory_uri() . '/inc/shortcodes/ux_countdown/ux-countdown.js', 'silver-js', '3.2.6', true);
}
add_action( 'wp_enqueue_scripts', 'silver_countdown_shortcode_scripts' );

// Register Shortcode
function ux_countdown_shortcode( $atts ){
    $atts = ux_builder_shortcode_atts( array(
      '_id' => 'timer-'.rand(),
      'before' => '',
      'after' => '',
      'year' => '2021',
      'month' => '12',
      'day' => '31',
      'color' => 'dark',
      'bg_color' => '',
      'bg_color__md' => '',
      'bg_color__sm' => '',
      'time' => '18:00',
      'style' => 'clock',
      'size' => '300',
      'size__md' => '',
      'size__sm' => '',
      't_hour' => 'hour',
      't_min' => 'min',
      't_day' => 'day',
      't_week' => 'week',
      't_sec' => 'sec',
      //
      't_plural' => 's',
      't_hour_p' => '',
      't_min_p' => '',
      't_day_p' => '',
      't_week_p' => '',
      't_sec_p' => '',

    ), $atts );

    extract( $atts );

    wp_enqueue_style('silver-countdown-style');
    wp_enqueue_script('silver-countdown-script');
    wp_enqueue_script('silver-countdown-theme-js');

		if ( ! empty( $before ) ) $before = wp_kses_post( $before );
		if ( ! empty( $after ) ) $after = wp_kses_post( $after );

    $date = $year.'/'.$month.'/'.$day;

    // Fix Time
    if($time == '24:00') $time = '23:59:59';

    if($time) $date = $date.' '.$time;

    $args = array(
      'size' => array(
        'selector' => '',
        'unit' => '%',
        'property' => 'font-size',
      ),
      'bg_color' => array(
        'selector' => 'span',
        'property' => 'background-color',
      ),
    );
    // data-text-hour-p="'.$t_hour_p.'" data-text-day-p="'.$t_day_p.'" data-text-week-p="'.$t_week_p.'" data-text-min-p="'.$t_min_p.'" data-text-sec-p="'.$t_sec_p.'"
    // Texts
    $translations = 'data-text-plural="'.esc_attr($t_plural).'" data-text-hour="'.esc_attr($t_hour).'" data-text-day="'.esc_attr($t_day).'" data-text-week="'.esc_attr($t_week).'" data-text-min="'.esc_attr($t_min).'" data-text-sec="'.esc_attr($t_sec).'"';

    // Add plurals
    if(isset($t_hour_p) || isset($t_day_p)) {
      $translations = $translations.' '.'data-text-hour-p="'.esc_attr($t_hour_p).'" data-text-day-p="'.esc_attr($t_day_p).'" data-text-week-p="'.esc_attr($t_week_p).'" data-text-min-p="'.esc_attr($t_min_p).'" data-text-sec-p="'.esc_attr($t_sec_p).'"';
    }

    if($style == 'clock'){
      // Responsive color (larger screens); a custom Bg Color keeps priority.
      $has_bg_color     = (bool) silver_responsive_values( $atts, 'bg_color' );
      $responsive_style = silver_responsive_style_tag( $_id, $atts, array(
        'color' => array(
          'selector'       => 'span',
          'only_overrides' => true,
          'default'        => 'dark',
          'map'            => array(
            'dark'  => 'color: #fff' . ( $has_bg_color ? '' : '; background-color: rgba(0,0,0,.85)' ),
            'light' => 'color: #000' . ( $has_bg_color ? '' : '; background-color: rgba(255,255,255,.85)' ),
          ),
        ),
      ) );

      return $before.'<div id="'.esc_attr($_id).'" class="ux-timer '.esc_attr($color).'" '.$translations.' data-countdown="'.esc_attr($date).'"><span>&nbsp;<div class="loading-spin dark centered"></div><strong>&nbsp;</strong></span></div>'.ux_builder_element_style_tag($_id, $args, $atts).$responsive_style.$after;
    } else{
      return $before.'<span id="'.esc_attr($_id).'" class="ux-timer-text" '.$translations.' data-countdown="'.esc_attr($date).'"></span>'.ux_builder_element_style_tag($_id, $args, $atts).''.$after;
    }
}
add_shortcode('ux_countdown', 'ux_countdown_shortcode');
