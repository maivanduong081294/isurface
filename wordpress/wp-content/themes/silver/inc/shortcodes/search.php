<?php

// [search]
function search_shortcode($atts) {
	$atts = ux_builder_shortcode_atts(array(
		'_id'          => '',
		'size'         => 'normal',
		'style'        => '',
		'class'        => '',
		'visibility'   => '',
		// Header layouts.
		'display'      => '', // '' (box) / icon-dropdown / icon-lightbox.
		'placeholder'  => '',
		'icon_pos'     => '', // '' (button right) / left.
		'width'        => '',
		'grow'         => '',
		'radius'       => '',
		'bg_color'     => '',
		'border_color' => '',
		'icon_size'    => '',
		'icon_color'   => '',
		'collapse'     => 'false', // Per screen (responsive): 'true' = icon + bar below, 'false' = box.
	), $atts);

	if ( 'hidden' === $atts['visibility'] ) return '';

	$display = in_array( $atts['display'], array( 'icon-dropdown', 'icon-lightbox' ), true ) ? $atts['display'] : '';

	if ( $display ) {
		return silver_search_icon_shortcode( $atts, $display );
	}

	// Screens (sm, md, ml, lg) where the box collapses to an icon.
	$collapse_screens = array();
	$screen_names     = array_keys( get_ux_builder_breakpoints() );
	foreach ( silver_responsive_filled( $atts, 'collapse', '' ) as $index => $value ) {
		if ( 'true' === $value && isset( $screen_names[ $index ] ) ) $collapse_screens[] = $screen_names[ $index ];
	}
	if ( $collapse_screens ) {
		return silver_search_collapse_shortcode( $atts, $collapse_screens );
	}

	$id = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : '';

	$classes = array( 'searchform-wrapper', 'ux-search-box', 'relative' );
	if ( $atts['class'] ) $classes[] = $atts['class'];
	if ( $atts['visibility'] ) $classes[] = $atts['visibility'];
	if ( $atts['style'] ) $classes[] = 'form-' . $atts['style'];
	if ( $atts['size'] ) $classes[] = 'is-' . $atts['size'];
	if ( 'left' === $atts['icon_pos'] ) $classes[] = 'ux-search-icon-left';
	if ( 'true' === $atts['grow'] ) $classes[] = 'ux-search-grow';

	$styles = array();
	if ( '' !== $atts['radius'] && is_numeric( $atts['radius'] ) ) $styles[] = '--ux-search-radius:' . floatval( $atts['radius'] ) . 'px';
	if ( $atts['bg_color'] ) $styles[] = '--ux-search-bg:' . $atts['bg_color'];
	if ( $atts['border_color'] ) $styles[] = '--ux-search-border:' . $atts['border_color'];

	// The width is responsive: it needs an id for the style tag.
	$has_width = '' !== $atts['width'] || false !== strpos( implode( '', array_keys( (array) $atts ) ), 'width__' );
	if ( ! $id && $has_width ) $id = 'ux-search-' . wp_rand();

	// The product search form reads Header > Search > Placeholder.
	$placeholder_filter = null;
	if ( '' !== trim( $atts['placeholder'] ) ) {
		$placeholder        = $atts['placeholder'];
		$placeholder_filter = function () use ( $placeholder ) {
			return $placeholder;
		};
		add_filter( 'theme_mod_search_placeholder', $placeholder_filter );
	}

	ob_start();

	echo '<div' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '' ) . '>';
	if ( function_exists( 'get_product_search_form' ) ) {
		get_product_search_form();
	} else {
		get_search_form();
	}
	if ( $id && $has_width ) {
		echo silver_responsive_style_tag( $id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
			'width' => array(
				'selector' => '&',
				'property' => 'max-width',
				'unit'     => 'px',
			),
		) );
	}
	echo '</div>';

	if ( $placeholder_filter ) {
		remove_filter( 'theme_mod_search_placeholder', $placeholder_filter );
	}

	return ob_get_clean();
}
add_shortcode("search", "search_shortcode");

/**
 * Search icon that opens the search field in a dropdown or a lightbox.
 * Same markup and behaviour as the header builder search element
 * (template-parts/header/partials/element-search.php).
 *
 * @param array  $atts    Search attributes.
 * @param string $display icon-dropdown / icon-lightbox.
 *
 * @return string
 */
function silver_search_icon_shortcode( $atts, $display ) {
	$id      = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : 'ux-search-' . wp_rand();
	$classes = array( 'ux-search-icon', 'ux-search-icon--' . ( 'icon-lightbox' === $display ? 'lightbox' : 'dropdown' ) );
	if ( $atts['class'] ) $classes[] = $atts['class'];
	if ( $atts['visibility'] ) $classes[] = $atts['visibility'];

	// The field inside the dropdown / lightbox is a normal search box.
	$inner = sprintf(
		'[search size="%s" style="%s" placeholder="%s"]',
		'icon-lightbox' === $display ? 'large' : esc_attr( $atts['size'] ),
		esc_attr( $atts['style'] ? $atts['style'] : get_theme_mod( 'header_search_form_style', '' ) ),
		esc_attr( $atts['placeholder'] )
	);

	ob_start();
	?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( 'icon-lightbox' === $display ) : ?>
			<?php
			printf(
				'<a %s>%s</a>',
				silver_html_atts( array( // phpcs:ignore WordPress.Security.EscapeOutput
					'href'          => '#' . $id . '-lightbox',
					'class'         => 'ux-search-icon__link',
					'aria-label'    => esc_attr__( 'Search', 'woocommerce' ),
					'data-open'     => '#' . $id . '-lightbox',
					'data-focus'    => 'input.search-field',
					'role'          => 'button',
					'aria-expanded' => 'false',
					'aria-haspopup' => 'dialog',
					'aria-controls' => $id . '-lightbox',
				) ),
				get_silver_icon( 'icon-search' ) // phpcs:ignore WordPress.Security.EscapeOutput
			);
			?>
			<div id="<?php echo esc_attr( $id ); ?>-lightbox" class="mfp-hide dark text-center">
				<?php echo do_shortcode( $inner ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php else : ?>
			<ul class="nav ux-search-icon__nav">
				<li class="header-search header-search-dropdown has-icon has-dropdown menu-item-has-children">
					<a href="#" class="ux-search-icon__link nav-top-link" aria-label="<?php esc_attr_e( 'Search', 'woocommerce' ); ?>" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>-dropdown"><?php echo get_silver_icon( 'icon-search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<ul id="<?php echo esc_attr( $id ); ?>-dropdown" class="nav-dropdown <?php silver_dropdown_classes(); ?>">
						<li class="header-search-form search-form html relative has-icon"><?php echo do_shortcode( $inner ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
					</ul>
				</li>
			</ul>
		<?php endif; ?>
		<?php
		echo silver_responsive_style_tag( $id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
			'icon_size'  => array(
				'selector' => '.ux-search-icon__link',
				'property' => 'font-size',
				'unit'     => 'px',
			),
			'icon_color' => array(
				'selector' => '.ux-search-icon__link',
				'property' => 'color',
			),
		) );
		?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Search box that collapses to an icon on some screens ("Collapse to icon").
 *
 * Markup: the normal box (shown on the other screens), a toggle button
 * (icon on the collapsed screens) and a second box in a bar. The bar is moved
 * by silver-search-toggle.js under the header row it belongs to (or the
 * parent block) and opens there at full width.
 *
 * @param array $atts    Search attributes.
 * @param array $screens Collapsed screens (sm, md, ml, lg).
 *
 * @return string
 */
function silver_search_collapse_shortcode( $atts, $screens ) {
	$id = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : 'ux-search-' . wp_rand();

	// The inner boxes keep the look (style, size, icon, radius, colours,
	// placeholder); id, visibility, width and collapse belong to the wrapper.
	$inner = array();
	foreach ( $atts as $key => $value ) {
		if ( preg_match( '/^(_id|class|visibility|grow|width|collapse)(__|$)/', $key ) ) continue;
		$inner[ $key ] = $value;
	}

	$classes = array( 'ux-search-collapse' );
	foreach ( $screens as $screen ) $classes[] = 'ux-search-collapse-' . sanitize_html_class( $screen );
	if ( 'true' === $atts['grow'] ) $classes[] = 'ux-search-grow';
	if ( $atts['class'] ) $classes[] = $atts['class'];
	if ( $atts['visibility'] ) $classes[] = $atts['visibility'];

	ob_start();
	?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-collapse="<?php echo esc_attr( implode( ' ', $screens ) ); ?>">
		<div class="ux-search-collapse__box"><?php echo search_shortcode( $inner ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<button type="button" class="ux-search-collapse__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>-bar" aria-label="<?php esc_attr_e( 'Search', 'woocommerce' ); ?>">
			<span class="ux-search-collapse__icon-open"><?php echo get_silver_icon( 'icon-search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<svg class="ux-search-collapse__icon-close" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M18 6 6 18M6 6l12 12"/></svg>
		</button>
		<div id="<?php echo esc_attr( $id ); ?>-bar" class="ux-search-collapse__bar" data-for="<?php echo esc_attr( $id ); ?>">
			<div class="ux-search-collapse__bar-inner"><?php echo search_shortcode( $inner ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
		<?php
		echo silver_responsive_style_tag( $id, $atts, array( // phpcs:ignore WordPress.Security.EscapeOutput
			'width'      => array(
				'selector' => '&',
				'property' => 'max-width',
				'unit'     => 'px',
			),
			'icon_size'  => array(
				'selector' => '.ux-search-collapse__toggle',
				'property' => 'font-size',
				'unit'     => 'px',
			),
			'icon_color' => array(
				'selector' => '.ux-search-collapse__toggle',
				'property' => 'color',
			),
		) );
		?>
	</div>
	<?php
	return ob_get_clean();
}
