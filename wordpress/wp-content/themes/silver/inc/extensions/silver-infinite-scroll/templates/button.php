<?php
/**
 * Load more button (Shop / Blog pagination: Load more button, or Infinite
 * scroll with Loading type: Button). Hidden until the script is ready.
 *
 * @var Silver_Infinite_Scroll $this
 */

$silver_archive_class = 'blog' === $this->context ? 'posts-archive' : 'products-archive';
?>
<div class="view-more-wrap text-center">
	<button type="button" class="view-more-button <?php echo esc_attr( $silver_archive_class ); ?> button primary hidden"><?php esc_html_e( 'Load more', 'silver' ); ?></button>
</div>
