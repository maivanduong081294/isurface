<?php
/**
 * Header top.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 */

if(silver_has_top_bar()['large_or_mobile']){ ?>
<div id="top-bar" class="header-top <?php header_inner_class('top'); ?>">
    <div class="flex-row container">
      <?php if ( silver_header_block_id( 'top' ) ) { ?>
      <div class="flex-col flex-grow header-block header-block-top">
          <?php silver_header_block( 'top' ); ?>
      </div>
      <?php } else { ?>
      <div class="flex-col hide-for-medium flex-left">
          <ul class="nav nav-left medium-nav-center nav-small <?php silver_nav_classes('top'); ?>">
              <?php silver_header_elements('topbar_elements_left'); ?>
          </ul>
      </div>

      <div class="flex-col hide-for-medium flex-center">
          <ul class="nav nav-center nav-small <?php silver_nav_classes('top'); ?>">
              <?php silver_header_elements('topbar_elements_center'); ?>
          </ul>
      </div>

      <div class="flex-col hide-for-medium flex-right">
         <ul class="nav top-bar-nav nav-right nav-small <?php silver_nav_classes('top'); ?>">
              <?php silver_header_elements('topbar_elements_right'); ?>
          </ul>
      </div>

      <?php if(get_theme_mod('header_mobile_elements_top')) { ?>
      <div class="flex-col show-for-medium flex-grow">
          <ul class="nav nav-center nav-small mobile-nav <?php silver_nav_classes('top'); ?>">
              <?php silver_header_elements('header_mobile_elements_top'); ?>
          </ul>
      </div>
      <?php } ?>
      <?php } ?>

    </div>
</div>
<?php } ?>
