<?php
/**
 * Posts archive 2 column (older layout, still saved on some sites): the Grid
 * layout, 2 posts per row on desktop until Posts per row is set.
 *
 * @package          Silver\Templates
 * @silver-version 3.18.0
 */

get_template_part( 'template-parts/posts/archive', 'grid', array( 'desktop_columns' => 2 ) );
