<?php
/**
 * Color swatches for text colors: the theme colors (colors.php) where "None"
 * (transparent, for backgrounds) is replaced by "Default" (inherit), which
 * gives the text back its normal color, e.g. on a larger screen when the
 * mobile value would otherwise be inherited.
 */

return ( function () {
	$colors = array_values( array_filter( require __DIR__ . '/colors.php', function ( $color ) {
		return 'transparent' !== $color['value'];
	} ) );

	array_unshift( $colors, array(
		'title' => 'Default',
		'value' => 'inherit',
	) );

	return $colors;
} )();
