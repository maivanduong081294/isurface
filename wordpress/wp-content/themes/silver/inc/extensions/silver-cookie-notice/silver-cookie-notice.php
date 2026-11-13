<?php
/**
 * Silver Cookie notice extension
 *
 * @author     UX Themes
 * @category   Extension
 * @package    Silver/Extensions
 * @since      3.12.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue extensions scripts.
 */
function silver_cookie_notice_scripts() {
	wp_enqueue_script(
		'silver-cookie-notice',
		get_template_directory_uri() . '/assets/js/extensions/silver-cookie-notice.js',
		[ 'jquery', 'silver-js', 'wp-dom-ready' ],
		silver()->version(),
		true
	);
}

add_action( 'wp_enqueue_scripts', 'silver_cookie_notice_scripts' );

/**
 * Html template for cookie notice.
 */
function silver_cookie_notice_template() {
	if ( ! get_theme_mod( 'cookie_notice' ) ) {
		return;
	}

	$classes = array( 'silver-cookies' );
	if ( get_theme_mod( 'cookie_notice_text_color' ) === 'dark' ) {
		$classes[] = 'dark';
	}
	$text = get_theme_mod( 'cookie_notice_text' );
	$id   = get_theme_mod( 'privacy_policy_page' );
	$page = $id ? get_post( $id ) : false;
	$text = $text ? $text : esc_html__( 'This site uses cookies to offer you a better browsing experience. By browsing this website, you agree to our use of cookies.', 'silver' );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" inert>
		<div class="silver-cookies__inner">
			<div class="silver-cookies__text">
				<?php echo do_shortcode( $text ); ?>
			</div>
			<div class="silver-cookies__buttons">
				<?php
				if ( $page ) {
					echo silver_apply_shortcode( 'button', array(
						'text'  => _x( 'More info', 'cookie notice', 'silver' ),
						'style' => get_theme_mod( 'cookie_notice_button_style', '' ),
						'link'  => get_permalink( $page->ID ),
						'color' => 'secondary',
						'class' => 'silver-cookies__more-btn',
					) );
				}
				?>
				<?php
				echo silver_apply_shortcode( 'button', array(
					'as'    => 'button',
					'text'  => _x( 'Accept', 'cookie notice', 'silver' ),
					'style' => get_theme_mod( 'cookie_notice_button_style', '' ),
					'color' => 'primary',
					'class' => 'silver-cookies__accept-btn',
				) );
				?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'wp_footer', 'silver_cookie_notice_template' );
