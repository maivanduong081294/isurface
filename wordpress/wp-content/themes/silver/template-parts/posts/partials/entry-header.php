<?php
/**
 * Post-entry header.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

?>
<header class="entry-header">
	<div class="entry-header-text entry-header-text-top text-<?php echo esc_attr( silver_blog_title_align() ); ?>">
		<?php get_template_part( 'template-parts/posts/partials/entry', 'title' ); ?>
	</div>
	<?php if ( has_post_thumbnail() ) : ?>
		<?php if ( ! is_single() || ( is_single() && get_theme_mod( 'blog_single_featured_image', 1 ) ) ) : ?>
			<div class="entry-image relative">
				<?php get_template_part( 'template-parts/posts/partials/entry-image', 'default' ); ?>
				<?php if ( get_theme_mod( 'blog_badge', 1 ) ) get_template_part( 'template-parts/posts/partials/entry', 'post-date' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</header>
