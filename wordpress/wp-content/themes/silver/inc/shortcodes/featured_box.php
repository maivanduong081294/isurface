<?php
// [featured_box]
function featured_box( $atts, $content = null ) {
	extract( ux_builder_shortcode_atts( array(
		'_id'        => '',
		'title'       => '',
		'title_small' => '',
		'font_size'   => '',
		'class'				=> '',
		'visibility'	=> '',
		'icon_type'   => 'image',
		'img'         => '',
		'icon'        => '',
		'inline_svg'  => 'true',
		'img_width'   => '60',
		'pos'         => 'top',
		'link'        => '',
		'target'      => '_self',
		'rel'         => '',
		'tooltip'     => '',
		'margin'      => '',
		'icon_border' => '',
		'icon_color'  => '',
		), $atts )
	);

	if($visibility == 'hidden') return;

	$classes     = array( 'featured-box' );
	$classes_img = array( 'icon-box-img' );
	
	if( $class ) $classes[] = $class;
	if( $visibility ) $classes[] = $visibility;

	$classes[] = 'icon-box-' . $pos;
	if ( $pos == 'left-center' ) $classes[] = 'icon-box-left';
	if ( $pos == 'right-center' ) $classes[] = 'icon-box-right';

	if ( $tooltip ) $classes[] = 'tooltip';
	if ( $pos == 'center' ) $classes[] = 'text-center';
	if ( $pos == 'left' || $pos == 'left-center' || $pos == 'top' ) $classes[] = 'text-left';
	if ( $pos == 'right' || $pos == 'right-center' ) $classes[] = 'text-right';
	if ( $font_size ) $classes[] = 'is-' . $font_size;
	$img_width_value = intval( $img_width );
	if ( $img_width ) $img_width = 'width: ' . intval( $img_width ) . 'px';
	if ( $icon_border ) $classes_img[] = 'has-icon-bg';

	// Font icon (Font Awesome) instead of an image.
	$use_icon  = ( 'icon' === $icon_type && $icon );
	$icon_size = null;
	if ( $use_icon ) {
		$classes_img[] = 'has-font-icon';
		$icon_size     = $img_width_value ? round( $img_width_value * ( $icon_border ? 0.5 : 1 ) ) . 'px' : null;
	}

	$css_args_out = array(
		'margin' => array(
			'attribute' => 'margin',
			'value'     => $margin,
		),
	);

	$css_args = array(
		'icon_border' => array(
			'attribute' => 'border-width',
			'unit'      => 'px',
			'value'     => $icon_border,
		),
		'icon_color'  => array(
			'attribute' => 'color',
			'value'     => $icon_color,
		),
	);

	$classes     = implode( ' ', $classes );
	$classes_img = implode( ' ', $classes_img );
	$link_atts   = array(
		'target' => $target,
		'rel'    => array( $rel ),
	);

	ob_start();
	?>

	<?php if ( $link ) echo '<a class="plain" href="' . esc_url( $link ) . '"' . flatsome_parse_target_rel( $link_atts ) . '>'; ?>
	<div<?php if ( $_id ) : ?> id="<?php echo esc_attr( $_id ); ?>"<?php endif; ?> class="icon-box <?php echo esc_attr( $classes ); ?>" <?php if ( $tooltip )
		echo 'title="' . esc_attr( $tooltip ) . '"' ?> <?php echo get_shortcode_inline_css( $css_args_out ); ?>>
		<?php if ( $img || $use_icon ) { ?>
			<div class="<?php echo esc_attr( $classes_img ); ?>" style="<?php if ( $img_width ) {
				echo $img_width;
			} ?>">
				<div class="icon">
					<div class="icon-inner" <?php echo get_shortcode_inline_css( $css_args ); ?>>
						<?php if ( $use_icon ) : ?>
							<?php echo get_flatsome_icon( $icon, $icon_size ); ?>
						<?php else : ?>
							<?php echo flatsome_get_image( $img, $size = 'medium', $alt = $title, $inline_svg ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="icon-box-text last-reset">
			<?php if ( $title ) { ?><h5 class="uppercase"><?php echo wp_kses_post( $title ); ?></h5><?php } ?>
			<?php if ( $title_small ) { ?><h6><?php echo wp_kses_post( $title_small ); ?></h6><?php } ?>
			<?php echo do_shortcode( $content ); ?>
		</div>
	</div>
	<?php if ( $link ) echo '</a>'; ?>

	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'featured_box', 'featured_box' );
