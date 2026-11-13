<?php

if ( ! fl_woocommerce_version_check( '9.9.0' ) ) {
	Silver_Option::add_field( 'option', array(
		'type'      => 'checkbox',
		'settings'  => 'woocommerce_store_notice_top',
		'label'     => __( 'Move store notice to the top', 'silver' ),
		'section'   => 'woocommerce_store_notice',
		'default'   => 0
	) );
}
