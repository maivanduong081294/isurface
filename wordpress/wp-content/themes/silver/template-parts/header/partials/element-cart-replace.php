<?php
/**
 * Cart replace element.
 *
 * @package          Silver\Templates
 * @silver-version 3.19.9
 */

if ( get_theme_mod( 'catalog_mode_header', '' ) ) echo '<li class="html cart-replace">' . do_shortcode( get_theme_mod( 'catalog_mode_header', '' ) ) . '</li>';
