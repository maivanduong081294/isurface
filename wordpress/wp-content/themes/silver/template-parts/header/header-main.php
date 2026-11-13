<?php
/**
 * Header main.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

?>
<div id="masthead" class="header-main <?php header_inner_class('main'); ?>">
      <?php if ( silver_header_block_id( 'main' ) ) { ?>
      <div class="header-inner flex-row container" role="navigation">
          <div class="flex-col flex-grow header-block header-block-main">
            <?php silver_header_block( 'main' ); ?>
          </div>
      </div>
      <?php } else { ?>
      <div class="header-inner flex-row container <?php silver_logo_position(); ?>" role="navigation">

          <!-- Logo -->
          <div id="logo" class="flex-col logo">
            <?php get_template_part('template-parts/header/partials/element','logo'); ?>
          </div>

          <!-- Mobile Left Elements -->
          <div class="flex-col show-for-medium flex-left">
            <ul class="mobile-nav nav nav-left <?php silver_nav_classes('main-mobile'); ?>">
              <?php silver_header_elements('header_mobile_elements_left','mobile'); ?>
            </ul>
          </div>

          <!-- Left Elements -->
          <div class="flex-col hide-for-medium flex-left
            <?php if(get_theme_mod('logo_position', 'left') == 'left') echo 'flex-grow'; ?>">
            <ul class="header-nav header-nav-main nav nav-left <?php silver_nav_classes('main'); ?>" >
              <?php silver_header_elements('header_elements_left'); ?>
            </ul>
          </div>

          <!-- Right Elements -->
          <div class="flex-col hide-for-medium flex-right">
            <ul class="header-nav header-nav-main nav nav-right <?php silver_nav_classes('main'); ?>">
              <?php silver_header_elements('header_elements_right'); ?>
            </ul>
          </div>

          <!-- Mobile Right Elements -->
          <div class="flex-col show-for-medium flex-right">
            <ul class="mobile-nav nav nav-right <?php silver_nav_classes('main-mobile'); ?>">
              <?php silver_header_elements('header_mobile_elements_right','mobile'); ?>
            </ul>
          </div>

      </div>
      <?php } ?>

      <?php if(get_theme_mod('header_divider', 1)) { ?>
      <div class="container"><div class="top-divider full-width"></div></div>
      <?php }?>
</div>
