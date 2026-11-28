<?php
/**
 * Posts layout.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

// Blog page, archives and single posts: the category list, the featured posts
// and the posts, or the post (with the sidebar), in one section, which holds
// the top and bottom padding (silver-modern.css, .blog-section); the related
// products section follows it (silver_after_blog).
echo '<section class="blog-section">';

do_action('silver_before_blog');
?>

<?php if(!is_single() && get_theme_mod('blog_featured', '') == 'top'){ get_template_part('template-parts/posts/featured-posts'); } ?>
<?php
// Single post with a table of contents (silver_single_post_toc()): the list
// takes the place of the sidebar, laid out like the sidebar layouts (same
// columns, divider and sidebar width), sticky while the post is read. The row
// is the [ux_toc] layout of silver-toc.js (current section; the bar under the
// header below 850px, where the column is hidden). The post is printed first,
// the list is known once its content is filtered.
if ( is_single() ) {
	ob_start();
	get_template_part( 'template-parts/posts/single' );
	comments_template();
	$silver_post_main = ob_get_clean();
	$silver_side_toc  = function_exists( 'silver_single_post_side_toc' ) ? silver_single_post_side_toc() : '';

	if ( $silver_side_toc ) {
		$silver_blog_divider = get_theme_mod( 'blog_single_layout_divider', get_theme_mod( 'blog_layout_divider', 1 ) );
		// Blog Single Post > Table of contents position. The HTML order stays
		// the same, the post first and the list after it (the main content
		// early in the page, for search engines and screen readers); on the
		// left, .row-reverse (silver.css) only shows the columns the other way
		// round, the divider line with them.
		$silver_toc_left = 'left' === get_theme_mod( 'blog_single_toc_position', 'right' );
		?>
		<div id="post-toc" class="row blog-layout-row ux-toc-layout ux-toc-layout--mobile-bar post-toc-layout<?php if ( $silver_toc_left ) echo ' row-reverse'; ?><?php if ( $silver_blog_divider ) echo ' row-large row-divided'; ?>">
			<div class="large-9 xlarge-9 col ux-toc-layout__content">
				<?php echo $silver_post_main; // phpcs:ignore WordPress.Security.EscapeOutput -- the post template. ?>
			</div>
			<div class="post-sidebar large-3 xlarge-3 col ux-toc-layout__aside">
				<?php echo $silver_side_toc; // phpcs:ignore WordPress.Security.EscapeOutput -- built in silver_single_post_toc(). ?>
			</div>
		</div>
		<?php
	} else {
		echo '<div class="row"><div class="xlarge-12 col">' . $silver_post_main . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the post template.
	}

	echo '</section>';
	do_action( 'silver_after_blog' );
	return;
}
?>
<?php // No sidebar: the posts take the full row (12 of 12 columns). ?>
<div class="row">
	<div class="xlarge-12 col">
	<?php if(!is_single() && get_theme_mod('blog_featured', '') == 'content'){ get_template_part('template-parts/posts/featured-posts'); } ?>

	<?php
		if(is_single()){
			get_template_part( 'template-parts/posts/single');
			comments_template();
		} elseif(get_theme_mod('blog_style_archive', '') && (is_archive() || is_search())){
			get_template_part( 'template-parts/posts/archive', get_theme_mod('blog_style_archive', '') );
		} else{
			get_template_part( 'template-parts/posts/archive', get_theme_mod('blog_style', 'normal') );
		}
	?>
	</div>

</div>
<?php echo '</section>'; ?>

<?php do_action('silver_after_blog');
