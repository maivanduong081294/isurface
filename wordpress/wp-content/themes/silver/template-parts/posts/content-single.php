<?php
/**
 * Posts content single.
 *
 * @package          Silver\Templates
 * @silver-version 3.20.0
 */

?>
<div class="entry-content single-page">

	<?php
	// The content, with a table of contents after the introduction
	// (Blog Single Post > Table of contents, silver_single_post_toc()).
	$silver_content = apply_filters( 'the_content', get_the_content() );
	echo silver_single_post_toc( str_replace( ']]>', ']]&gt;', $silver_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- post content.
	?>

	<?php
	wp_link_pages();
	?>
</div>

<?php
// Under the post: categories and tags as pills, then the share buttons, in
// one bar (silver-modern.css, .entry-footer-bar).
$silver_footer_meta = get_theme_mod( 'blog_single_footer_meta', 1 );
$silver_share       = get_theme_mod( 'blog_share', 1 );
if ( $silver_footer_meta || $silver_share ) : ?>
	<footer class="entry-footer-bar">
		<?php if ( $silver_footer_meta ) : ?>
			<div class="entry-terms">
				<?php
				$silver_terms = array();
				foreach ( (array) get_the_category() as $silver_term ) {
					$silver_terms[] = '<a class="entry-term entry-term--category" href="' . esc_url( get_category_link( $silver_term ) ) . '" rel="category tag">' . esc_html( $silver_term->name ) . '</a>';
				}
				foreach ( (array) get_the_tags() as $silver_term ) {
					if ( $silver_term instanceof WP_Term ) {
						$silver_terms[] = '<a class="entry-term" href="' . esc_url( get_tag_link( $silver_term ) ) . '" rel="tag">#' . esc_html( $silver_term->name ) . '</a>';
					}
				}
				if ( $silver_terms ) {
					echo '<span class="entry-footer-bar__label">' . esc_html__( 'Tags', 'silver' ) . '</span>';
					echo implode( '', $silver_terms ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( $silver_share ) : ?>
			<div class="blog-share">
				<span class="entry-footer-bar__label"><?php esc_html_e( 'Share', 'silver' ); ?></span>
				<?php
				echo silver_apply_shortcode( 'share', array( // phpcs:ignore WordPress.Security.EscapeOutput -- shortcode output.
					'style'   => get_theme_mod( 'social_icons_style', 'outline' ),
					'tooltip' => get_theme_mod( 'social_icons_tooltip', 1 ) ? 'true' : 'false',
				) );
				?>
			</div>
		<?php endif; ?>
	</footer>
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_author_box', 1 ) ) :
	$silver_author_id   = (int) get_the_author_meta( 'ID' );
	$silver_author_desc = get_the_author_meta( 'description' );
	?>
	<div class="entry-author author-box">
		<div class="blog-author-image">
			<?php echo get_avatar( $silver_author_id, apply_filters( 'silver_author_bio_avatar_size', 144 ) ); ?>
		</div>
		<div class="entry-author__body">
			<span class="entry-author__label"><?php esc_html_e( 'Written by', 'silver' ); ?></span>
			<div class="author-name heading-font"><?php the_author_meta( 'display_name' ); ?></div>
			<?php if ( $silver_author_desc ) : ?>
				<p class="author-desc"><?php echo wp_kses_post( $silver_author_desc ); ?></p>
			<?php endif; ?>
			<a class="entry-author__link" href="<?php echo esc_url( get_author_posts_url( $silver_author_id ) ); ?>">
				<?php esc_html_e( 'View all posts', 'silver' ); ?> <?php echo get_silver_icon( 'icon-angle-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>
	</div>
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_single_next_prev_nav', 1 ) ) :
	silver_content_nav( 'nav-below' );
endif; ?>
