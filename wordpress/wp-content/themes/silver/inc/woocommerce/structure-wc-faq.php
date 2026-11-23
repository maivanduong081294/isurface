<?php
/**
 * FAQs of product categories and products.
 *
 * "FAQs" field on the Add / Edit product category screens and in a box of the
 * Edit product screen:
 * - None.
 * - Rank Math FAQ schema: the questions of the FAQ schema added in the Schema
 *   tab of Rank Math SEO on the category (Rank Math PRO) or product itself,
 *   which Rank Math prints; or of a Rank Math PRO schema template
 *   (Rank Math > Schema Templates) of the FAQ type.
 * - Custom questions: a list of question / answer pairs.
 *
 * Printed as an accordion (the theme's accordion script) with the FAQPage
 * JSON-LD of the page (silver_print_faq_schema(), inc/shortcodes/accordion.php),
 * except for the schema of the Schema tab, already printed by Rank Math:
 * - Product category: a full-width section after the category layout, under
 *   the category description (first page only).
 * - Product: a tab "Câu hỏi thường gặp" with the product tabs.
 *
 * Stored as one array {source, schema, items} in the term meta / product meta
 * "_silver_faq".
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_FAQ_META = '_silver_faq';

/** Post type of the Rank Math PRO schema templates. */
const SILVER_FAQ_RANK_MATH_POST_TYPE = 'rank_math_schema';

/**
 * Taxonomies that get the field.
 *
 * @return string[]
 */
function silver_faq_taxonomies() {
	return (array) apply_filters( 'silver_faq_taxonomies', array( 'product_cat' ) );
}

/**
 * Cleans the submitted field.
 *
 * @param mixed $raw Submitted value (unslashed).
 *
 * @return array{source: string, schema: int, items: array}
 */
function silver_faq_sanitize( $raw ) {
	$raw    = is_array( $raw ) ? $raw : array();
	$source = isset( $raw['source'] ) ? (string) $raw['source'] : '';
	$items  = array();

	foreach ( isset( $raw['items'] ) && is_array( $raw['items'] ) ? $raw['items'] : array() as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$question = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? trim( wp_kses_post( $item['answer'] ) ) : '';

		if ( '' === $question && '' === $answer ) {
			continue;
		}

		$items[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	return array(
		'source' => in_array( $source, array( 'rank_math', 'custom' ), true ) ? $source : '',
		'schema' => isset( $raw['schema'] ) ? absint( $raw['schema'] ) : 0,
		'items'  => $items,
	);
}

/**
 * Saved value, with the defaults.
 *
 * @param mixed $value Meta value.
 *
 * @return array{source: string, schema: int, items: array}
 */
function silver_faq_value( $value ) {
	return silver_faq_sanitize( is_array( $value ) ? $value : array() );
}

/**
 * Whether the value is worth storing (a source, or questions typed in, kept
 * when the source is set back to None).
 *
 * @param array $faq Clean value.
 *
 * @return bool
 */
function silver_faq_is_empty( $faq ) {
	return '' === $faq['source'] && ! $faq['schema'] && ! $faq['items'];
}

/*
 * Rank Math schema templates.
 */

/**
 * Questions of a schema (Rank Math format, like schema.org): FAQPage with
 * mainEntity: Question {name, acceptedAnswer: Answer {text}}.
 *
 * @param mixed $schema Schema array.
 *
 * @return array List of {question, answer}.
 */
function silver_faq_items_from_schema( $schema ) {
	if ( ! is_array( $schema ) || empty( $schema['@type'] ) || ! in_array( 'FAQPage', (array) $schema['@type'], true ) ) {
		return array();
	}

	$entities = isset( $schema['mainEntity'] ) && is_array( $schema['mainEntity'] ) ? $schema['mainEntity'] : array();
	// A single question.
	if ( isset( $entities['name'] ) || isset( $entities['@type'] ) ) {
		$entities = array( $entities );
	}

	$items = array();
	foreach ( $entities as $entity ) {
		if ( ! is_array( $entity ) ) {
			continue;
		}

		$answer = isset( $entity['acceptedAnswer'] ) ? $entity['acceptedAnswer'] : '';
		if ( is_array( $answer ) && isset( $answer[0] ) ) {
			$answer = $answer[0];
		}
		if ( is_array( $answer ) ) {
			$answer = isset( $answer['text'] ) ? $answer['text'] : '';
		}

		$question = isset( $entity['name'] ) && is_string( $entity['name'] ) ? sanitize_text_field( $entity['name'] ) : '';
		$answer   = is_string( $answer ) ? trim( wp_kses_post( $answer ) ) : '';

		if ( '' !== $question && '' !== $answer ) {
			$items[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
	}

	return $items;
}

/**
 * Questions of a Rank Math schema template (meta "rank_math_schema_{Type}"
 * of a "rank_math_schema" post).
 *
 * @param int $post_id Template ID.
 *
 * @return array List of {question, answer}, empty when it is not an FAQ.
 */
function silver_faq_rank_math_items( $post_id ) {
	$post = $post_id ? get_post( $post_id ) : null;

	if ( ! $post || SILVER_FAQ_RANK_MATH_POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
		return array();
	}

	$items = silver_faq_items_from_meta( get_post_meta( $post->ID ) );

	return (array) apply_filters( 'silver_faq_rank_math_items', $items, $post );
}

/**
 * Questions of the first FAQ schema among Rank Math schemas stored as meta
 * ("rank_math_schema_{Type}"), as get_post_meta() / get_term_meta() return
 * them without a key.
 *
 * @param array $meta Meta key => list of raw values.
 *
 * @return array List of {question, answer}.
 */
function silver_faq_items_from_meta( $meta ) {
	foreach ( (array) $meta as $key => $values ) {
		if ( 0 !== strpos( (string) $key, 'rank_math_schema_' ) ) {
			continue;
		}
		foreach ( (array) $values as $value ) {
			$items = silver_faq_items_from_schema( maybe_unserialize( $value ) );
			if ( $items ) {
				return $items;
			}
		}
	}

	return array();
}

/**
 * Questions of the FAQ schema added in the Schema tab of Rank Math SEO on the
 * product category (Rank Math PRO) or the product itself.
 *
 * @param WP_Term|WC_Product $object Product category or product.
 *
 * @return array List of {question, answer}.
 */
function silver_faq_own_rank_math_items( $object ) {
	if ( $object instanceof WP_Term ) {
		$meta = get_term_meta( $object->term_id );
	} elseif ( $object instanceof WC_Product ) {
		$meta = get_post_meta( $object->get_id() );
	} else {
		return array();
	}

	return (array) apply_filters( 'silver_faq_own_rank_math_items', silver_faq_items_from_meta( $meta ), $object );
}

/**
 * Whether Rank Math prints the schemas of the Schema tab of the product
 * category or product itself (then the theme does not print the FAQ schema
 * again).
 *
 * @param WP_Term|WC_Product $object Product category or product.
 *
 * @return bool
 */
function silver_faq_rank_math_prints_own( $object ) {
	if ( ! class_exists( '\RankMath\Helper' ) || ! \RankMath\Helper::is_module_active( 'rich-snippet' ) ) {
		$prints = false;
	} elseif ( $object instanceof WP_Term ) {
		// Same rules as RankMathPro\Schema\Taxonomy::can_add().
		$taxonomy = $object->taxonomy;
		$prints   = class_exists( '\RankMathPro\Schema\Taxonomy' )
			&& \RankMath\Helper::get_settings( 'titles.tax_' . $taxonomy . '_add_meta_box' )
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- Rank Math hook.
			&& true !== apply_filters( 'rank_math/snippet/remove_taxonomy_data', \RankMath\Helper::get_settings( 'titles.remove_' . $taxonomy . '_snippet_data' ), $taxonomy );
	} else {
		$prints = $object instanceof WC_Product;
	}

	return (bool) apply_filters( 'silver_faq_rank_math_prints_own', $prints, $object );
}

/**
 * Rank Math schema templates of the FAQ type, for the select.
 *
 * @return array Template ID => title.
 */
function silver_faq_rank_math_templates() {
	static $templates = null;

	if ( null !== $templates ) {
		return $templates;
	}

	$templates = array();
	$ids       = get_posts( array(
		'post_type'        => SILVER_FAQ_RANK_MATH_POST_TYPE,
		'post_status'      => 'publish',
		'posts_per_page'   => 200,
		'orderby'          => 'title',
		'order'            => 'ASC',
		'fields'           => 'ids',
		'suppress_filters' => true,
	) );

	foreach ( $ids as $id ) {
		if ( silver_faq_rank_math_items( $id ) ) {
			$title            = get_the_title( $id );
			$templates[ $id ] = '' === $title ? '#' . $id : $title;
		}
	}

	return $templates;
}

/**
 * Questions to show for a saved value.
 *
 * Rank Math: the selected schema template, else (0) the FAQ schema of the
 * Schema tab of the object itself.
 *
 * @param array                   $faq    Clean value.
 * @param WP_Term|WC_Product|null $object Product category or product.
 *
 * @return array List of {question, answer}.
 */
function silver_faq_items( $faq, $object = null ) {
	if ( 'rank_math' === $faq['source'] ) {
		return $faq['schema'] ? silver_faq_rank_math_items( $faq['schema'] ) : silver_faq_own_rank_math_items( $object );
	}

	if ( 'custom' === $faq['source'] ) {
		return array_values( array_filter( $faq['items'], function ( $item ) {
			return '' !== $item['question'] && '' !== $item['answer'];
		} ) );
	}

	return array();
}

/**
 * Whether the theme prints the FAQ schema of the questions: not for the FAQ
 * of the Schema tab of the object itself when Rank Math prints it.
 *
 * @param array              $faq    Clean value.
 * @param WP_Term|WC_Product $object Product category or product.
 *
 * @return bool
 */
function silver_faq_prints_schema( $faq, $object ) {
	return ! ( 'rank_math' === $faq['source'] && ! $faq['schema'] && silver_faq_rank_math_prints_own( $object ) );
}

/**
 * Questions of a product category.
 *
 * @param WP_Term|int $term Term or term ID.
 *
 * @return array List of {question, answer}.
 */
function silver_get_term_faq( $term ) {
	$term = get_term( $term );
	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$items = silver_faq_items( silver_faq_value( get_term_meta( $term->term_id, SILVER_FAQ_META, true ) ), $term );

	return (array) apply_filters( 'silver_term_faq', $items, $term );
}

/**
 * Questions of a product.
 *
 * @param WC_Product|int|null $product Product, product ID, or the current one.
 *
 * @return array List of {question, answer}.
 */
function silver_get_product_faq( $product = null ) {
	$product = $product ? wc_get_product( $product ) : wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return array();
	}

	$items = silver_faq_items( silver_faq_value( $product->get_meta( SILVER_FAQ_META, true ) ), $product );

	return (array) apply_filters( 'silver_product_faq', $items, $product );
}

/*
 * Admin.
 */

/**
 * The field: source radios, the template select, the question rows.
 *
 * @param array  $faq     Clean value.
 * @param string $context "term" or "product".
 */
function silver_faq_fields_html( $faq, $context = 'term' ) {
	$sources   = array(
		''          => __( 'No FAQs', 'silver' ),
		'rank_math' => __( 'Rank Math FAQ schema', 'silver' ),
		'custom'    => __( 'Custom questions', 'silver' ),
	);
	$templates = silver_faq_rank_math_templates();
	$items     = $faq['items'];
	?>
	<div class="silver-faq-fields">
		<fieldset class="silver-faq-sources">
			<legend class="screen-reader-text"><?php esc_html_e( 'Show FAQs', 'silver' ); ?></legend>
			<?php foreach ( $sources as $value => $label ) : ?>
				<label><input type="radio" name="silver_faq[source]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $faq['source'], $value ); ?> /> <?php echo esc_html( $label ); ?></label>
			<?php endforeach; ?>
		</fieldset>

		<div class="silver-faq-panel" data-faq-panel="rank_math"<?php echo 'rank_math' === $faq['source'] ? '' : ' hidden'; ?>>
			<select name="silver_faq[schema]" aria-label="<?php esc_attr_e( 'Rank Math FAQ schema', 'silver' ); ?>">
				<option value=""><?php echo esc_html( 'product' === $context ? __( 'FAQ in the Schema tab of this product', 'silver' ) : __( 'FAQ in the Schema tab of this category', 'silver' ) ); ?></option>
				<?php if ( $templates ) : ?>
					<optgroup label="<?php esc_attr_e( 'Schema Templates', 'silver' ); ?>">
						<?php foreach ( $templates as $id => $title ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $faq['schema'], $id ); ?>><?php echo esc_html( $title ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>
			</select>
			<p class="description"><?php esc_html_e( 'The questions of the FAQ schema added in the Schema tab of Rank Math SEO (Schema Generator), or of a schema template. Rank Math prints the schema of the Schema tab, the theme only shows the questions. A template is printed by the theme, twice if its display conditions in Rank Math also apply to this page.', 'silver' ); ?></p>
		</div>

		<div class="silver-faq-panel" data-faq-panel="custom"<?php echo 'custom' === $faq['source'] ? '' : ' hidden'; ?>>
			<div class="silver-faq-rows" data-next="<?php echo esc_attr( count( $items ) ); ?>">
				<?php
				foreach ( $items as $index => $item ) {
					silver_faq_row_html( $index, $item );
				}
				?>
			</div>
			<p><button type="button" class="button silver-faq-add"><?php esc_html_e( 'Add question', 'silver' ); ?></button></p>
			<template class="silver-faq-row-template"><?php silver_faq_row_html( '__i__', array( 'question' => '', 'answer' => '' ) ); ?></template>
		</div>
	</div>
	<?php
}

/**
 * One question row.
 *
 * @param int|string $index Row index.
 * @param array      $item  {question, answer}.
 */
function silver_faq_row_html( $index, $item ) {
	$name = 'silver_faq[items][' . $index . ']';
	?>
	<div class="silver-faq-row">
		<span class="silver-faq-row__handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'silver' ); ?>" aria-hidden="true"></span>
		<div class="silver-faq-row__fields">
			<input type="text" class="widefat" name="<?php echo esc_attr( $name ); ?>[question]" value="<?php echo esc_attr( $item['question'] ); ?>" placeholder="<?php esc_attr_e( 'Question', 'silver' ); ?>" aria-label="<?php esc_attr_e( 'Question', 'silver' ); ?>" />
			<textarea class="widefat" name="<?php echo esc_attr( $name ); ?>[answer]" rows="3" placeholder="<?php esc_attr_e( 'Answer', 'silver' ); ?>" aria-label="<?php esc_attr_e( 'Answer', 'silver' ); ?>"><?php echo esc_textarea( $item['answer'] ); ?></textarea>
		</div>
		<div class="silver-faq-row__actions">
			<button type="button" class="button-link silver-faq-up" aria-label="<?php esc_attr_e( 'Move up', 'silver' ); ?>"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
			<button type="button" class="button-link silver-faq-down" aria-label="<?php esc_attr_e( 'Move down', 'silver' ); ?>"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
			<button type="button" class="button-link button-link-delete silver-faq-remove" aria-label="<?php esc_attr_e( 'Remove question', 'silver' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
		</div>
	</div>
	<?php
}

/** Add term screen. */
function silver_faq_add_term_field() {
	wp_nonce_field( 'silver_faq_term', 'silver_faq_term_nonce' );
	?>
	<div class="form-field term-silver-faq-wrap">
		<label><?php esc_html_e( 'Show FAQs', 'silver' ); ?></label>
		<?php silver_faq_fields_html( silver_faq_value( array() ), 'term' ); ?>
		<p><?php esc_html_e( 'Shown under the category description, with the FAQ schema.', 'silver' ); ?></p>
	</div>
	<?php
}

/**
 * Edit term screen.
 *
 * @param WP_Term $term Term.
 */
function silver_faq_edit_term_field( $term ) {
	?>
	<tr class="form-field term-silver-faq-wrap">
		<th scope="row"><?php esc_html_e( 'Show FAQs', 'silver' ); ?></th>
		<td>
			<?php
			wp_nonce_field( 'silver_faq_term', 'silver_faq_term_nonce' );
			silver_faq_fields_html( silver_faq_value( get_term_meta( $term->term_id, SILVER_FAQ_META, true ) ), 'term' );
			?>
			<p class="description"><?php esc_html_e( 'Shown under the category description, with the FAQ schema.', 'silver' ); ?></p>
		</td>
	</tr>
	<?php
}

/**
 * Saves the term field (created_term / edited_term).
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy.
 */
function silver_faq_save_term( $term_id, $tt_id, $taxonomy ) {
	if ( ! in_array( $taxonomy, silver_faq_taxonomies(), true ) ) {
		return;
	}
	if ( ! isset( $_POST['silver_faq_term_nonce'], $_POST['silver_faq'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_faq_term_nonce'] ) ), 'silver_faq_term' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$faq = silver_faq_sanitize( wp_unslash( $_POST['silver_faq'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized here.

	if ( silver_faq_is_empty( $faq ) ) {
		delete_term_meta( $term_id, SILVER_FAQ_META );
	} else {
		update_term_meta( $term_id, SILVER_FAQ_META, $faq );
	}
}
add_action( 'created_term', 'silver_faq_save_term', 10, 3 );
add_action( 'edited_term', 'silver_faq_save_term', 10, 3 );

/** Term hooks (on admin_init, after the taxonomies are registered). */
function silver_faq_register_term_fields() {
	foreach ( silver_faq_taxonomies() as $taxonomy ) {
		add_action( "{$taxonomy}_add_form_fields", 'silver_faq_add_term_field', 20 );
		add_action( "{$taxonomy}_edit_form_fields", 'silver_faq_edit_term_field', 20 );
	}
}
add_action( 'admin_init', 'silver_faq_register_term_fields' );

/** Edit product screen: the box, under the quick description. */
function silver_faq_meta_box() {
	add_meta_box(
		'silver-faq',
		__( 'FAQs', 'silver' ),
		'silver_faq_meta_box_html',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'silver_faq_meta_box', 41 );

/**
 * Box content. Saved with the product (silver_faq_save_product()), so the
 * WooCommerce nonce and capability checks apply.
 *
 * @param WP_Post $post Product post.
 */
function silver_faq_meta_box_html( $post ) {
	$product = wc_get_product( $post->ID );
	?>
	<p class="silver-faq-heading"><strong><?php esc_html_e( 'Show FAQs', 'silver' ); ?></strong></p>
	<?php silver_faq_fields_html( silver_faq_value( $product ? $product->get_meta( SILVER_FAQ_META, true ) : array() ), 'product' ); ?>
	<p class="description"><?php esc_html_e( 'Shown under the product tabs, with the FAQ schema.', 'silver' ); ?></p>
	<?php
}

/**
 * Saves the box with the product.
 *
 * @param WC_Product $product Product being saved.
 */
function silver_faq_save_product( $product ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked by WooCommerce before this hook.
	if ( ! isset( $_POST['silver_faq'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized here.
	$faq = silver_faq_sanitize( wp_unslash( $_POST['silver_faq'] ) );

	if ( silver_faq_is_empty( $faq ) ) {
		$product->delete_meta_data( SILVER_FAQ_META );
	} else {
		$product->update_meta_data( SILVER_FAQ_META, $faq );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'silver_faq_save_product' );

/**
 * Script and styles of the field (product category screens, product screen).
 *
 * @param string $hook Admin page.
 */
function silver_faq_admin_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_term    = in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) && in_array( $screen->taxonomy, silver_faq_taxonomies(), true );
	$is_product = in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'product' === $screen->post_type;

	if ( ! $is_term && ! $is_product ) {
		return;
	}

	$file = '/assets/js/admin/silver-faq.js';
	wp_enqueue_script( 'silver-faq-admin', get_template_directory_uri() . $file, array( 'jquery', 'jquery-ui-sortable' ), filemtime( get_template_directory() . $file ), true );
	wp_add_inline_style( 'common', '.silver-faq-sources{display:flex;flex-wrap:wrap;gap:6px 20px;margin:0 0 10px}.silver-faq-sources label{display:inline-flex;align-items:center;gap:4px;margin:0}.silver-faq-panel[hidden]{display:none}.silver-faq-panel select{max-width:100%;min-width:280px}.silver-faq-row{display:flex;gap:8px;align-items:flex-start;padding:10px;margin:0 0 8px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px}.silver-faq-row__fields{flex:1;display:grid;gap:6px}.silver-faq-row__fields input{font-weight:600}.silver-faq-row__actions{display:flex;flex-direction:column;gap:2px}.silver-faq-row__actions .button-link{padding:2px;text-decoration:none}.silver-faq-row:first-child .silver-faq-up,.silver-faq-row:last-child .silver-faq-down{visibility:hidden}.form-field .silver-faq-fields input[type=radio]{width:auto}.silver-faq-heading{margin-top:0}.silver-faq-row__handle{flex:none;margin-top:6px;color:#8c8f94;cursor:grab;touch-action:none}.silver-faq-row__handle:hover{color:#1d2327}.silver-faq-row.ui-sortable-helper{box-shadow:0 6px 16px rgba(0,0,0,.15);background:#fff}.silver-faq-row.ui-sortable-helper .silver-faq-row__handle{cursor:grabbing}.silver-faq-placeholder{margin:0 0 8px;border:2px dashed #c3c4c7;border-radius:4px;background:#f0f6fc;visibility:visible!important}' );
}
add_action( 'admin_enqueue_scripts', 'silver_faq_admin_enqueue' );

/*
 * Front end.
 */

/**
 * The accordion of the questions. Adds them to the FAQPage JSON-LD of the page.
 *
 * @param array  $items       List of {question, answer}.
 * @param string $context     "term" or "product".
 * @param bool   $with_schema Whether to add the questions to the FAQ schema.
 *
 * @return string HTML.
 */
function silver_faq_html( $items, $context, $with_schema = true ) {
	if ( ! $items ) {
		return '';
	}

	$title = apply_filters( 'silver_faq_title', __( 'Frequently asked questions', 'silver' ), $context );
	$html  = '';

	foreach ( array_values( $items ) as $index => $item ) {
		$id      = 'silver-faq-' . $context . '-' . ( $index + 1 );
		$is_open = 0 === $index;
		$answer  = wpautop( wptexturize( $item['answer'] ) );

		$html .= '<div class="accordion-item" id="' . esc_attr( $id ) . '">'
			. '<a class="accordion-title plain' . ( $is_open ? ' active' : '' ) . '" id="' . esc_attr( $id ) . '-label" href="#' . esc_attr( $id ) . '" role="button" aria-expanded="' . ( $is_open ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $id ) . '-content">'
			. '<span class="silver-faq__question">' . esc_html( $item['question'] ) . '</span>'
			. '<span class="silver-faq__icon" aria-hidden="true">' . get_silver_icon( 'icon-angle-down' ) . '</span>'
			. '</a>'
			. '<div class="accordion-inner" id="' . esc_attr( $id ) . '-content" role="region" aria-labelledby="' . esc_attr( $id ) . '-label"' . ( $is_open ? ' style="display: block;"' : '' ) . '>'
			. wp_kses_post( $answer )
			. '</div>'
			. '</div>';

		if ( $with_schema ) {
			silver_faq_add_schema( array(
				'question' => $item['question'],
				'answer'   => trim( $answer ),
			) );
		}
	}

	return '<div class="silver-faq">'
		. ( $title ? '<h2 class="silver-faq__title">' . esc_html( $title ) . '</h2>' : '' )
		. '<div class="accordion silver-faq__list">' . $html . '</div>'
		. '</div>';
}

/**
 * Adds a question to the FAQPage JSON-LD of the page, the one of the
 * accordions with "FAQ schema" (one FAQPage per page).
 *
 * @param array $item {question, answer}.
 */
function silver_faq_add_schema( $item ) {
	if ( ! apply_filters( 'silver_faq_schema', true, $item ) ) {
		return;
	}

	$GLOBALS['silver_accordion_faq_schema']   = isset( $GLOBALS['silver_accordion_faq_schema'] ) ? (array) $GLOBALS['silver_accordion_faq_schema'] : array();
	$GLOBALS['silver_accordion_faq_schema'][] = array(
		'question' => $item['question'],
		'answer'   => $item['answer'],
	);

	if ( ! function_exists( 'silver_print_faq_schema' ) ) {
		return;
	}
	if ( ! has_action( 'wp_footer', 'silver_print_faq_schema' ) ) {
		add_action( 'wp_footer', 'silver_print_faq_schema' );
	}
}

/** Product category: a section of its own, under the description. */
function silver_faq_term_section() {
	if ( ! function_exists( 'is_product_taxonomy' ) || ! is_product_taxonomy() || 0 !== absint( get_query_var( 'paged' ) ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || ! in_array( $term->taxonomy, silver_faq_taxonomies(), true ) ) {
		return;
	}

	$faq  = silver_faq_value( get_term_meta( $term->term_id, SILVER_FAQ_META, true ) );
	$html = silver_faq_html( silver_get_term_faq( $term ), 'term', silver_faq_prints_schema( $faq, $term ) );
	if ( '' === $html ) {
		return;
	}

	echo '<section class="silver-faq-section silver-faq-section--term"><div class="container">' . $html . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_faq_html().
}
// woocommerce/archive-product.php, after the category layout; the description is at 10.
add_action( 'silver_after_category_layout', 'silver_faq_term_section', 20 );

/** Product: the FAQs as a section (not hooked any more: the FAQ tab shows them). */
function silver_faq_product_section() {
	if ( ! is_product() ) {
		return;
	}

	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return;
	}

	$faq  = silver_faq_value( $product->get_meta( SILVER_FAQ_META, true ) );
	$html = silver_faq_html( silver_get_product_faq( $product ), 'product', silver_faq_prints_schema( $faq, $product ) );
	if ( '' === $html ) {
		return;
	}

	echo '<section class="silver-faq-section silver-faq-section--product">' . $html . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_faq_html().
}

/**
 * Product: the FAQs in a tab of their own, "Câu hỏi thường gặp", after the
 * specifications and before the reviews (silver_faq_product_section() is
 * kept for templates that print the section themselves).
 *
 * @param array $tabs Product tabs.
 *
 * @return array
 */
function silver_faq_product_tab( $tabs ) {
	global $product;

	if ( ! $product instanceof WC_Product || ! silver_get_product_faq( $product ) ) {
		return $tabs;
	}

	$tabs['silver_faq'] = array(
		'title'    => __( 'Frequently asked questions', 'silver' ),
		'priority' => 25,
		'callback' => 'silver_faq_product_tab_content',
	);

	return $tabs;
}
// Before woocommerce_sort_product_tabs (99), so the priority places it.
add_filter( 'woocommerce_product_tabs', 'silver_faq_product_tab', 98 );

/** Content of the FAQ tab: the accordion, without its title (the tab's). */
function silver_faq_product_tab_content() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$faq = silver_faq_value( $product->get_meta( SILVER_FAQ_META, true ) );

	add_filter( 'silver_faq_title', '__return_empty_string', 99 );
	$html = silver_faq_html( silver_get_product_faq( $product ), 'product', silver_faq_prints_schema( $faq, $product ) );
	remove_filter( 'silver_faq_title', '__return_empty_string', 99 );

	echo '<div class="silver-faq-tab">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_faq_html().
}
