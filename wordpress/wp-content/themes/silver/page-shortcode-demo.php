<?php
/**
 * Template name: Page - Shortcode Demo
 *
 * Renders every theme shortcode with its code beside it, to review the layout
 * and the UI / UX. Create a page, choose this template and open it on the
 * front end. Shortcodes that need data (posts, products, menus) use what the
 * site has; WooCommerce ones appear only while WooCommerce is active.
 * Registered tags without an example are listed at the end.
 *
 * @package          Silver\Templates
 */

/**
 * Renders one demo block: title, rendered shortcode and its code.
 * A shortcode that throws is reported instead of stopping the page.
 *
 * @param string $title Block title.
 * @param string $code  Shortcode source.
 */
function silver_shortcode_demo_block( $title, $code ) {
	?>
	<div class="sc-demo-block">
		<h3 class="sc-demo-title"><?php echo esc_html( $title ); ?></h3>
		<div class="sc-demo-preview">
			<?php
			try {
				$html = trim( do_shortcode( $code ) );
				// phpcs:ignore WordPress.Security.EscapeOutput
				echo '' !== $html ? $html : '<em class="sc-demo-empty">Renders nothing here (needs content or a specific page).</em>';
			} catch ( Throwable $e ) {
				echo '<em class="sc-demo-error">' . esc_html( $e->getMessage() ) . '</em>';
			}
			?>
		</div>
		<details class="sc-demo-code-wrap">
			<summary>Code</summary>
			<pre class="sc-demo-code"><code><?php echo esc_html( $code ); ?></code></pre>
		</details>
	</div>
	<?php
}

$sc_lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.';
$sc_box   = static function ( $text ) {
	return '<div style="background:#eef3f8;padding:16px;text-align:center">' . $text . '</div>';
};

$is_shop = function_exists( 'is_woocommerce_activated' ) && is_woocommerce_activated();

// Images of the media library for the image shortcodes (none: they show their placeholder).
$sc_images = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => 3,
		'fields'         => 'ids',
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
// The second image when there is one: the first is often the WooCommerce placeholder.
$sc_img  = $sc_images ? (int) ( $sc_images[1] ?? $sc_images[0] ) : '';
$sc_ids  = $sc_images ? implode( ',', $sc_images ) : '';

$groups = array();

/* ------------------------------------------------------------------ Button */
$groups['Button'] = array(
	'Colors'               => '[button text="Default"] [button text="Primary" color="primary"] [button text="Secondary" color="secondary"] [button text="Success" color="success"] [button text="Alert" color="alert"] [button text="White" color="white"]',
	'Styles'               => '[button text="Default"] [button text="Outline" style="outline"] [button text="Simple" style="link"] [button text="Underline" style="underline"] [button text="Shade" style="shade"] [button text="Bevel" style="bevel"] [button text="Gloss" style="gloss"]',
	'Outline, colored'     => '[button text="Primary" color="primary" style="outline"] [button text="Secondary" color="secondary" style="outline"] [button text="Alert" color="alert" style="outline"]',
	'Sizes'                => '[button text="XS" size="xsmall"] [button text="Small" size="small"] [button text="Medium" size="medium"] [button text="Large" size="large"] [button text="XL" size="xlarge"]',
	'Radius'               => '[button text="Square" radius="0"] [button text="Round" radius="10"] [button text="Circle" radius="99"]',
	'Depth and hover'      => '[button text="Depth 2" style="shade" depth="2"] [button text="Depth 4" style="shade" depth="4" depth_hover="5"]',
	'Icon'                 => '[button text="Call us" icon="icon-phone"] [button text="Left icon" icon="icon-phone" icon_pos="left"] [button text="Hover icon" icon="icon-phone" icon_reveal="true"]',
	'Letter case / expand' => '[button text="Uppercase"] [button text="Lowercase" letter_case="lowercase"] [button text="Expand" expand="true" color="secondary"]',
	'Phone / header button / video button / login' => '[phone number="+84 000 000 000"] [header_button text="Order now" link="#"] [video_button video="https://www.youtube.com/watch?v=f3Hh_qSkpaA"] [facebook_login_button]',
);

/* ------------------------------------------------------------------ Layout */
$groups['Layout'] = array(
	'row, col (3 columns)'          => '[row] [col span="4"]' . $sc_box( 'Col 4' ) . '[/col] [col span="4"]' . $sc_box( 'Col 4' ) . '[/col] [col span="4"]' . $sc_box( 'Col 4' ) . '[/col] [/row]',
	'row styles (collapse / small / large)' => '[row style="collapse"] [col span="6"]' . $sc_box( 'Collapse' ) . '[/col] [col span="6"]' . $sc_box( 'Collapse' ) . '[/col] [/row] [row style="small"] [col span="6"]' . $sc_box( 'Small' ) . '[/col] [col span="6"]' . $sc_box( 'Small' ) . '[/col] [/row] [row style="large"] [col span="6"]' . $sc_box( 'Large' ) . '[/col] [col span="6"]' . $sc_box( 'Large' ) . '[/col] [/row]',
	'row_inner, col_inner'          => '[row] [col span="12"] [row_inner] [col_inner span="6"]' . $sc_box( 'Inner 6' ) . '[/col_inner] [col_inner span="6"]' . $sc_box( 'Inner 6' ) . '[/col_inner] [/row_inner] [/col] [/row]',
	'section'                       => '[section bg_color="rgb(236, 240, 245)" padding="30px"] [row] [col span="12"]' . $sc_box( 'Section content' ) . '[/col] [/row] [/section]',
	'section_inner'                 => '[section] [row] [col span="12"] [section_inner]' . $sc_box( 'Inner section' ) . '[/section_inner] [/col] [/row] [/section]',
	'ux_container, ux_container_inner' => '[ux_container]' . $sc_box( 'Container' ) . '[/ux_container]',
	'ux_stack'                      => '[ux_stack] [ux_stack_inner]' . $sc_box( 'Stack item 1' ) . '[/ux_stack_inner] [ux_stack_inner]' . $sc_box( 'Stack item 2' ) . '[/ux_stack_inner] [/ux_stack]',
	'gap'                           => $sc_box( 'Before' ) . '[gap height="30px"]' . $sc_box( 'After' ),
	'divider'                       => '<p>Above</p>[divider]<p>Below</p>',
	'background'                    => '[background bg="" bg_color="rgb(236, 240, 245)" height="120px"]',
	'scroll_to'                     => '[scroll_to title="Scroll target" bullet="false"]',
);

/* -------------------------------------------------------------------- Text */
$groups['Text and titles'] = array(
	'title: style (simple / normal / center / bold / bold-center)' => '[title text="Simple title" style="simple"] [title text="Normal title" style="normal"] [title text="Center title" style="center"] [title text="Left bold title" style="bold"] [title text="Center bold title" style="bold-center"]',
	'title: link, icon, color, size' => '[title text="With a link" style="normal" link_text="View all" link="#"] [title text="With an icon" style="center" icon="icon-star"] [title text="Colored, 80%" style="bold" color="rgb(210, 110, 75)" size="80"]',
	'divider: align, width, height, color' => '[divider] [divider align="center" width="80px"] [divider align="right" width="120px" height="2px" color="rgb(210, 110, 75)"] [divider width="full" height="1px"]',
	'ux_text'          => '[ux_text]<p>' . $sc_lorem . '</p>[/ux_text]',
	'text_box (inside a banner; the text is light by default)' => '[ux_banner height="220px" bg_color="rgb(68, 96, 132)"] [text_box width="60"]<h3>Text box</h3><p>' . $sc_lorem . '</p>[/text_box] [/ux_banner]',
	'text_box (dark text on a light banner)' => '[ux_banner height="220px" bg_color="rgb(236, 240, 245)"] [text_box width="60" text_color="dark"]<h3>Text box</h3><p>' . $sc_lorem . '</p>[/text_box] [/ux_banner]',
	'message_box: default' => '[message_box] [row_inner v_align="middle" h_align="center"] [col_inner span__lg="9" span="12"]<p>' . $sc_lorem . '</p>[/col_inner] [col_inner span__lg="3" span="12" align="center"] [button text="Click me" color="white" style="outline"] [/col_inner] [/row_inner] [/message_box]',
	'message_box: bg color (primary / secondary / success / alert)' => '[message_box bg_color="rgb(68, 96, 132)"]<p>Primary background. ' . $sc_lorem . '</p>[/message_box] [gap height="12px"] [message_box bg_color="rgb(210, 110, 75)"]<p>Secondary background. ' . $sc_lorem . '</p>[/message_box] [gap height="12px"] [message_box bg_color="rgb(98, 125, 71)"]<p>Success background. ' . $sc_lorem . '</p>[/message_box] [gap height="12px"] [message_box bg_color="rgb(178, 0, 0)"]<p>Alert background. ' . $sc_lorem . '</p>[/message_box]',
	'message_box: dark text on a light background, padding 30' => '[message_box text_color="light" bg_color="rgb(236, 240, 245)" padding="30"]<p>' . $sc_lorem . '</p>[/message_box]',
	'ux_html'          => '[ux_html]<p>Custom <strong>HTML</strong> block</p>[/ux_html]',
	'bullet_item'      => '[bullet_item text="Bullet item one"] [bullet_item text="Bullet item two"] [bullet_item text="Disabled item" enabled="false"] [bullet_item text="With tooltip" tooltip="Tooltip text"]',
	'ux_current_year'  => '&copy; [ux_current_year]',
	'site_url'         => '[site_url] [site_url_secure]',
	'ux_shortcode'     => '[ux_shortcode]',
);

/* ------------------------------------------------------------- Interactive */
$sc_tabs       = '[tab title="Overview"]<p>Overview. ' . $sc_lorem . '</p>[/tab] [tab title="Details"]<p>Details. ' . $sc_lorem . '</p>[/tab] [tab title="Reviews"]<p>Reviews. ' . $sc_lorem . '</p>[/tab]';
$sc_tab_styles = array( 'simple', 'line', 'divided', 'line-grow', 'line-bottom', 'outline', 'tabs', 'bold', 'pills' );

$groups['Tabs'] = array();
foreach ( $sc_tab_styles as $sc_style ) {
	$groups['Tabs'][ 'tabgroup: style ' . $sc_style ] = '[tabgroup style="' . $sc_style . '"] ' . $sc_tabs . ' [/tabgroup]';
}
$groups['Tabs'] += array(
	'tabgroup: align center / right'    => '[tabgroup align="center"] ' . $sc_tabs . ' [/tabgroup] [tabgroup style="pills" align="right"] ' . $sc_tabs . ' [/tabgroup]',
	'tabgroup: nav style normal, sizes' => '[tabgroup nav_style="normal" nav_size="small"] ' . $sc_tabs . ' [/tabgroup] [tabgroup nav_style="normal" nav_size="large"] ' . $sc_tabs . ' [/tabgroup] [tabgroup style="tabs" nav_style="normal" nav_size="xlarge"] ' . $sc_tabs . ' [/tabgroup]',
	'tabgroup: title'                   => '[tabgroup title="Tab group title" style="outline"] ' . $sc_tabs . ' [/tabgroup]',
);
foreach ( array( 'line', 'simple', 'outline', 'tabs', 'pills', 'bold' ) as $sc_style ) {
	$groups['Tabs'][ 'tabgroup_vertical: style ' . $sc_style ] = '[tabgroup_vertical style="' . $sc_style . '"] ' . $sc_tabs . ' [/tabgroup_vertical]';
}

$sc_price = static function ( $title, $price, $atts = '', $button = 'primary' ) {
	return '[col span="4" span__sm="12"] [ux_price_table title="' . $title . '" price="' . $price . '" description="Per month" ' . $atts . '][bullet_item text="Feature one"][bullet_item text="Feature two" tooltip="More about it"][bullet_item text="Feature three" enabled="false"][button text="Choose plan" color="' . $button . '"][/ux_price_table] [/col]';
};

$groups['Accordion, countdown, counter, price table'] = array(
	'accordion'                    => '[accordion] [accordion-item title="Question one"]<p>Answer one. ' . $sc_lorem . '</p>[/accordion-item] [accordion-item title="Question two"]<p>Answer two.</p>[/accordion-item] [accordion-item title="Question three"]<p>Answer three.</p>[/accordion-item] [/accordion]',
	'accordion: title, first open' => '[accordion title="Frequently asked" open="1"] [accordion-item title="Open item"]<p>Open by default. ' . $sc_lorem . '</p>[/accordion-item] [accordion-item title="Closed item"]<p>Closed.</p>[/accordion-item] [/accordion]',
	'ux_countdown: clock, dark (default)'      => '[ux_countdown year="2030" month="12" day="31"]',
	'ux_countdown: clock, light'               => '[ux_countdown year="2030" month="12" day="31" color="light"]',
	'ux_countdown: background color, size 200' => '[ux_countdown year="2030" month="12" day="31" bg_color="rgb(210, 110, 75)" size="200"]',
	'ux_countdown: text'                       => '[ux_countdown year="2030" month="12" day="31" style="text" size="150"]',
	'ux_countdown: on a dark banner'           => '[ux_banner height="260px" bg_color="rgb(34, 48, 68)"] [text_box width="70"]<h3>Sale ends in</h3>[ux_countdown year="2030" month="12" day="31" color="light" size="220"][/text_box] [/ux_banner]',
	'ux_counter: in a row'                     => '[row] [col span="3" span__sm="6"] [ux_counter value="1250" label="Customers"] [/col] [col span="3" span__sm="6"] [ux_counter value="98%" label="Satisfaction"] [/col] [col span="3" span__sm="6"] [ux_counter value="24/7" label="Support"] [/col] [col span="3" span__sm="6"] [ux_counter value="15+" label="Years"] [/col] [/row]',
	'ux_counter: align, colors, sizes'         => '[row] [col span="4" span__sm="12"] [ux_counter value="320" label="Left" align="left"] [/col] [col span="4" span__sm="12"] [ux_counter value="4.9" label="Value color" value_color="rgb(210, 110, 75)" value_size="64px"] [/col] [col span="4" span__sm="12"] [ux_counter value="12k" label="Right" align="right"] [/col] [/row]',
	'ux_price_table: default / featured / depth'   => '[row] ' . $sc_price( 'Basic', '$19' ) . $sc_price( 'Pro', '$49', 'featured="true"' ) . $sc_price( 'Team', '$99', 'depth="2" depth_hover="4"', 'secondary' ) . ' [/row]',
	'ux_price_table: radius, bg color, light text' => '[row] ' . $sc_price( 'Radius 0', '$19', 'radius="0"' ) . $sc_price( 'Radius 30', '$49', 'radius="30"' ) . $sc_price( 'Dark', '$99', 'color="dark" bg_color="rgb(34, 48, 68)"', 'white' ) . ' [/row]',
);

/* ------------------------------------------------------------------- Media */
$groups['Banner, image, slider, gallery, video'] = array(
	'ux_banner'         => '[ux_banner height="300px" bg_color="rgb(68, 96, 132)" bg_overlay="rgba(0, 0, 0, 0.2)"] [text_box width="60" text_color="light"]<h3>Banner title</h3><p>Banner text</p>[button text="Read more" color="white" style="outline"][/text_box] [/ux_banner]',
	'ux_banner_grid'    => '[ux_banner_grid height__lg="420px"] [col_grid span__lg="6"] [ux_banner bg_color="rgb(68, 96, 132)"] [text_box]<h3>Grid 1</h3>[/text_box] [/ux_banner] [/col_grid] [col_grid span__lg="3" height="1-2"] [ux_banner bg_color="rgb(192, 85, 48)"] [text_box]<h3>Grid 2</h3>[/text_box] [/ux_banner] [/col_grid] [col_grid span__lg="3" height="1-2"] [ux_banner bg_color="rgb(98, 125, 71)"] [text_box]<h3>Grid 3</h3>[/text_box] [/ux_banner] [/col_grid] [col_grid span__lg="6" height="1-2"] [ux_banner bg_color="rgb(178, 0, 0)"] [text_box]<h3>Grid 4</h3>[/text_box] [/ux_banner] [/col_grid] [/ux_banner_grid]',
	'ux_image'          => '[ux_image id="' . $sc_img . '" width="50"]',
	'ux_image_box: style normal / shade / overlay' => '[row] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '"]<h4>Normal</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="shade"]<h4>Shade</h4><p>Text over the image.</p>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="overlay"]<h4>Overlay</h4><p>Text over the image.</p>[/ux_image_box] [/col] [/row]',
	'ux_image_box: style label / badge / push' => '[row] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="label"]<h4>Label</h4>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="badge"]<h4>Badge</h4>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="push"]<h4>Push</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [/row]',
	'ux_image_box: style bounce / vertical' => '[row] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="bounce"]<h4>Bounce</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [col span="8" span__sm="12"] [ux_image_box img="' . $sc_img . '" style="vertical" image_width="40"]<h4>Vertical</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [/row]',
	'ux_image_box: depth, radius, text bg, align' => '[row] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" depth="1" depth_hover="4" text_align="left"]<h4>Depth, left</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" image_radius="100" image_width="70"]<h4>Round image</h4><p>' . $sc_lorem . '</p>[/ux_image_box] [/col] [col span="4" span__sm="12"] [ux_image_box img="' . $sc_img . '" text_bg="rgb(68, 96, 132)" text_color="dark" text_padding="20px 20px 20px 20px"]<h4>Text background</h4><p>Light text.</p>[/ux_image_box] [/col] [/row]',
	'ux_slider'         => '[ux_slider] [ux_banner height="240px" bg_color="rgb(68, 96, 132)"] [text_box]<h3>Slide 1</h3>[/text_box] [/ux_banner] [ux_banner height="240px" bg_color="rgb(192, 85, 48)"] [text_box]<h3>Slide 2</h3>[/text_box] [/ux_banner] [/ux_slider]',
	'ux_gallery'        => '[ux_gallery ids="' . $sc_ids . '"]',
	'gallery'           => '[gallery ids="' . $sc_ids . '"]',
	'ux_video'          => '[ux_video url="https://www.youtube.com/watch?v=f3Hh_qSkpaA"]',
	'ux_lottie'         => '[ux_lottie]',
	'ux_hotspot'        => '[ux_hotspot]',
	'lightbox'          => '[lightbox id="sc-demo-lightbox" width="600px"]<p>Lightbox content</p>[/lightbox] <a href="#sc-demo-lightbox">Open lightbox</a>',
	'map'               => '[map height="260px"]Map content[/map]',
	'logo'              => '[logo]',
);

/* ------------------------------------------------------- Banner grid layouts */
// Every preset layout of the banner grid (silver_get_grid), to check the CSS
// grid placement. Presets with one cell repeat it.
$groups['Banner grid: layouts'] = array();
if ( function_exists( 'silver_get_grid' ) ) {
	$sc_grid_colors = array( 'rgb(68, 96, 132)', 'rgb(210, 110, 75)', 'rgb(98, 125, 71)', 'rgb(34, 48, 68)', 'rgb(150, 110, 160)', 'rgb(178, 60, 60)' );
	for ( $sc_g = 1; $sc_g <= 14; $sc_g++ ) {
		$sc_cells = silver_get_grid( (string) $sc_g );
		if ( ! $sc_cells ) continue;
		if ( 1 === count( $sc_cells ) ) $sc_cells = array_fill( 0, 4, $sc_cells[0] );
		$sc_code = '[ux_banner_grid height__lg="420px"]';
		foreach ( $sc_cells as $sc_i => $sc_cell ) {
			$sc_code .= ' [col_grid span__lg="' . $sc_cell['span'] . '" span__md="' . $sc_cell['md'] . '" height="' . $sc_cell['height'] . '"] [ux_banner bg_color="' . $sc_grid_colors[ $sc_i % count( $sc_grid_colors ) ] . '"] [text_box]<h4>' . ( $sc_i + 1 ) . ': ' . $sc_cell['span'] . ' x ' . $sc_cell['height'] . '</h4>[/text_box] [/ux_banner] [/col_grid]';
		}
		$groups['Banner grid: layouts'][ 'ux_banner_grid: layout ' . $sc_g ] = $sc_code . ' [/ux_banner_grid]';
	}
}

/* ----------------------------------------------- Grid type of content elements */
// Grid type of the content elements: the same CSS grid as the banner grid
// (.row-grid in silver.css). Products need WooCommerce.
$groups['Grid type (posts, gallery, products, terms)'] = array(
	'blog_posts: type grid, layout 1'  => '[blog_posts type="grid" grid="1" grid_height="480px" posts="4" style="overlay" text_align="center"]',
	'blog_posts: type grid, layout 10' => '[blog_posts type="grid" grid="10" grid_height="480px" posts="5" style="shade" col_spacing="collapse"]',
	'ux_gallery: type grid, layout 3'  => '[ux_gallery ids="' . $sc_ids . '" type="grid" grid="3" grid_height="420px"]',
	'ux_gallery: type grid, layout 13, spacing large' => '[ux_gallery ids="' . $sc_ids . ',' . $sc_ids . '" type="grid" grid="13" grid_height="420px" col_spacing="large"]',
	'ux_terms: type grid, layout 6'    => '[ux_terms type="grid" grid="6" grid_height="420px"]',
);
if ( $is_shop ) {
	$groups['Grid type (posts, gallery, products, terms)'] += array(
		'ux_products: type grid, layout 1'   => '[ux_products type="grid" grid="1" grid_height="520px" products="4" style="overlay"]',
		'ux_products: type grid, layout 11'  => '[ux_products type="grid" grid="11" grid_height="520px" products="5" style="shade" col_spacing="xsmall"]',
		'ux_product_categories: type grid, layout 2' => '[ux_product_categories type="grid" grid="2" grid_height="520px" number="4"]',
	);
}

/* ------------------------------------------------------ Team, social, forms */
$sc_person = static function ( $name, $atts = '' ) use ( $sc_img ) {
	return '[col span="3" span__sm="6"] [team_member img="' . $sc_img . '" name="' . $name . '" title="Designer" facebook="#" instagram="#" email="#" ' . $atts . ']<p>Short text about this person.</p>[/team_member] [/col]';
};
$sc_feature = static function ( $title, $atts ) use ( $sc_lorem ) {
	return '[featured_box title="' . $title . '" icon_type="icon" ' . $atts . ']<p>' . $sc_lorem . '</p>[/featured_box]';
};
$sc_quote = static function ( $atts ) use ( $sc_lorem ) {
	return '[col span="4" span__sm="12"] [testimonial name="John Smith" company="Company" ' . $atts . ']<p>' . $sc_lorem . '</p>[/testimonial] [/col]';
};
$sc_follow = 'facebook="#" instagram="#" x="#" youtube="#" email="#"';

$groups['People, social, search'] = array(
	'team_member: social icons outline / fill / small, round image' => '[row] ' . $sc_person( 'Outline' ) . $sc_person( 'Fill', 'icon_style="fill"' ) . $sc_person( 'Small', 'icon_style="small"' ) . $sc_person( 'Round image', 'image_radius="100" image_width="80"' ) . ' [/row]',
	'team_member: style overlay / shade / bounce, depth' => '[row] ' . $sc_person( 'Overlay', 'style="overlay"' ) . $sc_person( 'Shade', 'style="shade"' ) . $sc_person( 'Bounce', 'style="bounce"' ) . $sc_person( 'Depth', 'depth="1" depth_hover="4"' ) . ' [/row]',
	'testimonial: initials / image / no avatar' => '[row] ' . $sc_quote( 'avatar="initials"' ) . $sc_quote( 'image="' . $sc_img . '" image_width="56"' ) . $sc_quote( 'stars="3"' ) . ' [/row]',
	'testimonial: align center / right, text size, no stars' => '[row] ' . $sc_quote( 'avatar="initials" text_align="center"' ) . $sc_quote( 'avatar="initials" initials="JS" initials_bg="rgb(210, 110, 75)" text_align="right"' ) . $sc_quote( 'avatar="initials" stars="0" font_size="large"' ) . ' [/row]',
	'testimonial: on a dark section' => '[section bg_color="rgb(34, 48, 68)" dark="true" padding="30px"] [row] ' . $sc_quote( 'avatar="initials"' ) . $sc_quote( 'avatar="initials" stars="4"' ) . $sc_quote( 'avatar="initials" stars="0"' ) . ' [/row] [/section]',
	'featured_box: icon position top / center' => '[row] [col span="6" span__sm="12"] ' . $sc_feature( 'Top', 'icon="icon-star" pos="top"' ) . ' [/col] [col span="6" span__sm="12"] ' . $sc_feature( 'Center', 'icon="icon-heart" pos="center"' ) . ' [/col] [/row]',
	'featured_box: icon position left / right' => '[row] [col span="6" span__sm="12"] ' . $sc_feature( 'Left', 'icon="icon-checkmark" pos="left"' ) . ' [/col] [col span="6" span__sm="12"] ' . $sc_feature( 'Right', 'icon="icon-clock" pos="right"' ) . ' [/col] [/row]',
	'featured_box: icon position left-center / right-center' => '[row] [col span="6" span__sm="12"] ' . $sc_feature( 'Left center', 'icon="icon-phone" pos="left-center"' ) . ' [/col] [col span="6" span__sm="12"] ' . $sc_feature( 'Right center', 'icon="icon-envelop" pos="right-center"' ) . ' [/col] [/row]',
	'featured_box: icon border, color, width' => '[row] [col span="4" span__sm="12"] ' . $sc_feature( 'Border 2', 'icon="icon-star" pos="center" icon_border="2"' ) . ' [/col] [col span="4" span__sm="12"] ' . $sc_feature( 'Border, colored', 'icon="icon-heart" pos="center" icon_border="2" icon_color="rgb(210, 110, 75)"' ) . ' [/col] [col span="4" span__sm="12"] ' . $sc_feature( 'Width 40, left', 'icon="icon-checkmark" pos="left" img_width="40" icon_border="2" icon_color="rgb(98, 125, 71)"' ) . ' [/col] [/row]',
	'featured_box: small title, text size, on a dark section' => '[section bg_color="rgb(34, 48, 68)" dark="true" padding="30px"] [row] [col span="4" span__sm="12"] ' . $sc_feature( 'Free shipping', 'icon="icon-star" pos="left" img_width="44" title_small="On every order"' ) . ' [/col] [col span="4" span__sm="12"] ' . $sc_feature( 'Support', 'icon="icon-chat" pos="left" img_width="44" icon_border="2"' ) . ' [/col] [col span="4" span__sm="12"] ' . $sc_feature( 'Large text', 'icon="icon-heart" pos="center" font_size="large"' ) . ' [/col] [/row] [/section]',
	'share: style outline / fill / small' => '[share title="Outline"] [gap height="12px"] [share title="Fill" style="fill"] [gap height="12px"] [share title="Small" style="small"]',
	'share: align center / right, scale'  => '[share align="center"] [share align="right" style="fill"] [share scale="150"]',
	'follow: style outline / fill / small' => '[follow title="Outline" ' . $sc_follow . '] [gap height="12px"] [follow title="Fill" style="fill" ' . $sc_follow . '] [gap height="12px"] [follow title="Small" style="small" ' . $sc_follow . ']',
	'follow: on a dark section' => '[section bg_color="rgb(34, 48, 68)" dark="true" padding="24px"] [follow ' . $sc_follow . '] [gap height="12px"] [follow style="fill" ' . $sc_follow . '] [gap height="12px"] [follow style="small" ' . $sc_follow . '] [/section]',
	'search: normal / flat' => '[search] [gap height="12px"] [search style="flat"]',
	'search: sizes'         => '[search size="small"] [gap height="12px"] [search size="large"] [gap height="12px"] [search size="xlarge" style="flat"]',
);

/* ------------------------------------------------------- Menus and navigation */
$groups['Menus and navigation'] = array(
	'ux_menu'            => '[ux_menu] [ux_menu_title text="Menu title"] [ux_menu_link text="Link one" link="#"] [ux_menu_link text="Link two" link="#"] [/ux_menu]',
	'ux_menu: divider, icons, labels' => '[ux_menu divider="solid"] [ux_menu_title text="Account"] [ux_menu_link text="Profile" icon="icon-user" link="#"] [ux_menu_link text="Wishlist" icon="icon-heart" label="label-new" link="#"] [ux_menu_link text="Messages" icon="icon-envelop" label="label-hot" link="#"] [/ux_menu]',
	'ux_nav'             => '[ux_nav]',
	'ux_mega_menu'       => '[ux_mega_menu]',
	'ux_subnav'          => '[ux_subnav]',
	'ux_breadcrumbs'     => '[ux_breadcrumbs]',
	'ux_pages'           => '[ux_pages]',
	'ux_sidebar'         => '[ux_sidebar]',
	'ux_quick_cart'      => '[ux_quick_cart]',
	'ux_payment_icons'   => '[ux_payment_icons]',
	'ux_instagram_feed'  => '[ux_instagram_feed]',
);

/* ---------------------------------------------------------- Posts and blog */
$groups['Posts, portfolio, terms'] = array(
	'blog_posts'      => '[blog_posts posts="3"]',
	'ux_posts'        => '[ux_posts]',
	'ux_portfolio'    => '[ux_portfolio]',
	'featured_items_grid'   => '[featured_items_grid]',
	'featured_items_slider' => '[featured_items_slider]',
	'ux_terms'        => '[ux_terms]',
	'ux_excerpt'      => '[ux_excerpt]',
	'block'           => '[block id=""]',
	'ux_gutenberg'    => '[ux_gutenberg]',
);

$groups['Post fields (use the current page)'] = array(
	'post_title'     => '[post_title]',
	'post_excerpt'   => '[post_excerpt]',
	'post_content'   => '[post_content]',
	'post_thumbnail' => '[post_thumbnail]',
	'post_author'    => '[post_author]',
	'post_created'   => '[post_created]',
	'post_meta'      => '[post_meta]',
	'post_share'     => '[post_share]',
	'page_header'    => '[page_header]',
);

/* ------------------------------------------------------------- WooCommerce */
if ( $is_shop ) {
	$groups['WooCommerce: products'] = array(
		'products'                  => '[products limit="4" columns="4"]',
		'ux_products'               => '[ux_products]',
		'ux_products_list'          => '[ux_products_list]',
		'ux_latest_products'        => '[ux_latest_products]',
		'ux_featured_products'      => '[ux_featured_products]',
		'ux_bestseller_products'    => '[ux_bestseller_products]',
		'ux_sale_products'          => '[ux_sale_products]',
		'ux_custom_products'        => '[ux_custom_products]',
		'recent_products'           => '[recent_products]',
		'featured_products'         => '[featured_products]',
		'best_selling_products'     => '[best_selling_products]',
		'sale_products'             => '[sale_products]',
		'top_rated_products'        => '[top_rated_products]',
		'products_pinterest_style'  => '[products_pinterest_style]',
		'product_lookbook'          => '[product_lookbook]',
		'ux_product_flip'           => '[ux_product_flip]',
		'ux_product_categories'     => '[ux_product_categories]',
		'ux_product_categories_grid' => '[ux_product_categories_grid]',
	);
	$groups['WooCommerce: product page (render on a product page only)'] = array(
		'ux_product_title'       => '[ux_product_title]',
		'ux_product_price'       => '[ux_product_price]',
		'ux_product_rating'      => '[ux_product_rating]',
		'ux_product_excerpt'     => '[ux_product_excerpt]',
		'ux_product_description' => '[ux_product_description]',
		'ux_product_add_to_cart' => '[ux_product_add_to_cart]',
		'ux_product_gallery'     => '[ux_product_gallery]',
		'ux_product_meta'        => '[ux_product_meta]',
		'ux_product_tabs'        => '[ux_product_tabs]',
		'ux_product_related'     => '[ux_product_related]',
		'ux_product_upsell'      => '[ux_product_upsell]',
		'ux_product_breadcrumbs' => '[ux_product_breadcrumbs]',
		'ux_product_next_prev_nav' => '[ux_product_next_prev_nav]',
		'ux_product_hook'        => '[ux_product_hook]',
	);
}

// Tags used by the examples above.
$used = array();
foreach ( $groups as $items ) {
	foreach ( $items as $code ) {
		if ( preg_match_all( '/\[([a-z0-9_]+)/', $code, $m ) ) {
			$used = array_merge( $used, $m[1] );
		}
	}
}
$used = array_unique( $used );

global $shortcode_tags;
$not_demoed = array_diff( array_keys( (array) $shortcode_tags ), $used );
sort( $not_demoed );

get_header();
do_action( 'silver_before_page' );
?>

<div id="content" role="main" class="content-area sc-demo">
	<style>
		.sc-demo { background: #f6f7f9; padding: 30px 16px 60px; }
		.sc-demo > h1, .sc-demo > p, .sc-demo-nav, .sc-demo-section { max-width: 1100px; margin-left: auto; margin-right: auto; }
		.sc-demo-nav { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 36px; padding: 0; list-style: none; }
		.sc-demo-nav li { margin: 0; }
		.sc-demo-nav a { display: inline-block; padding: 6px 14px; border: 1px solid #e2e5ea; border-radius: 99px; background: #fff; font-size: 13px; transition: border-color .2s, box-shadow .2s; }
		.sc-demo-nav a:hover { border-color: #98a2b3; box-shadow: 0 1px 3px rgba(16, 24, 40, .1); }
		.sc-demo-section { margin-bottom: 56px; scroll-margin-top: 20px; }
		.sc-demo-section > h2 { margin-bottom: 20px; font-size: 1.5em; }
		.sc-demo-block { margin-bottom: 20px; padding: 18px 20px 12px; border: 1px solid #e8eaee; border-radius: 12px; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
		.sc-demo-title { margin: 0 0 14px; color: #667085; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
		.sc-demo-preview { padding: 16px 0 14px; overflow: hidden; }
		.sc-demo-empty, .sc-demo-error { color: #98a2b3; font-size: 13px; }
		.sc-demo-error { color: #b20000; }
		.sc-demo-code-wrap { margin-top: 10px; border-top: 1px solid #eef0f3; padding-top: 8px; }
		.sc-demo-code-wrap summary { color: #667085; cursor: pointer; font-size: 12px; font-weight: 600; }
		.sc-demo-code { margin: 8px 0 4px; padding: 10px 12px; background: #f6f7f9; border-radius: 8px; font-size: 12px; white-space: pre-wrap; word-break: break-all; }
		.sc-demo-list { columns: 3 200px; font-size: 13px; }
	</style>

	<h1>Shortcode Demo</h1>
	<p>Every theme shortcode, rendered with the code used. Registered tags: <?php echo (int) count( (array) $shortcode_tags ); ?>.</p>

	<ul class="sc-demo-nav">
		<?php foreach ( array_keys( $groups ) as $i => $name ) : ?>
			<li><a href="#sc-demo-<?php echo (int) $i; ?>"><?php echo esc_html( $name ); ?></a></li>
		<?php endforeach; ?>
		<li><a href="#sc-demo-rest">Other tags</a></li>
	</ul>

	<?php
	$i = 0;
	foreach ( $groups as $name => $items ) :
		?>
		<section class="sc-demo-section" id="sc-demo-<?php echo (int) $i++; ?>">
			<h2><?php echo esc_html( $name ); ?></h2>
			<?php
			foreach ( $items as $title => $code ) {
				silver_shortcode_demo_block( $title, $code );
			}
			?>
		</section>
	<?php endforeach; ?>

	<section class="sc-demo-section" id="sc-demo-rest">
		<h2>Other registered tags (no example)</h2>
		<p>Child tags (row_inner, col_inner, tab, accordion_item...), cart / checkout pages and aliases. Listed, not rendered.</p>
		<ul class="sc-demo-list">
			<?php foreach ( $not_demoed as $tag ) : ?>
				<li><code>[<?php echo esc_html( $tag ); ?>]</code></li>
			<?php endforeach; ?>
		</ul>
	</section>
</div>

<?php do_action( 'silver_after_page' ); ?>

<?php get_footer(); ?>
