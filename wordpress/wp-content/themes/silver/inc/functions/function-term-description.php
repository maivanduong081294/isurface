<?php
/**
 * Term description editor: the Description field of the Edit term screen is a
 * visual editor (wp_editor()) for every public taxonomy, and the description
 * keeps the HTML of a post (headings, images, lists, links).
 *
 * The Add term screen keeps the plain field: it is saved with Ajax, which the
 * visual editor does not support.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomies that get the editor.
 *
 * @return string[]
 */
function silver_term_description_editor_taxonomies() {
	$taxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true ) );
	unset( $taxonomies['post_format'] );

	return array_values( apply_filters( 'silver_term_description_editor_taxonomies', $taxonomies ) );
}

/**
 * Edit term screen: the editor row. It takes the place of the core
 * Description row, which is removed (the editor posts the same "description"
 * field, and comes later in the form, so it wins even without JavaScript).
 *
 * @param WP_Term $term     Term.
 * @param string  $taxonomy Taxonomy.
 */
function silver_term_description_editor_field( $term, $taxonomy ) {
	$description = get_term_field( 'description', $term->term_id, $taxonomy, 'raw' );
	?>
	<tr class="form-field term-description-editor-wrap">
		<th scope="row"><label for="silver-term-description"><?php esc_html_e( 'Description' ); ?></label></th>
		<td>
			<?php
			wp_editor( is_wp_error( $description ) ? '' : (string) $description, 'silver-term-description', array(
				'textarea_name' => 'description',
				'textarea_rows' => 12,
				'media_buttons' => true,
			) );
			?>
		</td>
	</tr>
	<script>
	( function () {
		// Before the editor starts (footer): move the row to the place of the
		// core Description row and remove that one.
		var editorRow = document.querySelector( '.term-description-editor-wrap' );
		var coreRow = document.querySelector( '.term-description-wrap' );
		if ( editorRow && coreRow ) {
			coreRow.parentNode.insertBefore( editorRow, coreRow );
			coreRow.parentNode.removeChild( coreRow );
		}
	} )();
	</script>
	<?php
}

/** Hooks for every taxonomy (registered on admin_init, after the taxonomies). */
function silver_term_description_editor_register() {
	foreach ( silver_term_description_editor_taxonomies() as $taxonomy ) {
		add_action( "{$taxonomy}_edit_form_fields", 'silver_term_description_editor_field', 5, 2 );
	}
}
add_action( 'admin_init', 'silver_term_description_editor_register' );

/**
 * Lets the description keep the HTML of a post (wp-includes/default-filters.php).
 *
 * Saved: core runs wp_filter_kses on every save, for every user, which keeps
 * links and a few inline tags only; the post rules (wp_filter_post_kses) are
 * used instead. Shown in the admin (terms list): wp_kses_data becomes
 * wp_kses_post the same way.
 */
function silver_term_description_allow_post_html() {
	if ( false !== has_filter( 'term_description', 'wp_kses_data' ) ) {
		remove_filter( 'term_description', 'wp_kses_data' );
		add_filter( 'term_description', 'wp_kses_post' );
	}

	if ( false !== has_filter( 'pre_term_description', 'wp_filter_kses' ) ) {
		remove_filter( 'pre_term_description', 'wp_filter_kses' );
		add_filter( 'pre_term_description', 'wp_filter_post_kses' );
	}
}
add_action( 'init', 'silver_term_description_allow_post_html', 20 );
