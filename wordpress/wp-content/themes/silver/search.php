<?php
/**
 * The blog template file.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

get_header();

?>

<div id="content" class="blog-wrapper blog-archive page-wrapper">
		<?php get_template_part( 'template-parts/posts/layout', get_theme_mod('blog_layout','right-sidebar') ); ?>
</div>


<?php get_footer(); ?>
