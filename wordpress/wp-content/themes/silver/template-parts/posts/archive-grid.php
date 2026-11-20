<?php
/**
 * Posts archive grid: posts in columns, as many per row as Blog Layout >
 * Posts per row sets for each screen (Desktop, Tablet landscape, Tablet
 * portrait, Mobile). The older Two Col / Three Col layouts land here too.
 *
 * @package Silver\Templates
 */

// Desktop columns while "Posts per row - Desktop" was never saved (null: 2
// for a layout still saved as Two Col, else 3).
$desktop_columns = isset( $args['desktop_columns'] ) ? (int) $args['desktop_columns'] : null;
$grid_columns    = silver_blog_grid_columns( $desktop_columns );

// A shared item style overrides presentation while retaining the columns.
$item_style = get_theme_mod( 'blog_post_item_style', '' );
if ( in_array( $item_style, array( 'none', 'default', 'normal', 'overlay', 'shade', 'vertical', 'label', 'push', 'badge', 'bounce' ), true ) ) {
	get_template_part( 'template-parts/posts/archive', 'items', array( 'layout' => 'grid', 'style' => $item_style, 'columns' => $grid_columns ) );
	return;
}

if ( have_posts() ) : ?>
	<div id="post-list">
		<?php
		$ids = array();
		while ( have_posts() ) : the_post();
			$ids[] = get_the_ID();
		endwhile; // end of the loop.

		echo silver_apply_shortcode( 'blog_posts', array_merge( array(
			'type'        => get_theme_mod( 'blog_style_type', 'masonry' ),
			'depth'       => get_theme_mod( 'blog_posts_depth', 0 ),
			'depth_hover' => get_theme_mod( 'blog_posts_depth_hover', 0 ),
			'text_align'  => get_theme_mod( 'blog_posts_title_align', 'center' ),
			'show_date'   => get_theme_mod( 'blog_badge', 1 ) ? 'true' : 'false',
			'ids'         => implode( ',', $ids ),
		), $grid_columns ) );
		?>

		<?php silver_posts_pagination(); ?>
	</div>
<?php else : ?>

	<?php get_template_part( 'template-parts/posts/content', 'none' ); ?>

<?php endif; ?>
