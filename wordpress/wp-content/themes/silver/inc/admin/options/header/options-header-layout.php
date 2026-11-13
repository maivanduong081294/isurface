<?php

/*************
 * Header Main
 *************/

Silver_Option::add_section( 'header-layout', array(
	'title'       => __( 'Elements', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'flatsome_version',
	'label'       => __( 'Silver Version', 'silver' ),
	'section'     => 'header-layout',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'topbar_elements_left',
	'label'       => __( '← Left Elements', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_topbar_elements_left(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'topbar_elements_center',
	'label'       => __( 'Center Elements', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => array(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'topbar_elements_right',
	'label'       => __( 'Right Elements →', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'transport' => silver_customizer_transport(),
	'default'     => silver_topbar_elements_right(),
	'choices'     => $nav_elements
));


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_header_layout_main',
    'label'       => __( '', 'silver' ),
	'section'     => 'header-layout',
    'default'     => '<div class="options-title-divider">Main Header</div>',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_elements_left',
	'label'       => __( 'Left Elements', 'silver' ),
	'section'     => 'header-layout',
	'transport' => silver_customizer_transport(),
	'multiple' => 5,
	'default'     => silver_header_elements_left(),
	'choices'     => $nav_elements
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_elements_right',
	'label'       => __( 'Right Elements', 'silver' ),
	'section'     => 'header-layout',
	'transport' => silver_customizer_transport(),
	'multiple' => 5,
	'default'     => silver_header_elements_right(),
	'choices'     => $nav_elements
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_elements_bottom_left',
	'label'       => __( 'Left Elements', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_elements_bottom_left(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_elements_bottom_center',
	'label'       => __( 'Left Elements', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_elements_bottom_center(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_elements_bottom_right',
	'label'       => __( 'Right Elements', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_elements_bottom_right(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));



Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_mobile_elements_top',
	'label'       => __( 'Mobile Top', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_mobile_elements_top(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_mobile_elements_left',
	'label'       => __( 'Left Elements', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_mobile_elements_left(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_mobile_elements_right',
	'label'       => __( 'Left Elements', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => silver_header_mobile_elements_right(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'select',
	'settings'     => 'header_mobile_elements_bottom',
	'label'       => __( 'Left Elements', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header-layout',
	'multiple'    => 5,
	'default'     => array(),
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_elements
));
