<?php // @codingStandardsIgnoreLine


namespace Silver\Extensions;

defined( 'ABSPATH' ) || exit;


global $extensions_url;
require $extensions_url . '/silver-variation-images/includes/class-variation-images.php';

variation_images();
