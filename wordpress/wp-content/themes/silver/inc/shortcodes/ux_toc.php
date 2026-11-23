<?php
/**
 * [ux_toc]...[/ux_toc] — Table of contents of the content it wraps.
 *
 * The list is built from the headings of $content (levels="h2,h3"): the first
 * level is numbered (01, 02...), the second one is nested under it. Headings
 * without an id get one from their text (accents removed), so the links work
 * without JS and as deep links. The content is printed with the list:
 *
 * - From 850px: two columns, the content and the list (position="right" /
 *   "left"; sticky="true" keeps it in view), or the list above it ("top").
 * - Below 850px: a bar fixed under the header while the content is read,
 *   showing the current section; it opens the list (mobile="bar"). Or the
 *   list above the content ("box"), or no list ("hidden").
 *
 * assets/js/src/silver-toc.js: current section, smooth scroll, mobile bar.
 * assets/css/src/silver-toc.css. Both merged into silver.css / silver.js by
 * build/build.mjs.
 */

/**
 * Reserves an id for a heading, unique among the tables of contents of the page.
 *
 * @param string $id Wanted id.
 *
 * @return string The id, with a "-2", "-3"... suffix when it was taken.
 */
function silver_toc_unique_id( $id ) {
	static $used = array();

	$base = $id;
	$n    = 2;
	while ( isset( $used[ $id ] ) ) {
		$id = $base . '-' . $n++;
	}
	$used[ $id ] = true;

	return $id;
}

/**
 * Finds the headings of the content, gives them an id, and returns the
 * content with these ids and the list of headings.
 *
 * @param string   $html   Rendered content.
 * @param string[] $levels First level, then the optional second level ("h2", "h3").
 *
 * @return array array( 'html' => string, 'headings' => array of array( level, id, text ) ).
 */
function silver_toc_parse( $html, $levels ) {
	$headings = array();
	$pattern  = '#<(' . implode( '|', array_map( 'preg_quote', $levels ) ) . ')(\s[^>]*)?>(.*?)</\1\s*>#is';

	$html = preg_replace_callback( $pattern, function ( $m ) use ( &$headings ) {
		$tag   = strtolower( $m[1] );
		$attrs = isset( $m[2] ) ? $m[2] : '';
		$text  = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $m[3] ), ENT_QUOTES, 'UTF-8' ) ) );

		// Empty headings, and headings marked to be skipped, are not listed.
		if ( '' === $text || preg_match( '/class=["\'][^"\']*\bno-toc\b/i', $attrs ) ) {
			return $m[0];
		}

		if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
			$id = $id_match[1];
			silver_toc_unique_id( $id );
		} else {
			$slug = sanitize_title( remove_accents( str_replace( array( 'đ', 'Đ' ), 'd', $text ) ) );
			$id   = silver_toc_unique_id( $slug ? $slug : 'section' );
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}

		$headings[] = array(
			'level' => $tag,
			'id'    => $id,
			'text'  => $text,
		);

		return '<' . $m[1] . $attrs . '>' . $m[3] . '</' . $m[1] . '>';
	}, $html );

	return array(
		'html'     => $html,
		'headings' => $headings,
	);
}

/**
 * The list of headings, as nested <ol>.
 *
 * @param array  $headings See silver_toc_parse().
 * @param string $top      First level tag.
 *
 * @return string
 */
function silver_toc_list_html( $headings, $top ) {
	$html   = '';
	$number = 0;
	$open   = false; // A first-level item is open.
	$sub    = false; // Its second-level list is open.

	foreach ( $headings as $heading ) {
		$link = '<a class="ux-toc__link" href="#' . esc_attr( $heading['id'] ) . '">';

		// A second-level heading before any first-level one is listed as first level.
		if ( $heading['level'] === $top || ! $open ) {
			if ( $sub ) {
				$html .= '</ol>';
				$sub   = false;
			}
			if ( $open ) {
				$html .= '</li>';
			}
			$number++;
			$html .= '<li class="ux-toc__item">' . $link
				. '<span class="ux-toc__num" aria-hidden="true">' . esc_html( sprintf( '%02d', $number ) ) . '</span>'
				. '<span class="ux-toc__text">' . esc_html( $heading['text'] ) . '</span></a>';
			$open  = true;
		} else {
			if ( ! $sub ) {
				$html .= '<ol class="ux-toc__sub">';
				$sub   = true;
			}
			$html .= '<li class="ux-toc__subitem">' . $link . esc_html( $heading['text'] ) . '</a></li>';
		}
	}

	if ( $sub ) {
		$html .= '</ol>';
	}
	if ( $open ) {
		$html .= '</li>';
	}

	return '<ol class="ux-toc__list">' . $html . '</ol>';
}

// [ux_toc]...[/ux_toc]
function silver_toc_shortcode( $atts, $content = null, $tag = 'ux_toc' ) {
	$atts = ux_builder_shortcode_atts( array(
		'_id'        => '',
		'class'      => '',
		'visibility' => '',
		'title'      => __( 'Table of contents', 'silver' ),
		'levels'     => 'h2,h3',
		'position'   => 'right',
		'width'      => '',
		'numbered'   => 'true',
		'sticky'     => 'true',
		'mobile'     => 'bar',
		'subs'       => 'all',
		// "closed" / "open": the list in a box that opens and closes (<details>),
		// its title as the button.
		'collapse'   => '',
	), $atts, $tag );

	if ( 'hidden' === $atts['visibility'] ) {
		return '';
	}

	// In the classic editor, wpautop leaves "</p>" / "<p>" at the edges of
	// the wrapped content.
	$html = do_shortcode( preg_replace( '#^\s*</p>|<p>\s*$#i', '', (string) $content ) );

	// "h2,h3" -> first level, optional second level.
	$levels = array_values( array_unique( array_filter(
		array_map( 'trim', explode( ',', strtolower( $atts['levels'] ) ) ),
		function ( $level ) {
			return (bool) preg_match( '/^h[2-6]$/', $level );
		}
	) ) );
	if ( empty( $levels ) ) {
		$levels = array( 'h2', 'h3' );
	}
	$levels = array_slice( $levels, 0, 2 );

	$parsed = silver_toc_parse( $html, $levels );
	$id     = $atts['_id'] ? sanitize_html_class( $atts['_id'] ) : 'ux-toc-' . wp_rand();

	$layout = array( 'ux-toc-layout' );
	if ( $atts['class'] ) {
		$layout[] = $atts['class'];
	}
	if ( $atts['visibility'] ) {
		$layout[] = $atts['visibility'];
	}

	// No heading: the content alone.
	if ( empty( $parsed['headings'] ) ) {
		return '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $layout ) ) . '"><div class="ux-toc-layout__content">' . $parsed['html'] . '</div></div>';
	}

	$position = in_array( $atts['position'], array( 'right', 'left', 'top' ), true ) ? $atts['position'] : 'right';
	$mobile   = in_array( $atts['mobile'], array( 'bar', 'box', 'hidden' ), true ) ? $atts['mobile'] : 'bar';
	$title    = trim( wp_strip_all_tags( $atts['title'] ) );
	$label    = '' !== $title ? $title : __( 'Table of contents', 'silver' );

	$layout[] = 'ux-toc-layout--' . $position;
	$layout[] = 'ux-toc-layout--mobile-' . $mobile;

	$toc = array( 'ux-toc' );
	if ( 'false' !== $atts['sticky'] && 'top' !== $position ) {
		$toc[] = 'is-sticky';
	}
	if ( 'false' === $atts['numbered'] ) {
		$toc[] = 'no-numbers';
	}
	// Second level: "all" (default) or "active" (only under the section being
	// read, once the script runs).
	if ( 'active' === $atts['subs'] ) {
		$toc[] = 'ux-toc--subs-active';
	}
	$collapse = in_array( $atts['collapse'], array( 'closed', 'open' ), true ) ? $atts['collapse'] : '';
	if ( $collapse ) {
		$toc[] = 'ux-toc--collapsible';
	}

	$style = '';
	$width = trim( preg_replace( '/[;{}<>"]/', '', $atts['width'] ) );
	if ( '' !== $width ) {
		$style = ' style="--ux-toc-width: ' . esc_attr( is_numeric( $width ) ? $width . 'px' : $width ) . ';"';
	}

	ob_start();
	?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $layout ) ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>>
		<aside class="ux-toc-layout__aside">
			<nav class="<?php echo esc_attr( implode( ' ', $toc ) ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" data-title="<?php echo esc_attr( $title ); ?>">
				<?php if ( $collapse ) : ?>
					<details class="ux-toc__details"<?php echo 'open' === $collapse ? ' open' : ''; ?>>
						<summary class="ux-toc__summary">
							<span class="ux-toc__summary-icon" aria-hidden="true"></span>
							<span class="ux-toc__summary-text"><?php echo esc_html( $label ); ?></span>
							<span class="ux-toc__summary-chevron" aria-hidden="true"></span>
						</summary>
						<?php echo silver_toc_list_html( $parsed['headings'], $levels[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the function. ?>
					</details>
				<?php else : ?>
					<?php if ( '' !== $title ) : ?>
						<div class="ux-toc__title"><?php echo esc_html( $title ); ?></div>
					<?php endif; ?>
					<?php echo silver_toc_list_html( $parsed['headings'], $levels[0] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the function. ?>
				<?php endif; ?>
			</nav>
		</aside>
		<div class="ux-toc-layout__content">
			<?php echo $parsed['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered content. ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ux_toc', 'silver_toc_shortcode' );
