<?php

/**
 * Generate position classes for banner content.
 *
 * @param  string $axis
 * @param  string $default
 * @param  string $sm
 * @param  string $md
 * @return string
 */
function silver_position_classes ( $axis, $default, $md = '', $ml = '', $lg = '' ) {
  $classes = array();

  // Mobile-first: the unsuffixed value is the base, larger tiers override.
  if ( strlen( (string) $default ) ) $classes[] = $axis . $default;
  if ( strlen( (string) $md ) ) $classes[] = 'md-' . $axis . $md;
  if ( strlen( (string) $ml ) ) $classes[] = 'lg-' . $axis . $ml;
  if ( strlen( (string) $lg ) ) $classes[] = 'xl-' . $axis . $lg;

  return implode( ' ', $classes );
}

function get_silver_repeater_start( $atts ) {
    $atts = wp_parse_args( $atts, array(
      'class' => '',
      'visibility' => '',
      'title' => '',
      'style' => '',
      'columns' => '',
      'columns__sm' => '',
      'columns__md' => '',
      'columns__ml' => '',
      'columns__lg' => '',
      'id' => '',
      'slider_nav_position' => '',
      'slider_bullets' => 'false',
      'slider_nav_color' => '',
      'auto_slide' => 'false',
	  'infinitive' => 'true',
      'thumb_role' => '',
      'thumb_key' => '',
      'format' => '',
	  'attrs' => '',
    ) );

	$row_classes      = array();
	$row_classes_full = array();

	if ( $atts['class'] ) {
		$row_classes[]      = $atts['class'];
		$row_classes_full[] = $atts['class'];
	}

	if ( $atts['visibility'] ) {
		$row_classes[]      = $atts['visibility'];
		$row_classes_full[] = $atts['visibility'];
	}

    if($atts['type'] == 'slider-full'){
      $atts['columns'] = false;
      $atts['columns__sm'] = false;
      $atts['columns__md'] = false;
      $atts['columns__ml'] = false;
      $atts['columns__lg'] = false;
    }

    if(empty($atts)) return;

    if(!empty($atts['filter'])){
      $row_classes[] = 'row-isotope';
    }

    // Slider options shared by the "slider" and "slider-full" types.
    $slider_options = array(
      'cellAlign'     => 'left',
      'wrapAround'    => 'false' !== $atts['infinitive'],
      'pageDots'      => 'true' === $atts['slider_bullets'],
      'autoPlay'      => $atts['auto_slide'],
      'dragThreshold' => 5,
    );

    // Add column classes
    if(!empty($atts['columns']) && $atts['type'] !== 'grid'){
      if($atts['columns'])  $row_classes[] = 'small-columns-'.$atts['columns'];

      if ( ! empty( $atts['columns__md'] ) ) $row_classes[] = 'medium-columns-' . $atts['columns__md'];

      if ( ! empty( $atts['columns__ml'] ) ) $row_classes[] = 'large-columns-' . $atts['columns__ml'];

      if ( ! empty( $atts['columns__lg'] ) ) $row_classes[] = 'xlarge-columns-' . $atts['columns__lg'];
    }

    // Add Row spacing
    if(!empty($atts['row_spacing'])){
      $row_classes[] = 'row-'.$atts['row_spacing'];
    }

    // Add row width
    if(!empty($atts['row_width'])){
      if($atts['row_width'] == 'full-width') $row_classes[] = 'row-full-width';
    }

    // Add Shadows
    if(!empty($atts['depth'])){
       $row_classes[] = 'has-shadow';
          $row_classes_full[] = 'box-shadow-'.$atts['depth'];
          $row_classes[] = 'row-box-shadow-'.$atts['depth'];
    }
    if(!empty($atts['depth_hover'])){
       $row_classes[] = 'has-shadow';
          $row_classes_full[] = 'box-shadow-'.$atts['depth_hover'].'-hover';
          $row_classes[] = 'row-box-shadow-'.$atts['depth_hover'].'-hover';
    }

    if($atts['type'] == 'masonry'){
      wp_enqueue_script('silver-masonry-js');
      $row_classes[] = 'row-masonry';
    }

    if($atts['type'] == 'grid'){
      wp_enqueue_script('silver-masonry-js');
      $row_classes[] = 'row-grid';
    }

    // Main slider + thumbnail slider pair ("Thumbnail Slider" options).
    $thumbnail_class = silver_slider_thumbnail_class( $atts['thumb_role'], $atts['thumb_key'] );

    if($atts['type'] == 'slider'){
      $row_classes[] = 'slider row-slider';

      if($thumbnail_class) $row_classes[] = $thumbnail_class;

      if($atts['slider_style']) $row_classes[] = 'slider-nav-'.$atts['slider_style'];

      if($atts['slider_nav_position']) $row_classes[] = 'slider-nav-'.$atts['slider_nav_position'];

      if($atts['slider_nav_color']) $row_classes[] = 'slider-nav-'.$atts['slider_nav_color'];

      // Add slider push class to normal text boxes
      if(!$atts['style'] || $atts['style'] == 'default' || $atts['style'] == 'normal' || $atts['style'] == 'bounce') $row_classes[] = 'slider-nav-push';

      // Move a full view of columns at a time.
      $slider_options['groupCells'] = '100%';

    } else if($atts['type'] == 'slider-full'){
      $row_classes_full[] = 'slider slider-auto-height row-collapse';

      if($thumbnail_class) $row_classes_full[] = $thumbnail_class;

      if($atts['slider_nav_position']) $row_classes_full[] = 'slider-nav-'.$atts['slider_nav_position'];

      if($atts['slider_style']) $row_classes_full[] = 'slider-nav-'.$atts['slider_style'];
    }

	$row_classes_full = array_unique( $row_classes_full );
	$row_classes      = array_unique( $row_classes );

	$row_classes_full = implode( ' ', $row_classes_full );
	$row_classes      = implode( ' ', $row_classes );
	$id_attr          = $atts['id'] ? ' id="' . esc_attr( $atts['id'] ) . '"' : '';

	// Responsive Layout width / alignment / Box styles of the shortcode
	// (values for larger screens), scoped to the row ID. Printed before the
	// row so sliders never turn the <style> tag into a slide.
	if ( ! empty( $atts['responsive_atts'] ) && $atts['id'] ) {
		echo silver_responsive_repeater_style( $atts['id'], $atts['responsive_atts'] ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
  ?>

  <?php if($atts['title']){?>
  <div class="row">
    <div class="xlarge-12 col">
      <h3 class="section-title"><span><?php echo wp_kses_post( $atts['title'] ); ?></span></h3>
    </div>
  </div>
  <?php } ?>

  <?php if($atts['type'] == 'slider') { // Slider grid ?>
  <div<?php echo $id_attr; ?> class="row <?php echo esc_attr( $row_classes ); ?>"<?php echo silver_slider_options_attr( $slider_options ); ?> <?php echo $atts['attrs'] ?>>

  <?php } else if($atts['type'] == 'slider-full') { // Full slider ?>
  <div<?php echo $id_attr; ?> class="<?php echo esc_attr( $row_classes_full ); ?>"<?php echo silver_slider_options_attr( $slider_options ); ?>>

  <?php } else if($atts['type'] == 'masonry') { // Masonry grid ?>
  <div<?php echo $id_attr; ?> class="row <?php echo esc_attr( $row_classes ); ?>" data-packery-options='{"itemSelector": ".col", "gutter": 0, "presentageWidth" : true}'>

  <?php } else if($atts['type'] == 'grid') { ?>
  <div<?php echo $id_attr; ?> class="row <?php echo esc_attr( $row_classes ); ?>" data-packery-options='{"itemSelector": ".col", "gutter": 0, "presentageWidth" : true}'>

  <?php } else if($atts['type'] == 'blank') { //Blank type ?>
  <div<?php echo $id_attr; ?> class="container">

  <?php } else { // Normal Rows ?>
  <div<?php echo $id_attr; ?> class="row <?php echo esc_attr( $row_classes ); ?>" <?php echo $atts['attrs'] ?>>
  <?php }
}

function get_silver_repeater_end($type){
  echo '</div>';
}

/**
 * Builds the options of a slider. Every slider in the theme declares its
 * options through this function, so the defaults live in one place.
 *
 * The options are read by assets/js/src/silver-swiper.js, which maps them to
 * Swiper parameters (wrapAround -> loop, pageDots -> pagination, ...).
 *
 * Shortcode attributes can be passed as they are: 'true', 'false' and numeric
 * strings are converted, so the result is always valid JSON.
 *
 * @param array $options Options that differ from the defaults:
 *                       cellAlign (center|left|right), wrapAround, autoPlay
 *                       (false or delay in ms), pauseAutoPlayOnHover,
 *                       prevNextButtons, pageDots, adaptiveHeight, contain,
 *                       groupCells, freeScroll, draggable, dragThreshold,
 *                       asNavFor (selector), parallax, rightToLeft.
 *
 * @return array
 */
function silver_slider_options( $options = array() ) {
	$options = array_merge(
		array(
			'cellAlign'       => 'center',
			'wrapAround'      => false,
			'autoPlay'        => false,
			'prevNextButtons' => true,
			'pageDots'        => true,
			'rightToLeft'     => is_rtl(),
		),
		(array) $options
	);

	foreach ( $options as $key => $value ) {
		if ( 'true' === $value ) {
			$options[ $key ] = true;
		} elseif ( 'false' === $value || '' === $value || null === $value ) {
			$options[ $key ] = false;
		} elseif ( is_string( $value ) && is_numeric( $value ) ) {
			$options[ $key ] = $value + 0;
		}
	}

	// The UX Builder preview sizes its slides before the images are loaded.
	if ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() ) {
		$options['imagesLoaded'] = true;
	}

	return apply_filters( 'silver_slider_options', $options );
}

/**
 * Returns the class that pairs a main slider with its thumbnail slider
 * ("Thumbnail Slider" options). Two sliders with the same pair name are linked
 * by assets/js/src/silver-swiper.js.
 *
 * @param string $role 'main', 'thumbnail' or empty for a normal slider.
 * @param string $key  Pair name. Empty pairs the unnamed sliders of the page.
 *
 * @return string Empty when the slider is not part of a pair.
 */
function silver_slider_thumbnail_class( $role, $key = '' ) {
	if ( 'main' !== $role && 'thumbnail' !== $role ) {
		return '';
	}

	$key = sanitize_html_class( sanitize_title( (string) $key ), 'default' );

	return ( 'main' === $role ? 'slider-bullet-' : 'slider-thumbnail-' ) . $key;
}

/**
 * Returns the slider options as an HTML attribute, with a leading space.
 *
 * @param array $options See silver_slider_options().
 *
 * @return string
 */
function silver_slider_options_attr( $options = array() ) {
	return " data-swiper-options='" . esc_attr( wp_json_encode( silver_slider_options( $options ) ) ) . "'";
}

/* Fix Normal Shortcodes */
function silver_contentfix($content){
    if ( ! is_string( $content ) ) {
      return $content;
    }

    $fix = array (
            '<p>_____</p>' => '<div class="is-divider large"></div>',
            '<p>____</p>' => '<div class="is-divider medium"></div>',
            '<p>___</p>' => '<div class="is-divider small"></div>',
            '</div></p>' => '</div>',
            '<p><div' => '<div',
            ']<br />' => ']',
            '<br />[' => '[',
            '<p>[' => '[',
            ']</p>' => ']',

            // For Gutenberg blocks that is encoded by UX Builder.
            '&lt;!&#8211;' => '<!--',
            '&#8211;&gt;' => '-->',
    );

    return strtr( $content, $fix );
}

add_filter( 'the_content', 'silver_contentfix' );
add_filter( 'widget_text', 'silver_contentfix' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'silver_contentfix' );
add_filter( 'the_excerpt', 'do_shortcode' );

/**
 * Remove whitespace characters \r\n\t\f\v from HTML between > and <
 * (Prevents wpautop from adding <p> or <br>)
 *
 * @param $html
 *
 * @return mixed
 */
function silver_sanitize_whitespace_chars( $html ) {
	$html = preg_replace( '/(?<=>)\s+(?=<)/', '', $html );
	return trim( $html );
}

// Get Shortcode Inline CSS
function get_shortcode_inline_css($args){
    $style = '';
      foreach ($args as $key => $value) {
        $unit = array_key_exists( 'unit', $value ) ? $value['unit'] : null;
        if($value['value']) $style .=  $value['attribute'].':'.$value['value'].$unit.';';
       }
    if($style) return 'style="'.esc_attr($style).'"';
}


// Get Parallax Options
function get_parallax_option($strength){
    return 'data-velocity="0.'.$strength.'"';
}

function silver_get_image_url($id, $size = 'large'){

    if(!$id) return get_template_directory_uri().'/assets/img/missing.jpg';

    if (!is_numeric($id)) {
        return $id;
    } else {
        $image = wp_get_attachment_image_src($id, $size);
        $image = $image ? $image[0] : '';
        return $image;
    }
}

function silver_get_image( $id, $size = 'large', $alt = 'bg_image', $inline = false, $image_title = false ) {

    if(!$id) return '<img src="'.get_template_directory_uri().'/assets/img/missing.jpg'.'" />';

	$attr       = array();
	$title_html = '';

	if ( $image_title ) {
		$the_title     = get_the_title( $id );
		$attr['title'] = $the_title;
		$title_html    = ' title="' . esc_attr( $the_title ) . '" ';
	}

    if (!is_numeric($id)) {
        return '<img src="' . esc_url( $id ) . '" alt="' . esc_attr( $alt ) . '"' . $title_html . '/>';
    } else {
        $meta = get_post_mime_type($id);

        if ( $meta == 'image/svg+xml' && $inline ){
          $file = get_attached_file( $id );
          if ( $file && file_exists( $file ) ) {
            return preg_replace(
              '#<script(.*?)>(.*?)</script>#is',
              '',
              file_get_contents( $file )
            );
          }
        }

        return wp_get_attachment_image( $id, $size, false, $attr );
    }
}

function silver_string_limit_words($string, $word_limit) {
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}

/**
 * Retrieves a custom trimmed excerpt from either the post excerpt or the post content.
 *
 * @param int $num_words Optional. Number of words to trim the excerpt to. Default 15.
 *
 * @return string The trimmed excerpt or password protection message.
 */
function silver_get_the_excerpt( $num_words = 15 ) {
	if ( has_excerpt() ) {
		global $post;

		if ( post_password_required( $post ) ) {
			return esc_html__( 'There is no excerpt because this is a protected post.', 'default' );
		}

		return wp_trim_words( $post->post_excerpt, $num_words, apply_filters( 'excerpt_more', ' [&hellip;]' ) );
	} else {
		$excerpt_length_callback = function () use ( $num_words ) {
			return $num_words;
		};
		add_filter( 'excerpt_length', $excerpt_length_callback, PHP_INT_MAX );
		$trimmed_excerpt = get_the_excerpt();
		remove_filter( 'excerpt_length', $excerpt_length_callback, PHP_INT_MAX );

		return $trimmed_excerpt;
	}
}

/* Create RGBA color of a #HEX color */
function silver_hex2rgba($color, $opacity = false) {
  $default = 'rgb(0,0,0)';
  //Return default if no color provided
  if(empty($color))
          return $default;

  //Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
          $color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
          if(abs($opacity) > 1)
            $opacity = 1.0;
          $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
          $output = 'rgb('.implode(",",$rgb).')';
        }
        //Return rgb(a) color string
        return $output;
}

function silver_fix_span($span){
  switch ($span) {
    case "1/1":
        $span = '12'; break;
    case "1/4":
        $span = '3'; break;
    case "2/4":
         $span ='6'; break;
    case "3/4":
        $span = '9'; break;
    case "1/3":
        $span = '4'; break;
    case "2/3":
         $span = '8'; break;
    case "1/2":
        $span = '6'; break;
    case "1/6":
        $span = '2'; break;
    case "2/6":
         $span = '4'; break;
    case "3/6":
        $span = '6'; break;
    case "4/6":
        $span = '8'; break;
    case "5/6":
        $span = '10'; break;
    case "1/12":
        $span = '1'; break;
    case "2/12":
        $span = '2'; break;
    case "3/12":
        $span = '3'; break;
    case "4/12":
        $span = '4'; break;
    case "5/12":
        $span = '5'; break;
    case "6/12":
        $span = '6'; break;
    case "7/12":
        $span = '7'; break;
    case "8/12":
        $span = '8'; break;
    case "9/12":
        $span = '9'; break;
    case "10/12":
        $span = '10'; break;
     case "11/12":
        $span = '11'; break;
  }
  return $span;
}


function silver_smart_links($link){
    if($link == 'shop' && is_woocommerce_activated()){
      $link = get_permalink( wc_get_page_id( 'shop' ) );
    }
    else if($link == 'cart' && is_woocommerce_activated()) {
      $link = wc_get_cart_url();
    }
    else if($link == 'checkout' && is_woocommerce_activated()) {
      $link = wc_get_checkout_url();
    }
    else if($link == 'account' && is_woocommerce_activated()){
      $link = get_permalink( get_option('woocommerce_myaccount_page_id') );
    }

    else if($link == 'home'){
      $link = get_home_url();
    }

    else if($link == 'blog'){
      $link = get_permalink( get_option( 'page_for_posts' ) );
    }

    else if($link == 'wishlist' && class_exists('YITH_WCWL')){
      $link =  YITH_WCWL()->get_wishlist_url();
    }
    // Get link by page title
    else if(strpos($link, '/') === false && !is_numeric($link)){
      $get_page = silver_get_page_by_title($link);
      if( $get_page ) $link = get_permalink($get_page->ID);
    }

	return esc_url( $link );
}

/**
 * Resolves the "Link" options (builder/shortcodes/commons/links.php) of a
 * shortcode into a URL and the object it points to.
 *
 * Called from ux_builder_shortcode_atts(), so every shortcode that declares a
 * `link` attribute receives the resolved URL without any extra code.
 *
 * @param array $atts Raw shortcode attributes.
 *
 * @return array {
 *     @type string $type        Link type: custom, post, term or archive.
 *     @type string $url         Resolved URL, empty when nothing is selected or the target is gone.
 *     @type string $object_type post_type, taxonomy, post_type_archive or empty string.
 *     @type int    $object_id   Post or term ID, 0 for custom links and archives.
 *     @type string $object_name Post type or taxonomy name.
 * }
 */
function silver_get_link_data( $atts ) {
	$atts = (array) $atts;
	$type = ! empty( $atts['link_type'] ) ? $atts['link_type'] : 'custom';
	$data = array(
		'type'        => $type,
		'url'         => '',
		'object_type' => '',
		'object_id'   => 0,
		'object_name' => '',
	);

	switch ( $type ) {
		case 'post':
			$post = ! empty( $atts['link_post'] ) ? get_post( absint( $atts['link_post'] ) ) : null;

			if ( $post && 'publish' === $post->post_status ) {
				$data['url']         = (string) get_permalink( $post );
				$data['object_type'] = 'post_type';
				$data['object_id']   = (int) $post->ID;
				$data['object_name'] = $post->post_type;
			}
			break;

		case 'term':
			// UX Builder does not save default values, so a missing taxonomy
			// means the default one of the `link_taxonomy` option.
			$taxonomy = ! empty( $atts['link_taxonomy'] ) ? $atts['link_taxonomy'] : 'category';
			$term_key = 'link_term_' . $taxonomy;
			$term     = ! empty( $atts[ $term_key ] ) ? get_term( absint( $atts[ $term_key ] ), $taxonomy ) : null;

			if ( $term && ! is_wp_error( $term ) ) {
				$url = get_term_link( $term );

				if ( ! is_wp_error( $url ) ) {
					$data['url']         = $url;
					$data['object_type'] = 'taxonomy';
					$data['object_id']   = (int) $term->term_id;
					$data['object_name'] = $term->taxonomy;
				}
			}
			break;

		case 'archive':
			$post_type = ! empty( $atts['link_archive'] ) ? $atts['link_archive'] : '';
			$url       = $post_type ? get_post_type_archive_link( $post_type ) : false;

			if ( $url ) {
				$data['url']         = $url;
				$data['object_type'] = 'post_type_archive';
				$data['object_name'] = $post_type;
			}
			break;

		default:
			$data['type'] = 'custom';
			$data['url']  = isset( $atts['link'] ) ? trim( (string) $atts['link'] ) : '';
	}

	return $data;
}

/**
 * Returns the name of the object the "Link" options point to: the post title,
 * the term name or the label of the post type archive.
 *
 * @param array $atts Raw shortcode attributes.
 *
 * @return string Empty for custom links, or when nothing is selected or the target is gone.
 */
function silver_get_link_title( $atts ) {
	$data = silver_get_link_data( $atts );

	switch ( $data['object_type'] ) {
		case 'post_type':
			return (string) get_the_title( $data['object_id'] );

		case 'taxonomy':
			$term = get_term( $data['object_id'], $data['object_name'] );

			return ( $term && ! is_wp_error( $term ) ) ? (string) $term->name : '';

		case 'post_type_archive':
			$post_type = get_post_type_object( $data['object_name'] );

			return $post_type ? esc_html( $post_type->labels->name ) : '';
	}

	return '';
}

function silver_to_dashed($className) {
   return strtolower(preg_replace('/([\S\s])\s/', '$1-', $className));
}

function silver_to_underscore( $className ) {
	return strtolower( preg_replace( '/([\S\s])\s/', '$1_', $className ) );
}

/*
function silver_get_gradient($primary){ ?>
  <style>
    .target{
      background: <?php echo $primary; ?>
      background: -moz-linear-gradient(<?php echo $direction; ?>, <?php echo $primary; ?> 0%, <?php echo $secondary; ?> 100%);
      background: -webkit-linear-gradient(<?php echo $direction; ?>, <?php echo $primary; ?> 0%, <?php echo $secondary; ?> 100%);
      background: linear-gradient(<?php echo $direction; ?>, <?php echo $primary; ?> 0%, <?php echo $secondary; ?> 100%);
    }
  </style>
  <?php
} */

/**
 * Parse rel attribute values based on target value.
 * Adds 'noopener' to rel when target is _blank.
 *
 * @deprecated 3.18 In favor of silver_html_atts()
 *
 * @param array $link_atts Link attributes 'target' and 'rel'.
 * @param bool  $trim      Trim start and end whitespaces?
 *
 * @return string Parsed target/rel string.
 */
function silver_parse_target_rel( array $link_atts, $trim = false ) {
	$attrs = array();

	if ( $link_atts['target'] == '_blank' ) {
		$attrs[]            = sprintf( 'target="%s"', esc_attr( $link_atts['target'] ) );
		$link_atts['rel'][] = 'noopener';
	}

	if ( isset( $link_atts['rel'] ) && is_array( $link_atts['rel'] ) && ! empty( array_filter( $link_atts['rel'] ) ) ) {
		$relations = array_unique( array_filter( $link_atts['rel'] ) );
		$rel       = implode( ' ', $relations );
		$attrs[]   = sprintf( 'rel="%s"', esc_attr( $rel ) );
	}

	$attrs = ! empty( $attrs ) ? ' ' . implode( ' ', $attrs ) . ' ' : ' ';

	return $trim ? trim( $attrs ) : $attrs;
}

/**
 * Returns the collection of ux_products shortcode box item hooks.
 *
 * @return array
 */
function silver_ux_product_box_items() {

	return array(
		'cat'              => array(
			'tag'      => 'woocommerce_shop_loop_item_title',
			'function' => 'silver_woocommerce_shop_loop_category',
		),
		'title'            => array(
			'tag'      => 'woocommerce_shop_loop_item_title',
			'function' => 'woocommerce_template_loop_product_title',
		),
		'rating'           => array(
			'tag'      => 'woocommerce_after_shop_loop_item_title',
			'function' => 'woocommerce_template_loop_rating',
		),
		'price'            => array(
			'tag'      => 'woocommerce_after_shop_loop_item_title',
			'function' => 'woocommerce_template_loop_price',
		),
		'add_to_cart'      => array(
			'tag'      => 'silver_product_box_after',
			'function' => 'silver_woocommerce_shop_loop_button',
		),
		'add_to_cart_icon' => array(
			'tag'      => 'silver_product_box_actions',
			'function' => 'silver_product_box_actions_add_to_cart',
		),
		'quick_view'       => array(
			'tag'      => 'silver_product_box_actions',
			'function' => 'silver_lightbox_button',
		),
	);
}

/**
 * Starts the toggle of box item hooks.
 *
 * @param array $items A collection of box items of a specific element.
 *
 * @return array $items Box items with additional data.
 */
function silver_box_item_toggle_start( $items ) {

	foreach ( $items as $key => $data ) {
		if ( isset( $data['show'] ) && ! $data['show'] ) {
			$priority = has_action( $data['tag'], $data['function'] );
			if ( $priority !== false ) {
				remove_action( $data['tag'], $data['function'], $priority );
				$items[ $key ]['priority'] = $priority;
				$items[ $key ]['re_hook']  = true;
			}
		}
	}

	return $items;
}

/**
 * Ends the toggle of box item hooks.
 *
 * @param array $items A collection of box items of a specific element.
 */
function silver_box_item_toggle_end( $items ) {
	foreach ( $items as $item ) {
		if ( isset( $item['re_hook'] ) && $item['re_hook'] == true ) {
			add_action( $item['tag'], $item['function'], $item['priority'] );
		}
	}
}

/**
 * Inserts items at offset in an associative array.
 *
 * @param array $array
 * @param array $values
 * @param int $offset
 * @return array
 */
function silver_array_insert( array $array, array $values, $offset ) {
  return array_slice( $array, 0, $offset, true ) + $values + array_slice( $array, $offset, null, true );
}

/**
 * Checks if a specific multi-gallery option is enabled.
 *
 * Determines whether a specific gallery type has been enabled for separate lightbox display.
 *
 * @since 3.20.0
 *
 * @param string $gallery_type The gallery type to check ('gallery' or 'banner-grid').
 * @return bool True if the specified gallery type is enabled, false otherwise
 */
function silver_is_multi_gallery( $gallery_type ) {
	return in_array( $gallery_type, get_theme_mod( 'flatsome_separate_lightbox_galleries', [] ), true );
}
