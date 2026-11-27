<?php
/**
 * A theme option printed as the value of a CSS variable: without the
 * characters that would end the declaration or the <style> element.
 *
 * @param string $value Option value (color, length...).
 *
 * @return string
 */
function silver_css_var_value( $value ) {
	return trim( preg_replace( '/[;{}<>]/', '', (string) $value ) );
}

/**
 * A CSS length typed in a theme option: "40%", "420px", "30rem"... A bare
 * number is read as px. Anything else is dropped.
 *
 * @param string $value Option value.
 *
 * @return string
 */
function silver_sanitize_css_length( $value ) {
	$value = strtolower( trim( (string) $value ) );

	if ( '' === $value ) {
		return '';
	}
	if ( is_numeric( $value ) ) {
		return ( (float) $value ) . 'px';
	}

	return preg_match( '/^\d+(\.\d+)?(px|%|rem|em|vw|ch)$/', $value ) ? $value : '';
}

/* CUSTOM CSS */
function silver_custom_css() {
if ( ! function_exists( 'fs_color_channels' ) ) {
	function fs_color_channels( $hex ) {
		$hex = ltrim( trim( (string) $hex ), '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = preg_replace( '/(.)/', '$1$1', $hex );
		}
		if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) {
			return null;
		}
		return sprintf( '%d, %d, %d', hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
	}
}

$fs_opacity_colors = array(
	'primary'   => get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ),
	'secondary' => get_theme_mod( 'color_secondary', Silver_Default::COLOR_SECONDARY ),
	'base'      => get_theme_mod( 'color_texts', Silver_Default::COLOR_BASE ),
);

// Theme options read by the components merged into silver.css (sources in
// assets/css/src: silver-dialog.css, silver-mega-menu.css). Each one is printed
// as --fs-{name} only while the option is set, so the component keeps its own
// default otherwise: var(--fs-button-radius, 6px).
$fs_button_radius   = get_theme_mod( 'button_radius' );
$fs_dropdown_radius = get_theme_mod( 'dropdown_radius' );
$fs_button_bg_hover = get_theme_mod( 'button_default_bg_hover' );
$fs_option_vars     = array_filter(
	apply_filters(
		'silver_option_css_vars',
		array(
			// Style > Typography > Headings color.
			'color-headings'  => get_theme_mod( 'type_headings_color' ),
			// Style > Global Styles > Default Button Radius.
			'button-radius'   => '0px' !== $fs_button_radius ? $fs_button_radius : '',
			// Style > Default Button: the "Default" color of the [button] element
			// (.button.default in silver.css).
			'button-default-bg'           => get_theme_mod( 'button_default_bg' ),
			'button-default-color'        => get_theme_mod( 'button_default_color' ),
			'button-default-border'       => get_theme_mod( 'button_default_border' ),
			'button-default-bg-hover'     => $fs_button_bg_hover,
			'button-default-color-hover'  => get_theme_mod( 'button_default_color_hover' ),
			'button-default-border-hover' => get_theme_mod( 'button_default_border_hover' ),
			// A hover background of its own replaces the dark tint every button gets on hover.
			'button-default-hover-tint'   => $fs_button_bg_hover ? 'transparent' : '',
			// Style > Lightbox > Backdrop color.
			'lightbox-bg'     => get_theme_mod( 'flatsome_lightbox_bg' ),
			// Header > Dropdown Style.
			'dropdown-bg'     => get_theme_mod( 'dropdown_bg' ),
			'dropdown-border' => get_theme_mod( 'dropdown_border_enabled', 1 ) ? get_theme_mod( 'dropdown_border' ) : '',
			'dropdown-radius' => '0' !== (string) $fs_dropdown_radius ? $fs_dropdown_radius : '',
		)
	),
	static function ( $value ) {
		return is_string( $value ) && '' !== trim( $value );
	}
);

// Layout spacing. Each entry is printed as the CSS variable --fs-{name}, which
// the layout classes in assets/css/silver.css read: rows and columns (.row,
// .col), .container, .section and the margin / padding / gap helpers.
// A pair is mobile-first: the value for mobile and tablet portrait, then the
// value from tablet landscape (850px) up.
$fs_layout_spacing = apply_filters(
	'silver_layout_spacing',
	array(
		'column-gap'        => array( '20px' ),
		'row-gap'           => array( '20px' ),
		'column-gap-small'  => '19.6px',
		'row-gap-small'     => '19.6px',
		'column-gap-xsmall' => '4px',
		'row-gap-xsmall'    => '3px',
		'column-gap-large'  => '60px',
		'row-gap-large'     => '30px',
		'container-padding' => 'calc(var(--fs-column-gap) / 2)',
		'section-padding'   => array( '20px', '30px' ),
		'margin'            => array( '20px', '30px' ),
		'padding'           => array( '20px', '30px' ),
		'gap'               => array( '20px', '30px' ),
	)
);
$fs_layout_spacing_portial = array();
foreach ( $fs_layout_spacing as $name => $value ) {
	if ( is_array( $value ) ) {
		$fs_layout_spacing[ $name ]           = reset( $value );
		$fs_layout_spacing_portial[ $name ] = end( $value );
	}
}
	
ob_start();
?>
<style id="custom-css" type="text/css">
:root {
	--primary-color: <?php echo esc_attr( get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ) ); ?>;
	--fs-color-primary: <?php echo esc_attr( get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ) ); ?>;
	--fs-color-secondary: <?php echo esc_attr( get_theme_mod( 'color_secondary', Silver_Default::COLOR_SECONDARY ) ); ?>;
	--fs-color-success: <?php echo esc_attr( get_theme_mod( 'color_success', Silver_Default::COLOR_SUCCESS ) ); ?>;
	--fs-color-alert: <?php echo esc_attr( get_theme_mod( 'color_alert', Silver_Default::COLOR_ALERT ) ); ?>;
	--fs-color-base: <?php echo esc_attr( get_theme_mod( 'color_texts', Silver_Default::COLOR_BASE ) ); ?>;
	--fs-experimental-link-color: <?php echo esc_attr( get_theme_mod( 'color_links' ) ?: Silver_Default::LINK_COLOR ); ?>;
	--fs-experimental-link-color-hover: <?php echo esc_attr( get_theme_mod( 'color_links_hover' ) ?: Silver_Default::LINK_COLOR_HOVER ); ?>;
<?php foreach ( $fs_opacity_colors as $name => $value ) :
	$rgb = fs_color_channels( $value );
	if ( ! $rgb ) {
		continue; // giá trị không phải hex (ví dụ rgba) thì bỏ qua
	}
	?>
	--fs-color-<?php echo esc_attr( $name ); ?>-rgb: <?php echo esc_attr( $rgb ); ?>;
<?php for ( $i = 10; $i <= 90; $i += 10 ) : ?>
	--fs-color-<?php echo esc_attr( $name ); ?>-<?php echo (int) $i; ?>: rgba(<?php echo esc_attr( $rgb ); ?>, 0.<?php echo (int) ( $i / 10 ); ?>);
<?php endfor; ?>
<?php endforeach; ?>
<?php foreach ( $fs_option_vars as $name => $value ) : ?>
	--fs-<?php echo esc_attr( $name ); ?>: <?php echo esc_attr( silver_css_var_value( $value ) ); ?>;
<?php endforeach; ?>
<?php foreach ( $fs_layout_spacing as $name => $value ) : ?>
	--fs-<?php echo esc_attr( $name ); ?>: <?php echo esc_attr( $value ); ?>;
<?php endforeach; ?>
}

<?php if ( $fs_layout_spacing_portial ) : ?>
@media (min-width: 550px) {
	:root {
<?php foreach ( $fs_layout_spacing_portial as $name => $value ) : ?>
		--fs-<?php echo esc_attr( $name ); ?>: <?php echo esc_attr( $value ); ?>;
<?php endforeach; ?>
	}
}
<?php endif; ?>

.tooltipster-base {
	--tooltip-color: <?php echo esc_attr( get_theme_mod( 'tooltip_color', Silver_Default::TOOLTIP_COLOR ) ); ?>;
	--tooltip-bg-color: <?php echo esc_attr( get_theme_mod( 'tooltip_bg_color', Silver_Default::TOOLTIP_BG_COLOR ) ); ?>;
}

.off-canvas-right .mfp-content, .off-canvas-left .mfp-content {
	--drawer-width: <?php echo esc_attr( get_theme_mod( 'drawer_width' ) ?: Silver_Default::DRAWER_WIDTH ); ?>;
}

<?php if ( is_woocommerce_activated() ) : ?>
.off-canvas .mfp-content.off-canvas-cart {
	--drawer-width: <?php echo esc_attr( get_theme_mod( 'cart_drawer_width' ) ?: Silver_Default::CART_DRAWER_WIDTH ); ?>;
}
<?php endif;

// Screen sizes: Mobile < 550px, Tablet portrait 550-849px, Tablet landscape
// 850-1199px, Desktop >= 1200px (same tiers as UX Builder and the grid CSS).
$small_screen = '550px';
$small_screen_max = '549px';
$large_screen = '850px';
$large_screen_max = '849px';
$medium_screen = '1200px';
$medium_screen_max = '1199px';
$admin_bar = 0;
if(is_admin_bar_showing()){
  $admin_bar = 32;
}

// Layout backgrounds
if ( get_theme_mod( 'body_bg_image' ) ) echo 'html{background-image: url(\'' . get_theme_mod( 'body_bg_image' ) . '\');}';
if ( get_theme_mod( 'body_bg' ) ) echo 'html{background-color:' . get_theme_mod( 'body_bg' ) . '!important;}';

/* Site Width */

if(get_theme_mod('site_width')) {
$site_width = intval(get_theme_mod('site_width')); ?>
.container-width, .full-width .ubermenu-nav, .container, .row{max-width: <?php echo $site_width - 30; ?>px}
.row.row-collapse{max-width: <?php echo $site_width - 60; ?>px}
.row.row-small{max-width: <?php echo $site_width - 37.5; ?>px}
.row.row-large{max-width: <?php echo $site_width; ?>px}
<?php } ?>

<?php if(get_theme_mod('body_layout') !== 'full-width' && get_theme_mod('site_width_boxed')){ ?>
body.framed, body.framed header, body.framed .header-wrapper, body.boxed, body.boxed header, body.boxed .header-wrapper, body.boxed .is-sticky-section{ max-width: <?php echo get_theme_mod('site_width_boxed'); ?>px
}
<?php } ?>


<?php
$content_bg = get_theme_mod('content_bg');
if($content_bg){ ?>
	.sticky-add-to-cart--active, #wrapper,#main,#main.dark{background-color: <?php echo $content_bg; ?>}
<?php } ?>

<?php
$cookie_notice_bg_color = get_theme_mod( 'cookie_notice_bg_color' );
if ( $cookie_notice_bg_color ) { ?>
	.silver-cookies {background-color: <?php echo $cookie_notice_bg_color; ?>}
<?php } ?>

<?php
$header_height = get_theme_mod('header_height', 90); ?>
.header-main{min-height: <?php echo $header_height; ?>px}
#logo img{max-height: <?php echo $header_height; ?>px}

#logo{width:<?php echo get_theme_mod('logo_width', 200); ?>px;}

<?php if(get_theme_mod('logo_padding')) echo '#logo img{padding:'.get_theme_mod('logo_padding').'px 0;}'; ?>
<?php if(get_theme_mod('logo_max_width')) echo '#logo a{max-width:'.get_theme_mod('logo_max_width').'px;}'; ?>
<?php if(get_theme_mod('sticky_logo_padding')) echo '.stuck #logo img{padding:'.get_theme_mod('sticky_logo_padding').'px 0;}'; ?>

<?php if(get_theme_mod('header_bottom_height')){ ?>
.header-bottom{min-height: <?php echo get_theme_mod('header_bottom_height'); ?>px}
<?php } ?>

.header-top{min-height: <?php echo get_theme_mod('header_top_height', 30); ?>px}

<?php $header_height_transparent = get_theme_mod( 'header_height_transparent', 90 ); ?>
.transparent .header-main{min-height: <?php echo $header_height_transparent; ?>px}
.transparent #logo img{max-height: <?php echo $header_height_transparent; ?>px}

<?php

$height = 0;
$height = $height + $header_height_transparent;
if(silver_has_top_bar()['large_or_mobile']) $height = $height + '30';
if(silver_has_bottom_bar()['large_or_mobile']) $height = $height + '50';

$mob_height = 0;
?>
.has-transparent + .page-title:first-of-type,
.has-transparent + #main > .page-title,
.has-transparent + #main > div > .page-title,
.has-transparent + #main .page-header-wrapper:first-of-type .page-title{
padding-top: <?php echo $height; ?>px;
}

<?php if(get_theme_mod('header_bg_transparent')){ ?>
.transparent .header-wrapper{background-color: <?php echo get_theme_mod('header_bg_transparent'); ?>!important;}
.transparent .top-divider{display: none;}
<?php } ?>

<?php
$header_height_sticky = get_theme_mod('header_height_sticky', 40); ?>
.header.show-on-scroll,
.stuck .header-main{min-height:<?php echo $header_height_sticky; ?>px!important}
.stuck #logo img{max-height: <?php echo $header_height_sticky; ?>px!important}

<?php if(get_theme_mod('header_search_width')){ ?>
.search-form{ width: <?php echo get_theme_mod('header_search_width')?>%;}
<?php } ?>

<?php if(get_theme_mod('header_bg')){ ?>
.header-bg-color {background-color: <?php echo get_theme_mod('header_bg', 'rgba(255,255,255,0.9)'); ?>}
<?php } ?>

<?php if(get_theme_mod('header_bg_img')){ ?>
.header-bg-image {background-image: url('<?php echo get_theme_mod('header_bg_img'); ?>');}
.header-bg-image {background-repeat: <?php echo get_theme_mod('header_bg_img_repeat','repeat') ?>;}
<?php } ?>

.header-bottom {background-color: <?php echo get_theme_mod('nav_position_bg','#f1f1f1'); ?>}

<?php if(get_theme_mod('nav_height_top')){ ?>
.top-bar-nav > li > a{line-height: <?php echo get_theme_mod('nav_height_top').'px';?> }
<?php } ?>

<?php if(get_theme_mod('nav_height')){ ?>
.header-main .nav > li > a{line-height: <?php echo get_theme_mod('nav_height').'px';?> }
<?php } ?>

<?php if(get_theme_mod('nav_push')){ ?>
.header-wrapper:not(.stuck) .header-main .header-nav{margin-top: <?php echo get_theme_mod('nav_push').'px';?> }
<?php } ?>

<?php if(get_theme_mod('nav_height_sticky')){ ?>
.stuck .header-main .nav > li > a{line-height: <?php echo get_theme_mod('nav_height_sticky').'px';?> }
<?php } ?>
<?php if(get_theme_mod('nav_height_bottom')){ ?>
.header-bottom-nav > li > a{line-height: <?php echo get_theme_mod('nav_height_bottom').'px';?> }
<?php } ?>

<?php
$header_height_mobile = get_theme_mod('header_height_mobile', 40);
if($header_height_mobile){ ?>
@media (max-width: <?php echo $small_screen_max ?>) {
	.header-main{min-height: <?php echo $header_height_mobile;?>px}
	#logo img{max-height: <?php echo $header_height_mobile; ?>px}
}
<?php } ?>

<?php if(get_theme_mod('mobile_overlay_bg')){ ?>
	.main-menu-overlay{
	  background-color: <?php echo get_theme_mod('mobile_overlay_bg'); ?>
	}
<?php } ?>

<?php if ( get_theme_mod( 'dropdown_border_enabled', 1 ) && get_theme_mod( 'dropdown_border' ) ) { ?>
.nav-dropdown-has-arrow.nav-dropdown-has-border li.has-dropdown:before{border-bottom-color: <?php echo get_theme_mod('dropdown_border'); ?>;}
.nav .nav-dropdown{  border-color: <?php echo get_theme_mod('dropdown_border'); ?> }
<?php } ?>

<?php if(get_theme_mod('dropdown_radius')){ ?>
.nav-dropdown{border-radius:<?php echo get_theme_mod('dropdown_radius'); ?>}
<?php } ?>

<?php if(get_theme_mod('dropdown_nav_size', 100) !== 100){ ?>
	.nav-dropdown{font-size:<?php echo get_theme_mod('dropdown_nav_size'); ?>%}
<?php } ?>

<?php if(get_theme_mod('dropdown_bg')){ ?>
  .nav-dropdown-has-arrow li.has-dropdown:after{border-bottom-color: <?php echo get_theme_mod('dropdown_bg'); ?>;}
  .nav .nav-dropdown{background-color: <?php echo get_theme_mod('dropdown_bg'); ?>}
<?php } ?>

<?php if(get_theme_mod('topbar_bg')){ ?>
.header-top{background-color:  <?php echo get_theme_mod('topbar_bg'); ?>!important;}
<?php } ?>

<?php if(get_theme_mod('blog_bg_color')){ ?>
.blog-wrapper{background-color: <?php echo get_theme_mod('blog_bg_color'); ?>;}
<?php } ?>

<?php
	if(get_theme_mod('type_headings_color')){
	  echo 'h1,h2,h3,h4,h5,h6,.heading-font{color: '.get_theme_mod('type_headings_color').';}';
	}
?>

<?php
// Get Type options.
if ( ! get_theme_mod( 'disable_fonts', 0 ) ) :
	$type_nav      = \Kirki_Field_Typography::sanitize( get_theme_mod( 'type_nav', array( 'font-family' => 'Inter', 'variant' => '700' ) ) );
	$type_texts    = \Kirki_Field_Typography::sanitize( get_theme_mod( 'type_texts', array( 'font-family' => 'Inter', 'variant' => 'regular' ) ) );
	$type_headings = \Kirki_Field_Typography::sanitize( get_theme_mod( 'type_headings', array( 'font-family' => 'Inter', 'variant' => '700' ) ) );
	$type_alt      = \Kirki_Field_Typography::sanitize( get_theme_mod( 'type_alt', array( 'font-family' => 'Dancing Script', 'variant' => 'regular' ) ) );

	// Type sizes
	if(get_theme_mod('type_size', 100) !== 100){
	   echo 'body{font-size: '.get_theme_mod('type_size').'%;}';
	}
	// Tablets (100 = the base size above).
	if ( 100 !== (int) get_theme_mod( 'type_size_tablet_landscape', 100 ) ) {
	   echo '@media screen and (min-width: ' . $large_screen . ') and (max-width: ' . $medium_screen_max . '){body{font-size: ' . (int) get_theme_mod( 'type_size_tablet_landscape' ) . '%;}}';
	}
	if ( 100 !== (int) get_theme_mod( 'type_size_tablet_portrait', 100 ) ) {
	   echo '@media screen and (min-width: ' . $small_screen . ') and (max-width: ' . $large_screen_max . '){body{font-size: ' . (int) get_theme_mod( 'type_size_tablet_portrait' ) . '%;}}';
	}
	if(get_theme_mod('type_size_mobile', 100) !== 100){
	   echo '@media screen and (max-width: ' . $small_screen_max . '){body{font-size: '.get_theme_mod('type_size_mobile').'%;}}';
	}

	// Fix old
	if(!is_array($type_nav)) {
	  $type_nav = array('font-family' => $type_nav, 'variant' => '700');
	}
	if(!is_array($type_texts)) {
	  $type_texts = array('font-family' => $type_texts, 'variant' => 'regular');
	}
	if(!is_array($type_alt)) {
	  $type_alt = array('font-family' => $type_alt, 'variant' => 'regular');
	}
	if(!is_array($type_headings)) {
	  $type_headings = array('font-family' => $type_headings, 'variant' => '700');
	}

	// Type Base
	if(!empty($type_texts['font-family'])) {
		echo 'body{font-family: '. silver_parse_font_family( $type_texts['font-family'] ).';}';
	}

	if ( ! empty( $type_texts['font-weight'] ) ) { ?>
	body {
		font-weight: <?php echo intval( $type_texts['font-weight'] ); ?>;
		font-style: <?php echo $type_texts['font-style']; ?>;
	}
	<?php }

	// Type Navigations
	if(!empty($type_nav['font-family'])) {
		echo '.nav > li > a {font-family: '. silver_parse_font_family( $type_nav['font-family'] ).';}';
		echo '.mobile-sidebar-levels-2 .nav > li > ul > li > a {font-family: '. silver_parse_font_family( $type_nav['font-family'] ).';}';
	}

	if ( ! empty( $type_nav['font-weight'] ) ) { ?>
	.nav > li > a,
	.mobile-sidebar-levels-2 .nav > li > ul > li > a {
		font-weight: <?php echo intval( $type_nav['font-weight'] ); ?>;
		font-style: <?php echo $type_nav['font-style']; ?>;
	}
	<?php }

	// Type Headings
	if(!empty($type_headings['font-family'])) {
	echo 'h1,h2,h3,h4,h5,h6,.heading-font, .off-canvas-center .nav-sidebar.nav-vertical > li > a{font-family: '. silver_parse_font_family( $type_headings['font-family'] ).';}';
	}

	if ( ! empty( $type_headings['font-weight'] ) ) { ?>
	h1,h2,h3,h4,h5,h6,.heading-font,.banner h1,.banner h2 {
		font-weight: <?php echo intval( $type_headings['font-weight'] ); ?>;
		font-style: <?php echo $type_headings['font-style']; ?>;
	}
	<?php }

	// Alt Type
	if(!empty($type_alt ['font-family'])) {
	echo '.alt-font{font-family: '. silver_parse_font_family( $type_alt['font-family'] ).';}';
	}

	if ( ! empty( $type_alt['font-weight'] ) ) { ?>
	.alt-font {
		font-weight: <?php echo intval( $type_alt['font-weight'] ); ?>!important;
		font-style: <?php echo $type_alt['font-style']; ?>!important;
	}
	<?php } ?>
<?php endif;

// Customizer > Breadcrumbs > Bar background color (silver_breadcrumbs_bar()).
$fs_breadcrumbs_bar_bg = silver_css_var_value( get_theme_mod( 'breadcrumbs_bar_bg', '' ) );
if ( '' !== $fs_breadcrumbs_bar_bg ) {
	echo ':root{--sv-breadcrumbs-bar-bg:' . esc_html( $fs_breadcrumbs_bar_bg ) . ';}';
}

// Shop > Product Catalog > Category description background
// (structure-wc-category-description.php).
$fs_term_description_bg = silver_css_var_value( get_theme_mod( 'category_description_bg', '' ) );
if ( '' !== $fs_term_description_bg ) {
	echo ':root{--sv-term-description-bg:' . esc_html( $fs_term_description_bg ) . ';}';
}
// Its padding, once saved (else the section spacing of the theme).
$fs_term_description_pad = get_theme_mod( 'category_description_padding' );
if ( false !== $fs_term_description_pad && '' !== $fs_term_description_pad ) {
	echo '@media (min-width:850px){:root{--sv-term-description-pad:' . absint( $fs_term_description_pad ) . 'px;}}';
}
$fs_term_description_pad_mobile = get_theme_mod( 'category_description_padding_mobile' );
if ( false !== $fs_term_description_pad_mobile && '' !== $fs_term_description_pad_mobile ) {
	echo '@media (max-width:849px){:root{--sv-term-description-pad:' . absint( $fs_term_description_pad_mobile ) . 'px;}}';
}

// Text Transforms
if(get_theme_mod('text_transform_breadcrumbs')){
	echo '.breadcrumbs{text-transform: '.get_theme_mod('text_transform_breadcrumbs').';}';
}
if(get_theme_mod('text_transform_buttons')){
	echo 'button,.button{text-transform: '.get_theme_mod('text_transform_buttons').';}';
}
if(get_theme_mod('text_transform_navigation')){
	echo '.nav > li > a, .links > li > a{text-transform: '.get_theme_mod('text_transform_navigation').';}';
}
if(get_theme_mod('text_transform_section_titles')){
	echo '.section-title span{text-transform: '.get_theme_mod('text_transform_section_titles').';}';
}
if(get_theme_mod('text_transform_widget_titles')){
	echo 'h3.widget-title,span.widget-title{text-transform: '.get_theme_mod('text_transform_widget_titles').';}';
}

if(get_theme_mod('type_nav_top_color')){ ?>
.header:not(.transparent) .top-bar-nav > li > a {
color: <?php echo get_theme_mod('type_nav_top_color'); ?>;
}
<?php } ?>
<?php if(get_theme_mod('type_nav_top_color_hover')) { ?>
.header:not(.transparent) .top-bar-nav.nav > li > a:hover,
.header:not(.transparent) .top-bar-nav.nav > li.active > a,
.header:not(.transparent) .top-bar-nav.nav > li.current > a,
.header:not(.transparent) .top-bar-nav.nav > li > a.active,
.header:not(.transparent) .top-bar-nav.nav > li > a.current{
	color: <?php echo get_theme_mod('type_nav_top_color_hover'); ?>;
}
.top-bar-nav.nav-line-bottom > li > a:before,
.top-bar-nav.nav-line-grow > li > a:before,
.top-bar-nav.nav-line > li > a:before,
.top-bar-nav.nav-box > li > a:hover,
.top-bar-nav.nav-box > li.active > a,
.top-bar-nav.nav-pills > li > a:hover,
.top-bar-nav.nav-pills > li.active > a{
color:#FFF!important;
background-color: <?php echo get_theme_mod('type_nav_top_color_hover'); ?>;
}
<?php } ?>

<?php if(get_theme_mod('type_nav_color')){ ?>
.header:not(.transparent) .header-nav-main.nav > li > a {
	color: <?php echo get_theme_mod('type_nav_color'); ?>;
}
<?php } ?>
<?php if(get_theme_mod('type_nav_color_hover')) { ?>
.header:not(.transparent) .header-nav-main.nav > li > a:hover,
.header:not(.transparent) .header-nav-main.nav > li.active > a,
.header:not(.transparent) .header-nav-main.nav > li.current > a,
.header:not(.transparent) .header-nav-main.nav > li > a.active,
.header:not(.transparent) .header-nav-main.nav > li > a.current{
	color: <?php echo get_theme_mod('type_nav_color_hover'); ?>;
}
.header-nav-main.nav-line-bottom > li > a:before,
.header-nav-main.nav-line-grow > li > a:before,
.header-nav-main.nav-line > li > a:before,
.header-nav-main.nav-box > li > a:hover,
.header-nav-main.nav-box > li.active > a,
.header-nav-main.nav-pills > li > a:hover,
.header-nav-main.nav-pills > li.active > a{
color:#FFF!important;
background-color: <?php echo get_theme_mod('type_nav_color_hover'); ?>;
}
<?php } ?>

<?php if(get_theme_mod('type_nav_bottom_color')){ ?>
.header:not(.transparent) .header-bottom-nav.nav > li > a{
color: <?php echo get_theme_mod('type_nav_bottom_color'); ?>;
}
<?php } ?>

<?php if(get_theme_mod('type_nav_bottom_color_hover')){ ?>
.header:not(.transparent) .header-bottom-nav.nav > li > a:hover,
.header:not(.transparent) .header-bottom-nav.nav > li.active > a,
.header:not(.transparent) .header-bottom-nav.nav > li.current > a,
.header:not(.transparent) .header-bottom-nav.nav > li > a.active,
.header:not(.transparent) .header-bottom-nav.nav > li > a.current{
color: <?php echo get_theme_mod('type_nav_bottom_color_hover'); ?>;
}
.header-bottom-nav.nav-line-bottom > li > a:before,
.header-bottom-nav.nav-line-grow > li > a:before,
.header-bottom-nav.nav-line > li > a:before,
.header-bottom-nav.nav-box > li > a:hover,
.header-bottom-nav.nav-box > li.active > a,
.header-bottom-nav.nav-pills > li > a:hover,
.header-bottom-nav.nav-pills > li.active > a{
color:#FFF!important;
background-color: <?php echo get_theme_mod('type_nav_bottom_color_hover'); ?>;
}
<?php } ?>

<?php if(get_theme_mod('color_widget_links')){ ?>
.widget:where(:not(.widget_shopping_cart)) a{color: <?php echo get_theme_mod('color_widget_links'); ?>;}
.widget:where(:not(.widget_shopping_cart)) a:hover{color: <?php echo get_theme_mod('color_widget_links_hover'); ?>;}
.widget .tagcloud a:hover{border-color: <?php echo get_theme_mod('color_widget_links_hover'); ?>; background-color: <?php echo get_theme_mod('color_widget_links_hover'); ?>;}
<?php } ?>

<?php if(get_theme_mod('color_divider')){ ?>
.is-divider{background-color: <?php echo get_theme_mod('color_divider'); ?>;}
<?php } ?>

<?php if(is_woocommerce_activated() && get_theme_mod('header_shop_bg_color')){ ?>
.shop-page-title.featured-title .title-overlay{
background-color: <?php echo get_theme_mod('header_shop_bg_color') ?>;}
<?php } ?>

<?php if(get_theme_mod('color_checkout')) { ?>
  	.current .breadcrumb-step, [data-icon-label]:after, .button#place_order,.button.checkout,.checkout-button,.single_add_to_cart_button.button, .sticky-add-to-cart-select-options-button{background-color: <?php echo get_theme_mod('color_checkout'); ?>!important }
<?php } ?>

<?php
// Product image ratio (silver_product_image_ratio()): the image box of every
// product card the same height, the image fills it (or fits in it), CSS only.
// The cards with the image beside the text (.box-vertical) keep theirs: their
// padding % would be of the whole card.
$fs_product_image_ratio = function_exists( 'silver_product_image_ratio' ) ? silver_product_image_ratio() : '';
if ( '' !== $fs_product_image_ratio ) {
	$fs_product_image_fit = 'contain' === get_theme_mod( 'product_image_fit', 'cover' ) ? 'contain' : 'cover';
	?>
  .product-small.box:not(.box-vertical) > .box-image,
  .has-equal-box-heights .box-image {
    padding-top: <?php echo esc_html( $fs_product_image_ratio ); ?>;
    width: 100%;
  }
  .product-small.box:not(.box-vertical) > .box-image img {
    bottom: 0;
    height: 100%;
    left: 0;
    object-fit: <?php echo esc_html( $fs_product_image_fit ); ?>;
    object-position: 50% 50%;
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
  }
	<?php if ( 'contain' === $fs_product_image_fit ) { ?>
  .product-small.box:not(.box-vertical) > .box-image,
  .has-equal-box-heights .box-image {
    background-color: var(--sv-surface, #fff);
  }
  .has-equal-box-heights .box-image img {
    object-fit: contain;
  }
	<?php } ?>
<?php } ?>

<?php
// Product page gallery ratio (silver_product_gallery_ratio()): every image of
// the gallery (all its layouts) and the thumbnails in a box of the same
// ratio, CSS only. The slider no longer changes height from one image to the
// next. aspect-ratio = width / height, from the height in %.
$fs_gallery_ratio = function_exists( 'silver_product_gallery_ratio' ) ? silver_product_gallery_ratio() : '';
if ( '' !== $fs_gallery_ratio ) {
	$fs_gallery_aspect = '100 / ' . (float) $fs_gallery_ratio;
	$fs_gallery_fit    = 'cover' === get_theme_mod( 'product_gallery_fit', 'contain' ) ? 'cover' : 'contain';
	?>
  .woocommerce-product-gallery .woocommerce-product-gallery__image > a {
    aspect-ratio: <?php echo esc_html( $fs_gallery_aspect ); ?>;
    box-sizing: border-box;
    display: block;
    overflow: hidden;
  }
  .woocommerce-product-gallery .woocommerce-product-gallery__image > a > img {
    height: 100%;
    object-fit: <?php echo esc_html( $fs_gallery_fit ); ?>;
    width: 100%;
  }
  /* Thumbnails: small tiles that fill (cover), whatever the fit of the main
     images: even, clean tiles; the whole image is in the main view. */
  .product-thumbnails .col a img,
  .product-gallery-rail.product-thumbnails .col a img {
    aspect-ratio: <?php echo esc_html( $fs_gallery_aspect ); ?>;
    height: auto;
    object-fit: cover;
    width: 100%;
  }
	<?php if ( 'contain' === $fs_gallery_fit ) { ?>
  /* Whole image: the image is not cut; the empty sides of the box show the
     same image blurred (--sv-gallery-backdrop: the small gallery thumbnail,
     silver_wc_get_gallery_image_html()), so the box is always full and its
     colours match the image. */
  .woocommerce-product-gallery .woocommerce-product-gallery__image > a {
    background-color: var(--sv-surface-muted, #f8fafc);
    isolation: isolate;
    position: relative;
  }
  .woocommerce-product-gallery .woocommerce-product-gallery__image > a::before {
    background: var(--sv-gallery-backdrop, none) center / cover no-repeat;
    content: "";
    filter: blur(32px) saturate(1.15);
    inset: -48px;
    opacity: 0.85;
    pointer-events: none;
    position: absolute;
    transform: translateZ(0);
    z-index: 0;
  }
  .woocommerce-product-gallery .woocommerce-product-gallery__image > a > img {
    filter: drop-shadow(0 4px 14px rgb(15 23 42 / 10%));
    position: relative;
    z-index: 1;
  }
	<?php } ?>
<?php } ?>

<?php if(get_theme_mod('color_sale')) { ?>
  .badge-inner.on-sale, .product-price-badge{background-color: <?php echo get_theme_mod('color_sale'); ?>}
<?php } ?>
<?php if ( get_theme_mod( 'color_price_badge' ) ) { // Style > Colors > Discount label (product page buy box). ?>
  .product-price-badge{background-color: <?php echo esc_attr( get_theme_mod( 'color_price_badge' ) ); ?>}
<?php } ?>

<?php if ( get_theme_mod( 'color_new_bubble_auto' ) ) { ?>
  .badge-inner.new-bubble-auto{background-color: <?php echo get_theme_mod( 'color_new_bubble_auto' ); ?>}
<?php } ?>

<?php if(get_theme_mod('color_new_bubble')) { ?>
  .badge-inner.new-bubble{background-color: <?php echo get_theme_mod('color_new_bubble'); ?>}
<?php } ?>

<?php if(get_theme_mod('color_review')) { ?>
	.star-rating span:before,.star-rating:before, .woocommerce-page .star-rating:before, .stars a:hover:after, .stars a.active:after, .star-rating-compact__icon, .product-rating-summary__star{color: <?php echo get_theme_mod('color_review'); ?>}
	:root{--sv-review-color: <?php echo get_theme_mod('color_review'); ?>}
<?php } ?>

<?php
// Price colours (Style > Colors):
// - Sale Price: the price a product sells at, on sale or not (a single
//   price, a price range, the price after a discount).
// - Regular Price: the struck-out price of a product on sale, at full colour
//   (the base CSS fades it to 60%).
// The Sale Price rule comes first, so the struck-out price keeps its own
// colour even when both are set.
if ( is_woocommerce_activated() && get_theme_mod( 'color_sale_price' ) ) { ?>
:root { --sv-price: <?php echo esc_attr( get_theme_mod( 'color_sale_price' ) ); ?>; }
.price > .woocommerce-Price-amount, .price ins .woocommerce-Price-amount, ins .woocommerce-Price-amount { color: <?php echo esc_attr( get_theme_mod( 'color_sale_price' ) ); ?>; }
<?php } ?>

<?php if ( is_woocommerce_activated() && get_theme_mod( 'color_regular_price' ) ) { ?>
.price del, .product_list_widget del, del .woocommerce-Price-amount, .price del .woocommerce-Price-amount { color: <?php echo esc_attr( get_theme_mod( 'color_regular_price' ) ); ?>; }
del span.amount { opacity: 1; }
<?php } ?>

<?php if(is_woocommerce_activated() && get_theme_mod('header_shop_bg_image')){ ?>
.shop-page-title.featured-title .title-bg{background-image: url(<?php echo get_theme_mod('header_shop_bg_image'); ?>);}
<?php } ?>

<?php if(get_theme_mod('button_radius') && get_theme_mod('button_radius')  !== '0px') { ?>
input[type='submit'], input[type="button"], button:not(.icon), .button:not(.icon){border-radius: <?php echo get_theme_mod('button_radius');?>!important}
<?php } ?>

<?php if(get_theme_mod('flatsome_lightbox_bg')) { ?>
  .pswp__bg,.mfp-bg.mfp-ready{background-color: <?php echo get_theme_mod('flatsome_lightbox_bg'); ?>}
<?php } ?>

<?php if(is_woocommerce_activated() && get_theme_mod('header_shop_bg_featured', 1)) {  ?>
<?php if(is_product_category() || is_product_tag()) { ?>
<?php
global $wp_query;
$cat = $wp_query->get_queried_object();
$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
$image = wp_get_attachment_url( $thumbnail_id );
if($image) echo '.shop-page-title.featured-title .title-bg{background-image: url('.$image.')!important;}';
?>
<?php } ?>
<?php if(is_product()) {
// On product pages
global $post;
$feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
echo '.shop-page-title.featured-title .title-bg{ background-image: url('.$feat_image.')!important;}';
} ?>
<?php } ?>

<?php if(is_woocommerce_activated()){
	$image_sizes = wc_get_image_size('thumbnail');
	$image_width = $image_sizes['width'];
?>
@media screen and (min-width: <?php echo $small_screen; ?>){
.products .box-vertical .box-image{
  min-width: <?php echo $image_width;?>px!important;
  width: <?php echo $image_width;?>px!important;
}
}
<?php } ?>


<?php
$header_icons_color = get_theme_mod('header_icons_color');
$header_icons_color_hover = get_theme_mod('header_icons_color_hover');
if(!$header_icons_color_hover) $header_icons_color_hover = $header_icons_color;
if($header_icons_color){ ?>
.header-main .social-icons,
.header-main .cart-icon strong,
.header-main .menu-title,
.header-main .header-button > .button.is-outline,
.header-main .nav > li > a > i:not(.icon-angle-down){
	color: <?php echo $header_icons_color; ?>!important;
}
.header-main .header-button > .button.is-outline,
.header-main .cart-icon strong:after,
.header-main .cart-icon strong{
	border-color: <?php echo $header_icons_color; ?>!important;
}
.header-main .header-button > .button:not(.is-outline){
	background-color: <?php echo $header_icons_color; ?>!important;
}

.header-main .current-dropdown .cart-icon strong,
.header-main .header-button > .button:hover,
.header-main .header-button > .button:hover i,
.header-main .header-button > .button:hover span{
	color:#FFF!important;
}
<?php if($header_icons_color_hover){ ?>
.header-main .menu-title:hover,
.header-main .social-icons a:hover,
.header-main .header-button > .button.is-outline:hover,
.header-main .nav > li > a:hover > i:not(.icon-angle-down){
	color: <?php echo $header_icons_color_hover; ?>!important;
}

.header-main .current-dropdown .cart-icon strong,
.header-main .header-button > .button:hover{
	background-color: <?php echo $header_icons_color_hover; ?>!important;
}
.header-main .current-dropdown .cart-icon strong:after,
.header-main .current-dropdown .cart-icon strong,
.header-main .header-button > .button:hover{
	border-color: <?php echo $header_icons_color_hover; ?>!important;
}
<?php } ?>
<?php } ?>

<?php if(get_theme_mod('footer_1_bg_image')){ ?>
.footer-1{background-image: url('<?php echo get_theme_mod('footer_1_bg_image'); ?>');}
<?php } ?>
<?php if(get_theme_mod('footer_2_bg_image')){ ?>
.footer-2{background-image: url('<?php echo get_theme_mod('footer_2_bg_image'); ?>');}
<?php } ?>
<?php if(get_theme_mod('footer_1_bg_color')){ ?>
.footer-1{background-color: <?php echo get_theme_mod('footer_1_bg_color') ;?>}
<?php } ?>
<?php if(get_theme_mod('footer_2_bg_color')){ ?>
.footer-2{background-color: <?php echo get_theme_mod('footer_2_bg_color') ;?>}
<?php } ?>


<?php if(get_theme_mod('product_header') == 'top') {
echo '.page-title-small + main .product-container > .row{padding-top:0;}';
} ?>

<?php if ( get_theme_mod( 'cart_auto_refresh' ) ) {
	echo 'button[name=\'update_cart\'] { display: none; }';
} ?>

<?php if ( get_theme_mod( 'header_nav_vertical_height', '50' ) != 50 ) { ?>
.header-vertical-menu__opener{height: <?php echo get_theme_mod('header_nav_vertical_height');?>px}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_width', '250' ) != 250 ) { ?>
.header-vertical-menu__opener {width: <?php echo get_theme_mod('header_nav_vertical_width');?>px}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_width', '250' ) != 250 ) { ?>
.header-vertical-menu__fly-out {width: <?php echo get_theme_mod('header_nav_vertical_fly_out_width');?>px}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_color' ) ) { ?>
	.header-vertical-menu__opener{color: <?php echo get_theme_mod('header_nav_vertical_color');?>}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_bg_color' ) ) { ?>
	.header-vertical-menu__opener{background-color: <?php echo get_theme_mod('header_nav_vertical_bg_color');?>}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_bg_color' ) ) { ?>
.header-vertical-menu__fly-out{background-color: <?php echo get_theme_mod('header_nav_vertical_fly_out_bg_color');?>}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_nav_divider', 1 ) ) { ?>
.nav-vertical-fly-out > li + li {border-top-width: 1px; border-top-style: solid;}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_nav_color' ) ) { ?>
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item > a {
	color: <?php echo get_theme_mod( 'header_nav_vertical_fly_out_nav_color' ); ?>;
}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_nav_color_hover' ) ) { ?>
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item > a:hover,
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item.active > a,
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item.current-dropdown > a {
	color: <?php echo get_theme_mod( 'header_nav_vertical_fly_out_nav_color_hover' ); ?>;
}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_nav_bg_color_hover' ) ) { ?>
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item > a:hover,
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item.active > a,
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item.current-dropdown > a {
	background-color: <?php echo get_theme_mod( 'header_nav_vertical_fly_out_nav_bg_color_hover' ); ?>;
}
<?php } ?>

<?php if ( get_theme_mod( 'header_nav_vertical_fly_out_nav_height' ) ) { ?>
.header-vertical-menu__fly-out .nav-vertical-fly-out > li.menu-item > a {
	height: <?php echo intval( get_theme_mod( 'header_nav_vertical_fly_out_nav_height' ) ); ?>px;
}
<?php } ?>

<?php if(get_theme_mod('html_custom_css')){
echo '/* Custom CSS */';
echo get_theme_mod('html_custom_css');
} ?>

<?php if(get_theme_mod('html_custom_css_tablet')){
echo '/* Custom CSS Tablet */';
echo '@media (max-width: ' . $medium_screen_max . '){';
echo get_theme_mod('html_custom_css_tablet');
echo '}';
} ?>

<?php if(get_theme_mod('html_custom_css_tablet_landscape')){
echo '/* Custom CSS Tablet landscape */';
echo '@media (min-width: ' . $large_screen . ') and (max-width: ' . $medium_screen_max . '){';
echo get_theme_mod('html_custom_css_tablet_landscape');
echo '}';
} ?>

<?php if(get_theme_mod('html_custom_css_tablet_portrait')){
echo '/* Custom CSS Tablet portrait */';
echo '@media (min-width: ' . $small_screen . ') and (max-width: ' . $large_screen_max . '){';
echo get_theme_mod('html_custom_css_tablet_portrait');
echo '}';
} ?>

<?php if(get_theme_mod('html_custom_css_mobile')){
echo '/* Custom CSS Mobile */';
echo '@media (max-width: ' . $small_screen_max . '){';
echo get_theme_mod('html_custom_css_mobile');
echo '}';
} ?>

<?php if(is_admin_bar_showing() && !is_customize_preview()){ ?>
@media (max-width: <?php echo $medium_screen_max; ?>){
	#wpadminbar{display: none!important;}
	html{margin-top: 0!important}
}
@media (min-width: <?php echo $medium_screen; ?>){
	.mfp-content,
	.stuck,
	button.mfp-close{
	top: 32px!important;
	}
	.is-full-height{height: calc(100vh - 32px)!important;}
}
<?php } ?>

<?php if(is_admin_bar_showing() || is_customize_preview()) { ?>
.xdebug-var-dump{
	z-index: 999999;
}
.shortcode-error{
border: 2px dashed #000;
padding: 20px;
color:#fff;
font-size:16px;
background-color: #71cedf;
}
.custom-product-page .shortcode-error {
	padding: 15% 10%;
	text-align: center;
}
<?php } ?>

<?php  if ( current_user_can( 'edit_pages' ) && is_admin_bar_showing() ) { ?>
	.edit-block-wrapper{
		position: relative;
	}
	.edit-block-button{
		font-size: 12px!important;
		background-color: #555!important;
		margin: 6px 2px 3px 0px!important;
		border-radius: 4px!important;
	}
	.edit-block-button-builder{
		background-color: #00a0d2!important;
	}
<?php } ?>

.label-new.menu-item > a:after{content:"<?php _e('New','silver'); ?>";}
.label-hot.menu-item > a:after{content:"<?php _e('Hot','silver'); ?>";}
.label-sale.menu-item > a:after{content:"<?php _e('Sale','silver'); ?>";}
.label-popular.menu-item > a:after{content:"<?php _e('Popular','silver'); ?>";}

</style>

<?php
$buffer = ob_get_clean();
echo silver_minify_css($buffer);
}
add_action( 'wp_head', 'silver_custom_css', 100 );
