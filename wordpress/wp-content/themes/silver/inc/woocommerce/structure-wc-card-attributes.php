<?php
/**
 * Product attributes on product cards ("Core i7 · 16GB · 512GB").
 *
 * Which global attributes (Products > Attributes) a card shows is set on:
 * - a product category (Products > Categories): Inherit (default) takes the
 *   rule of the parent category; Custom uses the attributes ticked there.
 * - a product (Product data > Product card): Inherit (default) takes the rule
 *   of its categories; Custom uses the attributes ticked there.
 * Custom with nothing ticked hides the attributes. With no rule anywhere up
 * the chain, nothing is shown.
 *
 * A product in several categories uses the deepest category that has a rule
 * (its own or inherited from its parents).
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/** Meta keys, on product_cat terms and on products. */
const SILVER_CARD_ATTRS_MODE = '_silver_card_attrs_mode'; // inherit / custom
const SILVER_CARD_ATTRS_LIST = '_silver_card_attrs';      // array of taxonomy names (pa_*)

/**
 * Global attributes that can be chosen: taxonomy name => label.
 *
 * @return array
 */
function silver_card_attrs_choices() {
	$choices = array();
	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		$choices[ wc_attribute_taxonomy_name( $attribute->attribute_name ) ] = $attribute->attribute_label ? $attribute->attribute_label : $attribute->attribute_name;
	}
	return $choices;
}

/**
 * Clean a submitted list: known attribute taxonomies only, in the order given.
 *
 * @param mixed $list Submitted value.
 * @return array
 */
function silver_card_attrs_sanitize( $list ) {
	$choices = silver_card_attrs_choices();
	$clean   = array();
	foreach ( (array) $list as $taxonomy ) {
		$taxonomy = sanitize_title( wp_unslash( $taxonomy ) );
		if ( isset( $choices[ $taxonomy ] ) && ! in_array( $taxonomy, $clean, true ) ) {
			$clean[] = $taxonomy;
		}
	}
	return $clean;
}

/* ------------------------------------------------------------- Resolution */

/**
 * Rule of a product category, following Inherit up to the parents.
 *
 * @param int $term_id Term ID.
 * @return array|null Attribute taxonomies (empty: hide), or null when no
 *                    category up the chain has a rule.
 */
function silver_card_attrs_for_term( $term_id ) {
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

		if ( 'custom' === get_term_meta( $current, SILVER_CARD_ATTRS_MODE, true ) ) {
			$result = array_values( array_filter( (array) get_term_meta( $current, SILVER_CARD_ATTRS_LIST, true ) ) );
			break;
		}

		$term    = get_term( $current, 'product_cat' );
		$current = ( $term && ! is_wp_error( $term ) ) ? (int) $term->parent : 0;
	}

	$cache[ $term_id ] = $result;
	return $result;
}

/**
 * Attribute taxonomies a product card shows.
 *
 * @param WC_Product|int $product Product (a variation uses its parent).
 * @return array
 */
function silver_card_attrs_for_product( $product ) {
	$product = wc_get_product( $product );
	if ( ! $product ) {
		return array();
	}

	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

	if ( 'custom' === get_post_meta( $product_id, SILVER_CARD_ATTRS_MODE, true ) ) {
		return array_values( array_filter( (array) get_post_meta( $product_id, SILVER_CARD_ATTRS_LIST, true ) ) );
	}

	$rule = silver_card_attrs_from_categories( $product_id );
	return null === $rule ? array() : $rule;
}

/**
 * Rule a product inherits from its categories: the deepest category that has
 * a rule (its own or one of its parents').
 *
 * @param int $product_id Product ID.
 * @return array|null Null when no category has a rule.
 */
function silver_card_attrs_from_categories( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	$depth = array();
	foreach ( $terms as $term ) {
		$depth[ $term->term_id ] = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
	}
	arsort( $depth );

	foreach ( array_keys( $depth ) as $term_id ) {
		$rule = silver_card_attrs_for_term( $term_id );
		if ( null !== $rule ) {
			return $rule;
		}
	}

	return null;
}

/**
 * Attribute groups to show, in the order of the attributes chosen. Each
 * attribute is one chip, with all its values ("Core i5 / Core i7"), so values
 * of the same attribute read as one group. The name is shown in front
 * ("CPU  Core i5 / Core i7") for an attribute set to show it on cards
 * (Products > Attributes).
 *
 * @param WC_Product $product Product.
 * @return array List of array( taxonomy, name (display name, or '' when not
 *               shown), label (display name, always), values (term names) ).
 */
function silver_card_attrs_values( $product ) {
	$groups = array();
	$id     = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

	foreach ( silver_card_attrs_for_product( $product ) as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		$names = array_values( array_filter( (array) wc_get_product_terms( $id, $taxonomy, array( 'fields' => 'names' ) ) ) );
		if ( ! $names ) {
			continue;
		}

		$has_meta = function_exists( 'silver_attribute_meta_by_taxonomy' );
		$label    = $has_meta ? silver_attribute_frontend_label( $taxonomy ) : wc_attribute_label( $taxonomy );
		$show     = $has_meta && silver_attribute_meta_by_taxonomy( $taxonomy )['card_label'];

		$groups[] = array(
			'taxonomy' => $taxonomy,
			'name'     => $show ? $label : '',
			'label'    => $label,
			'values'   => $names,
		);
	}

	/**
	 * Filters the attribute groups shown on a product card.
	 *
	 * @param array      $groups  See silver_card_attrs_values().
	 * @param WC_Product $product Product.
	 */
	return (array) apply_filters( 'silver_card_attributes', $groups, $product );
}

/* --------------------------------------------------------------- Settings */

/** Styles of the attribute block. */
const SILVER_CARD_ATTRS_STYLES = array( 'pill', 'outline', 'tag', 'plain' );

/**
 * Overrides of a product shortcode while it renders its cards (a stack, for
 * a shortcode inside another one).
 *
 * @param array|null $override Override to push, or null to pop the last one.
 * @return array Current stack.
 */
function silver_card_attrs_override( $override = null ) {
	static $stack = array();

	if ( is_array( $override ) ) {
		$stack[] = $override;
	} elseif ( false === $override ) {
		array_pop( $stack );
	}

	return $stack;
}

/**
 * Display settings of the attribute block: Theme Options (Shop > Product
 * Catalog), overridden by the product shortcode being rendered.
 *
 * @return array show (bool), style (pill / outline / tag / plain), max (int).
 */
function silver_card_attrs_settings() {
	$settings = array(
		'show'   => (bool) get_theme_mod( 'product_box_attrs', 1 ),
		'style'  => get_theme_mod( 'product_box_attrs_style', 'pill' ),
		'max'    => (int) get_theme_mod( 'product_box_attrs_max', 3 ),
		'bg'     => get_theme_mod( 'product_box_attrs_bg', '' ),
		'color'  => get_theme_mod( 'product_box_attrs_color', '' ),
		'border' => get_theme_mod( 'product_box_attrs_border', '' ),
	);

	$stack = silver_card_attrs_override();
	if ( $stack ) {
		foreach ( end( $stack ) as $key => $value ) {
			if ( null !== $value && '' !== $value ) {
				$settings[ $key ] = $value;
			}
		}
	}

	$settings['style'] = in_array( $settings['style'], SILVER_CARD_ATTRS_STYLES, true ) ? $settings['style'] : 'pill';
	foreach ( array( 'bg', 'color', 'border' ) as $key ) {
		$settings[ $key ] = silver_card_attrs_sanitize_color( $settings[ $key ] );
	}

	/**
	 * Most values shown in one chip; the rest are counted ("+2").
	 *
	 * @param int $max Number of values.
	 */
	$settings['max'] = max( 1, (int) apply_filters( 'silver_card_attributes_max_values', $settings['max'] ) );

	return $settings;
}

/**
 * Override from the attributes of a product shortcode: show_attrs (true /
 * false / '' = Theme Options), attrs_style ('' = Theme Options), attrs_max.
 *
 * @param array $atts Shortcode attributes.
 * @return array
 */
function silver_card_attrs_override_from_atts( $atts ) {
	$show = isset( $atts['show_attrs'] ) ? (string) $atts['show_attrs'] : '';
	$max  = isset( $atts['attrs_max'] ) ? (int) $atts['attrs_max'] : 0;

	return array(
		'show'   => '' === $show ? null : in_array( $show, array( 'true', '1', 'yes' ), true ),
		'style'  => isset( $atts['attrs_style'] ) && in_array( $atts['attrs_style'], SILVER_CARD_ATTRS_STYLES, true ) ? $atts['attrs_style'] : null,
		'max'    => $max > 0 ? $max : null,
		'bg'     => isset( $atts['attrs_bg'] ) ? silver_card_attrs_sanitize_color( $atts['attrs_bg'] ) : null,
		'color'  => isset( $atts['attrs_color'] ) ? silver_card_attrs_sanitize_color( $atts['attrs_color'] ) : null,
		'border' => isset( $atts['attrs_border'] ) ? silver_card_attrs_sanitize_color( $atts['attrs_border'] ) : null,
	);
}

/**
 * A CSS colour from an option: hex, rgb(a), hsl(a) or transparent.
 *
 * @param mixed $color Value.
 * @return string The colour, or '' when it is empty or not a colour.
 */
function silver_card_attrs_sanitize_color( $color ) {
	$color = trim( (string) $color );
	if ( '' === $color ) {
		return '';
	}
	if ( preg_match( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color )
		|| preg_match( '/^(rgba?|hsla?)\(\s*[0-9.,%\s\/]+\)$/i', $color )
		|| 'transparent' === strtolower( $color ) ) {
		return $color;
	}
	return '';
}

/**
 * Inline style with the colour variables of the attribute block.
 *
 * @param array $settings See silver_card_attrs_settings().
 * @return string ' style="..."' or ''.
 */
function silver_card_attrs_style_attr( $settings ) {
	$vars = array();
	foreach ( array( 'bg' => '--card-attrs-bg', 'color' => '--card-attrs-color', 'border' => '--card-attrs-border' ) as $key => $var ) {
		if ( '' !== $settings[ $key ] ) {
			$vars[] = $var . ':' . $settings[ $key ];
		}
	}
	return $vars ? ' style="' . esc_attr( implode( ';', $vars ) ) . '"' : '';
}

/* ----------------------------------------------------------------- Output */

/**
 * Print the attribute chips of the current product card, after the rating
 * (priority 5). In product boxes silver_product_box_price_wrapper() prints
 * them outside (before) the price wrapper.
 */
function silver_card_attrs_output() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$settings = silver_card_attrs_settings();
	if ( ! $settings['show'] ) {
		return;
	}

	$groups = silver_card_attrs_values( $product );
	if ( ! $groups ) {
		return;
	}

	$max = $settings['max'];

	echo '<ul class="product-card-attrs product-card-attrs--' . esc_attr( $settings['style'] ) . '"' . silver_card_attrs_style_attr( $settings ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	foreach ( $groups as $group ) {
		$values = (array) $group['values'];
		$shown  = array_slice( $values, 0, $max );
		$more   = count( $values ) - count( $shown );

		// The full list for the tooltip and screen readers: "CPU: Core i5, Core i7".
		/* translators: 1: attribute name, 2: its values */
		$full = sprintf( __( '%1$s: %2$s', 'silver' ), $group['label'], implode( ', ', $values ) );

		printf( '<li class="product-card-attr product-card-attr--%1$s" title="%2$s">', esc_attr( sanitize_html_class( $group['taxonomy'] ) ), esc_attr( $full ) );
		echo '<span class="screen-reader-text">' . esc_html( $full ) . '</span><span class="product-card-attr__body" aria-hidden="true">';
		if ( '' !== $group['name'] ) {
			echo '<span class="product-card-attr__name">' . esc_html( $group['name'] ) . '</span>';
		}
		foreach ( $shown as $i => $value ) {
			if ( $i ) {
				echo '<span class="product-card-attr__sep">/</span>';
			}
			echo '<span class="product-card-attr__value">' . esc_html( $value ) . '</span>';
		}
		if ( $more > 0 ) {
			echo '<span class="product-card-attr__more">+' . (int) $more . '</span>';
		}
		echo '</span></li>';
	}
	echo '</ul>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'silver_card_attrs_output', 7 );

/* ------------------------------------------------------------ Admin: shared */

/**
 * The mode radios and the attribute checklist.
 *
 * @param string $mode     inherit / custom.
 * @param array  $selected Ticked taxonomies.
 * @param string $inherit  Description of what Inherit gives now.
 */
function silver_card_attrs_fields( $mode, $selected, $inherit ) {
	$choices  = silver_card_attrs_choices();
	$mode     = 'custom' === $mode ? 'custom' : 'inherit';
	$selected = (array) $selected;
	// Ticked attributes first, in their saved order, then the others.
	$ordered = array_merge( array_intersect_key( array_flip( $selected ), $choices ), $choices );

	wp_nonce_field( 'silver_card_attrs', 'silver_card_attrs_nonce' );
	?>
	<div class="silver-card-attrs">
		<p>
			<label style="margin-right:1.5em"><input type="radio" name="silver_card_attrs_mode" value="inherit" <?php checked( $mode, 'inherit' ); ?>> <?php esc_html_e( 'Kế thừa', 'silver' ); ?></label>
			<label><input type="radio" name="silver_card_attrs_mode" value="custom" <?php checked( $mode, 'custom' ); ?>> <?php esc_html_e( 'Cấu hình riêng', 'silver' ); ?></label>
		</p>
		<p class="description silver-card-attrs-inherit"<?php echo 'custom' === $mode ? ' hidden' : ''; ?>><?php echo esc_html( $inherit ); ?></p>
		<div class="silver-card-attrs-list"<?php echo 'inherit' === $mode ? ' hidden' : ''; ?>>
			<?php if ( ! $choices ) : ?>
				<p class="description">
					<?php
					printf(
						/* translators: %s: link to Products > Attributes */
						esc_html__( 'Chưa có thuộc tính nào. Tạo thuộc tính tại %s.', 'silver' ),
						'<a href="' . esc_url( admin_url( 'edit.php?post_type=product&page=product_attributes' ) ) . '">' . esc_html__( 'Sản phẩm > Thuộc tính', 'silver' ) . '</a>'
					);
					?>
				</p>
			<?php else : ?>
				<?php // The order of the ticked items is the order of the chips on product cards. ?>
				<ul class="silver-card-attrs-sortable">
					<?php foreach ( $ordered as $taxonomy => $label ) : ?>
						<li>
							<span class="silver-card-attrs-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Kéo để sắp xếp', 'silver' ); ?>" aria-hidden="true"></span>
							<label><input type="checkbox" name="silver_card_attrs[]" value="<?php echo esc_attr( $taxonomy ); ?>" <?php checked( in_array( $taxonomy, $selected, true ) ); ?>> <?php echo esc_html( $label ); ?></label>
							<span class="silver-card-attrs-move">
								<button type="button" class="button-link" data-move="up" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: attribute */ __( 'Đưa %s lên', 'silver' ), $label ) ); ?>"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
								<button type="button" class="button-link" data-move="down" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: attribute */ __( 'Đưa %s xuống', 'silver' ), $label ) ); ?>"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="description"><?php esc_html_e( 'Kéo thả hoặc dùng nút mũi tên để sắp xếp: thẻ sản phẩm hiển thị theo đúng thứ tự này. Không chọn thuộc tính nào: ẩn thuộc tính trên thẻ sản phẩm.', 'silver' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<script>
		( function () {
			var box = document.currentScript.previousElementSibling;

			// Inherit / Custom.
			box.addEventListener( 'change', function ( e ) {
				if ( e.target.name !== 'silver_card_attrs_mode' ) return;
				var custom = e.target.value === 'custom';
				box.querySelector( '.silver-card-attrs-list' ).hidden = ! custom;
				box.querySelector( '.silver-card-attrs-inherit' ).hidden = custom;
			} );

			var list = box.querySelector( '.silver-card-attrs-sortable' );
			if ( ! list ) return;

			// Up / down buttons (keyboard friendly). The checkboxes are posted in
			// the order of the list, which is the saved order.
			list.addEventListener( 'click', function ( e ) {
				var button = e.target.closest( '[data-move]' );
				if ( ! button ) return;
				e.preventDefault();
				var item = button.closest( 'li' );
				if ( button.dataset.move === 'up' && item.previousElementSibling ) {
					list.insertBefore( item, item.previousElementSibling );
				} else if ( button.dataset.move === 'down' && item.nextElementSibling ) {
					list.insertBefore( item.nextElementSibling, item );
				}
				button.focus();
			} );

			// Drag and drop.
			if ( window.jQuery && jQuery.fn.sortable ) {
				jQuery( list ).sortable( {
					axis: 'y',
					containment: 'parent',
					handle: '.silver-card-attrs-handle',
					items: '> li',
					tolerance: 'pointer'
				} );
			}
		} )();
	</script>
	<?php
	silver_card_attrs_admin_style();
}

/**
 * Styles of the sortable list (printed once).
 */
function silver_card_attrs_admin_style() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	?>
	<style>
		.silver-card-attrs-sortable { margin: 0 0 6px; max-width: 420px; }
		.silver-card-attrs-sortable li { align-items: center; background: #fff; border: 1px solid #dcdcde; border-radius: 4px; display: flex; gap: 8px; margin: 0 0 4px; padding: 4px 8px; }
		.silver-card-attrs-sortable li.ui-sortable-helper { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
		.silver-card-attrs-sortable li.ui-sortable-placeholder { background: #f6f7f7; border-style: dashed; visibility: visible !important; }
		.silver-card-attrs-sortable label { flex: 1 1 auto; margin: 0 !important; }
		.silver-card-attrs-handle { color: #8c8f94; cursor: move; flex: none; }
		.silver-card-attrs-move { display: flex; flex: none; gap: 2px; }
		.silver-card-attrs-move .button-link { color: #50575e; line-height: 1; }
		.silver-card-attrs-move .button-link:hover, .silver-card-attrs-move .button-link:focus { color: #2271b1; }
		.silver-card-attrs-sortable li:first-child [data-move="up"], .silver-card-attrs-sortable li:last-child [data-move="down"] { visibility: hidden; }
	</style>
	<?php
}

/**
 * jQuery UI Sortable on the category and product screens.
 *
 * @param string $hook Admin page.
 */
function silver_card_attrs_admin_scripts( $hook ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'edit-product_cat', 'product' ), true ) ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}
}
add_action( 'admin_enqueue_scripts', 'silver_card_attrs_admin_scripts' );

/**
 * "Inherit" description for a rule: the attribute names, hidden, or none.
 *
 * @param array|null $rule   Rule (null: none up the chain).
 * @param string     $source Where it comes from.
 * @return string
 */
function silver_card_attrs_describe( $rule, $source ) {
	if ( null === $rule ) {
		/* translators: %s: where the rule would come from */
		return sprintf( __( 'Hiện tại: không hiển thị (%s chưa cấu hình).', 'silver' ), $source );
	}
	if ( ! $rule ) {
		/* translators: %s: where the rule comes from */
		return sprintf( __( 'Hiện tại: ẩn (theo %s).', 'silver' ), $source );
	}
	$choices = silver_card_attrs_choices();
	$labels  = array();
	foreach ( $rule as $taxonomy ) {
		$labels[] = isset( $choices[ $taxonomy ] ) ? $choices[ $taxonomy ] : $taxonomy;
	}
	/* translators: 1: attribute names, 2: where the rule comes from */
	return sprintf( __( 'Hiện tại: %1$s (theo %2$s).', 'silver' ), implode( ', ', $labels ), $source );
}

/**
 * Read the submitted mode and list.
 *
 * @return array|null array( mode, list ), or null when the form was not sent.
 */
function silver_card_attrs_submitted() {
	if ( ! isset( $_POST['silver_card_attrs_nonce'], $_POST['silver_card_attrs_mode'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_card_attrs_nonce'] ) ), 'silver_card_attrs' ) ) {
		return null;
	}

	$mode = 'custom' === sanitize_key( wp_unslash( $_POST['silver_card_attrs_mode'] ) ) ? 'custom' : 'inherit';
	$list = isset( $_POST['silver_card_attrs'] ) ? silver_card_attrs_sanitize( (array) $_POST['silver_card_attrs'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	return array( $mode, $list );
}

/* ------------------------------------------------------- Admin: categories */

/**
 * Field on Products > Categories > Add new.
 */
function silver_card_attrs_term_add_field() {
	?>
	<div class="form-field">
		<label><?php esc_html_e( 'Thuộc tính trên thẻ sản phẩm', 'silver' ); ?></label>
		<?php silver_card_attrs_fields( 'inherit', array(), __( 'Kế thừa từ danh mục cha.', 'silver' ) ); ?>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'silver_card_attrs_term_add_field', 20 );

/**
 * Field on the edit screen of a category.
 *
 * @param WP_Term $term Term.
 */
function silver_card_attrs_term_edit_field( $term ) {
	$parent = $term->parent ? silver_card_attrs_for_term( $term->parent ) : null;
	?>
	<tr class="form-field">
		<th scope="row"><label><?php esc_html_e( 'Thuộc tính trên thẻ sản phẩm', 'silver' ); ?></label></th>
		<td>
			<?php
			silver_card_attrs_fields(
				get_term_meta( $term->term_id, SILVER_CARD_ATTRS_MODE, true ),
				get_term_meta( $term->term_id, SILVER_CARD_ATTRS_LIST, true ),
				$term->parent ? silver_card_attrs_describe( $parent, __( 'danh mục cha', 'silver' ) ) : __( 'Danh mục gốc: kế thừa nghĩa là không hiển thị thuộc tính.', 'silver' )
			);
			?>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'silver_card_attrs_term_edit_field', 20 );

/**
 * Save the category field.
 *
 * @param int $term_id Term ID.
 */
function silver_card_attrs_term_save( $term_id ) {
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}
	$submitted = silver_card_attrs_submitted();
	if ( ! $submitted ) {
		return;
	}
	list( $mode, $list ) = $submitted;

	if ( 'custom' === $mode ) {
		update_term_meta( $term_id, SILVER_CARD_ATTRS_MODE, 'custom' );
		update_term_meta( $term_id, SILVER_CARD_ATTRS_LIST, $list );
	} else {
		delete_term_meta( $term_id, SILVER_CARD_ATTRS_MODE );
		delete_term_meta( $term_id, SILVER_CARD_ATTRS_LIST );
	}
}
add_action( 'created_product_cat', 'silver_card_attrs_term_save' );
add_action( 'edited_product_cat', 'silver_card_attrs_term_save' );

/* --------------------------------------------------------- Admin: products */

/**
 * "Product card" tab in Product data.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function silver_card_attrs_product_tab( $tabs ) {
	$tabs['silver_card'] = array(
		'label'    => __( 'Thẻ sản phẩm', 'silver' ),
		'target'   => 'silver_card_attrs_panel',
		'class'    => array(),
		'priority' => 65,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'silver_card_attrs_product_tab' );

/**
 * Content of the "Product card" tab.
 */
function silver_card_attrs_product_panel() {
	global $post;

	$product_id = $post ? $post->ID : 0;
	$mode       = get_post_meta( $product_id, SILVER_CARD_ATTRS_MODE, true );
	$list       = get_post_meta( $product_id, SILVER_CARD_ATTRS_LIST, true );
	// What Inherit gives now (categories as saved; a category changed on this
	// screen counts after saving).
	$inherited = silver_card_attrs_from_categories( $product_id );
	?>
	<div id="silver_card_attrs_panel" class="panel woocommerce_options_panel hidden">
		<div class="options_group" style="padding:0 12px">
			<p><strong><?php esc_html_e( 'Thuộc tính trên thẻ sản phẩm', 'silver' ); ?></strong></p>
			<?php silver_card_attrs_fields( $mode, $list, silver_card_attrs_describe( $inherited, __( 'danh mục', 'silver' ) ) ); ?>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_product_data_panels', 'silver_card_attrs_product_panel' );

/**
 * Save the product field.
 *
 * @param int $product_id Product ID.
 */
function silver_card_attrs_product_save( $product_id ) {
	if ( ! current_user_can( 'edit_product', $product_id ) ) {
		return;
	}
	$submitted = silver_card_attrs_submitted();
	if ( ! $submitted ) {
		return;
	}
	list( $mode, $list ) = $submitted;

	if ( 'custom' === $mode ) {
		update_post_meta( $product_id, SILVER_CARD_ATTRS_MODE, 'custom' );
		update_post_meta( $product_id, SILVER_CARD_ATTRS_LIST, $list );
	} else {
		delete_post_meta( $product_id, SILVER_CARD_ATTRS_MODE );
		delete_post_meta( $product_id, SILVER_CARD_ATTRS_LIST );
	}
}
add_action( 'woocommerce_process_product_meta', 'silver_card_attrs_product_save' );
