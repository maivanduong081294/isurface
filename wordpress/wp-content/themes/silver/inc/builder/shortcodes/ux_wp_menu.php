<?php
/**
 * Element "WP Menu": a menu from Appearance > Menus (WordPress nav menu),
 * horizontal with dropdowns or vertical with an accordion for sub menus.
 * Render: inc/shortcodes/ux_wp_menu.php.
 */

$silver_wp_menus = array( '' => __( '-- Select a menu --', 'silver' ) );
foreach ( wp_get_nav_menus() as $silver_wp_menu ) {
	$silver_wp_menus[ $silver_wp_menu->term_id ] = $silver_wp_menu->name;
}

add_ux_builder_shortcode( 'ux_wp_menu', array(
	'name'      => __( 'WP Menu', 'silver' ),
	'category'  => __( 'Content' ),
	'thumbnail' => silver_ux_builder_thumbnail( 'nav' ),
	'info'      => '{{ direction }}',
	'wrap'      => false,
	'options'   => array(
		'menu'      => array(
			'type'        => 'select',
			'heading'     => __( 'Menu', 'silver' ),
			'description' => __( 'Menus are created in Appearance > Menus.', 'silver' ),
			'default'     => '',
			'options'     => $silver_wp_menus,
		),
		'direction' => array(
			'type'    => 'radio-buttons',
			'heading' => __( 'Direction', 'silver' ),
			'default' => 'horizontal',
			'options' => array(
				'horizontal' => array( 'title' => 'Horizontal' ),
				'vertical'   => array( 'title' => 'Vertical' ),
			),
		),
		'depth'     => array(
			'type'        => 'select',
			'heading'     => __( 'Sub menus', 'silver' ),
			'description' => __( 'Horizontal: dropdown on hover. Vertical: open / close (accordion).', 'silver' ),
			'default'     => '0',
			'options'     => array(
				'0' => __( 'All levels', 'silver' ),
				'1' => __( 'None (top level only)', 'silver' ),
				'2' => __( '2 levels', 'silver' ),
				'3' => __( '3 levels', 'silver' ),
			),
		),
		'style'     => array(
			'type'    => 'select',
			'heading' => __( 'Style', 'silver' ),
			'default' => 'simple',
			'options' => require __DIR__ . '/values/nav-styles.php',
		),
		'align'     => array(
			'type'       => 'radio-buttons',
			'heading'    => __( 'Align', 'silver' ),
			'responsive' => true,
			'default'    => 'left',
			'options'    => require __DIR__ . '/values/align-radios.php',
		),
		'size'      => array(
			'type'    => 'radio-buttons',
			'heading' => __( 'Size', 'silver' ),
			'default' => '',
			'options' => require __DIR__ . '/values/text-sizes.php',
		),
		'case'      => array(
			'type'    => 'radio-buttons',
			'heading' => __( 'Letter case', 'silver' ),
			'default' => '',
			'options' => array(
				''          => array( 'title' => 'Normal' ),
				'uppercase' => array( 'title' => 'UPPERCASE' ),
			),
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
