<?php
/**
 * Silver Floating contact extension.
 *
 * Fixed contact bar: vertical on the screen edge (desktop), bottom bar (mobile).
 * A channel with several values (e.g. several hotlines) opens a small list.
 *
 * @package Silver/Extensions
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/silver-floating-contact-items.php';

/**
 * Enqueue extension assets.
 */
function silver_floating_contact_scripts() {
	if ( ! get_theme_mod( 'floating_contact' ) ) {
		return;
	}

	$uri     = get_template_directory_uri() . '/inc/extensions/silver-floating-contact';
	$version = wp_get_theme( get_template() )->get( 'Version' );
	// Version of each file: its last change, so browsers load a new copy.
	$css_ver = file_exists( __DIR__ . '/silver-floating-contact.css' ) ? $version . '.' . filemtime( __DIR__ . '/silver-floating-contact.css' ) : $version;
	$js_ver  = file_exists( __DIR__ . '/silver-floating-contact.js' ) ? $version . '.' . filemtime( __DIR__ . '/silver-floating-contact.js' ) : $version;

	wp_enqueue_style( 'silver-floating-contact', $uri . '/silver-floating-contact.css', array(), $css_ver );
	wp_enqueue_script( 'silver-floating-contact', $uri . '/silver-floating-contact.js', array(), $js_ver, true );
}

add_action( 'wp_enqueue_scripts', 'silver_floating_contact_scripts', 20 );

/**
 * Body class so the page can reserve space for the mobile bar.
 *
 * @param array $classes Body classes.
 *
 * @return array
 */
function silver_floating_contact_body_class( $classes ) {
	if ( ! get_theme_mod( 'floating_contact' ) ) {
		return $classes;
	}

	$items = silver_floating_contact_get_items();
	if ( ! $items ) {
		return $classes;
	}

	if ( 'style-2' === silver_floating_contact_style() ) {
		$classes[] = 'has-floating-contact-2';
		return $classes;
	}

	foreach ( $items as $item ) {
		if ( 'desktop' !== $item['device'] ) {
			$classes[] = 'has-floating-contact';
			break;
		}
	}

	return $classes;
}

/**
 * Active style.
 *
 * @return string
 */
function silver_floating_contact_style() {
	return 'style-2' === get_theme_mod( 'floating_contact_style', 'style-1' ) ? 'style-2' : 'style-1';
}

add_filter( 'body_class', 'silver_floating_contact_body_class' );

/**
 * Default SVG icons.
 *
 * @param string $key Channel key.
 *
 * @return string
 */
function silver_floating_contact_icon( $key ) {
	$rounded = '<rect x="3" y="3" width="42" height="42" rx="12" fill="%s"/>';
	$circle  = '<circle cx="24" cy="24" r="21" fill="%s"/>';
	$phone   = 'M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z';
	$bubble  = '<path fill="none" stroke="#fff" stroke-width="2.6" stroke-linejoin="round" d="M24 9.5a14.5 14.5 0 0 0-12.6 21.7L9.5 38.5l7.5-1.9A14.5 14.5 0 1 0 24 9.5z"/>';
	$text    = '<text x="24" y="%2$s" text-anchor="middle" font-family="Arial,Helvetica,sans-serif" font-size="%3$s" font-weight="700" fill="%4$s">%1$s</text>';
	$tiktok  = 'M12.53.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z';

	$icons = array(
		'messenger' => '<defs><radialGradient id="sfc-msg" cx="0.19" cy="0.99" r="1.1"><stop offset="0" stop-color="#0099ff"/><stop offset=".6" stop-color="#a033ff"/><stop offset=".9" stop-color="#ff5280"/><stop offset="1" stop-color="#ff7061"/></radialGradient></defs><path fill="url(#sfc-msg)" d="M24 4C12.7 4 4 12.3 4 23.5c0 5.9 2.4 11 6.3 14.5.3.3.5.7.5 1.2l.1 3.6c0 1.1 1.2 1.9 2.2 1.4l4-1.8c.3-.2.7-.2 1.1-.1 1.8.5 3.8.8 5.8.8 11.3 0 20-8.3 20-19.5S35.3 4 24 4z"/><path fill="#fff" d="M12 29.1l5.9-9.3c.9-1.5 2.9-1.8 4.3-.8l4.7 3.5c.4.3 1 .3 1.4 0l6.3-4.8c.8-.6 1.9.4 1.3 1.2l-5.9 9.3c-.9 1.5-2.9 1.8-4.3.8l-4.7-3.5c-.4-.3-1-.3-1.4 0l-6.3 4.8c-.8.6-1.9-.4-1.3-1.2z"/>',
		'zalo'      => sprintf( $rounded, '#0068ff' ) . sprintf( $text, 'Zalo', '29.5', '15', '#fff' ),
		'tiktok'    => '<g transform="scale(2)"><path fill="#25f4ee" transform="translate(-.6 -.4)" d="' . $tiktok . '"/><path fill="#fe2c55" transform="translate(.6 .4)" d="' . $tiktok . '"/><path fill="#111" d="' . $tiktok . '"/></g>',
		'facebook'  => sprintf( $circle, '#1877f2' ) . '<path fill="#fff" d="M26.6 45V30.5h4.9l.7-5.7h-5.6v-3.6c0-1.6.5-2.8 2.8-2.8h3V13.3c-.5-.1-2.3-.2-4.4-.2-4.3 0-7.3 2.6-7.3 7.5v4.2h-4.9v5.7h4.9V45z"/>',
		'instagram' => '<defs><radialGradient id="sfc-ig" cx=".3" cy="1.07" r="1.3"><stop offset="0" stop-color="#ffdd55"/><stop offset=".1" stop-color="#ffdd55"/><stop offset=".5" stop-color="#ff543e"/><stop offset="1" stop-color="#c837ab"/></radialGradient></defs><rect x="3" y="3" width="42" height="42" rx="12" fill="url(#sfc-ig)"/><rect x="12" y="12" width="24" height="24" rx="7" fill="none" stroke="#fff" stroke-width="3"/><circle cx="24" cy="24" r="5.5" fill="none" stroke="#fff" stroke-width="3"/><circle cx="31" cy="17" r="1.8" fill="#fff"/>',
		'threads'   => sprintf( $rounded, '#000' ) . sprintf( $text, '@', '33', '27', '#fff' ),
		'youtube'   => '<rect x="3" y="9" width="42" height="30" rx="9" fill="#ff0000"/><path fill="#fff" d="M20 17v14l12-7z"/>',
		'x'         => sprintf( $rounded, '#000' ) . '<path fill="#fff" d="M13 13h7.2l5 7 6.1-7h2.6l-7.5 8.6L35 35h-7.2l-5.4-7.6L15.7 35H13l8.2-9.3z"/><path fill="#000" d="M16.6 14.6h2.8l12.2 18.8h-2.8z"/>',
		'linkedin'  => sprintf( $rounded, '#0a66c2' ) . sprintf( $text, 'in', '32', '21', '#fff' ),
		'pinterest' => sprintf( $circle, '#e60023' ) . '<text x="24.5" y="34" text-anchor="middle" font-family="Georgia,serif" font-size="28" font-weight="700" fill="#fff">P</text>',
		'telegram'  => sprintf( $circle, '#29a9eb' ) . '<path fill="#fff" d="M11.5 23.3l21.3-8.2c1-.4 1.9.2 1.6 1.7l-3.6 17c-.3 1.2-1 1.5-2 .9l-5.5-4.1-2.7 2.6c-.3.3-.5.5-1.1.5l.4-5.6 10.2-9.2c.4-.4-.1-.6-.7-.2l-12.6 7.9-5.4-1.7c-1.2-.4-1.2-1.2.1-1.6z"/>',
		'whatsapp'  => sprintf( $circle, '#25d366' ) . $bubble . '<path fill="#fff" transform="translate(16.6 16.6) scale(.62)" d="' . $phone . '"/>',
		'viber'     => sprintf( $rounded, '#7360f2' ) . $bubble . '<path fill="#fff" transform="translate(16.6 16.6) scale(.62)" d="' . $phone . '"/>',
		'line'      => sprintf( $rounded, '#06c755' ) . '<ellipse cx="24" cy="23" rx="15" ry="11.5" fill="#fff"/><path fill="#fff" d="M22 33l-1 5 7-5z"/>' . sprintf( $text, 'LINE', '27', '10', '#06c755' ),
		'wechat'    => sprintf( $rounded, '#07c160' ) . '<ellipse cx="20" cy="21" rx="10.5" ry="8.5" fill="#fff"/><ellipse cx="29.5" cy="28.5" rx="9" ry="7.2" fill="#fff" stroke="#07c160" stroke-width="2"/><circle cx="16.5" cy="19" r="1.5" fill="#07c160"/><circle cx="23.5" cy="19" r="1.5" fill="#07c160"/><circle cx="26.5" cy="27.5" r="1.2" fill="#07c160"/><circle cx="32.5" cy="27.5" r="1.2" fill="#07c160"/>',
		'kakaotalk' => sprintf( $rounded, '#fee500' ) . '<ellipse cx="24" cy="22.5" rx="14" ry="11" fill="#3c1e1e"/><path fill="#3c1e1e" d="M17 30.5l-2 6.5 6.5-4.5z"/>' . sprintf( $text, 'TALK', '25.5', '8', '#fee500' ),
		'discord'   => sprintf( $rounded, '#5865f2' ) . '<path fill="#fff" d="M14 16.5c3-2 6-2.8 7.2-3l.9 1.9c1.3-.2 2.6-.2 3.8 0l.9-1.9c1.2.2 4.2 1 7.2 3 2.2 4 3.2 8.6 3 14-2.3 1.8-5 2.8-7.2 3.2l-1.8-2.8 2.9-1.4-.7-.6c-4.1 1.9-8.3 1.9-12.4 0l-.7.6 2.9 1.4-1.8 2.8c-2.2-.4-4.9-1.4-7.2-3.2-.2-5.4.8-10 3-14z"/><ellipse cx="19.5" cy="25" rx="2.4" ry="2.7" fill="#5865f2"/><ellipse cx="28.5" cy="25" rx="2.4" ry="2.7" fill="#5865f2"/>',
		'shopee'    => sprintf( $rounded, '#ee4d2d' ) . '<path fill="none" stroke="#fff" stroke-width="2.6" d="M19 18.5a5 5 0 0 1 10 0"/><path fill="#fff" d="M13.5 18.5h21l-1.6 17.5H15.1z"/>' . sprintf( $text, 'S', '33', '13', '#ee4d2d' ),
		'lazada'    => '<defs><linearGradient id="sfc-lz" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffa400"/><stop offset=".5" stop-color="#f5006b"/><stop offset="1" stop-color="#1b1b9b"/></linearGradient></defs><rect x="3" y="3" width="42" height="42" rx="12" fill="url(#sfc-lz)"/>' . sprintf( $text, 'Laz', '29.5', '15', '#fff' ),
		'tiki'      => sprintf( $rounded, '#1a94ff' ) . sprintf( $text, 'Tiki', '29.5', '15', '#fff' ),
		'maps'      => '<path fill="#ea4335" d="M24 4c-7.7 0-14 6.1-14 13.8C10 28 24 44 24 44s14-16 14-26.2C38 10.1 31.7 4 24 4z"/><circle cx="24" cy="18" r="5.2" fill="#fff"/>',
		'website'   => sprintf( $circle, '#0ea5e9' ) . '<g fill="none" stroke="#fff" stroke-width="2.4"><circle cx="24" cy="24" r="12.5"/><ellipse cx="24" cy="24" rx="5.5" ry="12.5"/><path d="M11.5 24h25"/></g>',
		'email'     => '<rect x="4" y="9" width="40" height="30" rx="5" fill="#f59e0b"/><path fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M9 15l15 11 15-11"/>',
		'sms'       => sprintf( $rounded, '#34c759' ) . '<path fill="#fff" d="M24 11c-8.3 0-15 5.4-15 12.1 0 3.9 2.3 7.3 5.8 9.5L13.5 38l6.3-3.4c1.3.3 2.8.5 4.2.5 8.3 0 15-5.4 15-12S32.3 11 24 11z"/><circle cx="18" cy="23" r="2" fill="#34c759"/><circle cx="24" cy="23" r="2" fill="#34c759"/><circle cx="30" cy="23" r="2" fill="#34c759"/>',
		'hotline'   => '<path fill="#e5202e" transform="scale(2)" d="' . $phone . '"/>',
	);

	if ( ! isset( $icons[ $key ] ) ) {
		$icons[ $key ] = sprintf( $circle, '#64748b' ) . '<path fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" d="M21 27l6-6M19.5 22.5l-2 2a4.6 4.6 0 0 0 6.5 6.5l2-2M28.5 25.5l2-2a4.6 4.6 0 0 0-6.5-6.5l-2 2"/>';
	}

	return '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">' . $icons[ $key ] . '</svg>';
}

/**
 * Turn a phone number into international digits (leading 0 → 84).
 *
 * @param string $value Phone number.
 *
 * @return string
 */
function silver_floating_contact_intl_phone( $value ) {
	$digits = preg_replace( '/[^0-9]/', '', $value );
	if ( 0 === strpos( $digits, '0' ) && 0 !== strpos( ltrim( $value ), '+' ) ) {
		$digits = apply_filters( 'silver_floating_contact_country_code', '84' ) . substr( $digits, 1 );
	}

	return $digits;
}

/**
 * Build the href for one value.
 *
 * @param string $type  Channel type.
 * @param string $value Raw value.
 *
 * @return string
 */
function silver_floating_contact_href( $type, $value ) {
	$value  = trim( $value );
	$is_url = (bool) preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $value ) || 0 === strpos( $value, '//' );
	$phone  = '/^[0-9 +().\-]+$/';

	if ( $is_url && 0 !== stripos( $value, 'javascript:' ) ) {
		return esc_url( $value, array( 'http', 'https', 'tel', 'sms', 'mailto', 'viber', 'weixin', 'whatsapp', 'zalo', 'tg', 'skype', 'kakaotalk', 'line' ) );
	}

	switch ( $type ) {
		case 'phone':
			return 'tel:' . preg_replace( '/[^0-9+]/', '', $value );
		case 'sms':
			return 'sms:' . preg_replace( '/[^0-9+]/', '', $value );
		case 'email':
			return 'mailto:' . sanitize_email( $value );
		case 'zalo':
			if ( preg_match( $phone, $value ) ) {
				return esc_url( 'https://zalo.me/' . preg_replace( '/[^0-9]/', '', $value ) );
			}
			break;
		case 'whatsapp':
			if ( preg_match( $phone, $value ) ) {
				return esc_url( 'https://wa.me/' . silver_floating_contact_intl_phone( $value ) );
			}
			break;
		case 'viber':
			if ( preg_match( $phone, $value ) ) {
				return 'viber://chat?number=%2B' . silver_floating_contact_intl_phone( $value );
			}
			break;
		case 'telegram':
			return esc_url( 'https://t.me/' . rawurlencode( ltrim( $value, '@' ) ) );
	}

	// Bare domain / path: assume https.
	return esc_url( 'https://' . ltrim( $value, '/' ) );
}

/**
 * Split a field into entries. Each line: "value" or "Name | value".
 *
 * @param string $type Channel type.
 * @param string $raw  Raw option value.
 *
 * @return array
 */
function silver_floating_contact_parse_entries( $type, $raw ) {
	$entries = array();

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		$name  = '';
		$value = $line;
		if ( false !== strpos( $line, '|' ) ) {
			list( $name, $value ) = array_map( 'trim', explode( '|', $line, 2 ) );
		}
		if ( '' === $value ) {
			continue;
		}

		$href = silver_floating_contact_href( $type, $value );
		if ( ! $href ) {
			continue;
		}

		$entries[] = array(
			'name'  => $name,
			'value' => $value,
			'href'  => $href,
		);
	}

	return $entries;
}

/**
 * Enabled channels that have a value, in the saved order.
 *
 * @return array
 */
function silver_floating_contact_get_items() {
	static $items = null;
	if ( null !== $items && ! is_customize_preview() ) {
		return $items;
	}

	$items    = array();
	$channels = silver_floating_contact_channels();
	$order    = get_theme_mod( 'floating_contact_items', array( 'messenger', 'zalo', 'tiktok', 'hotline' ) );

	if ( ! is_array( $order ) ) {
		return $items;
	}

	foreach ( $order as $key ) {
		$key = sanitize_key( $key );
		if ( ! isset( $channels[ $key ] ) ) {
			continue;
		}

		$channel = $channels[ $key ];
		$entries = silver_floating_contact_parse_entries( $channel['type'], get_theme_mod( 'floating_contact_' . $key . '_link', '' ) );
		if ( ! $entries ) {
			continue;
		}

		$icon = get_theme_mod( 'floating_contact_' . $key . '_icon', '' );
		if ( is_numeric( $icon ) ) {
			$icon = wp_get_attachment_image_url( (int) $icon, 'thumbnail' );
		} elseif ( is_array( $icon ) && isset( $icon['url'] ) ) {
			$icon = $icon['url'];
		}

		$device = get_theme_mod( 'floating_contact_' . $key . '_device', isset( $channel['device'] ) ? $channel['device'] : 'all' );

		$items[] = array(
			'key'        => $key,
			'label'      => get_theme_mod( 'floating_contact_' . $key . '_label', $channel['label'] ),
			'icon'       => $icon,
			'icon_class' => sanitize_text_field( get_theme_mod( 'floating_contact_' . $key . '_icon_class', '' ) ),
			'device'     => in_array( $device, array( 'all', 'desktop', 'mobile' ), true ) ? $device : 'all',
			'entries'    => $entries,
		);
	}

	return $items;
}

/**
 * Target / rel attributes for a link.
 *
 * @param string $href Link.
 *
 * @return string
 */
function silver_floating_contact_link_attrs( $href ) {
	return 0 === strpos( $href, 'http' ) ? ' target="_blank" rel="noopener nofollow"' : '';
}

/**
 * Icon markup for an item.
 *
 * @param array $item Item.
 *
 * @return string
 */
function silver_floating_contact_icon_html( $item ) {
	if ( $item['icon'] ) {
		return '<img src="' . esc_url( $item['icon'] ) . '" alt="" width="28" height="28" loading="lazy" decoding="async">';
	}

	if ( ! empty( $item['icon_class'] ) ) {
		return get_silver_icon( $item['icon_class'] );
	}

	return silver_floating_contact_icon( $item['key'] );
}

/**
 * Html template.
 */
function silver_floating_contact_template() {
	if ( ! get_theme_mod( 'floating_contact' ) ) {
		return;
	}

	$items = silver_floating_contact_get_items();
	if ( ! $items ) {
		return;
	}

	$style    = silver_floating_contact_style();
	$position = 'left' === get_theme_mod( 'floating_contact_position', 'right' ) ? 'left' : 'right';

	if ( 'style-2' === $style ) {
		silver_floating_contact_template_style_2( $items, $position );
		return;
	}

	$classes  = array(
		'silver-fc',
		'silver-fc--' . sanitize_html_class( $style ),
		'silver-fc--' . $position,
	);

	$vars = sprintf(
		'--sfc-bg:%s;--sfc-color:%s;--sfc-toggle:%s;',
		sanitize_hex_color( get_theme_mod( 'floating_contact_bg', '#ffffff' ) ) ?: '#ffffff',
		sanitize_hex_color( get_theme_mod( 'floating_contact_color', '#8a8a8a' ) ) ?: '#8a8a8a',
		sanitize_hex_color( get_theme_mod( 'floating_contact_toggle_color', '#2e8b3a' ) ) ?: '#2e8b3a'
	);
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $vars ); ?>" data-silver-fc>
		<ul class="silver-fc__list">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$count      = count( $item['entries'] );
				$sub_id     = 'silver-fc-sub-' . $index;
				$item_class = 'silver-fc__item silver-fc__item--' . $item['key'];
				if ( 'all' !== $item['device'] ) {
					$item_class .= ' silver-fc__item--' . $item['device'] . '-only';
				}
				if ( $count > 1 ) {
					$item_class .= ' has-sub';
				}
				$icon = silver_floating_contact_icon_html( $item ); // Escaped / static SVG.
				?>
				<li class="<?php echo esc_attr( $item_class ); ?>">
					<?php if ( 1 === $count ) : ?>
						<a class="silver-fc__link" href="<?php echo esc_attr( $item['entries'][0]['href'] ); ?>"<?php echo silver_floating_contact_link_attrs( $item['entries'][0]['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( $item['label'] ); ?>">
							<span class="silver-fc__icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="silver-fc__label"><?php echo esc_html( $item['label'] ); ?></span>
						</a>
					<?php else : ?>
						<button type="button" class="silver-fc__link silver-fc__sub-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $sub_id ); ?>">
							<span class="silver-fc__icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="silver-fc__badge"><?php echo esc_html( $count ); ?></span></span>
							<span class="silver-fc__label"><?php echo esc_html( $item['label'] ); ?></span>
						</button>
						<div class="silver-fc__sub" id="<?php echo esc_attr( $sub_id ); ?>" hidden>
							<div class="silver-fc__sub-title"><?php echo esc_html( $item['label'] ); ?></div>
							<?php foreach ( $item['entries'] as $entry ) : ?>
								<a class="silver-fc__sub-link" href="<?php echo esc_attr( $entry['href'] ); ?>"<?php echo silver_floating_contact_link_attrs( $entry['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
									<span class="silver-fc__sub-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<span class="silver-fc__sub-text">
										<?php if ( $entry['name'] ) : ?>
											<span class="silver-fc__sub-name"><?php echo esc_html( $entry['name'] ); ?></span>
										<?php endif; ?>
										<span class="silver-fc__sub-value"><?php echo esc_html( $entry['value'] ); ?></span>
									</span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
			<?php if ( get_theme_mod( 'floating_contact_toggle', true ) ) : ?>
				<?php $toggle_label = get_theme_mod( 'floating_contact_toggle_label', 'Ẩn' ); ?>
				<li class="silver-fc__item silver-fc__item--toggle">
					<button type="button" class="silver-fc__link silver-fc__toggle" aria-expanded="true" aria-label="<?php echo esc_attr( $toggle_label ); ?>">
						<span class="silver-fc__icon silver-fc__chevron">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
						</span>
						<span class="silver-fc__label"><?php echo esc_html( $toggle_label ); ?></span>
					</button>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<?php
}

/**
 * Style 2: floating button in the corner that opens a stack of buttons.
 * A channel with several values gets one button per value.
 *
 * @param array  $items    Items.
 * @param string $position left|right.
 */
function silver_floating_contact_template_style_2( $items, $position ) {
	$launcher_icon = sanitize_text_field( get_theme_mod( 'floating_contact_launcher_icon', '' ) );
	$image = get_theme_mod( 'floating_contact_launcher_image', '' );
	if ( is_numeric( $image ) ) {
		$image = wp_get_attachment_image_url( (int) $image, 'medium' );
	} elseif ( is_array( $image ) && isset( $image['url'] ) ) {
		$image = $image['url'];
	}

	$open    = (bool) get_theme_mod( 'floating_contact_open', false );
	$classes = array( 'silver-fc', 'silver-fc--style-2', 'silver-fc--' . $position );
	if ( $image ) {
		$classes[] = 'silver-fc--has-image';
	}
	if ( get_theme_mod( 'floating_contact_launcher_animate', true ) ) {
		$classes[] = 'silver-fc--animate';
	}
	if ( $open ) {
		$classes[] = 'is-open';
	}

	$vars = sprintf(
		'--sfc-size:%dpx;--sfc-bottom:%dpx;--sfc-side:%dpx;--sfc-launcher:%s;--sfc-pill-bg:%s;--sfc-pill-color:%s;',
		max( 40, min( 140, (int) get_theme_mod( 'floating_contact_launcher_size', 64 ) ) ),
		max( 0, min( 200, (int) get_theme_mod( 'floating_contact_offset_bottom', 20 ) ) ),
		max( 0, min( 200, (int) get_theme_mod( 'floating_contact_offset_side', 20 ) ) ),
		sanitize_hex_color( get_theme_mod( 'floating_contact_launcher_color', '#2e8b3a' ) ) ?: '#2e8b3a',
		sanitize_hex_color( get_theme_mod( 'floating_contact_pill_bg', '#000000' ) ) ?: '#000000',
		sanitize_hex_color( get_theme_mod( 'floating_contact_pill_color', '#ffffff' ) ) ?: '#ffffff'
	);

	$label = __( 'Contact', 'silver' );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="<?php echo esc_attr( $vars ); ?>" data-silver-fc>
		<div class="silver-fc__panel" id="silver-fc-panel">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$pill_class = 'silver-fc__pill silver-fc__item--' . $item['key'];
				if ( 'all' !== $item['device'] ) {
					$pill_class .= ' silver-fc__item--' . $item['device'] . '-only';
				}
				$icon  = silver_floating_contact_icon_html( $item ); // Escaped / static SVG.
				$multi = count( $item['entries'] ) > 1;
				?>
				<?php foreach ( $item['entries'] as $entry ) : ?>
					<?php $text = $multi && $entry['name'] ? $entry['name'] : ( $multi ? $entry['value'] : $item['label'] ); ?>
					<a class="<?php echo esc_attr( $pill_class ); ?>" href="<?php echo esc_attr( $entry['href'] ); ?>"<?php echo silver_floating_contact_link_attrs( $entry['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="silver-fc__pill-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="silver-fc__pill-text"><?php echo esc_html( $text ); ?></span>
					</a>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
		<button type="button" class="silver-fc__launcher" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="silver-fc-panel" aria-label="<?php echo esc_attr( $label ); ?>">
			<?php if ( $image ) : ?>
				<img class="silver-fc__launcher-img" src="<?php echo esc_url( $image ); ?>" alt="" decoding="async">
			<?php else : ?>
				<?php if ( $launcher_icon ) : ?>
					<?php echo get_silver_icon( $launcher_icon, null, array( 'class' => 'silver-fc__launcher-chat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php else : ?>
				<svg class="silver-fc__launcher-chat" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 3C6.5 3 2 6.9 2 11.7c0 2.7 1.4 5.1 3.7 6.7L5 22l4.1-2.2c.9.2 1.9.3 2.9.3 5.5 0 10-3.9 10-8.7S17.5 3 12 3zm-4.5 10a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6zm4.5 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6zm4.5 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6z"/></svg>
				<?php endif; ?>
				<svg class="silver-fc__launcher-close" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
			<?php endif; ?>
		</button>
	</div>
	<?php
}

add_action( 'wp_footer', 'silver_floating_contact_template' );
