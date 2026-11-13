<?php
/**
 * Posts archive.
 *
 * @package          Silver\Templates
 * @silver-version 3.19.9
 */

if ( have_posts() ) : ?>
<div id="post-list">

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="article-inner <?php silver_blog_article_classes(); ?>">
		<?php get_template_part( 'template-parts/posts/partials/entry-header', get_theme_mod( 'blog_posts_header_style', 'normal' ) ); ?>
		<?php get_template_part('template-parts/posts/content', 'default' ); ?>
		<?php get_template_part('template-parts/posts/partials/entry-footer', 'default' ); ?>
	</div>
</article>

<?php endwhile; ?>

<?php silver_posts_pagination(); ?>

</div>

<?php else : ?>

	<?php get_template_part( 'template-parts/posts/content','none'); ?>

<?php endif; ?>
