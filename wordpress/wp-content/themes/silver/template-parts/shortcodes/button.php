<?php
/**
 * Shortcode button.
 *
 * @package          Silver\Templates
 * @silver-version 3.20.0
 */
?>
<div<?php if ( $_id ) : ?> id="<?php echo esc_attr( $_id ); ?>"<?php endif; ?> class="silver-button-wrapper<?php echo $class ? ' ' . esc_attr( $class ) : ''; ?>">
<<?php echo tag_escape( $as ); ?> <?php echo $attributes; ?> <?php echo $styles; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php echo $icon_left; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo wp_kses_post( $text ); ?></span>
	<?php echo $icon_right; // phpcs:ignore WordPress.Security.EscapeOutput ?>
</<?php echo tag_escape( $as ); ?>>
</div>
