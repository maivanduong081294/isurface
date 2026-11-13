<?php

return array(
	// "None": removes the overlay on a screen (an empty value means "inherit").
	array(
		'title' => 'None',
		'value' => 'transparent',
	),
	array(
		'title' => 'Dark',
		'value' => 'rgba(0,0,0,.5)',
	),
	array(
		'title' => 'White',
		'value' => 'rgba(255,255,255,.5)',
	),
	array(
		'title' => 'Primary',
		'value' => get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ),
	),
);
