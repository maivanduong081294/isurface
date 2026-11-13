<?php
/**
 * The overlay menu.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

$silver_mobile_overlay         = get_theme_mod( 'mobile_overlay' );
$silver_mobile_sidebar_classes = array( 'mobile-sidebar', 'no-scrollbar', 'mfp-hide' );
$silver_nav_classes            = array( 'nav', 'nav-sidebar', 'nav-vertical', 'nav-uppercase' );
$silver_levels                 = 0;

if ( 'center' == $silver_mobile_overlay ) {
	$silver_nav_classes[] = 'nav-anim';
}

if (
	'center' != $silver_mobile_overlay &&
	'slide' == get_theme_mod( 'mobile_submenu_effect' )
) {
	$silver_levels = (int) get_theme_mod( 'mobile_submenu_levels', '1' );

	$silver_mobile_sidebar_classes[] = 'mobile-sidebar-slide';
	$silver_nav_classes[]            = 'nav-slide';

	for ( $level = 1; $level <= $silver_levels; $level++ ) {
		$silver_mobile_sidebar_classes[] = "mobile-sidebar-levels-{$level}";
	}
}
?>
<div id="main-menu" class="<?php echo esc_attr( implode( ' ', $silver_mobile_sidebar_classes ) ); ?>"<?php echo $silver_levels ? ' data-levels="' . esc_attr( $silver_levels ) . '"' : ''; ?>>

	<?php do_action( 'silver_before_sidebar_menu' ); ?>

	<div class="sidebar-menu no-scrollbar <?php if ( $silver_mobile_overlay == 'center') echo 'text-center'; ?>">

		<?php do_action( 'silver_before_sidebar_menu_elements' ); ?>

		<?php if ( get_theme_mod( 'mobile_sidebar_tabs' ) ) : ?>

			<ul class="sidebar-menu-tabs flex nav nav-line-bottom nav-uppercase">
				<li class="sidebar-menu-tabs__tab active">
					<a class="sidebar-menu-tabs__tab-link" href="#">
						<span class="sidebar-menu-tabs__tab-text"><?php echo get_theme_mod( 'mobile_sidebar_tab_text' ) ? esc_html( get_theme_mod( 'mobile_sidebar_tab_text' ) ) : esc_html__( 'Menu', 'silver' ); ?></span>
					</a>
				</li>
				<li class="sidebar-menu-tabs__tab">
					<a class="sidebar-menu-tabs__tab-link" href="#">
						<span class="sidebar-menu-tabs__tab-text"><?php echo get_theme_mod( 'mobile_sidebar_tab_2_text' ) ? esc_html( get_theme_mod( 'mobile_sidebar_tab_2_text' ) ) : esc_html__( 'Categories', 'silver' ); ?></span>
					</a>
				</li>
			</ul>

			<ul class="<?php echo esc_attr( implode( ' ', $silver_nav_classes ) ); ?> hidden" data-tab="2">
				<?php silver_header_elements( 'mobile_sidebar_tab_2', 'sidebar' ); ?>
			</ul>
			<ul class="<?php echo esc_attr( implode( ' ', $silver_nav_classes ) ); ?>" data-tab="1">
				<?php silver_header_elements( 'mobile_sidebar', 'sidebar' ); ?>
			</ul>
		<?php else : ?>
			<ul class="<?php echo esc_attr( implode( ' ', $silver_nav_classes ) ); ?>" data-tab="1">
				<?php silver_header_elements( 'mobile_sidebar', 'sidebar' ); ?>
			</ul>
		<?php endif; ?>

		<?php do_action( 'silver_after_sidebar_menu_elements' ); ?>

	</div>

	<?php do_action( 'silver_after_sidebar_menu' ); ?>

</div>
