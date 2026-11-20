<?php
/**
 * Floating contact: channel registry shared by the Customizer options and the front end.
 *
 * @package Silver/Extensions
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'silver_floating_contact_channels' ) ) {
	/**
	 * Every supported channel.
	 *
	 * Keys of each channel:
	 * - label:  default label.
	 * - type:   how the value becomes a link (url, phone, sms, email, zalo, whatsapp, viber, telegram).
	 * - hint:   example value shown in the Customizer.
	 * - multi:  the field is a textarea (one value per line).
	 * - device: default visibility (all, desktop, mobile).
	 *
	 * @return array
	 */
	function silver_floating_contact_channels() {
		return apply_filters( 'silver_floating_contact_channels', array(
			'messenger' => array( 'label' => 'Messenger', 'type' => 'url', 'hint' => 'https://m.me/your-page' ),
			'zalo'      => array( 'label' => 'Zalo', 'type' => 'zalo', 'hint' => '0900000000 or https://zalo.me/...', 'multi' => true ),
			'tiktok'    => array( 'label' => 'Tiktok', 'type' => 'url', 'hint' => 'https://www.tiktok.com/@your-account' ),
			'facebook'  => array( 'label' => 'Facebook', 'type' => 'url', 'hint' => 'https://facebook.com/your-page' ),
			'instagram' => array( 'label' => 'Instagram', 'type' => 'url', 'hint' => 'https://instagram.com/your-account' ),
			'threads'   => array( 'label' => 'Threads', 'type' => 'url', 'hint' => 'https://www.threads.net/@your-account' ),
			'youtube'   => array( 'label' => 'YouTube', 'type' => 'url', 'hint' => 'https://youtube.com/@your-channel' ),
			'x'         => array( 'label' => 'X', 'type' => 'url', 'hint' => 'https://x.com/your-account' ),
			'linkedin'  => array( 'label' => 'LinkedIn', 'type' => 'url', 'hint' => 'https://linkedin.com/company/...' ),
			'pinterest' => array( 'label' => 'Pinterest', 'type' => 'url', 'hint' => 'https://pinterest.com/your-account' ),
			'telegram'  => array( 'label' => 'Telegram', 'type' => 'telegram', 'hint' => '@username or https://t.me/...', 'multi' => true ),
			'whatsapp'  => array( 'label' => 'WhatsApp', 'type' => 'whatsapp', 'hint' => '0900000000 (0 → +84) or 84900000000', 'multi' => true ),
			'viber'     => array( 'label' => 'Viber', 'type' => 'viber', 'hint' => '0900000000 (0 → +84)', 'multi' => true ),
			'line'      => array( 'label' => 'LINE', 'type' => 'url', 'hint' => 'https://line.me/ti/p/~your-id' ),
			'wechat'    => array( 'label' => 'WeChat', 'type' => 'url', 'hint' => 'weixin://dl/chat?your-id or QR link' ),
			'kakaotalk' => array( 'label' => 'KakaoTalk', 'type' => 'url', 'hint' => 'https://open.kakao.com/o/...' ),
			'discord'   => array( 'label' => 'Discord', 'type' => 'url', 'hint' => 'https://discord.gg/...' ),
			'shopee'    => array( 'label' => 'Shopee', 'type' => 'url', 'hint' => 'https://shopee.vn/your-shop' ),
			'lazada'    => array( 'label' => 'Lazada', 'type' => 'url', 'hint' => 'https://www.lazada.vn/shop/...' ),
			'tiki'      => array( 'label' => 'Tiki', 'type' => 'url', 'hint' => 'https://tiki.vn/cua-hang/...' ),
			'maps'      => array( 'label' => 'Chỉ đường', 'type' => 'url', 'hint' => 'https://maps.app.goo.gl/...', 'multi' => true ),
			'website'   => array( 'label' => 'Website', 'type' => 'url', 'hint' => 'https://example.com', 'multi' => true ),
			'email'     => array( 'label' => 'Email', 'type' => 'email', 'hint' => 'hello@example.com', 'multi' => true ),
			'sms'       => array( 'label' => 'SMS', 'type' => 'sms', 'hint' => '0900000000', 'multi' => true, 'device' => 'mobile' ),
			'hotline'   => array( 'label' => 'Hotline', 'type' => 'phone', 'hint' => '0900000000', 'multi' => true, 'device' => 'mobile' ),
		) );
	}
}
