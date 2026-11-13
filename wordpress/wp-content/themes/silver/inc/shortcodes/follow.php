<?php
/**
 * Registers the `follow` shortcode.
 *
 * @package silver
 */

/**
 * Renders the `follow` shortcode.
 *
 * @param array  $atts    An array of attributes.
 * @param string $content The shortcode content.
 * @param string $tag     The name of the shortcode, provided for context to enable filtering.
 *
 * @return string
 */
function silver_follow( $atts, $content = null, $tag = '' ) {
	extract( $atts = ux_builder_shortcode_atts(
		array(
			'_id'        => '',
			'title'      => '',
			'class'      => '',
			'visibility' => '',
			'style'      => 'outline',
			'align'      => '',
			'scale'      => '',
			'tooltip'    => 'true',
			'icon_order' => '',
			'facebook'   => '',
			'instagram'  => '',
			'tiktok'     => '',
			'snapchat'   => '',
			'x'          => '',
			'twitter'    => '',
			'threads'    => '',
			'linkedin'   => '',
			'email'      => '',
			'phone'      => '',
			'pinterest'  => '',
			'rss'        => '',
			'youtube'    => '',
			'flickr'     => '',
			'vkontakte'  => '',
			'px500'      => '',
			'telegram'   => '',
			'discord'    => '',
			'twitch'     => '',
			'whatsapp'   => '',
			'zalo'       => '',
			// Deprecated.
			'size'       => '',
		),
		$atts,
		$tag
	) );

	$wrapper_class = array( 'social-icons', 'follow-icons' );

	if ( $class ) $wrapper_class[]      = $class;
	if ( $visibility ) $wrapper_class[] = $visibility;
	// "inline" is the default (explicit value for responsive options).
	if ( $align && $align !== 'inline' ) {
		$wrapper_class[] = 'full-width';
		$wrapper_class[] = 'text-' . $align;
	}

	// Responsive align (larger screens).
	$responsive_style = '';
	if ( silver_responsive_has_overrides( (array) $atts, 'align' ) ) {
		if ( ! $_id ) $_id = 'follow-' . wp_rand();
		$responsive_style = silver_responsive_style_tag( $_id, (array) $atts, array(
			'align' => array( 'selector' => '', 'only_overrides' => true, 'default' => 'inline', 'important' => false, 'map' => silver_responsive_map( 'icons_align' ) ),
		) );
	}

	$_social_links = array(
		'facebook'  => $facebook,
		'instagram' => $instagram,
		'tiktok'    => $tiktok,
		'snapchat'  => $snapchat,
		'x'         => $x,
		'twitter'   => $twitter,
		'threads'   => $threads,
		'email'     => $email,
		'phone'     => $phone,
		'pinterest' => $pinterest,
		'rss'       => $rss,
		'linkedin'  => $linkedin,
		'youtube'   => $youtube,
		'flickr'    => $flickr,
		'px500'     => $px500,
		'vkontakte' => $vkontakte,
		'telegram'  => $telegram,
		'twitch'    => $twitch,
		'discord'   => $discord,
		'whatsapp'  => $whatsapp,
		'zalo'      => $zalo,
	);

	$social_links        = apply_filters( "silver_shortcode_{$tag}_social_links", $_social_links, $atts );
	$custom_social_links = array_diff( $_social_links, $social_links );
	$use_global_link     = count( array_filter( $social_links ) ) === 0 && count( array_filter( $custom_social_links ) ) === 0;

	// Use global follow links if non is set individually.
	if ( $use_global_link ) {
		$facebook  = get_theme_mod( 'follow_facebook' );
		$instagram = get_theme_mod( 'follow_instagram' );
		$tiktok    = get_theme_mod( 'follow_tiktok' );
		$snapchat  = get_theme_mod( 'follow_snapchat' );
		$x         = get_theme_mod( 'follow_x' );
		$twitter   = get_theme_mod( 'follow_twitter' );
		$threads   = get_theme_mod( 'follow_threads' );
		$email     = get_theme_mod( 'follow_email' );
		$phone     = get_theme_mod( 'follow_phone' );
		$pinterest = get_theme_mod( 'follow_pinterest' );
		$rss       = get_theme_mod( 'follow_rss' );
		$linkedin  = get_theme_mod( 'follow_linkedin' );
		$youtube   = get_theme_mod( 'follow_youtube' );
		$flickr    = get_theme_mod( 'follow_flickr' );
		$px500     = get_theme_mod( 'follow_500px' );
		$vkontakte = get_theme_mod( 'follow_vk' );
		$telegram  = get_theme_mod( 'follow_telegram' );
		$twitch    = get_theme_mod( 'follow_twitch' );
		$discord   = get_theme_mod( 'follow_discord' );
		$whatsapp  = get_theme_mod( 'follow_whatsapp' );
		$zalo      = get_theme_mod( 'follow_zalo' );
	}

	if ( $size === 'small' ) {
		$style = 'small';
	}
	$style = get_silver_icon_class( $style );

	// Scale.
	if ( $scale ) $scale = 'style="font-size:' . esc_attr( $scale ) . '%"';

	// Tooltip.
	if ( $tooltip === 'true' ) $style .= ' tooltip';

	$follow_links = apply_filters( 'silver_follow_links', array(
		'facebook'  => array(
			'enabled'  => ! empty( $facebook ),
			'atts'     => array(
				'href'       => esc_url( $facebook ),
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'data-label' => 'Facebook',
				'class'      => $style . ' facebook',
				'title'      => esc_attr__( 'Follow on Facebook', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Facebook', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-facebook' ),
			'priority' => 10,
		),
		'instagram' => array(
			'enabled'  => ! empty( $instagram ),
			'atts'     => array(
				'href'       => esc_url( $instagram ),
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'data-label' => 'Instagram',
				'class'      => $style . ' instagram',
				'title'      => esc_attr__( 'Follow on Instagram', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Instagram', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-instagram' ),
			'priority' => 20,
		),
		'tiktok'    => array(
			'enabled'  => ! empty( $tiktok ),
			'atts'     => array(
				'href'       => esc_url( $tiktok ),
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'data-label' => 'TikTok',
				'class'      => $style . ' tiktok',
				'title'      => esc_attr__( 'Follow on TikTok', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on TikTok', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-tiktok' ),
			'priority' => 30,
		),
		'snapchat'  => array(
			'enabled'  => ! empty( $snapchat ),
			'atts'     => array(
				'href'       => '#',
				'data-open'  => '#follow-snapchat-lightbox',
				'data-color' => 'dark',
				'data-pos'   => 'center',
				'data-label' => 'SnapChat',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' snapchat',
				'title'      => esc_attr__( 'Follow on SnapChat', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on SnapChat', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-snapchat' ),
			'after'    => sprintf(
				'<div id="follow-snapchat-lightbox" class="mfp-hide"><div class="text-center">%1$s<p>%2$s</p></div></div>',
				do_shortcode( silver_get_image( $snapchat ) ),
				esc_html__( 'Point the SnapChat camera at this to add us to SnapChat.', 'silver' )
			),
			'priority' => 40,
		),
		'x'         => array(
			'enabled'  => ! empty( $x ),
			'atts'     => array(
				'href'       => esc_url( $x ),
				'data-label' => esc_attr_x( 'X', 'social media', 'silver' ),
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' x',
				'title'      => esc_attr__( 'Follow on X', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on X', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-x' ),
			'priority' => 50,
		),
		'twitter'   => array(
			'enabled'  => ! empty( $twitter ),
			'atts'     => array(
				'href'       => esc_url( $twitter ),
				'data-label' => 'Twitter',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' twitter',
				'title'      => esc_attr__( 'Follow on Twitter', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Twitter', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-twitter' ),
			'priority' => 50,
		),
		'threads'   => array(
			'enabled'  => ! empty( $threads ),
			'atts'     => array(
				'href'       => esc_url( $threads ),
				'data-label' => 'Threads',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' threads',
				'title'      => esc_attr__( 'Follow on Threads', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Threads', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-threads' ),
			'priority' => 55,
		),
		'email'     => array(
			'enabled'  => ! empty( $email ),
			'atts'     => array(
				'href'       => 'mailto:' . esc_attr( $email ),
				'data-label' => 'E-mail',
				'target'     => '_blank',
				'rel'        => 'nofollow',
				'class'      => $style . ' email',
				'title'      => esc_attr__( 'Send us an email', 'silver' ),
				'aria-label' => esc_attr__( 'Send us an email', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-envelop' ),
			'priority' => 60,
		),
		'phone'     => array(
			'enabled'  => ! empty( $phone ),
			'atts'     => array(
				'href'       => 'tel:' . esc_attr( $phone ),
				'data-label' => 'Phone',
				'target'     => '_blank',
				'rel'        => 'nofollow',
				'class'      => $style . ' phone',
				'title'      => esc_attr__( 'Call us', 'silver' ),
				'aria-label' => esc_attr__( 'Call us', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-phone' ),
			'priority' => 70,
		),
		'pinterest' => array(
			'enabled'  => ! empty( $pinterest ),
			'atts'     => array(
				'href'       => esc_url( $pinterest ),
				'data-label' => 'Pinterest',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' pinterest',
				'title'      => esc_attr__( 'Follow on Pinterest', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Pinterest', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-pinterest' ),
			'priority' => 80,
		),
		'rss'       => array(
			'enabled'  => ! empty( $rss ),
			'atts'     => array(
				'href'       => esc_url( $rss ),
				'data-label' => 'RSS Feed',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' rss',
				'title'      => esc_attr__( 'Subscribe to RSS', 'silver' ),
				'aria-label' => esc_attr__( 'Subscribe to RSS', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-feed' ),
			'priority' => 90,
		),
		'linkedin'  => array(
			'enabled'  => ! empty( $linkedin ),
			'atts'     => array(
				'href'       => esc_url( $linkedin ),
				'data-label' => 'LinkedIn',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' linkedin',
				'title'      => esc_attr__( 'Follow on LinkedIn', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on LinkedIn', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-linkedin' ),
			'priority' => 100,
		),
		'youtube'   => array(
			'enabled'  => ! empty( $youtube ),
			'atts'     => array(
				'href'       => esc_url( $youtube ),
				'data-label' => 'YouTube',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' youtube',
				'title'      => esc_attr__( 'Follow on YouTube', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on YouTube', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-youtube' ),
			'priority' => 110,
		),
		'flickr'    => array(
			'enabled'  => ! empty( $flickr ),
			'atts'     => array(
				'href'       => esc_url( $flickr ),
				'data-label' => 'Flickr',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' flickr',
				'title'      => esc_attr__( 'Flickr', 'silver' ),
				'aria-label' => esc_attr__( 'Flickr', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-flickr' ),
			'priority' => 120,
		),
		'500px'     => array(
			'enabled'  => ! empty( $px500 ),
			'atts'     => array(
				'href'       => esc_url( $px500 ),
				'data-label' => '500px',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' px500',
				'title'      => esc_attr__( 'Follow on 500px', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on 500px', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-500px' ),
			'priority' => 130,
		),
		'vkontakte' => array(
			'enabled'  => ! empty( $vkontakte ),
			'atts'     => array(
				'href'       => esc_url( $vkontakte ),
				'data-label' => 'VKontakte',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' vk',
				'title'      => esc_attr__( 'Follow on VKontakte', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on VKontakte', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-vk' ),
			'priority' => 140,
		),
		'telegram'  => array(
			'enabled'  => ! empty( $telegram ),
			'atts'     => array(
				'href'       => esc_url( $telegram ),
				'data-label' => 'Telegram',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' telegram',
				'title'      => esc_attr__( 'Follow on Telegram', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Telegram', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-telegram' ),
			'priority' => 150,
		),
		'twitch'    => array(
			'enabled'  => ! empty( $twitch ),
			'atts'     => array(
				'href'       => esc_url( $twitch ),
				'data-label' => 'Twitch',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' twitch',
				'title'      => esc_attr__( 'Follow on Twitch', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Twitch', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-twitch' ),
			'priority' => 160,
		),
		'discord'   => array(
			'enabled'  => ! empty( $discord ),
			'atts'     => array(
				'href'       => esc_url( $discord ),
				'data-label' => 'Discord',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' discord',
				'title'      => esc_attr__( 'Follow on Discord', 'silver' ),
				'aria-label' => esc_attr__( 'Follow on Discord', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-discord' ),
			'priority' => 170,
		),
		'whatsapp'  => array(
			'enabled'  => ! empty( $whatsapp ),
			'atts'     => array(
				'href'       => esc_url( $whatsapp ),
				'data-label' => 'WhatsApp',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' whatsapp',
				'title'      => esc_attr__( 'Chat on WhatsApp', 'silver' ),
				'aria-label' => esc_attr__( 'Chat on WhatsApp', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-whatsapp' ),
			'priority' => 180,
		),
		'zalo'      => array(
			'enabled'  => ! empty( $zalo ),
			'atts'     => array(
				'href'       => esc_url( $zalo ),
				'data-label' => 'Zalo',
				'target'     => '_blank',
				'rel'        => 'noopener nofollow',
				'class'      => $style . ' zalo',
				'title'      => esc_attr__( 'Chat on Zalo', 'silver' ),
				'aria-label' => esc_attr__( 'Chat on Zalo', 'silver' ),
			),
			'icon'     => get_silver_icon( 'icon-zalo' ),
			'priority' => 190,
		),
	), array( // phpcs:ignore PEAR.Functions.FunctionCallSignature.Indent
		'style'           => $style,
		'atts'            => $atts,
		'use_global_link' => $use_global_link,
	) );

	// Sort links based on priority.
	uasort( $follow_links, 'silver_sort_on_priority' );

	// Explicit element order takes precedence; keep unlisted icons in their existing order.
	if ( ! empty( $icon_order ) ) {
		$networks = is_array( $icon_order ) ? $icon_order : preg_split( '/[\s,]+/', trim( $icon_order ), -1, PREG_SPLIT_NO_EMPTY );
		$ordered_links = array();
		foreach ( $networks as $network ) {
			if ( is_string( $network ) && isset( $follow_links[ $network ] ) ) {
				$ordered_links[ $network ] = $follow_links[ $network ];
				unset( $follow_links[ $network ] );
			}
		}
		$follow_links = $ordered_links + $follow_links;
	}

	ob_start();
	?>
	<div<?php if ( $_id ) : ?> id="<?php echo esc_attr( $_id ); ?>"<?php endif; ?> class="<?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>" <?php echo $scale; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		if ( $title ) echo '<span>' . wp_kses_post( $title ) . '</span>';
		foreach ( $follow_links as $follow_link ) {
			if ( isset( $follow_link['enabled'] ) && $follow_link['enabled'] == false ) continue;
			printf( '<a %1$s>%2$s</a>',
				silver_html_atts( $follow_link['atts'] ),
				! empty( $follow_link['icon'] ) ? $follow_link['icon'] : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			if ( ! empty( $follow_link['after'] ) ) {
				echo $follow_link['after']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		?>
	</div>
	<?php
	$content = ob_get_clean();

	return silver_sanitize_whitespace_chars( $content ) . $responsive_style;
}

add_shortcode( 'follow', 'silver_follow' );
