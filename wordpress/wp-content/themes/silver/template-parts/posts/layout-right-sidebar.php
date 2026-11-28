<?php
/**
 * Posts layout right sidebar.
 *
 * @package          Silver\Templates
 * @silver-version 3.19.9
 */

// Blog page, archives and single posts: the category list, the featured posts
// and the posts, or the post (with the sidebar), in one section, which holds
// the top and bottom padding (silver-modern.css, .blog-section); the related
// products section follows it (silver_after_blog).
echo '<section class="blog-section">';

do_action('silver_before_blog');
?>

<?php if(!is_single() && get_theme_mod( 'blog_featured', '' ) == 'top'){ get_template_part('template-parts/posts/featured-posts'); } ?>

<?php
// The wide gutter (row-large) only goes with the sidebar divider. Single
// posts have their own setting, which falls back to the archive one.
$silver_blog_divider = get_theme_mod( 'blog_layout_divider', 1 );
// Sticky sidebar: Blog Layout, or Blog Single Post on posts (it falls back
// to the archive one until saved).
$silver_blog_sticky      = get_theme_mod( 'blog_sticky_sidebar', 0 );
$silver_blog_sticky_mode = get_theme_mod( 'blog_sticky_sidebar_mode', '' );
if ( is_single() ) {
	$silver_blog_divider     = get_theme_mod( 'blog_single_layout_divider', $silver_blog_divider );
	$silver_blog_sticky      = get_theme_mod( 'blog_single_sticky_sidebar', $silver_blog_sticky );
	$silver_blog_sticky_mode = get_theme_mod( 'blog_single_sticky_sidebar_mode', $silver_blog_sticky_mode );
}
?>
<div class="row blog-layout-row<?php if ( $silver_blog_divider ) echo ' row-large row-divided'; ?>">

	<div class="xlarge-9 col">
	<?php if(!is_single() && get_theme_mod( 'blog_featured', '' ) == 'content'){ get_template_part('template-parts/posts/featured-posts'); } ?>
	<?php
		if(is_single()){
			get_template_part( 'template-parts/posts/single');
			comments_template();
		} elseif ( get_theme_mod( 'blog_style_archive', '' ) && (is_archive() || is_search())){
			get_template_part( 'template-parts/posts/archive', get_theme_mod( 'blog_style_archive', '' ) );
		} else {
			get_template_part( 'template-parts/posts/archive', get_theme_mod( 'blog_style', 'normal' ) );
		}
	?>
	</div>
	<div class="post-sidebar xlarge-3 col">
		<?php if ( $silver_blog_sticky ) silver_sticky_column_open( '', $silver_blog_sticky_mode ); ?>
		<?php get_sidebar(); ?>
		<?php if ( $silver_blog_sticky ) silver_sticky_column_close(); ?>
	</div>
</div>
<?php echo '</section>'; ?>

<?php
	do_action('silver_after_blog');
?>
