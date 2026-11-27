<?php

/**
 * Posts archive list.
 *
 * @package          Silver\Templates
 * @silver-version 3.18.0
 */

// A shared item style overrides presentation while retaining the archive layout.
$item_style = get_theme_mod('blog_post_item_style', '');
if (in_array($item_style, array('none', 'default', 'normal', 'overlay', 'shade', 'vertical', 'label', 'push', 'badge', 'bounce'), true)) {
	get_template_part('template-parts/posts/archive', 'items', array('layout' => 'list', 'style' => $item_style));
	return;
}

if (have_posts()) : ?>
	<div id="post-list">
		<?php echo silver_blog_list_title_html(); // Blog Layout > Posts list title (H2). ?>
		<?php
		// The posts of the main query, shown without a second query.
		echo silver_apply_shortcode('blog_posts', array(
			'type'        => 'row',
			'image_width' => '40',
			'depth'       => get_theme_mod('blog_posts_depth', 0),
			'depth_hover' => get_theme_mod('blog_posts_depth_hover', 0),
			'text_align'  => get_theme_mod('blog_posts_title_align', 'center'),
			'style'       => 'vertical',
			'columns'     => '1',
			'show_date'   => get_theme_mod('blog_badge', 1) ? 'true' : 'false',
			'query'       => $GLOBALS['wp_query'],
		));
		?>

		<?php silver_posts_pagination(); ?>
	</div>
<?php else : ?>

	<?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>