<?php
/**
 * Single Product tabs
 *
 * @author           WooThemes
 * @package          WooCommerce/Templates
 * @version          2.0.0
 * @silver-version 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

// Full-width product footer (silver_product_footer_sections()): each section
// full width with a container of its own, so it can have its own background.
$silver_section_container = did_action( 'silver_product_footer_sections' ) > 0;

// Product Page > merge the description and the specifications in one section:
// two columns, the description on the left or the right (stacked on phones,
// the description first).
$silver_merge = get_theme_mod( 'product_sections_merge_specs', 0 ) && isset( $product_tabs['description'], $product_tabs['additional_information'] );
$silver_merge_right = 'right' === get_theme_mod( 'product_sections_merge_position', 'left' );

/**
 * One column of the merged section: the title and the content of a tab.
 *
 * @param string $key Tab key.
 * @param array  $tab Tab.
 */
$silver_tab_column = static function ( $key, $tab ) {
	?>
	<h2 class="uppercase product-tab-title product-tab-title--section"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
	<div class="panel entry-content">
		<?php
		if ( isset( $tab['callback'] ) ) {
			call_user_func( $tab['callback'], $key, $tab );
		}
		?>
	</div>
	<?php
};

if ( ! empty( $product_tabs ) ) : ?>
<div class="product-page-sections">
	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
	<?php
	if ( $silver_merge && 'additional_information' === $key ) {
		continue; // In the description section.
	}

	if ( $silver_merge && 'description' === $key ) :
		?>
		<div class="product-section product-section--description product-section--merged<?php echo $silver_merge_right ? ' is-description-right' : ''; ?>">
		<?php if ( $silver_section_container ) : ?><div class="container"><?php endif; ?>
		<div class="row product-section__columns">
			<div class="col medium-12 large-7 pb-0 mb-0 product-section__column product-section__column--description">
				<?php $silver_tab_column( 'description', $product_tabs['description'] ); ?>
			</div>
			<div class="col medium-12 large-5 pb-0 mb-0 product-section__column product-section__column--specs">
				<?php $silver_tab_column( 'additional_information', $product_tabs['additional_information'] ); ?>
			</div>
		</div>
		<?php if ( $silver_section_container ) : ?></div><?php endif; ?>
		</div>
		<?php
		continue;
	endif;
	?>
	<div class="product-section product-section--<?php echo esc_attr( sanitize_html_class( $key ) ); ?>">
	<?php if ( $silver_section_container ) : ?><div class="container"><?php endif; ?>
	<?php // Two rows: the title, then the content at full width. ?>
	<div class="row">
		<div class="col small-12 pb-0 mb-0">
			<h2 class="uppercase product-tab-title product-tab-title--section"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ); ?></h2>
		</div>

		<div class="col small-12 pb-0 mb-0">
			<div class="panel entry-content">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		</div>
	</div>
	<?php if ( $silver_section_container ) : ?></div><?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
