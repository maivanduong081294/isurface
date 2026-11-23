<?php
/**
 * Product reviews: the theme's layout (single-product-reviews.php loads it
 * when Theme Options > WooCommerce > Product Reviews > "Theme layout" is on).
 *
 * - Overview: average, stars, a bar per star, "Write a review".
 * - The list (#comments): the first reviews, "More reviews".
 * - The form: shown here without JavaScript; with it, opened in a
 *   silverDialog popup (assets/js/src/silver-product-reviews.js).
 *
 * Functions: inc/woocommerce/structure-wc-product-reviews.php.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! comments_open() ) {
	return;
}

$product_id = $product->get_id();
$settings   = silver_product_reviews_settings();
$ratings_on = wc_review_ratings_enabled();
$can_review = silver_product_reviews_can_review( $product_id );

$review_count = (int) $product->get_review_count();
$average      = (float) $product->get_average_rating();
$star_counts  = array_map( 'intval', (array) $product->get_rating_counts() );
$rated        = array_sum( $star_counts );

$list = silver_product_reviews_query( $product_id, 1 );

$messages = array(
	'tooMany'  => sprintf(
		/* translators: %d: number of photos */
		_n( 'Up to %d photo.', 'Up to %d photos.', $settings['images_max'], 'silver' ),
		$settings['images_max']
	),
	/* translators: %s: size, e.g. "1 MB" */
	'tooBig'   => sprintf( __( 'Each photo up to %s.', 'silver' ), size_format( $settings['image_size'] ) ),
	'type'     => __( 'Photos only (JPG, PNG, WebP).', 'silver' ),
	'rating'   => __( 'Please select a rating', 'silver' ),
	'error'    => __( 'Your review could not be sent.', 'silver' ),
	'title'    => __( 'Write a review', 'silver' ),
	'photo'    => __( 'Customer photos', 'silver' ),
	'prev'     => __( 'Previous photo', 'silver' ),
	'next'     => __( 'Next photo', 'silver' ),
	'remove'   => __( 'Remove photo', 'silver' ),
	'loading'  => __( 'Loading…', 'silver' ),
);
?>
<div id="reviews" class="woocommerce-Reviews product-reviews" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-images-max="<?php echo esc_attr( $settings['images_max'] ); ?>" data-image-size="<?php echo esc_attr( $settings['image_size'] ); ?>" data-messages="<?php echo esc_attr( wp_json_encode( $messages ) ); ?>">

	<div class="product-reviews__overview<?php echo $ratings_on ? '' : ' product-reviews__overview--no-ratings'; ?>">
		<?php if ( $ratings_on ) : ?>
			<div class="product-reviews__score">
				<?php if ( $rated ) : ?>
					<p class="product-reviews__average">
						<span class="product-reviews__average-value"><?php echo esc_html( wc_format_localized_decimal( number_format( floor( $average * 10 ) / 10, 1 ) ) ); ?></span><span class="product-reviews__average-base">/5</span>
					</p>
					<?php echo silver_product_review_stars( $average, 'product-reviews__stars' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function. ?>
					<p class="product-reviews__total">
						<?php
						/* translators: %s: number of reviews */
						echo esc_html( sprintf( _n( '%s review', '%s reviews', $review_count, 'silver' ), number_format_i18n( $review_count ) ) );
						?>
					</p>
				<?php else : ?>
					<p class="product-reviews__average product-reviews__average--none">
						<span class="product-reviews__average-value">0</span><span class="product-reviews__average-base">/5</span>
					</p>
					<?php echo silver_product_review_stars( 0, 'product-reviews__stars' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function. ?>
					<p class="product-reviews__total"><?php esc_html_e( 'No reviews yet', 'silver' ); ?></p>
				<?php endif; ?>
			</div>

			<ul class="product-reviews__bars">
				<?php
				for ( $star = 5; $star >= 1; $star-- ) :
					$count   = isset( $star_counts[ $star ] ) ? $star_counts[ $star ] : 0;
					$percent = $rated ? round( $count / $rated * 100 ) : 0;
					?>
					<li class="product-reviews__bar">
						<span class="product-reviews__bar-label" aria-hidden="true"><?php echo esc_html( number_format_i18n( $star ) ); ?><?php echo get_silver_icon( 'icon-star', null, array( 'class' => 'product-rating-summary__star' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon. ?></span>
						<span class="product-reviews__bar-track" aria-hidden="true"><span class="product-reviews__bar-fill" style="width:<?php echo esc_attr( $percent ); ?>%"></span></span>
						<span class="product-reviews__bar-count" aria-hidden="true"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
						<span class="screen-reader-text">
							<?php
							/* translators: 1: number of stars, 2: number of reviews */
							echo esc_html( sprintf( _n( '%1$s star: %2$s', '%1$s stars: %2$s', $star, 'silver' ), number_format_i18n( $star ), number_format_i18n( $count ) ) );
							?>
						</span>
					</li>
				<?php endfor; ?>
			</ul>
		<?php endif; ?>

		<div class="product-reviews__cta">
			<?php if ( $can_review ) : ?>
				<p class="product-reviews__cta-text">
					<?php echo $review_count ? esc_html__( 'Used this product? Share your thoughts.', 'silver' ) : esc_html__( 'Be the first to review this product.', 'silver' ); ?>
				</p>
				<a class="button primary product-reviews__write" href="#review_form" data-review-open>
					<?php echo get_silver_icon( 'icon-pen-alt-fill', null, array( 'class' => 'product-reviews__write-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon. ?>
					<span><?php esc_html_e( 'Write a review', 'silver' ); ?></span>
				</a>
			<?php else : ?>
				<p class="product-reviews__cta-text woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'silver' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div id="comments" class="product-reviews__list">
		<?php if ( $list['comments'] ) : ?>
			<ol class="commentlist product-reviews__items">
				<?php echo silver_product_reviews_list_html( $list['comments'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- templates. ?>
			</ol>

			<?php if ( $list['more'] ) : ?>
				<div class="product-reviews__more">
					<button type="button" class="button is-outline product-reviews__more-button" data-review-more data-page="2">
						<?php esc_html_e( 'More reviews', 'silver' ); ?>
					</button>
				</div>
			<?php endif; ?>
		<?php elseif ( ! $ratings_on ) : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'silver' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $can_review ) : ?>
		<div id="review_form_wrapper" class="product-reviews__form">
			<div id="review_form" class="product-review-form" data-review-form>
				<div class="product-review-form__product">
					<?php echo $product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'product-review-form__product-img', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?>
					<span class="product-review-form__product-name"><?php echo esc_html( $product->get_name() ); ?></span>
				</div>
				<?php
				$commenter = wp_get_current_commenter();
				$required  = '&nbsp;<span class="required" aria-hidden="true">*</span>';
				$field     = static function ( $key, $label, $type, $value, $is_required, $attrs = '' ) use ( $required ) {
					return '<p class="product-review-form__field comment-form-' . esc_attr( $key ) . '">'
						. '<label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . ( $is_required ? $required : '' ) . '</label>'
						. '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '" ' . $attrs . ( $is_required ? ' required' : '' ) . ' /></p>';
				};

				$html = '';

				if ( $ratings_on ) {
					$labels = array(
						1 => __( 'Very poor', 'silver' ),
						2 => __( 'Not that bad', 'silver' ),
						3 => __( 'Average', 'silver' ),
						4 => __( 'Good', 'silver' ),
						5 => __( 'Perfect', 'silver' ),
					);
					$html .= '<fieldset class="product-review-form__rating comment-form-rating">';
					$html .= '<legend>' . esc_html__( 'How would you rate this product?', 'silver' ) . ( wc_review_ratings_required() ? $required : '' ) . '</legend>';
					$html .= '<div class="product-review-stars" data-review-stars>';
					foreach ( $labels as $value => $label ) {
						$html .= '<input type="radio" class="product-review-stars__input" id="review-rating-' . $value . '" name="rating" value="' . $value . '"' . ( 1 === $value && wc_review_ratings_required() ? ' required' : '' ) . ' />';
						$html .= '<label class="product-review-stars__star" for="review-rating-' . $value . '" data-label="' . esc_attr( $label ) . '">' . get_silver_icon( 'icon-star' ) . '<span class="screen-reader-text">' . esc_html( $label ) . '</span></label>';
					}
					$html .= '<span class="product-review-stars__text" aria-hidden="true"></span>';
					$html .= '</div></fieldset>';
				}

				if ( ! is_user_logged_in() ) {
					$html .= '<div class="product-review-form__fields">';
					$html .= $field( 'author', __( 'Name', 'silver' ), 'text', $commenter['comment_author'], true, 'autocomplete="name" maxlength="245"' );
					if ( 'hidden' !== $settings['phone'] ) {
						$html .= $field( 'phone', __( 'Phone', 'silver' ), 'tel', '', 'required' === $settings['phone'], 'autocomplete="tel" inputmode="tel" maxlength="15"' );
					}
					if ( 'hidden' !== $settings['email'] ) {
						$html .= $field( 'email', __( 'Email', 'silver' ), 'email', $commenter['comment_author_email'], 'required' === $settings['email'], 'autocomplete="email" maxlength="100"' );
					}
					$html .= '</div>';
				}

				$html .= '<p class="product-review-form__field comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'silver' ) . $required . '</label>'
					. '<textarea id="comment" name="comment" rows="5" maxlength="65525" required placeholder="' . esc_attr__( 'What did you like or dislike? How do you use it?', 'silver' ) . '"></textarea></p>';

				if ( $settings['images'] ) {
					$html .= '<div class="product-review-form__photos" data-review-photos>';
					$html .= '<span class="product-review-form__photos-label" id="review-photos-label">' . esc_html__( 'Photos', 'silver' ) . ' <span class="product-review-form__hint">'
						/* translators: 1: number of photos, 2: size, e.g. "1 MB" */
						. esc_html( sprintf( __( '(up to %1$d, %2$s each)', 'silver' ), $settings['images_max'], size_format( $settings['image_size'] ) ) ) . '</span></span>';
					$html .= '<ul class="product-review-photos" data-review-photo-list></ul>';
					$html .= '<label class="product-review-photos__add" for="review-images"><i class="fa-solid fa-camera" aria-hidden="true"></i><span>' . esc_html__( 'Add photos', 'silver' ) . '</span></label>';
					$html .= '<input type="file" class="product-review-photos__input" id="review-images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple aria-labelledby="review-photos-label" />';
					$html .= '</div>';
				}

				$html .= '<p class="product-review-form__error" role="alert" hidden></p>';

				$comment_form = array(
					'title_reply'          => '',
					'title_reply_to'       => '',
					'title_reply_before'   => '',
					'title_reply_after'    => '',
					'cancel_reply_link'    => ' ',
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'logged_in_as'         => '',
					'fields'               => array(),
					'comment_field'        => $html,
					'class_form'           => 'comment-form product-review-form__form',
					'class_submit'         => 'submit button primary product-review-form__submit',
					'label_submit'         => __( 'Send review', 'silver' ),
					'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
				);

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'silver' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}

				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
