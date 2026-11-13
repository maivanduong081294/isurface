<?php

Silver_Option::add_field( 'option',  array(
	'type'        => 'textfield',
	'settings'     => 'nav_position',
	'label'       => __( 'Nav Position', 'silver' ),
	'section'     => 'header-layout',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'textfield',
	'settings'     => 'search_pos',
	'label'       => __( 'Search Position', 'silver' ),
	'section'     => 'header-layout',
	'default'     => '',
));