<?php
/**
 * Newsletter sidebar element.
 *
 * @package          Silver\Templates
 * @silver-version 3.20.0
 */

$label     = get_theme_mod( 'header_newsletter_label', 'Newsletter' );
$title     = get_theme_mod( 'header_newsletter_title', 'Sign up for Newsletter' );
$link_atts = [
	'href'          => '#header-newsletter-signup',
	'class'         => 'tooltip',
	'title'         => esc_attr( $title ),
	'role'          => 'button',
	'aria-expanded' => 'false',
	'aria-haspopup' => 'dialog',
	'aria-controls' => 'header-newsletter-signup',
];
?>
<li class="header-newsletter-item has-icon">
	<a <?php echo silver_html_atts( $link_atts ); ?>>
		<?php echo get_silver_icon( 'icon-envelop' ); ?>
		<span class="header-newsletter-title">
			<?php echo $label; ?>
		</span>
	</a>
</li>
