<?php

/**
 * Shared post item presentation for blog and archive layouts.
 *
 * @package Silver\Templates
 */

if (! have_posts()) {
	get_template_part('template-parts/posts/content', 'none');
	return;
}

$layout = isset($args['layout']) ? $args['layout'] : 'normal';
$style  = isset($args['style']) ? $args['style'] : 'normal';

// Grid: the columns of each screen (archive-grid.php, Blog Layout > Posts per
// row). Normal, Inline and List: one post per row.
if (isset($args['columns']) && is_array($args['columns'])) {
	$column_atts = $args['columns'];
} elseif (in_array($layout, array('grid', '2-col', '3-col'), true)) {
	$column_atts = silver_blog_grid_columns('2-col' === $layout ? 2 : ('3-col' === $layout ? 3 : null));
} else {
	$column_atts = array('columns' => '1');
}
$count = max(array_map('intval', array_filter($column_atts)));

$item_atts = array_merge(array(
	'type'        => $count > 1 ? get_theme_mod('blog_style_type', 'masonry') : 'row',
	'style'       => $style,
	// One column everywhere unless the grid sets more (blog_posts would
	// otherwise default to 4 desktop columns).
	'columns__lg' => '1',
), $column_atts, array(
	'depth'       => get_theme_mod('blog_posts_depth', 0),
	'depth_hover' => get_theme_mod('blog_posts_depth_hover', 0),
	'text_align'  => get_theme_mod('blog_posts_title_align', 'center'),
	'show_date'   => get_theme_mod('blog_badge', 1) ? 'true' : 'false',
	// The posts of the main query, shown without a second query.
	'query'       => $GLOBALS['wp_query'],
));

if ('vertical' === $style) {
	$item_atts['image_width'] = '40';
}
$item_options = require get_template_directory() . '/inc/helpers/blog-post-item-options.php';
foreach ($item_options as $attribute => $field) {
	$value = get_theme_mod('blog_item_' . $attribute, '');
	if ('' === $value || null === $value) continue;
	if (isset($field['choices']) && 'select' === $field['type'] && ! array_key_exists($value, $field['choices'])) continue;
	$item_atts[$attribute] = 'excerpt_length' === $attribute ? max(5, min(100, (int) $value)) : $value;
}
?>
<div id="post-list">
	<?php echo silver_blog_list_title_html(); // Blog Layout > Posts list title (H2). ?>
	<?php echo silver_apply_shortcode('blog_posts', $item_atts); ?>
	<?php silver_posts_pagination(); ?>
</div>