<?php
/**
 * Short description of terms: a text field (term meta
 * "silver_short_description") for every public taxonomy, on the Add / Edit
 * term screens.
 *
 * Printed under the title in the bar under the header (silver_breadcrumbs_bar())
 * on product categories, product tags, blog categories and tags, instead of
 * the default description (number of products, term description).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_SHORT_DESCRIPTION_TERM_META = 'silver_short_description';

/**
 * Taxonomies that get the field.
 *
 * @return string[]
 */
function silver_short_description_taxonomies() {
	$taxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true ) );
	unset( $taxonomies['post_format'] );

	return array_values( apply_filters( 'silver_short_description_taxonomies', $taxonomies ) );
}

/**
 * Keeps plain text with a few inline tags (links, bold, italic, line breaks).
 *
 * @param string $value Raw value.
 *
 * @return string
 */
function silver_short_description_sanitize( $value ) {
	$allowed = array(
		'a'      => array(
			'href'   => true,
			'title'  => true,
			'target' => true,
			'rel'    => true,
		),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'br'     => array(),
	);

	return trim( wp_kses( (string) $value, $allowed ) );
}

/**
 * The short description of a term, as HTML paragraphs.
 *
 * @param WP_Term $term Term.
 *
 * @return string Empty when not set.
 */
function silver_get_term_short_description( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$value = silver_short_description_sanitize( get_term_meta( $term->term_id, SILVER_SHORT_DESCRIPTION_TERM_META, true ) );

	return (string) apply_filters( 'silver_term_short_description', '' === $value ? '' : wpautop( $value ), $term );
}

/**
 * The short description of the queried term (category, tag, product
 * category...), or an empty string on other views.
 *
 * @return string HTML.
 */
function silver_queried_short_description() {
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return '';
	}

	return silver_get_term_short_description( get_queried_object() );
}

/** Add term screen. */
function silver_short_description_add_term_field() {
	wp_nonce_field( 'silver_short_description_term', 'silver_short_description_term_nonce' );
	?>
	<div class="form-field term-short-description-wrap">
		<label for="silver-short-description"><?php esc_html_e( 'Short description', 'silver' ); ?></label>
		<textarea name="silver_short_description" id="silver-short-description" rows="3"></textarea>
		<p><?php esc_html_e( 'Shown under the title in the bar under the header, instead of the default description.', 'silver' ); ?></p>
	</div>
	<?php
}

/**
 * Edit term screen.
 *
 * @param WP_Term $term Term.
 */
function silver_short_description_edit_term_field( $term ) {
	?>
	<tr class="form-field term-short-description-wrap">
		<th scope="row"><label for="silver-short-description"><?php esc_html_e( 'Short description', 'silver' ); ?></label></th>
		<td>
			<?php wp_nonce_field( 'silver_short_description_term', 'silver_short_description_term_nonce' ); ?>
			<textarea name="silver_short_description" id="silver-short-description" rows="3" class="large-text"><?php echo esc_textarea( get_term_meta( $term->term_id, SILVER_SHORT_DESCRIPTION_TERM_META, true ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Shown under the title in the bar under the header, instead of the default description.', 'silver' ); ?></p>
		</td>
	</tr>
	<?php
}

/**
 * Saves the field (created_term / edited_term).
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy.
 */
function silver_short_description_save_term( $term_id, $tt_id, $taxonomy ) {
	if ( ! in_array( $taxonomy, silver_short_description_taxonomies(), true ) ) {
		return;
	}
	if ( ! isset( $_POST['silver_short_description_term_nonce'], $_POST['silver_short_description'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_short_description_term_nonce'] ) ), 'silver_short_description_term' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$value = silver_short_description_sanitize( wp_unslash( $_POST['silver_short_description'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized here.

	if ( '' === $value ) {
		delete_term_meta( $term_id, SILVER_SHORT_DESCRIPTION_TERM_META );
	} else {
		update_term_meta( $term_id, SILVER_SHORT_DESCRIPTION_TERM_META, $value );
	}
}
add_action( 'created_term', 'silver_short_description_save_term', 10, 3 );
add_action( 'edited_term', 'silver_short_description_save_term', 10, 3 );

/** Hooks for every taxonomy (registered on admin_init, after the taxonomies). */
function silver_short_description_register_term_fields() {
	foreach ( silver_short_description_taxonomies() as $taxonomy ) {
		add_action( "{$taxonomy}_add_form_fields", 'silver_short_description_add_term_field' );
		add_action( "{$taxonomy}_edit_form_fields", 'silver_short_description_edit_term_field' );
	}
}
add_action( 'admin_init', 'silver_short_description_register_term_fields' );
