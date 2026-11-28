<?php
/**
 * Breadcrumbs per page: a "Breadcrumbs" field (Default / Hide / Show) on blog
 * posts, pages, products, post categories and product categories (the shop
 * is a page: its field applies to the shop).
 *
 * Default follows the theme options:
 * - blog posts: Blog > Blog Single Post > Breadcrumbs;
 * - post categories: Blog > Blog Archive > Breadcrumbs;
 * - shop, product categories, products: shown (as before);
 * - pages: Breadcrumbs > Show breadcrumbs on pages (off by default).
 * Hide removes every breadcrumb of the page (also the Page Header element and
 * the product breadcrumbs element); Show turns them on where the option is off.
 *
 * Pages print them in the bar under the header (Breadcrumbs position "Below
 * header"), else above the content.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_BREADCRUMBS_META = 'silver_breadcrumbs';

/**
 * Post types and taxonomies with the field.
 *
 * @return array{post_types: string[], taxonomies: string[]}
 */
function silver_breadcrumbs_field_objects() {
	$post_types = array( 'post', 'page' );
	$taxonomies = array( 'category' );
	if ( post_type_exists( 'product' ) ) {
		$post_types[] = 'product';
	}
	if ( taxonomy_exists( 'product_cat' ) ) {
		$taxonomies[] = 'product_cat';
	}

	return apply_filters( 'silver_breadcrumbs_field_objects', array(
		'post_types' => $post_types,
		'taxonomies' => $taxonomies,
	) );
}

/**
 * The field of the page being shown.
 *
 * @return string '' (Default), 'hide' or 'show'.
 */
function silver_breadcrumbs_override() {
	static $value = null;
	if ( null !== $value && did_action( 'wp' ) ) {
		return $value;
	}

	$raw     = '';
	$objects = silver_breadcrumbs_field_objects();

	if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$raw = get_post_meta( wc_get_page_id( 'shop' ), SILVER_BREADCRUMBS_META, true );
	} elseif ( is_category() || is_tax( $objects['taxonomies'] ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && in_array( $term->taxonomy, $objects['taxonomies'], true ) ) {
			$raw = get_term_meta( $term->term_id, SILVER_BREADCRUMBS_META, true );
		}
	} elseif ( is_singular( $objects['post_types'] ) ) {
		$raw = get_post_meta( get_queried_object_id(), SILVER_BREADCRUMBS_META, true );
	}

	$raw = in_array( $raw, array( 'hide', 'show' ), true ) ? $raw : '';
	if ( did_action( 'wp' ) ) {
		$value = $raw;
	}

	return $raw;
}

/**
 * Blog posts and post categories: the field over the blog options.
 *
 * @param bool $enabled Blog option.
 * @return bool
 */
function silver_breadcrumbs_blog_override( $enabled ) {
	if ( is_singular( 'post' ) || is_category() ) {
		$override = silver_breadcrumbs_override();
		if ( 'hide' === $override ) {
			return false;
		}
		if ( 'show' === $override ) {
			return true;
		}
	}

	return $enabled;
}
add_filter( 'silver_blog_breadcrumbs_enabled', 'silver_breadcrumbs_blog_override' );

/**
 * Whether the page being shown is a page with its breadcrumbs on.
 *
 * @return bool
 */
function silver_page_breadcrumbs_enabled() {
	if ( ! is_page() || is_front_page() ) {
		return false;
	}
	// The shop page is a shop view (silver_shop_breadcrumbs_view()).
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return false;
	}
	$override = silver_breadcrumbs_override();
	$enabled  = 'show' === $override || ( '' === $override && get_theme_mod( 'page_breadcrumbs', 0 ) );

	return (bool) apply_filters( 'silver_page_breadcrumbs_enabled', $enabled );
}

/**
 * Pages, breadcrumbs position "Default": above the content.
 */
function silver_page_breadcrumbs() {
	if ( ! silver_page_breadcrumbs_enabled() || silver_breadcrumbs_bar_printed() ) {
		return;
	}
	ob_start();
	silver_breadcrumb();
	$breadcrumb = trim( ob_get_clean() );
	if ( '' === $breadcrumb ) {
		return;
	}
	echo '<div class="page-breadcrumbs-row"><div class="container">' . $breadcrumb . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the breadcrumb source.
}
add_action( 'silver_before_page', 'silver_page_breadcrumbs', 5 );

/* --------------------------------------------------------------- Admin */

/**
 * The select.
 *
 * @param string $value Stored value.
 * @return string
 */
function silver_breadcrumbs_field_select( $value ) {
	$choices = array(
		''     => __( 'Default', 'silver' ),
		'hide' => __( 'Hide', 'silver' ),
		'show' => __( 'Show', 'silver' ),
	);
	$html = '<select name="' . esc_attr( SILVER_BREADCRUMBS_META ) . '" id="silver-breadcrumbs">';
	foreach ( $choices as $key => $label ) {
		$html .= '<option value="' . esc_attr( $key ) . '"' . selected( (string) $value, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}

	return $html . '</select>';
}

/**
 * What Default means, per object.
 *
 * @param string $object Post type or taxonomy.
 * @return string
 */
function silver_breadcrumbs_field_help( $object ) {
	switch ( $object ) {
		case 'post':
			return __( 'Default: Blog > Single post > Breadcrumbs.', 'silver' );
		case 'category':
			return __( 'Default: Blog > Archives > Breadcrumbs.', 'silver' );
		case 'page':
			return __( 'Default: Breadcrumbs > Show breadcrumbs on pages. The shop page: shown.', 'silver' );
		default:
			return __( 'Default: shown.', 'silver' );
	}
}

/** Meta box (sidebar) of posts, pages and products. */
function silver_breadcrumbs_meta_box() {
	add_meta_box( 'silver-breadcrumbs', __( 'Breadcrumbs', 'silver' ), 'silver_breadcrumbs_meta_box_html', silver_breadcrumbs_field_objects()['post_types'], 'side', 'default' );
}
add_action( 'add_meta_boxes', 'silver_breadcrumbs_meta_box' );

/**
 * @param WP_Post $post Post.
 */
function silver_breadcrumbs_meta_box_html( $post ) {
	wp_nonce_field( 'silver_breadcrumbs', 'silver_breadcrumbs_nonce' );
	echo silver_breadcrumbs_field_select( get_post_meta( $post->ID, SILVER_BREADCRUMBS_META, true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
	echo '<p class="description">' . esc_html( silver_breadcrumbs_field_help( $post->post_type ) ) . '</p>';
}

/**
 * @param int $post_id Post ID.
 */
function silver_breadcrumbs_save_post( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! in_array( get_post_type( $post_id ), silver_breadcrumbs_field_objects()['post_types'], true ) ) {
		return;
	}
	silver_breadcrumbs_save( 'post', $post_id );
}
add_action( 'save_post', 'silver_breadcrumbs_save_post' );

/**
 * @param string $meta_type post or term.
 * @param int    $object_id Object ID.
 */
function silver_breadcrumbs_save( $meta_type, $object_id ) {
	if ( ! isset( $_POST['silver_breadcrumbs_nonce'], $_POST[ SILVER_BREADCRUMBS_META ] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_breadcrumbs_nonce'] ) ), 'silver_breadcrumbs' ) ) {
		return;
	}
	$value = sanitize_key( wp_unslash( $_POST[ SILVER_BREADCRUMBS_META ] ) );

	if ( in_array( $value, array( 'hide', 'show' ), true ) ) {
		update_metadata( $meta_type, $object_id, SILVER_BREADCRUMBS_META, $value );
	} else {
		delete_metadata( $meta_type, $object_id, SILVER_BREADCRUMBS_META );
	}
}

/**
 * Add category screen.
 *
 * @param string $taxonomy Taxonomy.
 */
function silver_breadcrumbs_term_add_field( $taxonomy ) {
	echo '<div class="form-field"><label for="silver-breadcrumbs">' . esc_html__( 'Breadcrumbs', 'silver' ) . '</label>';
	wp_nonce_field( 'silver_breadcrumbs', 'silver_breadcrumbs_nonce' );
	echo silver_breadcrumbs_field_select( '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
	echo '<p>' . esc_html( silver_breadcrumbs_field_help( $taxonomy ) ) . '</p></div>';
}

/**
 * Edit category screen.
 *
 * @param WP_Term $term Term.
 */
function silver_breadcrumbs_term_edit_field( $term ) {
	echo '<tr class="form-field"><th scope="row"><label for="silver-breadcrumbs">' . esc_html__( 'Breadcrumbs', 'silver' ) . '</label></th><td>';
	wp_nonce_field( 'silver_breadcrumbs', 'silver_breadcrumbs_nonce' );
	echo silver_breadcrumbs_field_select( get_term_meta( $term->term_id, SILVER_BREADCRUMBS_META, true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
	echo '<p class="description">' . esc_html( silver_breadcrumbs_field_help( $term->taxonomy ) ) . '</p></td></tr>';
}

/**
 * @param int $term_id Term ID.
 */
function silver_breadcrumbs_save_term( $term_id ) {
	if ( current_user_can( 'edit_term', $term_id ) ) {
		silver_breadcrumbs_save( 'term', $term_id );
	}
}

/** Hooks for each taxonomy (after WooCommerce registers product_cat). */
function silver_breadcrumbs_register_term_fields() {
	foreach ( silver_breadcrumbs_field_objects()['taxonomies'] as $taxonomy ) {
		add_action( "{$taxonomy}_add_form_fields", 'silver_breadcrumbs_term_add_field', 25 );
		add_action( "{$taxonomy}_edit_form_fields", 'silver_breadcrumbs_term_edit_field', 25 );
		add_action( "created_{$taxonomy}", 'silver_breadcrumbs_save_term' );
		add_action( "edited_{$taxonomy}", 'silver_breadcrumbs_save_term' );
	}
}
add_action( 'init', 'silver_breadcrumbs_register_term_fields', 20 );
