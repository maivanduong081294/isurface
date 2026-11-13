<?php

// [testimonial]
function silver_testimonial($atts, $content = null) {
  global $silver_opt;
  $sliderrandomid = rand();
  extract(ux_builder_shortcode_atts(array(
	'_id' => '',
    'name' => '',
    'class' => '',
    'visibility' => '',
    'company' => '',
    'stars' => '5',
    'font_size' => '',
    'text_align' => '',
    'image'  => '',
    'avatar' => 'image', // image / initials
    'initials' => '',
    'initials_bg' => '',
    'initials_color' => '',
    'image_width' => '80',
    'pos' => 'left',
    'link' => '',
  ), $atts));
  ob_start();

  // Layout: stars, text, then a footer with the avatar next to name / company.
  // ("pos" of the old layout is no longer used.)
  $classes = array('testimonial-box');
  $classes_img = array('icon-box-img','testimonial-image','circle');

  if ( $class ) $classes[] = $class;
  if ( $visibility ) $classes[] = $visibility;
  if ( in_array( $text_align, array( 'left', 'center', 'right' ), true ) ) $classes[] = 'text-' . $text_align;
  if($font_size) $classes[] = 'is-'.$font_size;
  if($image_width) $image_width = 'width: '.intval($image_width).'px';

	$star_width = array(
		'1' => 25,
		'2' => 35,
		'3' => 55,
		'4' => 75,
		'5' => 100,
	);

	$star_row = '';
	if ( isset( $star_width[ $stars ] ) ) {
		$star_row = sprintf('<div class="star-rating" role="img" aria-label="%1$s"><span style="width:%2$d%%"><strong class="rating"></strong></span></div>',
			/* translators: %d is the star rating from 1-5. */
			esc_attr( sprintf( __( 'Rated %d out of 5', 'silver' ), $stars ) ),
			$star_width[ $stars ],
		);
	}

  $classes = implode(" ", $classes);
  $classes_img = implode(" ", $classes_img);

  // Responsive image width (larger screens).
  if ( silver_responsive_has_overrides( (array) $atts, 'image_width' ) ) {
    if ( ! $_id ) $_id = 'testimonial-' . wp_rand();
    echo silver_responsive_style_tag( $_id, (array) $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
      'image_width' => array( 'selector' => '.icon-box-img', 'property' => 'width', 'unit' => 'px', 'only_overrides' => true, 'default' => '80' ),
    ) );
  }
  ?>
  <?php
  // Short name avatar: the "Short name" option, else the initials of the name.
  $short_name = '';
  if ( 'initials' === $avatar ) {
    $short_name = trim( wp_strip_all_tags( $initials ) );
    if ( '' === $short_name ) $short_name = silver_testimonial_initials( wp_strip_all_tags( $name ) );
  }
  ?>
  <div<?php if ( $_id ) : ?> id="<?php echo esc_attr( $_id ); ?>"<?php endif; ?> class="<?php echo esc_attr( $classes ); ?>">
    <div class="testimonial-body">
      <?php if ( $stars > 0 ) echo $star_row; // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <div class="testimonial-text test_text first-reset last-reset">
        <?php echo do_shortcode( $content ); ?>
      </div>
    </div>

    <?php if ( '' !== $short_name || ( 'initials' !== $avatar && $image ) || $name || $company ) : ?>
    <div class="testimonial-footer">
      <?php if ( '' !== $short_name ) : ?>
        <div class="<?php echo esc_attr( $classes_img ); ?> testimonial-avatar-initials" style="<?php if ( $image_width ) echo esc_attr( $image_width ); ?>">
          <span class="testimonial-initials" aria-hidden="true"<?php
            $initials_style = array_filter( array(
              $initials_bg ? 'background-color:' . $initials_bg : '',
              $initials_color ? 'color:' . $initials_color : '',
            ) );
            if ( $initials_style ) echo ' style="' . esc_attr( implode( ';', $initials_style ) ) . '"';
          ?>><?php echo esc_html( $short_name ); ?></span>
        </div>
      <?php elseif ( 'initials' !== $avatar && $image ) : ?>
        <div class="<?php echo esc_attr( $classes_img ); ?>" style="<?php if ( $image_width ) echo esc_attr( $image_width ); ?>">
          <?php echo silver_get_image( $image, 'thumbnail', $name ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
      <?php endif; ?>

      <?php if ( $name || $company ) : ?>
        <div class="testimonial-meta">
          <?php if ( $name ) : ?><strong class="testimonial-name test_name"><?php echo wp_kses_post( $name ); ?></strong><?php endif; ?>
          <?php if ( $company ) : ?><span class="testimonial-company test_company"><?php echo wp_kses_post( $company ); ?></span><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}

add_shortcode("testimonial", "silver_testimonial");

/**
 * Initials of a name: first letter of the first and of the last word
 * ("Minh Anh" → "MA", "Nguyễn Văn An" → "NA", "Lan" → "L").
 *
 * @param string $name
 * @return string
 */
function silver_testimonial_initials( $name ) {
  $words = preg_split( '/\s+/u', trim( (string) $name ), -1, PREG_SPLIT_NO_EMPTY );
  if ( ! $words ) return '';

  $first = mb_substr( $words[0], 0, 1 );
  $last  = count( $words ) > 1 ? mb_substr( end( $words ), 0, 1 ) : '';

  return mb_strtoupper( $first . $last );
}

