<?php
// [blog_posts]
function shortcode_latest_from_blog($atts, $content = null, $tag = '')
{

	// PHP callers only (silver_apply_shortcode()): 'query' => a WP_Query that
	// already holds the posts to show, e.g. the main query of the blog page
	// and archives (template-parts/posts/archive-*.php). Its posts are shown
	// as they are, without a second query.
	$main_query = null;
	if (is_array($atts) && isset($atts['query'])) {
		$main_query = $atts['query'] instanceof WP_Query ? $atts['query'] : null;
		unset($atts['query']);
	}

	$defined_atts = $atts;
	$normal_layout = in_array(isset($atts['style']) ? $atts['style'] : '', array('', 'normal'), true)
		&& (! isset($atts['type']) || 'grid' !== $atts['type']);

	extract($atts = ux_builder_shortcode_atts(array(
		"_id" => 'row-' . rand(),
		'style' => '',
		'class' => '',
		'visibility' => '',

		// Layout
		"columns" => '1',
		// Inherit an explicit column count instead of overriding it on desktop.
		"columns__lg" => isset($atts['columns']) ? '' : '4',
		"columns__sm" => '',
		"columns__md" => '',
		'col_spacing' => '',
		"type" => 'slider', // slider, row, masonery, grid
		'width' => '',
		'grid' => '1',
		'grid_height' => '600px',
		'grid_height__md' => '500px',
		'grid_height__sm' => '400px',
		'slider_nav_style' => 'reveal',
		'slider_nav_position' => '',
		'slider_nav_color' => '',
		'slider_bullets' => 'false',
		'slider_arrows' => 'true',
		'auto_slide' => 'false',
		'infinitive' => 'true',
		'thumb_role' => '',
		'thumb_key' => '',
		'depth' => '',
		'depth_hover' => '',
		// Relay
		'relay' => '',
		'relay_control_result_count' => 'true',
		'relay_control_position' => 'bottom',
		'relay_control_align' => 'center',
		'relay_id' => '',
		'relay_class' => '',
		// posts
		'posts' => '8',
		'ids' => '', // Custom IDs
		'cat' => '',
		'category' => '', // Added for Silver v2 fallback
		'excerpt' => $normal_layout ? 'false' : 'visible',
		'excerpt_length' => 15,
		'offset' => '',
		'orderby' => 'date',
		'order' => 'DESC',
		'tags' => '',
		'page_number' => '1',

		// Read more
		'readmore' => '',
		'readmore_color' => '',
		'readmore_style' => 'outline',
		'readmore_size' => 'small',

		// div meta
		'post_icon' => 'true',
		'comments' => $normal_layout ? 'false' : 'true',
		'show_date' => $normal_layout ? 'text' : 'badge', // badge, text
		'badge_style' => '',
		'show_category' => $normal_layout ? 'label' : 'false',

		//Title
		'title_size' => 'large',
		'title_style' => '',
		'title_tag' => 'div', // h2 / h3 / h4 / p / div. Blog page and archives: h2 (archive-items.php).

		// Box styles
		'animate' => '',
		'text_pos' => 'bottom',
		'text_padding' => '',
		'text_bg' => '',
		'text_size' => '',
		'text_color' => '',
		'text_hover' => '',
		'text_align' => $normal_layout ? 'left' : 'center',
		'image_size' => $normal_layout ? 'medium_large' : 'medium',
		'image_width' => '',
		'image_radius' => '',
		'image_height' => '56%',
		'image_hover' => '',
		'image_hover_alt' => '',
		'image_overlay' => '',
		'image_depth' => '',
		'image_depth_hover' => '',

	), $atts));

	// Stop if visibility is hidden
	if ($visibility == 'hidden') return;

	// Responsive image size: the smallest screen with a value is the base.
	$image_height = silver_responsive_base($atts, 'image_height');

	ob_start();

	$classes_box = array();
	$classes_image = array();
	$classes_text = array();

	// Fix overlay color
	if ($style == 'text-overlay') {
		$image_hover = 'zoom';
	}
	$style = str_replace('text-', '', $style);

	// Fix grids
	if ($type == 'grid') {
		if (!$text_pos) $text_pos = 'center';
		$columns = 0;
		$current_grid = 0;
		$grid = silver_get_grid($grid);
		$grid_total = count($grid);
	}

	// Fix overlay
	if ($style == 'overlay' && !$image_overlay) $image_overlay = 'rgba(0,0,0,.25)';

	// Set box style
	if ($normal_layout) $classes_box[] = 'blog-post-normal';
	if ($style) $classes_box[] = 'box-' . $style;
	if ($style == 'overlay') $classes_box[] = 'dark';
	if ($style == 'shade') $classes_box[] = 'dark';
	if ($style == 'badge') $classes_box[] = 'hover-dark';
	if ($text_pos) $classes_box[] = 'box-text-' . $text_pos;

	if ($image_hover)  $classes_image[] = 'image-' . $image_hover;
	if ($image_hover_alt)  $classes_image[] = 'image-' . $image_hover_alt;
	if ($image_height) $classes_image[] = 'image-cover';

	// Text classes
	if ($text_hover) $classes_text[] = 'show-on-hover hover-' . $text_hover;
	if ($text_align) $classes_text[] = 'text-' . $text_align;
	if ($text_size) $classes_text[] = 'is-' . $text_size;
	if ($text_color == 'dark') $classes_text[] = 'dark';

	$css_args_img = array(
		array('attribute' => 'border-radius', 'value' => $image_radius, 'unit' => '%'),
		array('attribute' => 'width', 'value' => $image_width, 'unit' => '%'),
	);

	$css_image_height = array(
		array('attribute' => 'padding-top', 'value' => $image_height),
	);

	$css_args = array(
		array('attribute' => 'background-color', 'value' => $text_bg),
		array('attribute' => 'padding', 'value' => $text_padding),
	);

	// Add Animations
	if ($animate) {
		$animate = 'data-animate="' . esc_attr($animate) . '"';
	}

	// Tag of the post titles: a heading (h2 on the blog page and archives)
	// gives the list its outline; the look stays the same (silver-modern.css).
	$title_tag = in_array($title_tag, array('h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div'), true) ? $title_tag : 'div';

	$classes_text = implode(' ', $classes_text);
	$classes_image = implode(' ', $classes_image);
	$classes_box = implode(' ', $classes_box);

	// Repeater styles
	$repeater['id'] = $_id;
	$repeater['tag'] = $tag;
	$repeater['type'] = $type;
	$repeater['class'] = $class;
	$repeater['visibility'] = $visibility;
	$repeater['style'] = $style;
	$repeater['slider_style'] = $slider_nav_style;
	$repeater['slider_nav_position'] = $slider_nav_position;
	$repeater['slider_nav_color'] = $slider_nav_color;
	$repeater['slider_bullets'] = $slider_bullets;
	$repeater['auto_slide'] = $auto_slide;
	$repeater['infinitive'] = $infinitive;
	$repeater['thumb_role'] = $thumb_role;
	$repeater['thumb_key'] = $thumb_key;
	$repeater['row_spacing'] = $col_spacing;
	$repeater['row_width'] = $width;
	$repeater['columns'] = $columns;
	$repeater['columns__md'] = $columns__md;
	$repeater['columns__ml'] = $columns__ml;
	$repeater['columns__lg'] = $columns__lg;
	$repeater['depth'] = $depth;
	$repeater['depth_hover'] = $depth_hover;
	$repeater['responsive_atts'] = $atts;

	if (! empty($offset)) {
		$found_posts_filter_callback = function ($found_posts, $query) use ($offset) {
			return $found_posts - (int) $offset;
		};

		add_filter('found_posts', $found_posts_filter_callback, 1, 2);
	}

	$offset = (int) $page_number > 1
		? (int) $offset + ((int) $page_number - 1) * (int) $posts
		: $offset;

	$args = array(
		'post_status' => 'publish',
		'post_type' => 'post',
		'offset' => $offset,
		'cat' => $cat,
		'tag__in' => $tags ? array_filter(array_map('trim', explode(',', $tags))) : '',
		'posts_per_page' => $posts,
		'paged' => $page_number,
		'ignore_sticky_posts' => true,
		'orderby'             => $orderby,
		'order'               => $order,
	);

	// Added for Silver v2 fallback
	if (get_theme_mod('flatsome_fallback', 0) && $category) {
		$args['category_name'] = $category;
	}

	// If custom ids
	if (!empty($ids)) {
		$ids = explode(',', $ids);
		$ids = array_map('trim', $ids);

		$args = array(
			'post__in' => $ids,
			'post_type' => array(
				'post',
				'featured_item', // Include for its tag archive listing.
			),
			'numberposts' => -1,
			'orderby' => 'post__in',
			'posts_per_page' => 9999,
			'ignore_sticky_posts' => true,
		);

		// Include for search archive listing.
		if (is_search()) {
			$args['post_type'][] = 'page';
		}
	}

	if ($main_query) {
		$recentPosts = $main_query;
		$recentPosts->rewind_posts();
	} else {
		$recentPosts = new WP_Query($args);
	}

	if (isset($found_posts_filter_callback)) {
		remove_filter('found_posts', $found_posts_filter_callback, 1);
	}

	Silver_Relay::render_container_open($recentPosts, $tag, $defined_atts, $atts);

	if ($type == 'grid') {
		silver_get_grid_height($grid_height, $_id);
	}

	get_silver_repeater_start($repeater);

	while ($recentPosts->have_posts()) : $recentPosts->the_post();

		$col_class    = array('post-item');
		$show_excerpt = $excerpt;

		if (get_post_format() == 'video') $col_class[] = 'has-post-icon';

		if ($type == 'grid') {
			if ($grid_total > $current_grid) $current_grid++;
			$current = $current_grid - 1;

			$col_class[] = 'grid-col';
			if ($grid[$current]['height']) $col_class[] = 'grid-col-' . $grid[$current]['height'];

			if ($grid[$current]['span']) $col_class[] = 'xlarge-' . $grid[$current]['span'];
			if ($grid[$current]['md']) $col_class[] = 'medium-' . $grid[$current]['md'];

			// Set image size
			if ($grid[$current]['size']) $image_size = $grid[$current]['size'];

			// Hide excerpt for small sizes
			if ($grid[$current]['size'] == 'thumbnail') $show_excerpt = 'false';
		}

	?><div class="col <?php echo esc_attr(implode(' ', $col_class)); ?>" <?php echo $animate; ?>>
			<div class="col-inner">
				<?php // Each post is an <article> (the .col stays a div: the cell of the row / grid / slider). ?>
				<article class="box <?php echo esc_attr($classes_box); ?> box-blog-post has-hover">
					<?php if (has_post_thumbnail() || $normal_layout) { ?>
						<div class="box-image" <?php echo get_shortcode_inline_css($css_args_img); ?>>
							<div class="<?php echo esc_attr($classes_image); ?>" <?php echo get_shortcode_inline_css($css_image_height); ?>>
								<a href="<?php the_permalink(); ?>" class="plain" aria-label="<?php echo esc_attr(get_the_title()); ?>">
									<?php if (has_post_thumbnail()) the_post_thumbnail($image_size); ?>
								</a>
								<?php if ($image_overlay) { ?><div class="overlay" style="<?php echo esc_attr(silver_overlay_style($image_overlay)); ?>"></div><?php } ?>
								<?php if ($style == 'shade') { ?><div class="shade"></div><?php } ?>
							</div>
							<?php if ($normal_layout && $show_category === 'label') {
								$card_categories = get_the_category();
								$card_category = $card_categories ? reset($card_categories) : null;
								if ($card_category) { ?>
									<span class="blog-post-category"><?php echo esc_html($card_category->name); ?></span>
								<?php }
							} ?>
							<?php if ($post_icon && $post_icon !== 'false' && get_post_format()) { ?>
								<div class="absolute no-click x50 y50 md-x50 md-y50 xl-x50 xl-y50">
									<div class="overlay-icon">
										<?php echo get_silver_icon('icon-play'); ?>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="box-text <?php echo esc_attr($classes_text); ?>" <?php echo get_shortcode_inline_css($css_args); ?>>
						<div class="box-text-inner blog-post-inner">

							<?php do_action('silver_blog_post_before'); ?>

							<?php if ((!$normal_layout || $show_category === 'text') && $show_category !== 'false') { ?>
								<p class="cat-label <?php if ($show_category == 'label') echo 'tag-label'; ?> is-xxsmall op-7 uppercase">
									<?php
									foreach ((get_the_category()) as $cat) {
										echo wp_kses_post($cat->cat_name) . ' ';
									}
									?>
								</p>
							<?php } ?>
							<?php if ($normal_layout && $show_date !== 'false' && ($show_date !== 'badge' || !has_post_thumbnail())) { ?>
								<div class="post-meta is-small op-8"><time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(get_the_date('d/m/Y')); ?></time></div>
							<?php } ?>
							<<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput -- whitelisted above. ?> class="post-title <?php if (isset($defined_atts['title_size']) && $defined_atts['title_size'] !== '') echo 'has-custom-title-size '; ?>is-<?php echo esc_attr($title_size); ?> <?php echo esc_attr($title_style); ?>">
								<a href="<?php the_permalink() ?>" class="plain"><?php the_title(); ?></a>
							</<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
							<?php if (!$normal_layout && ((!has_post_thumbnail() && $show_date !== 'false') || $show_date == 'text')) { ?><div class="post-meta is-small op-8"><?php echo esc_html(get_the_date('d/m/Y')); ?></div><?php } ?>
							<?php if (!$normal_layout) { ?><div class="is-divider"></div><?php } ?>
							<?php if ($show_excerpt !== 'false') { ?>
								<p class="from_the_blog_excerpt <?php if ($show_excerpt !== 'visible') {
																	echo 'show-on-hover hover-' . esc_attr($show_excerpt);
																} ?>">
									<?php echo silver_get_the_excerpt($excerpt_length); ?>
								</p>
							<?php } ?>
							<?php if (in_array($comments, array('true', 'visible'), true) && comments_open() && '0' != get_comments_number()) { ?>
								<p class="from_the_blog_comments uppercase is-xsmall">
									<?php
									$comments_number = get_comments_number(get_the_ID());
									/* translators: %s: Comment count */
									printf(
										_n('%s Comment', '%s Comments', $comments_number, 'silver'),
										number_format_i18n($comments_number)
									)
									?>
								</p>
							<?php } ?>

							<?php if ($readmore) { ?>
								<a href="<?php the_permalink(); ?>" class="button <?php echo esc_attr($readmore_color); ?> is-<?php echo esc_attr($readmore_style); ?> is-<?php echo esc_attr($readmore_size); ?> mb-0">
									<?php echo wp_kses_post($readmore); ?>
								</a>
							<?php } ?>

							<?php do_action('silver_blog_post_after'); ?>

						</div>
					</div>
					<?php if (has_post_thumbnail() && ($show_date == 'badge' || (!$normal_layout && $show_date == 'true'))) { ?>
						<?php if (!$badge_style) $badge_style = get_theme_mod('blog_badge_style', 'outline'); ?>
						<div class="badge absolute top post-date badge-<?php echo esc_attr($badge_style); ?>">
							<div class="badge-inner">
								<span class="post-date-day"><?php echo get_the_time('d', get_the_ID()); ?></span><br>
								<span class="post-date-month is-xsmall"><?php echo get_the_time('M', get_the_ID()); ?></span>
							</div>
						</div>
					<?php } ?>
				</article>
			</div>
		</div><?php endwhile;
			wp_reset_query();

			// Get repeater end.
			get_silver_repeater_end($atts);

			Silver_Relay::render_container_close();

			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

		add_shortcode("blog_posts", "shortcode_latest_from_blog");
