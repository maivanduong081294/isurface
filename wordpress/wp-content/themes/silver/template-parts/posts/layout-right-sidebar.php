<?php
/**
 * Posts layout right sidebar.
 *
 * @package          Silver\Templates
 * @silver-version 3.19.9
 */

// Blog page and archives: the category list, the featured posts and the
// posts (with the sidebar) in one section, which holds the top and bottom
// padding (silver-modern.css, .blog-section).
if ( ! is_single() ) echo '<section class="blog-section">';

do_action('silver_before_blog');
?>

<?php if(!is_single() && get_theme_mod( 'blog_featured', '' ) == 'top'){ get_template_part('template-parts/posts/featured-posts'); } ?>

<?php // The wide gutter (row-large) only goes with the sidebar divider. ?>
<div class="row<?php if ( get_theme_mod( 'blog_layout_divider', 1 ) ) echo ' row-large row-divided'; ?>">

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
		<?php silver_sticky_column_open( 'blog_sticky_sidebar' ); ?>
		<?php get_sidebar(); ?>
		<?php silver_sticky_column_close( 'blog_sticky_sidebar' ); ?>
	</div>
</div>
<?php if ( ! is_single() ) echo '</section>'; ?>

<?php
	do_action('silver_after_blog');
?>
