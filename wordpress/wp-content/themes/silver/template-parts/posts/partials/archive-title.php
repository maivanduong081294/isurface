<?php
/**
 * Post archive title.
 *
 * @package          Silver\Templates
 * @silver-version 3.16.0
 *
 * @var array $args show_title: false when only the breadcrumbs are on
 *                  (see silver_archive_title()).
 */

$show_title = isset( $args['show_title'] ) ? $args['show_title'] : true;
// Blog > Blog Layout > Page title tag: a div when the description has its own H1.
$title_tag  = function_exists( 'silver_page_header_has_own_h1' ) && silver_page_header_has_own_h1() ? 'div' : 'h1';

?>
<header class="archive-page-header">
	<div class="row">
	<div class="xlarge-12 text-center col">
	<?php silver_blog_breadcrumbs(); ?>
	<?php if ( $show_title ) : ?>
	<<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- h1 or div. ?> class="page-title is-large uppercase heading-font">
		<?php

			if ( is_category() ) :
				printf( __( 'Category Archives: %s', 'silver' ), '<span>' . single_cat_title( '', false ) . '</span>' );

			elseif ( is_tag() ) :
				printf( __( 'Tag Archives: %s', 'silver' ), '<span>' . single_tag_title( '', false ) . '</span>' );

			elseif ( is_search() ) :
				printf( __( 'Search Results for: %s', 'silver' ), '<span>' . get_search_query() . '</span>' );

			elseif ( is_author() ) :
				/* Queue the first post, that way we know
				 * what author we're dealing with (if that is the case).
				*/
				the_post();
				printf( __( 'Author Archives: %s', 'silver' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
				/* Since we called the_post() above, we need to
				 * rewind the loop back to the beginning that way
				 * we can run the loop properly, in full.
				 */
				rewind_posts();

			elseif ( is_day() ) :
				printf( __( 'Daily Archives: %s', 'silver' ), '<span>' . get_the_date() . '</span>' );

			elseif ( is_month() ) :
				printf( __( 'Monthly Archives: %s', 'silver' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

			elseif ( is_year() ) :
				printf( __( 'Yearly Archives: %s', 'silver' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

			elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
				_e( 'Asides', 'silver' );

			elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
				_e( 'Images', 'silver');

			elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
				_e( 'Videos', 'silver' );

			elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
				_e( 'Quotes', 'silver' );

			elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
				_e( 'Links', 'silver' );

			else :
				_e( '', 'silver' );

			endif;
		?>
	</<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php
		if ( is_category() ) :
			// show an optional category description
			$category_description = category_description();
			if ( ! empty( $category_description ) ) :
				echo apply_filters( 'category_archive_meta', '<div class="taxonomy-description">' . $category_description . '</div>' );
			endif;

		elseif ( is_tag() ) :
			// show an optional tag description
			$tag_description = tag_description();
			if ( ! empty( $tag_description ) ) :
				echo apply_filters( 'tag_archive_meta', '<div class="taxonomy-description">' . $tag_description . '</div>' );
			endif;

		endif;
	?>
	<?php endif; // $show_title ?>
	</div>
	</div>
</header>
