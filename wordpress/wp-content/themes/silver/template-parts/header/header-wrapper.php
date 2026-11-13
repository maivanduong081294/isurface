<?php
/**
 * Header wrapper.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

// Get Header Top template. Located in silver/template-parts/header/header-top.php
  get_template_part('template-parts/header/header','top');

  // Get Header Main template. Located in silver/template-parts/header/header-main.php
  get_template_part('template-parts/header/header', 'main');

  // Get Header Bottom template. Located in silver/template-parts/header/header-bottom-*.php
  get_template_part('template-parts/header/header', 'bottom');


  // Header Backgrounds
  echo '<div class="header-bg-container fill">';
  do_action('silver_header_background');
  echo '</div>';

  do_action('silver_header_wrapper');
?>
