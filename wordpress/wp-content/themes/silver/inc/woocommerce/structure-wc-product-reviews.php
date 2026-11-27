<?php
/**
 * Product reviews: the theme's layout (Theme Options > WooCommerce > Product
 * Reviews).
 *
 * - An overview (average, stars, a bar per star) with a "Write a review"
 *   button that opens the form in a silverDialog popup; photos open in one
 *   too (woocommerce/single-product/reviews-layout.php,
 *   assets/js/src/silver-product-reviews.js).
 * - The form: stars, name, phone, email (each field shown / optional /
 *   required as set), the review, photos (checked by their content, saved in
 *   uploads/comments/{comment ID}/). Sent with AJAX.
 * - "Purchased" badge: a WooCommerce verified owner, or a phone that matches a
 *   completed order.
 * - The list: the first reviews, then "More reviews" (AJAX).
 * - A Telegram message for each new review (optional).
 *
 * Comment meta: phone, buyed (1: the phone matches a completed order), images
 * (attachment IDs, comma separated); the same keys as the former "Đánh giá
 * sản phẩm" plugin, so its reviews keep their photos and badge.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the theme's review layout is on.
 *
 * @return bool
 */
function silver_product_reviews_enabled() {
	return (bool) apply_filters( 'silver_product_reviews_enabled', get_theme_mod( 'product_reviews_layout', 1 ) );
}

/**
 * Settings of the review form and list.
 *
 * @return array
 */
function silver_product_reviews_settings() {
	$field = static function ( $value, $default ) {
		return in_array( $value, array( 'hidden', 'optional', 'required' ), true ) ? $value : $default;
	};

	return apply_filters( 'silver_product_reviews_settings', array(
		'per_page'   => max( 1, (int) get_theme_mod( 'product_reviews_per_page', 5 ) ),
		'phone'      => $field( get_theme_mod( 'product_reviews_phone', 'required' ), 'required' ),
		'email'      => $field( get_theme_mod( 'product_reviews_email', 'hidden' ), 'hidden' ),
		'images'     => (bool) get_theme_mod( 'product_reviews_images', 1 ),
		'images_max' => max( 1, min( 10, (int) get_theme_mod( 'product_reviews_images_max', 3 ) ) ),
		'image_size' => max( 1, min( 10, (int) get_theme_mod( 'product_reviews_image_size', 1 ) ) ) * MB_IN_BYTES,
	) );
}

/**
 * Review item hooks of the layout (single-product/review.php): the initial of
 * the name instead of the avatar, a head (name, badge, stars, date), the
 * photos after the text.
 */
function silver_product_reviews_setup() {
	if ( ! silver_product_reviews_enabled() ) {
		return;
	}

	remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
	remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
	remove_action( 'woocommerce_review_meta', 'woocommerce_review_display_meta', 10 );

	add_action( 'woocommerce_review_before', 'silver_product_review_avatar', 10 );
	add_action( 'woocommerce_review_before_comment_meta', 'silver_product_review_head', 10 );
	add_action( 'woocommerce_review_after_comment_text', 'silver_product_review_images', 10 );

	// The list loads more with a button: no "Read more" cut on the tab.
	add_filter( 'silver_product_tabs_readmore_keys', 'silver_product_reviews_no_readmore' );
}
add_action( 'wp', 'silver_product_reviews_setup' );

/**
 * No "Read more" cut on the reviews tab.
 *
 * @param array $keys Tab keys.
 * @return array
 */
function silver_product_reviews_no_readmore( $keys ) {
	return array_values( array_diff( (array) $keys, array( 'reviews' ) ) );
}

/**
 * Stars of a rating (the markup of the buy box rating, colored by Theme
 * Options > Colors).
 *
 * @param float  $rating Rating, 0 to 5.
 * @param string $class  Extra class.
 * @return string
 */
function silver_product_review_stars( $rating, $class = '' ) {
	$rating  = max( 0, min( 5, (float) $rating ) );
	$outline = str_repeat( get_silver_icon( 'icon-star-o', null, array( 'class' => 'product-rating-summary__star' ) ), 5 );
	$solid   = str_repeat( get_silver_icon( 'icon-star', null, array( 'class' => 'product-rating-summary__star' ) ), 5 );
	/* translators: %s: rating */
	$label = sprintf( __( 'Rated %s out of 5', 'silver' ), wc_format_decimal( $rating, 1, true ) );

	return '<span class="product-rating-summary__stars ' . esc_attr( $class ) . '" role="img" aria-label="' . esc_attr( $label ) . '">'
		. '<span class="product-rating-summary__stars-base">' . $outline . '</span>'
		. '<span class="product-rating-summary__stars-fill" style="width:' . esc_attr( $rating * 20 ) . '%">' . $solid . '</span>'
		. '</span>';
}

/**
 * Whether the visitor may review the product (WooCommerce "verified owners
 * only" setting).
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function silver_product_reviews_can_review( $product_id ) {
	return 'yes' !== get_option( 'woocommerce_review_rating_verification_required' )
		|| wc_customer_bought_product( '', get_current_user_id(), $product_id );
}

/**
 * Top-level reviews of a product, newest first, with their replies.
 *
 * @param int $product_id Product ID.
 * @param int $page       Page, from 1.
 * @return array { comments: WP_Comment[] (replies included), more: bool }
 */
function silver_product_reviews_query( $product_id, $page = 1 ) {
	$settings = silver_product_reviews_settings();

	$query = new WP_Comment_Query( array(
		'post_id'                   => (int) $product_id,
		'status'                    => 'approve',
		'type'                      => 'review',
		'parent'                    => 0,
		'orderby'                   => 'comment_date_gmt',
		'order'                     => 'DESC',
		'number'                    => $settings['per_page'],
		'paged'                     => max( 1, (int) $page ),
		'no_found_rows'             => false,
		'hierarchical'              => 'flat', // Replies (from the shop) follow.
		'update_comment_meta_cache' => true,
	) );

	$comments = (array) $query->comments;

	// Photos of the page in one query.
	$ids = array();
	foreach ( $comments as $comment ) {
		$ids = array_merge( $ids, silver_product_review_image_ids( $comment->comment_ID ) );
	}
	if ( $ids ) {
		_prime_post_caches( array_unique( $ids ), false, true );
	}

	return array(
		'comments' => $comments,
		'more'     => (int) $query->found_comments > max( 1, (int) $page ) * $settings['per_page'],
	);
}

/**
 * HTML of reviews (the <li> of the list).
 *
 * @param WP_Comment[] $comments Reviews and their replies.
 * @return string
 */
function silver_product_reviews_list_html( $comments ) {
	if ( ! $comments ) {
		return '';
	}

	return (string) wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array(
		'callback'          => 'woocommerce_comments',
		'echo'              => false,
		'per_page'          => 0,
		'max_depth'         => get_option( 'thread_comments' ) ? (int) get_option( 'thread_comments_depth', 5 ) : -1,
		'reverse_top_level' => false,
	) ), $comments );
}

/**
 * Photo IDs of a review.
 *
 * @param int $comment_id Comment ID.
 * @return int[]
 */
function silver_product_review_image_ids( $comment_id ) {
	$images = (string) get_comment_meta( $comment_id, 'images', true );
	return $images ? array_values( array_filter( array_map( 'absint', explode( ',', $images ) ) ) ) : array();
}

/**
 * Review avatar: the initial of the name.
 *
 * @param WP_Comment $comment Comment.
 */
function silver_product_review_avatar( $comment ) {
	$name    = trim( wp_strip_all_tags( (string) $comment->comment_author ) );
	$initial = '' !== $name ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '?';
	$tone    = abs( crc32( $name ) ) % 6; // One of 6 colors, the same for a name.

	echo '<span class="product-review__avatar product-review__avatar--' . esc_attr( $tone ) . '" aria-hidden="true">' . esc_html( $initial ) . '</span>';
}

/**
 * Whether the review shows the "Purchased" badge.
 *
 * @param WP_Comment $comment Comment.
 * @return bool
 */
function silver_product_review_purchased( $comment ) {
	if ( 'no' === get_option( 'woocommerce_review_rating_verification_label' ) ) {
		return false;
	}
	// Reviews the former plugin made up ("fake") never have it.
	if ( get_comment_meta( $comment->comment_ID, 'fake', true ) ) {
		return false;
	}

	return wc_review_is_from_verified_owner( $comment->comment_ID ) || '1' === (string) get_comment_meta( $comment->comment_ID, 'buyed', true );
}

/**
 * Review head: name, badge, stars, date (and "awaiting approval").
 *
 * @param WP_Comment $comment Comment.
 */
function silver_product_review_head( $comment ) {
	$is_review = 'review' === $comment->comment_type || ! $comment->comment_parent;
	$rating    = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
	$time      = (int) get_comment_time( 'U', true, true, $comment );
	$shop      = $comment->user_id && user_can( (int) $comment->user_id, 'manage_woocommerce' );

	if ( time() - $time < DAY_IN_SECONDS ) {
		/* translators: %s: time, e.g. "2 hours" */
		$date = sprintf( __( '%s ago', 'silver' ), human_time_diff( $time ) );
	} else {
		$date = get_comment_date( get_option( 'date_format' ), $comment );
	}
	?>
	<div class="product-review__head">
		<strong class="product-review__author woocommerce-review__author"><?php echo esc_html( get_comment_author( $comment ) ); ?></strong>
		<?php if ( $shop ) : ?>
			<span class="product-review__badge product-review__badge--shop"><?php esc_html_e( 'Shop', 'silver' ); ?></span>
		<?php elseif ( silver_product_review_purchased( $comment ) ) : ?>
			<span class="product-review__badge product-review__badge--purchased"><?php echo get_silver_icon( 'icon-checkmark', null, array( 'class' => 'product-review__badge-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon. ?><?php esc_html_e( 'Purchased', 'silver' ); ?></span>
		<?php endif; ?>
	</div>
	<div class="product-review__sub">
		<?php
		if ( $is_review && $rating && wc_review_ratings_enabled() ) {
			echo silver_product_review_stars( $rating, 'product-review__stars' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function.
		}
		?>
		<time class="product-review__date woocommerce-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>"><?php echo esc_html( $date ); ?></time>
	</div>
	<?php if ( '0' === (string) $comment->comment_approved ) : ?>
		<p class="product-review__pending"><em class="woocommerce-review__awaiting-approval"><?php esc_html_e( 'Your review is awaiting approval', 'silver' ); ?></em></p>
	<?php endif; ?>
	<?php
}

/**
 * Review photos.
 *
 * @param WP_Comment $comment Comment.
 */
function silver_product_review_images( $comment ) {
	$items = array();
	foreach ( silver_product_review_image_ids( $comment->comment_ID ) as $id ) {
		$thumb = wp_get_attachment_image( $id, 'woocommerce_gallery_thumbnail', false, array(
			'loading' => 'lazy',
			'class'   => 'product-review__photo-img',
			'alt'     => '',
		) );
		$full  = wp_get_attachment_image_url( $id, 'large' );
		if ( $thumb && $full ) {
			// Opened in a silverDialog popup (silver-product-reviews.js).
			$items[] = '<li><a class="product-review__photo" href="' . esc_url( $full ) . '" data-review-photo>' . $thumb . '<span class="screen-reader-text">' . esc_html__( 'View photo', 'silver' ) . '</span></a></li>';
		}
	}

	if ( $items ) {
		echo '<ul class="product-review__photos" aria-label="' . esc_attr__( 'Customer photos', 'silver' ) . '">' . implode( '', $items ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}

/**
 * Vietnamese mobile number (10 digits, a carrier prefix).
 *
 * @param string $phone Phone.
 * @return bool
 */
function silver_product_review_phone_valid( $phone ) {
	$valid = (bool) preg_match( '/^(03[2-9]|05[6896]|07[06789]|08[1-689]|09[0-46-9])[0-9]{7}$/', (string) $phone );
	return (bool) apply_filters( 'silver_product_review_phone_valid', $valid, $phone );
}

/**
 * Phone typed with spaces, dots, dashes or +84: digits from 0.
 *
 * @param string $phone Phone.
 * @return string
 */
function silver_product_review_phone_normalize( $phone ) {
	$phone = preg_replace( '/[^0-9+]/', '', (string) $phone );
	return (string) preg_replace( '/^\+?84/', '0', $phone );
}

/**
 * Whether a completed order with this phone has this product (the
 * "Purchased" badge). Any completed order with the phone is not enough: a
 * visitor could type the phone of a customer and get the badge on any product.
 *
 * @param string $phone      Phone (normalized).
 * @param int    $product_id Product ID.
 * @return bool
 */
function silver_product_review_phone_bought( $phone, $product_id ) {
	$orders = wc_get_orders( array(
		'limit'         => 20,
		'status'        => 'completed',
		'billing_phone' => $phone,
		'orderby'       => 'date',
		'order'         => 'DESC',
	) );

	foreach ( (array) $orders as $order ) {
		if ( ! $order instanceof WC_Order ) {
			continue;
		}
		foreach ( $order->get_items() as $item ) {
			if ( $item instanceof WC_Order_Item_Product && (int) $item->get_product_id() === (int) $product_id ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Uploaded photos ($_FILES['images']), empty slots left out.
 *
 * @return array
 */
function silver_product_review_uploaded_images() {
	if ( empty( $_FILES['images'] ) || ! is_array( $_FILES['images']['name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- public review form, like the core comment form.
		return array();
	}

	$raw   = $_FILES['images']; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput -- checked by silver_product_review_validate_images().
	$files = array();
	foreach ( array_keys( $raw['name'] ) as $i ) {
		$error = isset( $raw['error'][ $i ] ) ? (int) $raw['error'][ $i ] : UPLOAD_ERR_NO_FILE;
		if ( UPLOAD_ERR_NO_FILE === $error ) {
			continue;
		}
		$files[] = array(
			'name'     => (string) $raw['name'][ $i ],
			'type'     => (string) $raw['type'][ $i ],
			'tmp_name' => (string) $raw['tmp_name'][ $i ],
			'error'    => $error,
			'size'     => (int) $raw['size'][ $i ],
		);
	}
	return $files;
}

/**
 * Photo types allowed (no SVG: it can hold scripts).
 *
 * @return array
 */
function silver_product_review_image_mimes() {
	return array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);
}

/**
 * Checks the photos by their content (not the type or name the browser sent).
 *
 * @return array|WP_Error Files with their checked extension.
 */
function silver_product_review_validate_images() {
	$files    = silver_product_review_uploaded_images();
	$settings = silver_product_reviews_settings();

	if ( ! $files ) {
		return array();
	}
	if ( count( $files ) > $settings['images_max'] ) {
		/* translators: %d: number of photos */
		return new WP_Error( 'review_images', sprintf( _n( 'Up to %d photo.', 'Up to %d photos.', $settings['images_max'], 'silver' ), $settings['images_max'] ) );
	}

	$mimes = silver_product_review_image_mimes();
	foreach ( $files as $i => $file ) {
		if ( UPLOAD_ERR_OK !== $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'review_images', __( 'A photo could not be uploaded.', 'silver' ) );
		}
		if ( $file['size'] <= 0 || $file['size'] > $settings['image_size'] || filesize( $file['tmp_name'] ) > $settings['image_size'] ) {
			/* translators: %s: size, e.g. "1 MB" */
			return new WP_Error( 'review_images', sprintf( __( 'Each photo up to %s.', 'silver' ), size_format( $settings['image_size'] ) ) );
		}
		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
		$real  = wp_get_image_mime( $file['tmp_name'] );
		if ( empty( $check['ext'] ) || empty( $check['type'] ) || ! $real || ! in_array( $real, $mimes, true ) || false === @getimagesize( $file['tmp_name'] ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- not an image: false.
			return new WP_Error( 'review_images', __( 'Photos only (JPG, PNG, WebP).', 'silver' ) );
		}
		$files[ $i ]['ext']  = $check['ext'];
		$files[ $i ]['type'] = $check['type'];
	}

	return $files;
}

/**
 * Photos sent from one IP address in the last hour (the photos are saved
 * before the review is approved: a limit against filling the disk).
 *
 * @param int $add Photos to add to the count (0: read only).
 * @return int Count, $add included.
 */
function silver_product_review_images_sent( $add = 0 ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? preg_replace( '/[^0-9a-fA-F:.]/', '', (string) $_SERVER['REMOTE_ADDR'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- cleaned here.
	$key = 'silver_review_images_' . md5( $ip );
	$log = get_transient( $key );
	$log = is_array( $log ) ? $log : array();
	$now = time();

	// Times of the photos of the last hour.
	$log = array_values( array_filter( array_map( 'intval', $log ), static function ( $time ) use ( $now ) {
		return $time > $now - HOUR_IN_SECONDS;
	} ) );

	if ( $add > 0 ) {
		$log = array_merge( $log, array_fill( 0, (int) $add, $now ) );
		set_transient( $key, $log, HOUR_IN_SECONDS );
	}

	return count( $log );
}

/**
 * Most photos one IP address may send in an hour.
 *
 * @return int
 */
function silver_product_review_images_hourly_limit() {
	return max( 1, (int) apply_filters( 'silver_product_review_images_hourly_limit', 20 ) );
}

/**
 * Saves the photos in uploads/comments/{comment ID}/ (attachments, a thumbnail
 * size only).
 *
 * @param int $comment_id Comment ID.
 * @return int[] Attachment IDs.
 */
function silver_product_review_save_images( $comment_id ) {
	$files = silver_product_review_validate_images();
	if ( is_wp_error( $files ) || ! $files ) {
		return array();
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$comment_id = (int) $comment_id;
	$dir_cb     = static function ( $dirs ) use ( $comment_id ) {
		$sub            = '/comments/' . $comment_id;
		$dirs['subdir'] = $sub;
		$dirs['path']   = $dirs['basedir'] . $sub;
		$dirs['url']    = $dirs['baseurl'] . $sub;
		return $dirs;
	};
	$sizes_cb   = static function ( $sizes ) {
		$keep = apply_filters( 'silver_product_review_image_sizes', array( 'thumbnail', 'woocommerce_gallery_thumbnail', 'large' ) );
		return array_intersect_key( $sizes, array_flip( $keep ) );
	};
	add_filter( 'upload_dir', $dir_cb );
	add_filter( 'intermediate_image_sizes_advanced', $sizes_cb );

	$ids = array();
	foreach ( $files as $i => $file ) {
		$file['name'] = 'review-' . $comment_id . '-' . ( $i + 1 ) . '.' . $file['ext'];
		unset( $file['ext'] );

		$moved = wp_handle_upload( $file, array(
			'test_form' => false,
			'mimes'     => silver_product_review_image_mimes(),
		) );
		if ( empty( $moved['file'] ) || ! empty( $moved['error'] ) ) {
			continue;
		}

		$id = wp_insert_attachment( array(
			'guid'           => $moved['url'],
			'post_mime_type' => $moved['type'],
			/* translators: 1: comment ID, 2: photo number */
			'post_title'     => sprintf( __( 'Review %1$d photo %2$d', 'silver' ), $comment_id, $i + 1 ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		), $moved['file'], 0, true );

		if ( is_wp_error( $id ) || ! $id ) {
			wp_delete_file( $moved['file'] );
			continue;
		}

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $moved['file'] ) );
		update_post_meta( $id, '_silver_review_comment', $comment_id );
		$ids[] = (int) $id;
	}

	remove_filter( 'upload_dir', $dir_cb );
	remove_filter( 'intermediate_image_sizes_advanced', $sizes_cb );

	if ( $ids ) {
		silver_product_review_images_sent( count( $ids ) );
	}

	return $ids;
}

/**
 * Deletes the photos with the review.
 *
 * @param int $comment_id Comment ID.
 */
function silver_product_review_delete_images( $comment_id ) {
	foreach ( silver_product_review_image_ids( $comment_id ) as $id ) {
		$owner = (int) get_post_meta( $id, '_silver_review_comment', true );
		// Photos of the theme or of the former plugin (uploads/comments/{ID}/).
		if ( $owner === (int) $comment_id || false !== strpos( (string) get_attached_file( $id ), '/comments/' . (int) $comment_id . '/' ) ) {
			wp_delete_attachment( $id, true );
		}
	}
}
add_action( 'delete_comment', 'silver_product_review_delete_images' );

/**
 * After a review is saved (AJAX form): phone, "Purchased" from a completed
 * order with that phone, photos, Telegram message.
 *
 * @param int        $comment_id Comment ID.
 * @param int|string $approved   1, 0 or "spam".
 */
function silver_product_review_saved( $comment_id, $approved ) {
	if ( 'spam' === $approved || 'trash' === $approved ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || 'product' !== get_post_type( $comment->comment_post_ID ) ) {
		return;
	}

	$phone = isset( $_POST['phone'] ) ? silver_product_review_phone_normalize( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- public review form.
	$buyed = 0;
	if ( $phone && silver_product_review_phone_valid( $phone ) ) {
		add_comment_meta( $comment_id, 'phone', $phone, true );
		$buyed = silver_product_review_phone_bought( $phone, (int) $comment->comment_post_ID ) ? 1 : 0;
	}
	update_comment_meta( $comment_id, 'buyed', $buyed );

	if ( silver_product_reviews_settings()['images'] ) {
		$ids = silver_product_review_save_images( $comment_id );
		if ( $ids ) {
			update_comment_meta( $comment_id, 'images', implode( ',', $ids ) );
		}
	}

	silver_product_review_telegram( $comment );
}

/**
 * Telegram message about a new review (Theme Options > WooCommerce > Product
 * Reviews: bot token, chat ID).
 *
 * @param WP_Comment $comment Comment.
 */
function silver_product_review_telegram( $comment ) {
	$token = preg_replace( '/[^A-Za-z0-9:_-]/', '', (string) get_theme_mod( 'product_reviews_telegram_token', '' ) );
	$chat  = trim( (string) get_theme_mod( 'product_reviews_telegram_chat', '' ) );

	if ( ! $token || ! preg_match( '/^(-?\d+|@[A-Za-z0-9_]{5,})$/', $chat ) ) {
		return;
	}

	$e      = static function ( $text ) {
		return htmlspecialchars( wp_strip_all_tags( (string) $text ), ENT_QUOTES, 'UTF-8' );
	};
	$rating = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
	$phone  = (string) get_comment_meta( $comment->comment_ID, 'phone', true );
	$images = count( silver_product_review_image_ids( $comment->comment_ID ) );

	$lines = array(
		'<b>' . $e( sprintf( /* translators: %s: site name */ __( 'New review on %s', 'silver' ), get_bloginfo( 'name' ) ) ) . '</b>',
		$e( __( 'Product', 'silver' ) ) . ': ' . $e( get_the_title( $comment->comment_post_ID ) ),
		$e( __( 'Name', 'silver' ) ) . ': ' . $e( $comment->comment_author ),
	);
	if ( $phone ) {
		$lines[] = $e( __( 'Phone', 'silver' ) ) . ': ' . $e( $phone );
	}
	if ( $rating ) {
		$lines[] = $e( __( 'Rating', 'silver' ) ) . ': ' . str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
	}
	if ( $images ) {
		/* translators: %d: number of photos */
		$lines[] = $e( sprintf( _n( '%d photo', '%d photos', $images, 'silver' ), $images ) );
	}
	$lines[] = '';
	$lines[] = $e( $comment->comment_content );
	$lines[] = '';
	$lines[] = '<a href="' . esc_url( admin_url( 'comment.php?action=editcomment&c=' . (int) $comment->comment_ID ) ) . '">' . $e( __( 'Open in the admin', 'silver' ) ) . '</a>';

	// Not blocking: the visitor does not wait for Telegram.
	wp_remote_post( 'https://api.telegram.org/bot' . $token . '/sendMessage', array(
		'blocking' => false,
		'timeout'  => 3,
		'body'     => array(
			'chat_id'                  => $chat,
			'text'                     => implode( "\n", $lines ),
			'parse_mode'               => 'HTML',
			'disable_web_page_preview' => 'true',
		),
	) );
}

/**
 * JSON error of the review form.
 *
 * @param string $message Message.
 * @param string $field   Field to focus (name attribute), if any.
 */
function silver_product_review_error( $message, $field = '' ) {
	wp_send_json_error( array(
		'message' => wp_strip_all_tags( $message ),
		'field'   => $field,
	), 200 );
}

/**
 * AJAX: sends the review form.
 */
function silver_product_review_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public review form, like the core comment form (wp-comments-post.php has no nonce either: pages are cached).
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- compared only.
		silver_product_review_error( __( 'Invalid request.', 'silver' ) );
	}

	$product_id = isset( $_POST['comment_post_ID'] ) ? absint( $_POST['comment_post_ID'] ) : 0;
	if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
		silver_product_review_error( __( 'Invalid request.', 'silver' ) );
	}

	if ( ! silver_product_reviews_can_review( $product_id ) ) {
		silver_product_review_error( __( 'Only logged in customers who have purchased this product may leave a review.', 'silver' ) );
	}

	$settings = silver_product_reviews_settings();
	$parent   = isset( $_POST['comment_parent'] ) ? absint( $_POST['comment_parent'] ) : 0;

	// Stars: WooCommerce does not check them on admin-ajax.php (is_admin()).
	$rating = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
	if ( ! $parent && wc_review_ratings_enabled() && wc_review_ratings_required() && ( $rating < 1 || $rating > 5 ) ) {
		silver_product_review_error( __( 'Please select a rating', 'silver' ), 'rating' );
	}

	if ( ! is_user_logged_in() ) {
		$author = isset( $_POST['author'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['author'] ) ) ) : '';
		if ( '' === $author ) {
			silver_product_review_error( __( 'Please enter your name.', 'silver' ), 'author' );
		}

		if ( 'hidden' !== $settings['phone'] ) {
			$phone = isset( $_POST['phone'] ) ? silver_product_review_phone_normalize( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
			if ( '' === $phone && 'required' === $settings['phone'] ) {
				silver_product_review_error( __( 'Please enter your phone number.', 'silver' ), 'phone' );
			}
			if ( '' !== $phone && ! silver_product_review_phone_valid( $phone ) ) {
				silver_product_review_error( __( 'Please enter a valid phone number.', 'silver' ), 'phone' );
			}
		} else {
			unset( $_POST['phone'] );
		}

		$email = isset( $_POST['email'] ) ? trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ) : '';
		if ( 'hidden' === $settings['email'] ) {
			$_POST['email'] = '';
		} elseif ( '' === $email && 'required' === $settings['email'] ) {
			silver_product_review_error( __( 'Please enter your email address.', 'silver' ), 'email' );
		} elseif ( isset( $_POST['email'] ) && '' !== trim( (string) wp_unslash( $_POST['email'] ) ) && ! is_email( $email ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- emptiness only.
			silver_product_review_error( __( 'Please enter a valid email address.', 'silver' ), 'email' );
		}
	}

	if ( $settings['images'] ) {
		$images = silver_product_review_validate_images();
		if ( is_wp_error( $images ) ) {
			silver_product_review_error( $images->get_error_message(), 'images[]' );
		}
		if ( $images && ! current_user_can( 'moderate_comments' ) && silver_product_review_images_sent() + count( $images ) > silver_product_review_images_hourly_limit() ) {
			silver_product_review_error( __( 'Too many photos sent. Please try again later.', 'silver' ), 'images[]' );
		}
	}

	// Name and email checked above, as set in Theme Options (not the
	// WordPress "name and email required" setting for all comments).
	$no_require = static function () {
		return 0;
	};
	// A review, not a comment (WooCommerce skips it on admin-ajax.php).
	$review_type = static function ( $data ) use ( $product_id, $parent ) {
		if ( ! $parent && (int) $data['comment_post_ID'] === $product_id && in_array( $data['comment_type'], array( '', 'comment' ), true ) ) {
			$data['comment_type'] = 'review';
		}
		return $data;
	};
	add_filter( 'pre_option_require_name_email', $no_require );
	add_filter( 'preprocess_comment', $review_type, 1 );
	add_action( 'comment_post', 'silver_product_review_saved', 20, 2 );

	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

	remove_filter( 'pre_option_require_name_email', $no_require );
	remove_filter( 'preprocess_comment', $review_type, 1 );
	remove_action( 'comment_post', 'silver_product_review_saved', 20 );

	if ( is_wp_error( $comment ) ) {
		silver_product_review_error( $comment->get_error_message() ? $comment->get_error_message() : __( 'Your review could not be sent.', 'silver' ) );
	}

	$user = wp_get_current_user();
	do_action( 'set_comment_cookies', $comment, $user, ! empty( $_POST['wp-comment-cookies-consent'] ) );

	$approved = '1' === (string) $comment->comment_approved;
	wp_send_json_success( array(
		'approved' => $approved,
		'message'  => $approved
			? __( 'Thank you for your review!', 'silver' )
			: __( 'Thank you! Your review will appear once it is approved.', 'silver' ),
	) );
	// phpcs:enable
}
add_action( 'wp_ajax_silver_product_review', 'silver_product_review_submit' );
add_action( 'wp_ajax_nopriv_silver_product_review', 'silver_product_review_submit' );

/**
 * AJAX: more reviews.
 */
function silver_product_reviews_more() {
	$product_id = isset( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public data.
	$page       = isset( $_REQUEST['page'] ) ? max( 2, absint( $_REQUEST['page'] ) ) : 2; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public data.
	$post       = $product_id ? get_post( $product_id ) : null;

	// Published products only (not the reviews of a private or draft one).
	if ( ! $post || 'product' !== $post->post_type || 'publish' !== $post->post_status || post_password_required( $post ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'silver' ) ) );
	}

	silver_product_reviews_setup();

	$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- for the templates; reset below.
	setup_postdata( $post );
	$GLOBALS['product'] = wc_get_product( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	$result = silver_product_reviews_query( $product_id, $page );
	$html   = silver_product_reviews_list_html( $result['comments'] );

	wp_reset_postdata();

	wp_send_json_success( array(
		'html' => $html,
		'more' => $result['more'],
		'page' => $page + 1,
	) );
}
add_action( 'wp_ajax_silver_product_reviews_more', 'silver_product_reviews_more' );
add_action( 'wp_ajax_nopriv_silver_product_reviews_more', 'silver_product_reviews_more' );
