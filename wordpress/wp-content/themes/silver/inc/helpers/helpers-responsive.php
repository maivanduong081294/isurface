<?php
/**
 * Responsive helpers for UX Builder shortcodes.
 *
 * Builder options marked `'responsive' => true` are saved mobile-first: the
 * unsuffixed attribute is the mobile value, "__md", "__ml" and "__lg" override
 * larger screens. These helpers turn such values into a scoped <style> tag
 * (one media query per breakpoint, same tiers as ux_builder_element_style_tag()).
 *
 * Shortcodes keep their original class/inline output for the base value
 * (backward compatibility, builder templates); the generated CSS uses
 * !important so it also overrides those classes on larger screens.
 */

/**
 * Responsive values of an option, indexed by breakpoint (0 = mobile).
 * Empty values are removed; the lowest configured tier becomes the base.
 *
 * UX Builder does not save a value equal to the option default, so a missing
 * mobile value means the default when the option has one ($default). Only
 * options without a default inherit the value of the next larger screen.
 *
 * @param array       $atts    Parsed shortcode attributes.
 * @param string      $name    Option name.
 * @param string|null $default Builder default of the option.
 * @return array
 */
function silver_responsive_values($atts, $name, $default = null)
{
	$atts = (array) $atts;
	if (! array_key_exists($name, $atts) || $atts[$name] === null || $atts[$name] === '') {
		$atts[$name] = ($default !== null && $default !== '') ? $default : null;
	}

	return ux_builder_process_breakpoint_values(ux_builder_get_responsive_values($name, $atts));
}

/**
 * Whether an option has a value for a larger screen (__md, __ml or __lg).
 *
 * @param array  $atts
 * @param string $name
 * @return bool
 */
function silver_responsive_has_overrides($atts, $name)
{
	foreach (array('md', 'ml', 'lg') as $suffix) {
		$key = $name . '__' . $suffix;
		if (isset($atts[$key]) && $atts[$key] !== '') return true;
	}
	return false;
}

/**
 * Build a scoped <style> tag for responsive options.
 *
 * Each rule is keyed by option name and accepts:
 * - selector:  CSS selector relative to "#$id" ('' = the element itself,
 *              comma separated for several selectors).
 * - property:  CSS property (comma separated for several) that receives the value.
 * - unit:      Unit appended to numeric values (e.g. "%", "px").
 * - map:       value => declarations ("prop: value; prop: value") for options
 *              whose values are keywords. Values missing from the map are ignored.
 * - callback:  callable( $value ) returning declarations.
 * - important: Add !important (default true).
 * - only_overrides: Skip the rule when the option has no __md/__ml/__lg value
 *              (the base value is already rendered by the shortcode itself).
 * - option:    Option name when the rule key is not the option name (lets
 *              several rules read the same option).
 * - values:    Precomputed values indexed by breakpoint (0 = mobile) used
 *              instead of reading the option from $atts.
 * - default:   Builder default of the option (used when the mobile value is
 *              missing, see silver_responsive_values()).
 *
 * A breakpoint that would print the same declarations as the previous one
 * is skipped (CSS inherits upwards).
 *
 * @param string $id    Element ID (without "#").
 * @param array  $atts  Parsed shortcode attributes.
 * @param array  $rules Rules, see above.
 * @return string
 */
function silver_responsive_style_tag($id, $atts, $rules)
{
	if (! $id || empty($atts)) return '';

	$breakpoints = array_values(get_ux_builder_breakpoints());
	$styles      = array_fill(0, count($breakpoints), array());

	foreach ($rules as $key => $rule) {
		$name = isset($rule['option']) ? $rule['option'] : $key;
		if (! isset($rule['values']) && ! empty($rule['only_overrides']) && ! silver_responsive_has_overrides($atts, $name)) continue;

		$important = ! isset($rule['important']) || $rule['important'];
		$selectors = array_map('trim', explode(',', isset($rule['selector']) ? $rule['selector'] : ''));
		$values    = isset($rule['values'])
			? (array) $rule['values']
			: silver_responsive_values($atts, $name, isset($rule['default']) ? $rule['default'] : null);
		$previous  = null;

		ksort($values);
		foreach ($values as $index => $value) {
			if ($value === null || $value === '') continue;
			if (! isset($styles[$index])) continue;
			$value = trim((string) $value);

			// Same value as the previous breakpoint: inherited, nothing to print.
			if ($previous === $value) continue;
			$previous = $value;

			$declarations = array();
			if (isset($rule['map'])) {
				if (! array_key_exists($value, $rule['map'])) continue;
				$declarations = silver_responsive_split_declarations($rule['map'][$value]);
			} elseif (isset($rule['callback'])) {
				$declarations = silver_responsive_split_declarations(call_user_func($rule['callback'], $value));
			} elseif (! empty($rule['property'])) {
				if (isset($rule['unit']) && is_numeric($value)) $value = floatval($value) . $rule['unit'];
				foreach (array_map('trim', explode(',', $rule['property'])) as $property) {
					if ($property !== '') $declarations[] = $property . ': ' . $value;
				}
			}

			foreach ($declarations as $declaration) {
				// Values come from shortcode attributes: never allow breaking out of the rule.
				$declaration = str_replace(array('{', '}', '<', '>'), '', $declaration);
				if ($important && false === stripos($declaration, '!important')) $declaration .= ' !important';

				foreach ($selectors as $selector) {
					// "&.class" targets the element itself, like in Sass.
					$full = strpos($selector, '&') === 0
						? '#' . $id . substr($selector, 1)
						: trim('#' . $id . ' ' . $selector);
					$styles[$index][$full][] = $declaration . ';';
				}
			}
		}
	}

	$output = '';
	foreach ($styles as $index => $rules_by_selector) {
		if (empty($rules_by_selector)) continue;

		$css = '';
		foreach ($rules_by_selector as $selector => $declarations) {
			$css .= $selector . '{' . implode('', array_unique($declarations)) . '}';
		}

		$output .= $index > 0
			? '@media (min-width:' . (int) $breakpoints[$index - 1]['width'] . 'px){' . $css . '}'
			: $css;
	}

	return $output !== '' ? '<style>' . $output . '</style>' : '';
}

/**
 * Value of an option for every breakpoint, inheriting from smaller screens
 * (mobile-first), as the browser would see it.
 *
 * @param array  $atts
 * @param string $name
 * @param mixed  $default Value used when nothing is set.
 * @return array Index => value for every breakpoint.
 */
function silver_responsive_filled($atts, $name, $default = '')
{
	$count  = count(get_ux_builder_breakpoints());
	$values = silver_responsive_values($atts, $name, $default);
	$filled = array();
	$last   = $default;

	for ($i = 0; $i < $count; $i++) {
		if (isset($values[$i])) $last = $values[$i];
		$filled[$i] = $last;
	}

	return $filled;
}

/**
 * Whether any of the given options has a value for a larger screen.
 *
 * @param array $atts
 * @param array $names
 * @return bool
 */
function silver_responsive_any_overrides($atts, $names)
{
	foreach ($names as $name) {
		if (silver_responsive_has_overrides($atts, $name)) return true;
	}
	return false;
}

/**
 * Max width of the theme container / rows (see function-custom-css.php).
 *
 * @param string $row_style Row style: '', 'collapse', 'small' or 'large'.
 * @return string
 */
function silver_responsive_container_width($row_style = '')
{
	$site_width = (int) get_theme_mod('site_width');
	$offsets    = array('' => 30, 'collapse' => 60, 'small' => 37.5, 'large' => 0);
	$offset     = isset($offsets[$row_style]) ? $offsets[$row_style] : 30;

	if (! $site_width) {
		// Defaults from assets/css/silver.css.
		$defaults = array('' => 1080, 'collapse' => 1050, 'small' => 1065, 'large' => 1110);
		return (isset($defaults[$row_style]) ? $defaults[$row_style] : 1080) . 'px';
	}

	return ($site_width - $offset) . 'px';
}

/**
 * Background <img> for a background image option (bg + bg_size).
 *
 * @param mixed  $bg    Attachment ID or URL.
 * @param string $size  Image size.
 * @param array  $attrs Extra attributes.
 * @return string
 */
function silver_responsive_background_image($bg, $size, $attrs = array())
{
	if (empty($bg)) return '';

	$extra_class = isset($attrs['class']) ? ' ' . $attrs['class'] : '';
	$image_id    = is_numeric($bg) ? (int) $bg : attachment_url_to_postid($bg);

	if ($image_id) {
		$attrs['class'] = "bg attachment-{$size} size-{$size}" . $extra_class;
		return silver_get_attachment_image_no_srcset($image_id, $size, false, $attrs);
	}

	// External URL (same markup as before).
	$attrs['class'] = 'bg' . $extra_class;
	$html_attrs     = '';
	foreach ($attrs as $name => $value) {
		$html_attrs .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
	}
	return '<img src="' . esc_url($bg) . '" alt=""' . $html_attrs . ' />';
}

/**
 * Background image of every breakpoint: the inherited "bg" value, or '' where
 * "Image visibility" (bg_visible) is "hide".
 *
 * @param array $atts
 * @return array Index => image ID/URL.
 */
function silver_responsive_background_values($atts)
{
	$images  = silver_responsive_filled($atts, 'bg', '');
	$visible = silver_responsive_filled($atts, 'bg_visible', 'show');

	foreach ($images as $index => $image) {
		if ($visible[$index] === 'hide') $images[$index] = '';
	}

	return $images;
}

/**
 * Whether the background image changes between breakpoints (another image,
 * size, or hidden on some screens).
 *
 * @param array $atts
 * @return bool
 */
function silver_responsive_background_is_responsive($atts)
{
	$atts = (array) $atts;
	return silver_responsive_any_overrides($atts, array('bg', 'bg_size', 'bg_visible'))
		|| (isset($atts['bg_visible']) && $atts['bg_visible'] === 'hide');
}

/**
 * Background images that change per breakpoint (bg / bg_size options).
 *
 * One <img> is printed for every run of breakpoints sharing the same image
 * and size; CSS shows the right one. Hidden images are lazy loaded so the
 * browser does not download them.
 *
 * @param array $atts Parsed shortcode attributes.
 * @return string
 */
function silver_responsive_background_images($atts)
{
	$images = silver_responsive_background_values($atts);
	$sizes  = silver_responsive_filled($atts, 'bg_size', ! empty($atts['bg_size']) ? $atts['bg_size'] : 'large');

	// Group consecutive breakpoints with the same image and size.
	$groups = array();
	foreach ($images as $index => $image) {
		$key  = $image . '|' . $sizes[$index];
		$last = end($groups);
		if ($last && $last['key'] === $key) {
			$groups[key($groups)]['to'] = $index;
		} else {
			$groups[] = array('key' => $key, 'bg' => $image, 'size' => $sizes[$index], 'from' => $index, 'to' => $index);
		}
	}

	if (count($groups) === 1) {
		return silver_responsive_background_image($groups[0]['bg'], $groups[0]['size']);
	}

	$breakpoints = array_values(get_ux_builder_breakpoints());
	// Random (not wp_unique_id): AJAX renders (load more, builder preview)
	// restart counters and could reuse a class already on the page.
	$uid         = 'bg-rsp-' . wp_rand();
	$html        = '';
	$css         = '';

	foreach ($groups as $i => $group) {
		if (empty($group['bg'])) continue;

		$class = $uid . '-' . $i;
		$attrs = array('class' => $class);
		if ($group['from'] > 0) $attrs['loading'] = 'lazy';

		$html .= silver_responsive_background_image($group['bg'], $group['size'], $attrs);

		$media = array();
		if ($group['from'] > 0) $media[] = '(min-width:' . (int) $breakpoints[$group['from'] - 1]['width'] . 'px)';
		if ($group['to'] < count($breakpoints) - 1) $media[] = '(max-width:' . ((int) $breakpoints[$group['to']]['width'] - 0.02) . 'px)';

		$css .= $media
			? '.' . $class . '{display:none !important}@media ' . implode(' and ', $media) . '{.' . $class . '{display:block !important}}'
			: '';
	}

	return $html . ($css ? '<style>' . $css . '</style>' : '');
}

/**
 * Responsive rules of the "Box styles" options (commons/box-styles.php):
 * image height/width, text position/align/color/background.
 *
 * @param string $scope Selector (relative to the element ID) that contains the boxes.
 * @param bool   $self  The element with the ID is the .box itself (Image box, Team member).
 * @return array
 */
function silver_responsive_box_rules($scope = '', $self = false)
{
	$scope = trim($scope) !== '' ? trim($scope) . ' ' : '';
	$box   = $self ? '&' : $scope;

	return array_merge(
		array(
			'image_height' => array(
				'selector'       => $scope . '.box-image .image-cover',
				'property'       => 'padding-top',
				'only_overrides' => true,
			),
			'image_width'  => array(
				'selector'       => $scope . '.box-image',
				'property'       => 'width',
				'unit'           => '%',
				'only_overrides' => true,
				'default'        => '100',
			),
			'text_pos'     => array(
				'selector'       => $box . '.box-overlay .box-text, ' . $box . '.box-shade .box-text',
				'only_overrides' => true,
				'default'        => 'bottom',
				'map'            => array(
					'top'    => 'top: 0; bottom: auto; transform: none',
					'middle' => 'top: 50%; bottom: auto; transform: translateY(-50%)',
					'bottom' => 'top: auto; bottom: 0; transform: none',
				),
			),
			'text_pos|vertical' => array(
				'option'         => 'text_pos',
				'selector'       => $box . '.box-vertical .box-text',
				'only_overrides' => true,
				'default'        => 'bottom',
				'map'            => array(
					'top'    => 'vertical-align: top',
					'middle' => 'vertical-align: middle',
					'bottom' => 'vertical-align: bottom',
				),
			),
			'text_align'   => array(
				'selector'       => $scope . '.box-text',
				'only_overrides' => true,
				'map'            => silver_responsive_map('text_align'),
			),
			'text_bg'      => array(
				'selector'       => $scope . '.box-text',
				'property'       => 'background-color',
				'only_overrides' => true,
			),
		),
		// "dark" adds the `dark` class (light text), "light" is the default.
		silver_responsive_scheme_rules('text_color', $scope . '.box-text', 'dark', 'light', array('only_overrides' => true, 'default' => 'light'))
	);
}

/**
 * Responsive CSS of a repeater row (get_silver_repeater_start()):
 * Layout width, column alignment and Box styles, scoped to the row ID.
 *
 * @param string $id   Row ID.
 * @param array  $atts Parsed shortcode attributes.
 * @return string
 */
function silver_responsive_repeater_style($id, $atts)
{
	$spacing = isset($atts['col_spacing']) ? $atts['col_spacing'] : '';
	if (! in_array($spacing, array('collapse', 'small', 'large'), true)) $spacing = '';

	$rules = array(
		'width'        => array(
			'selector'       => '',
			'only_overrides' => true,
			'map'            => array(
				'container'  => 'max-width: ' . silver_responsive_container_width($spacing),
				'full-width' => 'max-width: 100%',
			),
		),
	);

	$rules['width']['default'] = 'container';

	return silver_responsive_style_tag($id, $atts, array_merge($rules, silver_responsive_row_align_rules(), silver_responsive_box_rules()));
}

/**
 * Column alignment of a row (v_align / h_align), same result as the
 * align-* classes. `display` is never changed so visibility classes
 * (hide-for-small...) keep working.
 *
 * @return array
 */
function silver_responsive_row_align_rules()
{
	return array(
		'v_align'       => array(
			'selector'       => '',
			'only_overrides' => true,
			'default'        => 'top',
			'map'            => silver_responsive_map('row_v_align'),
		),
		// "Equal": the column content fills the column height.
		'v_align|cols'  => array(
			'option'         => 'v_align',
			'selector'       => '> .col',
			'only_overrides' => true,
			'default'        => 'top',
			'map'            => array(
				'top'    => 'align-items: flex-start',
				'equal'  => 'align-items: stretch',
				'middle' => 'align-items: flex-start',
				'bottom' => 'align-items: flex-start',
			),
		),
		'v_align|inner' => array(
			'option'         => 'v_align',
			'selector'       => '> .col > .col-inner',
			'only_overrides' => true,
			'default'        => 'top',
			'map'            => array(
				'top'    => 'height: auto',
				'equal'  => 'height: 100%',
				'middle' => 'height: auto',
				'bottom' => 'height: auto',
			),
		),
		'h_align'       => array(
			'selector'       => '',
			'only_overrides' => true,
			'default'        => 'left',
			'map'            => silver_responsive_map('justify'),
		),
	);
}

/**
 * Options of commons/box-styles.php that can change per breakpoint.
 *
 * @return array
 */
function silver_responsive_box_options()
{
	return array('image_height', 'image_width', 'text_pos', 'text_align', 'text_bg', 'text_color');
}

/**
 * ID + <style> for a single box element (Image box, Team member) when one of
 * its Box styles options has a value for a larger screen.
 *
 * @param string $id   Element ID ('' to generate one when needed).
 * @param array  $atts Shortcode attributes.
 * @param string $prefix Prefix of a generated ID.
 * @return array { string $id, string $style }
 */
function silver_responsive_single_box($id, $atts, $prefix)
{
	if (! silver_responsive_any_overrides((array) $atts, silver_responsive_box_options())) {
		return array($id, '');
	}

	if (! $id) $id = $prefix . wp_rand();

	return array($id, silver_responsive_style_tag($id, (array) $atts, silver_responsive_box_rules('', true)));
}

/**
 * Base value of a responsive option: the mobile value, or the value of the
 * smallest screen that has one (UX Builder inherits from larger screens).
 *
 * @param array  $atts
 * @param string $name
 * @return string
 */
function silver_responsive_base($atts, $name)
{
	$values = silver_responsive_values($atts, $name);
	return isset($values[0]) ? (string) $values[0] : '';
}

/**
 * Split "a: 1; b: 2" into array( 'a: 1', 'b: 2' ).
 *
 * @param string|array $declarations
 * @return array
 */
function silver_responsive_split_declarations($declarations)
{
	if (is_array($declarations)) return array_filter(array_map('trim', $declarations));
	return array_filter(array_map('trim', explode(';', (string) $declarations)));
}

/**
 * Responsive value maps shared by several shortcodes.
 *
 * @param string $type
 * @return array
 */
function silver_responsive_map($type)
{
	switch ($type) {
		// Text alignment (text-left / text-center / text-right classes).
		case 'text_align':
			return array(
				'left'   => 'text-align: left',
				'center' => 'text-align: center',
				'right'  => 'text-align: right',
			);

		// Justify flex children (align-center / align-right on rows).
		case 'justify':
			return array(
				'left'   => 'justify-content: flex-start',
				'center' => 'justify-content: center',
				'right'  => 'justify-content: flex-end',
			);

		// Share / Follow icons: inline or a full width line (full-width + text-*).
		// Use with 'important' => false: `display` must not beat visibility
		// classes, the other declarations carry their own !important.
		case 'icons_align':
			return array(
				'inline' => 'display: inline-block; width: auto !important; text-align: inherit !important',
				'left'   => 'display: block; width: 100% !important; text-align: left !important',
				'center' => 'display: block; width: 100% !important; text-align: center !important',
				'right'  => 'display: block; width: 100% !important; text-align: right !important',
			);

		// Navigation alignment (nav-left / nav-center / nav-right).
		case 'nav_align':
			return array(
				'left'   => 'justify-content: flex-start',
				'center' => 'justify-content: center',
				'right'  => 'justify-content: flex-end',
			);

		// Vertical alignment of row columns (align-middle/bottom/equal);
		// "top" is the row without class (default stretch).
		case 'row_v_align':
			return array(
				'top'    => 'align-items: stretch',
				'equal'  => 'align-items: stretch',
				'middle' => 'align-items: center',
				'bottom' => 'align-items: flex-end',
			);

	}

	return array();
}

/**
 * Rules that emulate the `dark` class (light text) per breakpoint for options
 * that switch between a light and a dark text color scheme.
 *
 * @param string $option      Option name.
 * @param string $selector    Element (relative to the ID) that gets/loses the `dark` class.
 * @param string $light_value Option value that renders light text (adds `dark`).
 * @param string $dark_value  Option value that renders the default dark text.
 * @param array  $extra       Extra rule settings (e.g. only_overrides).
 * @return array
 */
function silver_responsive_scheme_rules($option, $selector, $light_value, $dark_value, $extra = array())
{
	$base     = trim($selector);
	$text     = array();
	$headings = array();

	foreach (array('', ' p', ' td') as $suffix) {
		$text[] = $base . $suffix;
	}
	foreach (array(' h1', ' h2', ' h3', ' h4', ' h5', ' h6', ' label', ' .heading-font', ' a.lead') as $suffix) {
		$headings[] = $base . $suffix;
	}

	$heading_color = get_theme_mod('type_headings_color') ?: '#555';

	return array(
		$option . '|text'     => array_merge(array(
			'option'   => $option,
			'selector' => implode(', ', $text),
			'map'      => array(
				$light_value => 'color: #f1f1f1',
				$dark_value  => 'color: var(--fs-color-base, #777)',
			),
		), $extra),
		$option . '|headings' => array_merge(array(
			'option'   => $option,
			'selector' => implode(', ', $headings),
			'map'      => array(
				$light_value => 'color: #fff',
				$dark_value  => 'color: ' . $heading_color,
			),
		), $extra),
	);
}
