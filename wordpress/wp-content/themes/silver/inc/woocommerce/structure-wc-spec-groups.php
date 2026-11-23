<?php
/**
 * Specification groups ("Thông số kỹ thuật").
 *
 * - Product category: a repeater of groups, each a label ("Bộ xử lý") and
 *   global attributes (select2, in the order chosen).
 * - Product page: the "Additional information" tab becomes "Thông số kỹ
 *   thuật" and lists the groups of the product's category, in order, with
 *   the chosen attributes only (rows without a value and empty groups left
 *   out).
 * - A category without groups takes the ones of its parent; a product in
 *   several categories uses the deepest category that has groups; no groups
 *   anywhere: the WooCommerce table stays.
 *
 * Reuses silver_card_attrs_choices() / silver_card_attrs_sanitize()
 * (structure-wc-card-attributes.php).
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/** Term meta of product_cat: list of array( label, attributes ). */
const SILVER_SPEC_GROUPS_META = '_silver_spec_groups';

/**
 * Clean groups: a label, known attributes in their order; groups without
 * attributes are left out.
 *
 * @param mixed $groups Submitted or saved value.
 *
 * @return array
 */
function silver_spec_groups_sanitize( $groups ) {
	$clean = array();

	foreach ( is_array( $groups ) ? $groups : array() as $group ) {
		if ( ! is_array( $group ) ) {
			continue;
		}

		$attributes = function_exists( 'silver_card_attrs_sanitize' ) ? silver_card_attrs_sanitize( isset( $group['attributes'] ) ? (array) $group['attributes'] : array() ) : array();
		if ( ! $attributes ) {
			continue;
		}

		$clean[] = array(
			'label'      => isset( $group['label'] ) ? sanitize_text_field( (string) $group['label'] ) : '',
			'attributes' => $attributes,
		);
	}

	return $clean;
}

/* ------------------------------------------------------------- Resolution */

/**
 * Groups of a product category, from the category or its nearest parent that
 * has some.
 *
 * @param int $term_id Term ID.
 *
 * @return array|null Null when no category up the chain has groups.
 */
function silver_spec_groups_for_term( $term_id ) {
	static $cache = array();

	$term_id = (int) $term_id;
	if ( array_key_exists( $term_id, $cache ) ) {
		return $cache[ $term_id ];
	}

	$result  = null;
	$current = $term_id;
	$seen    = array();

	while ( $current && ! isset( $seen[ $current ] ) ) {
		$seen[ $current ] = true;

		$groups = silver_spec_groups_sanitize( get_term_meta( $current, SILVER_SPEC_GROUPS_META, true ) );
		if ( $groups ) {
			$result = $groups;
			break;
		}

		$term    = get_term( $current, 'product_cat' );
		$current = ( $term && ! is_wp_error( $term ) ) ? (int) $term->parent : 0;
	}

	$cache[ $term_id ] = $result;

	return $result;
}

/**
 * Groups of a product: the deepest of its categories that has groups (its
 * own or its parents').
 *
 * @param WC_Product|int $product Product.
 *
 * @return array|null Null when none of its categories has groups.
 */
function silver_spec_groups_for_product( $product ) {
	$product = wc_get_product( $product );
	if ( ! $product ) {
		return null;
	}

	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	$terms      = get_the_terms( $product_id, 'product_cat' );
	$result     = null;

	if ( $terms && ! is_wp_error( $terms ) ) {
		$depth = array();
		foreach ( $terms as $term ) {
			$depth[ $term->term_id ] = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
		}
		arsort( $depth );

		foreach ( array_keys( $depth ) as $term_id ) {
			$result = silver_spec_groups_for_term( $term_id );
			if ( null !== $result ) {
				break;
			}
		}
	}

	/**
	 * Filters the specification groups of a product.
	 *
	 * @param array|null $result  Groups (label, attributes), null for none.
	 * @param WC_Product $product Product.
	 */
	return apply_filters( 'silver_spec_groups', $result, $product );
}

/**
 * Rows to print: groups with the attributes the product has values for.
 *
 * @param WC_Product $product Product.
 *
 * @return array|null List of array( label, rows: list of array( taxonomy,
 *                    label, value, values ) ); null when the product has no
 *                    groups.
 */
function silver_spec_groups_rows( $product ) {
	$groups = silver_spec_groups_for_product( $product );
	if ( null === $groups ) {
		return null;
	}

	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	$output     = array();

	foreach ( $groups as $group ) {
		$rows = array();

		foreach ( $group['attributes'] as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$names = array_values( array_filter( (array) wc_get_product_terms( $product_id, $taxonomy, array( 'fields' => 'names' ) ) ) );
			if ( ! $names ) {
				continue;
			}

			$rows[] = array(
				'taxonomy' => $taxonomy,
				'label'    => function_exists( 'silver_attribute_frontend_label' ) ? silver_attribute_frontend_label( $taxonomy ) : wc_attribute_label( $taxonomy ),
				'value'    => implode( ', ', $names ),
				'values'   => $names,
			);
		}

		if ( $rows ) {
			$output[] = array(
				'label' => $group['label'],
				'rows'  => $rows,
			);
		}
	}

	return $output;
}

/* ---------------------------------------------------------------- Output */

/**
 * The "Additional information" tab becomes "Thông số kỹ thuật" when the
 * product's category has groups and the product has values for them.
 *
 * @param array $tabs Product tabs.
 *
 * @return array
 */
function silver_spec_groups_product_tab( $tabs ) {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}

	$groups = silver_spec_groups_rows( $product );
	if ( ! $groups ) {
		return $tabs;
	}

	$tab             = isset( $tabs['additional_information'] ) ? $tabs['additional_information'] : array( 'priority' => 20 );
	$tab['title']    = __( 'Thông số kỹ thuật', 'silver' );
	$tab['callback'] = 'silver_spec_groups_tab';

	$tabs['additional_information'] = $tab;

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'silver_spec_groups_product_tab', 98 );

/**
 * Content of the tab: one card per group, label above value in each row.
 */
function silver_spec_groups_tab() {
	global $product;

	$groups = $product instanceof WC_Product ? silver_spec_groups_rows( $product ) : array();
	if ( ! $groups ) {
		return;
	}

	echo '<div class="spec-groups">';
	foreach ( $groups as $index => $group ) {
		$heading_id = 'spec-group-' . ( $index + 1 );

		echo '<section class="spec-group"' . ( '' !== $group['label'] ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : '' ) . '>';
		if ( '' !== $group['label'] ) {
			echo '<h3 class="spec-group__title" id="' . esc_attr( $heading_id ) . '">' . esc_html( $group['label'] ) . '</h3>';
		}
		echo '<dl class="spec-group__list">';
		foreach ( $group['rows'] as $row ) {
			echo '<div class="spec-group__row spec-group__row--' . esc_attr( sanitize_html_class( $row['taxonomy'] ) ) . '">';
			echo '<dt class="spec-group__label">' . esc_html( $row['label'] ) . '</dt>';
			// Several values: one per line, as a list.
			if ( count( $row['values'] ) > 1 ) {
				echo '<dd class="spec-group__value"><ul class="spec-group__values">';
				foreach ( $row['values'] as $value ) {
					echo '<li>' . esc_html( $value ) . '</li>';
				}
				echo '</ul></dd>';
			} else {
				echo '<dd class="spec-group__value">' . esc_html( $row['value'] ) . '</dd>';
			}
			echo '</div>';
		}
		echo '</dl></section>';
	}
	echo '</div>';
}

/* ----------------------------------------------------------------- Admin */

/**
 * One row of the repeater.
 *
 * @param int|string $index   Row index.
 * @param array      $group   array( label, attributes ).
 * @param array      $choices Taxonomy => label.
 */
function silver_spec_groups_row_html( $index, $group, $choices ) {
	$name     = 'silver_spec_groups[' . $index . ']';
	$selected = isset( $group['attributes'] ) ? (array) $group['attributes'] : array();
	// The chosen attributes first, in their saved order: select2 shows and
	// posts them in the order of the options.
	$ordered = array_merge( array_intersect_key( array_flip( $selected ), $choices ), $choices );
	?>
	<div class="silver-spec-row">
		<span class="silver-spec-row__handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Kéo để sắp xếp', 'silver' ); ?>" aria-hidden="true"></span>
		<div class="silver-spec-row__fields">
			<input type="text" class="widefat" name="<?php echo esc_attr( $name ); ?>[label]" value="<?php echo esc_attr( isset( $group['label'] ) ? $group['label'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Nhãn nhóm, ví dụ: Bộ xử lý', 'silver' ); ?>" aria-label="<?php esc_attr_e( 'Nhãn nhóm', 'silver' ); ?>" />
			<select class="silver-spec-row__attributes silver-ordered-select" name="<?php echo esc_attr( $name ); ?>[attributes][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Chọn thuộc tính theo thứ tự hiển thị', 'silver' ); ?>" aria-label="<?php esc_attr_e( 'Thuộc tính', 'silver' ); ?>" style="width:100%">
				<?php foreach ( $ordered as $taxonomy => $label ) : ?>
					<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php selected( in_array( $taxonomy, $selected, true ) ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button type="button" class="button-link button-link-delete silver-spec-row__remove" aria-label="<?php esc_attr_e( 'Xoá nhóm', 'silver' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
	</div>
	<?php
}

/**
 * The repeater.
 *
 * @param array $groups Saved groups.
 */
function silver_spec_groups_fields( $groups ) {
	$choices = function_exists( 'silver_card_attrs_choices' ) ? silver_card_attrs_choices() : array();
	$groups  = silver_spec_groups_sanitize( $groups );

	wp_nonce_field( 'silver_spec_groups', 'silver_spec_groups_nonce' );

	if ( ! $choices ) {
		echo '<p class="description">';
		printf(
			/* translators: %s: link to Products > Attributes */
			esc_html__( 'Chưa có thuộc tính nào. Tạo thuộc tính tại %s.', 'silver' ),
			'<a href="' . esc_url( admin_url( 'edit.php?post_type=product&page=product_attributes' ) ) . '">' . esc_html__( 'Sản phẩm > Thuộc tính', 'silver' ) . '</a>'
		);
		echo '</p>';
		return;
	}
	?>
	<div class="silver-spec-groups">
		<div class="silver-spec-groups__rows" data-next="<?php echo esc_attr( count( $groups ) ); ?>">
			<?php
			foreach ( $groups as $index => $group ) {
				silver_spec_groups_row_html( $index, $group, $choices );
			}
			?>
		</div>
		<p><button type="button" class="button silver-spec-groups__add"><?php esc_html_e( 'Thêm nhóm', 'silver' ); ?></button></p>
		<template class="silver-spec-groups__template"><?php silver_spec_groups_row_html( '__i__', array(), $choices ); ?></template>
	</div>
	<?php
}

/** Field on Products > Categories > Add new. */
function silver_spec_groups_term_add_field() {
	?>
	<div class="form-field term-silver-spec-groups-wrap">
		<label><?php esc_html_e( 'Nhóm thông số kỹ thuật', 'silver' ); ?></label>
		<?php silver_spec_groups_fields( array() ); ?>
		<p><?php esc_html_e( 'Tab "Thông số kỹ thuật" của sản phẩm chỉ hiện các nhóm và thuộc tính chọn ở đây, theo đúng thứ tự (kéo để đổi). Để trống: kế thừa danh mục cha.', 'silver' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'silver_spec_groups_term_add_field', 30 );

/**
 * Field on the edit screen of a category.
 *
 * @param WP_Term $term Term.
 */
function silver_spec_groups_term_edit_field( $term ) {
	?>
	<tr class="form-field term-silver-spec-groups-wrap">
		<th scope="row"><?php esc_html_e( 'Nhóm thông số kỹ thuật', 'silver' ); ?></th>
		<td>
			<?php silver_spec_groups_fields( get_term_meta( $term->term_id, SILVER_SPEC_GROUPS_META, true ) ); ?>
			<p class="description"><?php esc_html_e( 'Tab "Thông số kỹ thuật" của sản phẩm chỉ hiện các nhóm và thuộc tính chọn ở đây, theo đúng thứ tự (kéo để đổi). Để trống: kế thừa danh mục cha.', 'silver' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'silver_spec_groups_term_edit_field', 30 );

/**
 * Saves the groups.
 *
 * @param int $term_id Term ID.
 */
function silver_spec_groups_term_save( $term_id ) {
	if ( ! isset( $_POST['silver_spec_groups_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_spec_groups_nonce'] ) ), 'silver_spec_groups' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	// Rows are posted in the order of the page (dragged), keys may skip.
	$groups = silver_spec_groups_sanitize( isset( $_POST['silver_spec_groups'] ) ? array_values( (array) wp_unslash( $_POST['silver_spec_groups'] ) ) : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized here.

	if ( $groups ) {
		update_term_meta( $term_id, SILVER_SPEC_GROUPS_META, $groups );
	} else {
		delete_term_meta( $term_id, SILVER_SPEC_GROUPS_META );
	}
}
add_action( 'created_product_cat', 'silver_spec_groups_term_save' );
add_action( 'edited_product_cat', 'silver_spec_groups_term_save' );

/**
 * Script and styles of the repeater (product category screens).
 *
 * @param string $hook Admin page.
 */
function silver_spec_groups_admin_enqueue( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) || 'product_cat' !== $screen->taxonomy ) {
		return;
	}

	$file = '/assets/js/admin/silver-spec-groups.js';
	wp_enqueue_script( 'silver-spec-groups', get_template_directory_uri() . $file, array( 'jquery', 'jquery-ui-sortable', 'silver-ordered-select' ), filemtime( get_template_directory() . $file ), true );
	wp_add_inline_style( 'common', '.silver-spec-groups{max-width:720px}.silver-spec-row{display:flex;gap:8px;align-items:flex-start;padding:10px;margin:0 0 8px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px}.silver-spec-row__fields{flex:1;min-width:0;display:grid;gap:6px}.silver-spec-row__fields input{font-weight:600}.silver-spec-row__handle{flex:none;margin-top:6px;color:#8c8f94;cursor:grab}.silver-spec-row__remove{flex:none;margin-top:4px;padding:2px;text-decoration:none}.silver-spec-row.ui-sortable-helper{background:#fff;box-shadow:0 6px 16px rgba(0,0,0,.15)}.silver-spec-placeholder{margin:0 0 8px;border:2px dashed #c3c4c7;border-radius:4px;background:#f0f6fc;visibility:visible!important}.silver-spec-row .select2-selection__choice{cursor:move}.form-field .silver-spec-row input[type=text]{width:100%}' );
}
add_action( 'admin_enqueue_scripts', 'silver_spec_groups_admin_enqueue' );
