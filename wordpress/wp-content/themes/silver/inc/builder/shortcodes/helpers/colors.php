<?php

// The theme colors are saved as CSS variables, not as the color they stand for
// today, so an element keeps following the theme options after it is saved.
// The variables, including the 10%..90% series, are printed by
// inc/functions/function-custom-css.php (frontend) and
// inc/builder/theme-colors.php (editor).
//
// This file is included from inside the option definitions, so it runs in the
// caller's scope: the closure keeps its variables out of it.
return ( function () {
	$colors = array(
		// "None": removes the color, e.g. on a larger screen when the mobile
		// value would otherwise be inherited (an empty value means "inherit").
		array(
			'title' => 'None',
			'value' => 'transparent',
		),
		array(
			'title' => 'Dark',
			'value' => 'rgb(0,0,0)',
		),
		array(
			'title' => 'White',
			'value' => 'rgb(255,255,255)',
		),
	);

	$theme_colors = array(
		'primary'   => 'Primary',
		'secondary' => 'Secondary',
		'base'      => 'Base',
	);

	// The colors themselves, then one series of 10%..90% opacity for each.
	// The editor lays the series out in rows by their title (" 10%" starts one):
	// inc/builder/theme-colors.php.
	foreach ( $theme_colors as $name => $title ) {
		$colors[] = array(
			'title' => $title,
			'value' => 'var(--fs-color-' . $name . ')',
		);
	}

	foreach ( $theme_colors as $name => $title ) {
		for ( $i = 10; $i <= 90; $i += 10 ) {
			$colors[] = array(
				'title' => $title . ' ' . $i . '%',
				'value' => 'var(--fs-color-' . $name . '-' . $i . ')',
			);
		}
	}

	return $colors;
} )();
