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
			$skip = $skip || $early_images > 0 || $first_main_image;
			--$early_images;
			$first_main_image = false;
		}
		if ( $skip ) {
			if ( 'lazy' === strtolower( (string) $html->get_attribute( 'loading' ) ) ) {
				$html->remove_attribute( 'loading' );
				if ( 'IMG' === $tag ) {
					$sizes = $html->get_attribute( 'sizes' );
					if ( is_string( $sizes ) && preg_match( '/^auto\s*,\s*/i', $sizes, $prefix ) ) {
						$html->set_attribute( 'sizes', substr( $sizes, strlen( $prefix[0] ) ) );
					}
				}
			}
			continue;
		}
		if ( $eager ) {
			continue;
		}
		if ( 'VIDEO' === $tag || 'AUDIO' === $tag ) {
			if ( null !== $html->get_attribute( 'autoplay' ) || ( 'AUDIO' === $tag && null === $html->get_attribute( 'controls' ) ) ) {
				continue;
			}
			$html->set_attribute( 'preload', 'none' );
		}
		$html->set_attribute( 'loading', 'lazy' );
	}
	return $html->get_updated_html();
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
