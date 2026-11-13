<?php
/**
 * Term thumbnail: an image field (term meta "thumbnail_id") for every public
 * taxonomy, on the Add / Edit term screens, plus an image column in the list.
 *
 * Same meta key as WooCommerce product categories, so the Terms element
 * ([ux_terms], image meta "thumbnail_id") shows it. Taxonomies that already
 * have this field from WooCommerce are skipped.
 *
 * @package silver
 */

defined('ABSPATH') || exit;

/**
 * Taxonomies that get the field.
 *
 * @return string[]
 */
function silver_term_thumbnail_taxonomies()
{
	$taxonomies = get_taxonomies(array('public' => true, 'show_ui' => true));
	unset($taxonomies['post_format']);

	// WooCommerce has its own thumbnail field for these.
	foreach (array('product_cat', 'product_brand') as $woo_taxonomy) {
		unset($taxonomies[$woo_taxonomy]);
	}

	return array_values(apply_filters('silver_term_thumbnail_taxonomies', $taxonomies));
}

/**
 * Field markup (image preview, hidden ID, buttons).
 *
 * @param int $image_id
 */
function silver_term_thumbnail_field($image_id = 0)
{
	$image = $image_id ? wp_get_attachment_image($image_id, 'thumbnail') : '';
	wp_nonce_field('silver_term_thumbnail', 'silver_term_thumbnail_nonce');
	?>
	<div class="silver-term-thumbnail-field">
		<div class="silver-term-thumbnail-preview"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<input type="hidden" name="silver_term_thumbnail_id" class="silver-term-thumbnail-id" value="<?php echo esc_attr($image_id ?: ''); ?>" />
		<button type="button" class="button silver-term-thumbnail-upload"><?php esc_html_e('Chọn ảnh', 'silver'); ?></button>
		<button type="button" class="button silver-term-thumbnail-remove"<?php echo $image ? '' : ' style="display:none"'; ?>><?php esc_html_e('Xoá ảnh', 'silver'); ?></button>
	</div>
	<?php
}

/** Add term screen. */
function silver_term_thumbnail_add_field()
{
	?>
	<div class="form-field term-thumbnail-wrap">
		<label><?php esc_html_e('Thumbnail', 'silver'); ?></label>
		<?php silver_term_thumbnail_field(); ?>
	</div>
	<?php
}

/**
 * Edit term screen.
 *
 * @param WP_Term $term
 */
function silver_term_thumbnail_edit_field($term)
{
	?>
	<tr class="form-field term-thumbnail-wrap">
		<th scope="row"><label><?php esc_html_e('Thumbnail', 'silver'); ?></label></th>
		<td><?php silver_term_thumbnail_field((int) get_term_meta($term->term_id, 'thumbnail_id', true)); ?></td>
	</tr>
	<?php
}

/**
 * Save the field (created_term / edited_term).
 *
 * @param int    $term_id
 * @param int    $tt_id
 * @param string $taxonomy
 */
function silver_term_thumbnail_save($term_id, $tt_id, $taxonomy)
{
	if (! in_array($taxonomy, silver_term_thumbnail_taxonomies(), true)) return;
	if (! isset($_POST['silver_term_thumbnail_nonce'], $_POST['silver_term_thumbnail_id'])) return;
	if (! wp_verify_nonce(sanitize_key(wp_unslash($_POST['silver_term_thumbnail_nonce'])), 'silver_term_thumbnail')) return;
	if (! current_user_can('edit_term', $term_id)) return;

	$image_id = absint($_POST['silver_term_thumbnail_id']);

	if ($image_id && wp_attachment_is_image($image_id)) {
		update_term_meta($term_id, 'thumbnail_id', $image_id);
	} else {
		delete_term_meta($term_id, 'thumbnail_id');
	}
}
add_action('created_term', 'silver_term_thumbnail_save', 10, 3);
add_action('edited_term', 'silver_term_thumbnail_save', 10, 3);

/**
 * Image column in the terms list.
 *
 * @param array $columns
 * @return array
 */
function silver_term_thumbnail_columns($columns)
{
	$new = array();
	foreach ($columns as $key => $label) {
		$new[$key] = $label;
		if ('cb' === $key) $new['silver_thumbnail'] = '<span class="dashicons dashicons-format-image" title="' . esc_attr__('Thumbnail', 'silver') . '"></span>';
	}
	return $new;
}

/**
 * Image column content.
 *
 * @param string $content
 * @param string $column
 * @param int    $term_id
 * @return string
 */
function silver_term_thumbnail_column($content, $column, $term_id)
{
	if ('silver_thumbnail' !== $column) return $content;

	$image_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
	return $image_id ? wp_get_attachment_image($image_id, array(40, 40)) : '';
}

/** Hooks for every taxonomy (registered on init). */
function silver_term_thumbnail_register()
{
	foreach (silver_term_thumbnail_taxonomies() as $taxonomy) {
		add_action("{$taxonomy}_add_form_fields", 'silver_term_thumbnail_add_field');
		add_action("{$taxonomy}_edit_form_fields", 'silver_term_thumbnail_edit_field');
		add_filter("manage_edit-{$taxonomy}_columns", 'silver_term_thumbnail_columns');
		add_filter("manage_{$taxonomy}_custom_column", 'silver_term_thumbnail_column', 10, 3);
	}
}
add_action('admin_init', 'silver_term_thumbnail_register');

/**
 * Media library + script on the terms screens.
 *
 * @param string $hook
 */
function silver_term_thumbnail_enqueue($hook)
{
	if (! in_array($hook, array('edit-tags.php', 'term.php'), true)) return;

	$screen = get_current_screen();
	if (! $screen || ! in_array($screen->taxonomy, silver_term_thumbnail_taxonomies(), true)) return;

	wp_enqueue_media();

	$file = '/assets/js/admin/term-thumbnail.js';
	wp_enqueue_script('silver-term-thumbnail', get_template_directory_uri() . $file, array('jquery'), filemtime(get_template_directory() . $file), true);
	wp_localize_script('silver-term-thumbnail', 'silverTermThumbnail', array(
		'title'  => __('Chọn ảnh thumbnail', 'silver'),
		'button' => __('Dùng ảnh này', 'silver'),
	));

	wp_add_inline_style('common', '.silver-term-thumbnail-preview img{display:block;max-width:150px;height:auto;margin-bottom:8px;border-radius:4px}.column-silver_thumbnail{width:52px}.column-silver_thumbnail img{border-radius:3px}');
}
add_action('admin_enqueue_scripts', 'silver_term_thumbnail_enqueue');
