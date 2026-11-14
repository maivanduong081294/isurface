<?php
/**
 * Style > Default Button.
 *
 * Colors of the "Default" color of the [button] element (UX Builder > Button >
 * Layout > Color). Each option is printed as a CSS variable
 * (inc/functions/function-custom-css.php) and read by the .button.default
 * rules in assets/css/src/silver.css. An empty option keeps the theme value:
 * primary color background, white text.
 */

Silver_Option::add_section( 'default-button', array(
	'title'       => __( 'Default Button', 'silver' ),
	'description' => __( 'Colors of the "Default" color of the Button element. Empty options use the primary color with white text.', 'silver' ),
	'panel'       => 'style',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_button_default',
	'label'    => '',
	'section'  => 'default-button',
	'default'  => '<div class="options-title-divider">' . esc_html__( 'Normal', 'silver' ) . '</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color-alpha',
	'settings'    => 'button_default_bg',
	'label'       => __( 'Background', 'silver' ),
	'description' => __( 'Also the text and border color of the Outline, Simple and Underline styles.', 'silver' ),
	'section'     => 'default-button',
	'default'     => '',
	'transport'   => 'refresh',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'settings'  => 'button_default_color',
	'label'     => __( 'Text Color', 'silver' ),
	'section'   => 'default-button',
	'default'   => '',
	'transport' => 'refresh',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'settings'  => 'button_default_border',
	'label'     => __( 'Border Color', 'silver' ),
	'section'   => 'default-button',
	'default'   => '',
	'transport' => 'refresh',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_button_default_hover',
	'label'    => '',
	'section'  => 'default-button',
	'default'  => '<div class="options-title-divider">' . esc_html__( 'Hover', 'silver' ) . '</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color-alpha',
	'settings'    => 'button_default_bg_hover',
	'label'       => __( 'Background :hover', 'silver' ),
	'description' => __( 'Empty: the background gets slightly darker on hover.', 'silver' ),
	'section'     => 'default-button',
	'default'     => '',
	'transport'   => 'refresh',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'settings'  => 'button_default_color_hover',
	'label'     => __( 'Text Color :hover', 'silver' ),
	'section'   => 'default-button',
	'default'   => '',
	'transport' => 'refresh',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'settings'  => 'button_default_border_hover',
	'label'     => __( 'Border Color :hover', 'silver' ),
	'section'   => 'default-button',
	'default'   => '',
	'transport' => 'refresh',
) );
