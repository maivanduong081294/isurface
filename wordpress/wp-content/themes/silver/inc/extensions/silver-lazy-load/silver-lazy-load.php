<?php
/**
 * Apply loading hints to the rendered storefront in document order.
 */
function silver_lazy_load_media( $output ) {
	$html = new WP_HTML_Tag_Processor( $output );
	if ( ! $html->next_tag( 'BODY' ) ) {
		return $output;
	}

	$enabled = array(
		'IMG'    => silver_perf( 'lazy_load_images' ),
		'IFRAME' => silver_perf( 'lazy_load_iframes' ),
		'VIDEO'  => silver_perf( 'lazy_load_video' ),
		'AUDIO'  => silver_perf( 'lazy_load_audio' ),
	);
	$early_images = wp_omit_loading_attr_threshold();
	$first_main_image = false;
	// After </main> (footer...): never the first screen, always lazy.
	$after_main = false;
	$template_depth = 0;

	while ( $html->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
		$tag = $html->get_tag();
		if ( 'TEMPLATE' === $tag ) {
			$template_depth += $html->is_tag_closer() ? -1 : 1;
			$template_depth = max( 0, $template_depth );
			continue;
		}
		if ( $template_depth ) {
			continue;
		}
		if ( 'MAIN' === $tag ) {
			$first_main_image = ! $html->is_tag_closer();
			$after_main       = $html->is_tag_closer();
			continue;
		}
		if ( empty( $enabled[ $tag ] ) || $html->is_tag_closer() ) {
			continue;
		}

		$eager = 'eager' === strtolower( (string) $html->get_attribute( 'loading' ) );
		$skip = $html->has_class( 'ux-skip-lazy' ) || 'high' === strtolower( (string) $html->get_attribute( 'fetchpriority' ) );
		if ( 'IMG' === $tag ) {
			$src = $html->get_attribute( 'src' );
			if ( ! is_string( $src ) || 0 === stripos( $src, 'data:' ) ) {
				continue;
			}
			if ( ! $after_main ) {
				$skip = $skip || $early_images > 0 || $first_main_image;
				--$early_images;
				$first_main_image = false;
			}
		}
		if ( $skip || $eager ) {
			if ( $skip && 'lazy' === strtolower( (string) $html->get_attribute( 'loading' ) ) ) {
				$html->remove_attribute( 'loading' );
			}
			if ( 'IMG' === $tag ) {
				silver_lazy_load_sizes( $html, false );
			}
			continue;
		}
		if ( 'VIDEO' === $tag || 'AUDIO' === $tag ) {
			if ( null !== $html->get_attribute( 'autoplay' ) || ( 'AUDIO' === $tag && null === $html->get_attribute( 'controls' ) ) ) {
				continue;
			}
			// `loading` does nothing on video / audio: no preload is what defers them.
			$html->set_attribute( 'preload', 'none' );
			continue;
		}
		$html->set_attribute( 'loading', 'lazy' );
		if ( 'IMG' === $tag ) {
			silver_lazy_load_sizes( $html, true );
		}
	}

	$output = $html->get_updated_html();

	// silver-lazy-load.js only resizes sliders and masonry grids once their
	// images load: left out of pages that have neither.
	if ( ! preg_match( '/class=(["\'])[^"\']*(?<![\w-])(slider|has-packery)(?![\w-])/', $output ) ) {
		$output = preg_replace( '#<script\b[^>]*\bid=(["\'])silver-lazy-js\1[^>]*>\s*</script>\s*#i', '', $output );
	}

	return $output;
}

/**
 * sizes of an image with srcset (HTML standard):
 * - lazy: "auto, <list>": the browser picks the srcset file from the width
 *   the image really has on the page (grid column, content width...), the
 *   list stays for browsers without sizes=auto;
 * - not lazy: no "auto" (only valid with loading="lazy").
 *
 * @param WP_HTML_Tag_Processor $html Processor on an IMG tag.
 * @param bool                  $lazy Whether the image is lazy.
 */
function silver_lazy_load_sizes( $html, $lazy ) {
	if ( ! $html->get_attribute( 'srcset' ) ) {
		return;
	}

	$sizes = trim( (string) $html->get_attribute( 'sizes' ) );
	$auto  = (bool) preg_match( '/^auto\s*(,\s*|$)/i', $sizes, $prefix );

	if ( $lazy && ! $auto ) {
		$html->set_attribute( 'sizes', '' === $sizes ? 'auto' : 'auto, ' . $sizes );
	} elseif ( ! $lazy && $auto ) {
		$rest = trim( substr( $sizes, strlen( $prefix[0] ) ) );
		if ( '' === $rest ) {
			$html->remove_attribute( 'sizes' );
		} else {
			$html->set_attribute( 'sizes', $rest );
		}
	}
}

function silver_setup_lazy_load_media() {
	if ( is_feed() || is_preview() || is_customize_preview() || is_embed() || is_trackback() || wp_is_json_request() || ( function_exists( 'ux_builder_is_iframe' ) && ux_builder_is_iframe() ) ) {
		return;
	}

	if ( function_exists( 'wp_should_output_buffer_template_for_enhancement' ) ) {
		add_filter( 'wp_template_enhancement_output_buffer', 'silver_lazy_load_media' );
	} else {
		// WordPress 6.4–6.8 has no template enhancement filter.
		add_filter( 'template_include', static function ( $template ) {
			ob_start( 'silver_lazy_load_media' );
			return $template;
		}, PHP_INT_MAX );
	}

	if ( silver_perf( 'lazy_load_images' ) ) {
		// Existing slider, masonry and third-party lazy-load markup use this behavior.
		add_action( 'wp_enqueue_scripts', static function () {
			global $extensions_uri;
			wp_enqueue_script( 'silver-lazy', $extensions_uri . '/silver-lazy-load/silver-lazy-load.js', array( 'silver-js' ), wp_get_theme( get_template() )->get( 'Version' ), true );
		}, 99 );
	}
}
add_action( 'wp', 'silver_setup_lazy_load_media' );
