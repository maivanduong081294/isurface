<?php
/**
 * Breadcrumbs.
 *
 * @package          Silver/WooCommerce/Templates
 * @silver-version 3.16.0
 */

$classes   = array();
$classes[] = 'is-' . get_theme_mod( 'breadcrumb_size', 'large' );
?>
<div class="<?php echo implode( ' ', $classes ); ?>">
	<?php silver_breadcrumb(); ?>
</div>
