<?php

//Access the WordPress Pages via an Array
$list_pages            = array();
$list_pages_by_id      = array();
$of_pages_obj          = get_pages('sort_column=post_parent,menu_order');
$list_pages['0']       = 'Select a page:';
$list_pages_by_id['0'] = 'Select a page:';
foreach ($of_pages_obj as $of_page) {
    $list_pages[$of_page->post_name] = $of_page->post_title;
    $list_pages_by_id[$of_page->ID] = $of_page->post_title;
}

function silver_customizer_blocks() {
  $blocks = array( false => '-- None --' );
  $items  = silver_get_post_type_items( 'blocks' );

  if ( ! empty( $items ) ) {
    foreach ( $items as $item ) {
      $blocks[ $item->post_name ] = $item->post_title;
    }
  }

  return $blocks;
}

// Deprecated global transport (use silver_customizer_transport() instead).
$transport = 'postMessage';
if ( ! isset( $wp_customize->selective_refresh ) ) {
  $transport = 'refresh';
}

function silver_customizer_transport() {
  global $wp_customize;
  return ! isset( $wp_customize->selective_refresh )
    ? 'refresh'
    : 'postMessage';
}

$image_url = get_template_directory_uri().'/inc/admin/customizer/img/';

function silver_customizer_images_uri() {
  return get_template_directory_uri() . '/inc/admin/customizer/img';
}


function silver_customizer_nav_elements() {
	return apply_filters( 'silver_header_element', array(
		'cart'          => __( 'Cart', 'silver' ),
		'account'       => __( 'Account', 'silver' ),
		'menu-icon'     => __( '☰ Nav Icon', 'silver' ),
		'nav'           => __( 'Main Menu', 'silver' ),
		'nav-secondary' => __( 'Secondary Menu', 'silver' ),
		'nav-top'       => __( 'Top Bar Menu', 'silver' ),
		'nav-vertical'  => __( '☰ Vertical Menu', 'silver' ),
		'search'        => __( 'Search Icon', 'silver' ),
		'search-form'   => __( 'Search Form', 'silver' ),
		'social'        => __( 'Social Icons', 'silver' ),
		'contact'       => __( 'Contact', 'silver' ),
		'button-1'      => __( 'Button 1', 'silver' ),
		'button-2'      => __( 'Button 2', 'silver' ),
		'checkout'      => __( 'Checkout Button', 'silver' ),
		'newsletter'    => __( 'Newsletter', 'silver' ),
		'languages'     => __( 'Languages', 'silver' ),
		'divider'       => __( '|', 'silver' ),
		'divider_2'     => __( '|', 'silver' ),
		'divider_4'     => __( '|', 'silver' ),
		'divider_3'     => __( '|', 'silver' ),
		'divider_5'     => __( '|', 'silver' ),
		'block-1'       => __( 'Block 1', 'silver' ),
		'block-2'       => __( 'Block 2', 'silver' ),
		'block-3'       => __( 'Block 3', 'silver' ),
		'block-4'       => __( 'Block 4', 'silver' ),
		'html'          => __( 'HTML 1', 'silver' ),
		'html-2'        => __( 'HTML 2', 'silver' ),
		'html-3'        => __( 'HTML 3', 'silver' ),
		'html-4'        => __( 'HTML 4', 'silver' ),
		'html-5'        => __( 'HTML 5', 'silver' ),
	) );
}

// Add Hooked Header Elements
$nav_elements = silver_customizer_nav_elements();

$visibility= array(
  '' => __( 'Show for All', 'silver' ),
  'hide-for-small' => __( 'Hide For Mobile', 'silver' ),
  'hide-for-medium' => __( 'Hide For Tablet', 'silver' ),
  'show-for-small' => __( 'Show For Mobile', 'silver' ),
  'show-for-medium' => __( 'Show For Tablet', 'silver' ),
  'show-for-large' => __( 'Show For Desktop', 'silver' ),
);

$nav_styles_img = array(
  '' => $image_url . 'nav-default.svg',
  'divided' => $image_url . 'nav-divided.svg',
  'line' => $image_url . 'nav-line.svg',
  'line-grow' => $image_url . 'nav-line-grow.svg',
  'line-bottom' => $image_url . 'nav-line-bottom.svg',
  'box' => $image_url . 'nav-box.svg',
  'outline' => $image_url . 'nav-outline.svg',
  'pills' => $image_url . 'nav-pills.svg',
  'tabs' => $image_url . 'nav-tabs.svg'
);

$smart_links = __( '', 'silver' );

$sizes = array(
    'xxlarge' => __( 'XX Large', 'silver' ),
    'xlarge' => __( 'X Large', 'silver' ),
    'larger' => __( 'Larger', 'silver' ),
    'large' => __( 'Large', 'silver' ),
    'medium' => __( 'Medium', 'silver' ),
    'small' => __( 'Small', 'silver' ),
    'smaller' => __( 'Smaller', 'silver' ),
    'xsmall' => __( 'X Small', 'silver' ),
);

$button_styles = array(
	''          => __( 'Default', 'silver' ),
	'outline'   => __( 'Outline', 'silver' ),
	'underline' => __( 'Underline', 'silver' ),
	'shade'     => __( 'Shade', 'silver' ),
	'bevel'     => __( 'Bevel', 'silver' ),
	'gloss'     => __( 'Gloss', 'silver' ),
	'link'      => __( 'Link', 'silver' ),
);

$nav_sizes = array(
  'xsmall' => __( 'XS', 'silver' ),
  'small' => __( 'S', 'silver' ),
  '' => __( 'Default', 'silver' ),
  'medium' => __( 'M', 'silver' ),
  'large' => __( 'L', 'silver' ),
  'xlarge' => __( 'XL', 'silver' ),
);

$nav_spacing = array(
  'xsmall' => __( 'XS', 'silver' ),
  'small' => __( 'S', 'silver' ),
  '' => __( 'Default', 'silver' ),
  'medium' => __( 'M', 'silver' ),
  'large' => __( 'L', 'silver' ),
  'xlarge' => __( 'XL', 'silver' ),
);


$bg_repeat = array(
  "repeat" => "Tiled",
  "repeat-x" => "Repeat X",
  "repeat-y" => "Repeat Y",
  "no-repeat" => "No Repeat"
);
