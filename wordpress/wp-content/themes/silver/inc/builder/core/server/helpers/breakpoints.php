<?php

$_ux_builder_breakpoints = array();
$_ux_builder_breakpoints_default = null;

/**
 * Add a breakpoint.
 *
 * @param string $name
 * @param number $width
 * @param string $title
 * @param string $icon
 */
function add_ux_builder_breakpoint($name, $width, $title = '', $icon = '')
{
  $GLOBALS['_ux_builder_breakpoints'][$name] = compact('width', 'title', 'icon');
  uasort($GLOBALS['_ux_builder_breakpoints'], function ($a, $b) {
    return ($a['width'] > $b['width']) ? 1 : -1;
  });
}

/**
 * Remove a breakpoint by name.
 *
 * @param  string $name
 */
function remove_ux_builder_breakpoint($name)
{
  unset($GLOBALS['_ux_builder_breakpoints'][$name]);
}

/**
 * Get an array with all breakpoints.
 *
 * @return array
 */
function get_ux_builder_breakpoints()
{
  return $GLOBALS['_ux_builder_breakpoints'];
}

/**
 * Get breakpoints for the editor.
 *
 * @return array
 */
function get_ux_builder_breakpoints_array()
{
  return array(
    'current' => 0,
    'default' => get_default_ux_builder_breakpoint(),
    'all' => get_ux_builder_breakpoints(),
  );
}

/**
 * Set the default breakpoint by name.
 *
 * @param string $name
 */
function set_default_ux_builder_breakpoint($name)
{
  $breakpoints = get_ux_builder_breakpoints();
  $index = array_search($name, array_keys($breakpoints));
  $GLOBALS['_ux_builder_breakpoints_default'] = $index;
}

/**
 * Get the default breakpoint name.
 *
 * @return string
 */
function get_default_ux_builder_breakpoint()
{
  return $GLOBALS['_ux_builder_breakpoints_default'];
}

/**
 * Get the default breakpoint index.
 *
 * @return number
 */
function get_default_ux_builder_breakpoint_index()
{
  return get_default_ux_builder_breakpoint();
}

/**
 * Convert legacy responsive attributes to the mobile-first format.
 *
 * Content saved while desktop was the default breakpoint stored the mobile
 * value as "X__sm" and the desktop value unsuffixed. Mobile-first content never
 * writes "__sm" (the unsuffixed value is the mobile value), so the presence of
 * "X__sm" identifies legacy values: it becomes the base value and the old
 * unsuffixed value moves to "X__lg" unless "X__lg" is already set.
 *
 * @param  array $atts
 * @return array
 */
function ux_builder_migrate_legacy_responsive_atts($atts)
{
  foreach ((array) $atts as $name => $value) {
    if (! preg_match('/^(.*)__sm$/', (string) $name, $match)) continue;

    $base = $match[1];
    unset($atts[$name]);

    if ($value === null || $value === '') continue;

    $has_base = array_key_exists($base, $atts) && $atts[$base] !== null && $atts[$base] !== '';
    $has_lg   = array_key_exists($base . '__lg', $atts) && $atts[$base . '__lg'] !== null && $atts[$base . '__lg'] !== '';

    if ($has_base && ! $has_lg) {
      $atts[$base . '__lg'] = $atts[$base];
    }

    $atts[$base] = $value;
  }

  return $atts;
}

/**
 * Parse shortcode attributes with responsive variants for every breakpoint.
 * The unsuffixed value is the mobile value; "__md", "__ml" and "__lg" override
 * larger screens. Legacy "__sm" values are migrated transparently.
 */
function ux_builder_shortcode_atts($pairs, $atts, $shortcode = '')
{
  // Preserve a custom Builder ID even when a shortcode does not declare it
  // in its own defaults.
  if (isset($atts['_id']) && $atts['_id'] !== '' && ! array_key_exists('_id', $pairs)) {
    $pairs['_id'] = '';
  }

  // Every option that declares one responsive variant gets all of them.
  foreach ($pairs as $name => $default) {
    if (preg_match('/^(.*)__(?:sm|md|ml|lg)$/', $name, $match)) {
      foreach (array('md', 'ml', 'lg') as $suffix) {
        if (! array_key_exists($match[1] . '__' . $suffix, $pairs)) {
          $pairs[$match[1] . '__' . $suffix] = null;
        }
      }
    }
  }

  $atts = ux_builder_migrate_legacy_responsive_atts((array) $atts);

  // Accept responsive variants of any declared option.
  foreach ($atts as $name => $value) {
    if (preg_match('/^(.*)__(md|ml|lg)$/', $name, $match) && array_key_exists($match[1], $pairs) && ! array_key_exists($name, $pairs)) {
      $pairs[$name] = null;
    }
  }

  return shortcode_atts($pairs, $atts, $shortcode);
}

/**
 * Get breakpoint index be name.
 *
 * @param  string $name
 * @return number
 */
function get_ux_builder_breakpoint_index($name)
{
  return array_search($name, array_keys(get_ux_builder_breakpoints()));
}

function ux_builder_parse_value($value, $use_indexes = false)
{
  $default_index = get_default_ux_builder_breakpoint();
  $names = array_keys(get_ux_builder_breakpoints());
  $colon_names = array();
  $values = array();

  foreach ($names as $name) {
    $colon_names[] = "$name:";
    $values[$name] = null;
  }

  $splitted_value = explode('][', preg_replace('/\s(' . implode('|', $colon_names) . ')(\s\S*?)?/', '][$1=', $value));

  // Set the default value.
  $values['_default'] = array_shift($splitted_value);
  $values[$names[$default_index]] = $values['_default'];

  // Extract and set the the rest.
  foreach ($splitted_value as $media_value) {
    $splitted = explode(':=', $media_value);
    $values[$splitted[0]] = trim($splitted[1]);
  }

  // Convert breakpoint names to indexes.
  if ($use_indexes) {
    foreach ($values as $name => $value) {
      if ($name == '_default') continue;
      $index = array_search($name, $names);
      $values[$index] = $value;
      unset($values[$name]);
    }
  }

  return $values;
}

/**
 * Prepare responsive values for mobile-first CSS: explicit values stay at
 * their own tier, CSS inherits upwards, and the lowest configured tier is
 * used as the base so smaller screens never end up without a value.
 *
 * @param  array $values
 * @return array
 */
function ux_builder_process_breakpoint_values($values)
{
  $values = array_filter($values, function ($value) {
    return $value !== null && $value !== '';
  });

  if (empty($values)) {
    return $values;
  }

  // Mobile-first: the lowest configured tier becomes the base value (no media
  // query) so a value set only for desktop still applies to smaller screens.
  $lowest = min(array_keys($values));
  if ($lowest > 0) {
    $values[0] = $values[$lowest];
    unset($values[$lowest]);
    ksort($values);
  }

  return $values;
}

/**
 * Convert a responsive string into an array. The breakpoint names will be
 * the keys unless $string_keys is set. Empty values will inherit lower or
 * higher values if $fill_empty is set.
 *
 * @param  string  $value
 * @param  boolean $string_keys
 * @param  boolean $fill_empty
 * @return array
 */
function ux_builder_parse_responsive_string($value, $string_keys = true, $fill_empty = false)
{

  static $cache;

  // Create cache if not set ut yet.
  if (! is_array($cache)) $cache = array();

  // Return the cached results if value is already parsed.
  if (array_key_exists("$value-$string_keys-$fill_empty", $cache)) {
    return $cache["$value-$string_keys-$fill_empty"];
  }

  $breakpoints = get_ux_builder_breakpoints();
  $breakpoint_keys = array_keys($breakpoints);
  $breakpoint_names = array();
  $media_values = array();

  foreach ($breakpoints as $name => $data) {
    $breakpoint_names[] = "$name:";
    $media_values[$name] = null;
  }

  // Split responsive options.
  $splitted_value = explode('][', preg_replace('/\s(' . implode('|', $breakpoint_names) . ')(\s\S*?)?/', '][$1=', $value));

  // Store the default value for later use.
  $default_value = array_shift($splitted_value);

  // Extract and set the media values.
  foreach ($splitted_value as $media_value) {
    $splitted = explode(':=', $media_value);
    $media_values[$splitted[0]] = trim($splitted[1]);
  }

  // The unsuffixed value belongs to desktop, including when values are sparse.
  $media_values[$breakpoint_keys[get_default_ux_builder_breakpoint()]] = $default_value;

  // Fill in empty values if someone wants to.
  if ($fill_empty) {
    for ($i = 1; $i < count($media_values); $i++) {
      if ($media_values[$breakpoint_keys[$i]] === null || $media_values[$breakpoint_keys[$i]] === '')
        $media_values[$breakpoint_keys[$i]] = $media_values[$breakpoint_keys[$i - 1]];
    }
  }

  // Convert keys to indexes.
  if (! $string_keys) {
    for ($i = 0; $i < count($media_values); $i++) {
      $media_values[$i] = $media_values[$breakpoint_keys[$i]];
      unset($media_values[$breakpoint_keys[$i]]);
    }
  }

  // Keep the default value.
  $media_values['_default'] = $default_value;

  // Cache the results.
  $cache["$value-$string_keys-$fill_empty"] = $media_values;

  return $media_values;
}

function ux_builder_parse_responsive_string_named($value)
{
  return ux_builder_parse_responsive_string($value);
}

/**
 * Convert a responsive option into a string.
 *
 * @param  array $option
 * @return string
 */
function ux_builder_parse_responsive_array($option)
{
  // Convert all values to strings.
  $option = array_map(function ($value) {
    return (string) $value;
  }, $option);

  $original_option = $option;
  $breakpoints = get_ux_builder_breakpoints();
  $breakpoint_keys = array_keys($breakpoints);
  $breakpoint_names = array();
  $media_values = array();

  foreach ($breakpoints as $name => $data) {
    $breakpoint_names[] = "$name:";
    $media_values[$name] = null;
  }

  $media_values = array_reverse($media_values);

  // Return the first value if all values are equal.
  if (count(array_unique($option)) == 1) return $option[0];

  // Clear values that equals a lower screen
  // value. But allways keep the default value.
  for ($i = 1; $i < count($option); $i++) {
    // if ( $i == get_default_ux_builder_breakpoint() ) continue;
    // if ( $option[$i] == $original_option[$i - 1] ) $option[$i] = null;
  }

  // Insert all values except the default value.
  foreach ($option as $key => $value) {
    if ($key == get_default_ux_builder_breakpoint_index()) continue;
    $media_values[$breakpoint_keys[$key]] = $value;
  }

  // Set default as first value in the string.
  $string = $option[get_default_ux_builder_breakpoint_index()];

  // Generate reset of the string value.
  foreach ($media_values as $media => $value) {
    if (strlen((string) $value) > 0) $string .= " $media:$value";
  }

  return $string;
}


/**
 * Parse given option an return wanted breakpoint value.
 * Return a rendered string if a templated is provided.
 *
 * @param  string $value
 * @param  string $breakpoint_name
 * @param  string $template
 * @return mixed
 */
function ux_builder_responsive_option($value, $breakpoint_name, $template = null)
{
  $parsed = ux_builder_parse_responsive_string($value);
  $breakpoints = get_ux_builder_breakpoints();
  $breakpoint_keys = array_keys($breakpoints);
  $default_breakpoint_name = get_default_ux_builder_breakpoint();
  $return_value = null;

  // Return null if only default value is set and we
  // try to get the default breakpoint value.
  if (
    $breakpoint_name == $default_breakpoint_name &&
    count(array_filter($parsed)) == 2
  ) {
    return null;
  }

  // Return the default value if trying to get smallest
  // breakpoint value but only the default is set.
  if (
    count(array_filter($parsed)) == 2 &&
    $breakpoint_name == $breakpoint_keys[0]
  ) {
    $return_value = $parsed['_default'];
  } else {
    $return_value = $parsed[$breakpoint_name];
  }

  if ($template) {
    return $return_value ? str_replace('$$', $return_value, $template) : null;
  }

  return $return_value;
}

/**
 * Collect all responsive values from an array with attributes.
 *
 * @param  string $param_name
 * @param  array  $values
 * @return array
 */
function ux_builder_get_responsive_values($param_name, $values)
{
  $values = ux_builder_migrate_legacy_responsive_atts((array) $values);

  $breakpoints = get_ux_builder_breakpoints();
  $default = get_default_ux_builder_breakpoint();

  // Prepare values and set default value.
  $responsive_values = array_fill(0, count($breakpoints), null);
  $responsive_values[$default] = $values[$param_name];

  // Loop through all breakpoints and get current value from $values array.
  foreach ($breakpoints as $breakpoint_name => $breakpoint) {
    $breakpoint_index = get_ux_builder_breakpoint_index($breakpoint_name);
    if (array_key_exists($param_name . '__' . $breakpoint_name, $values)) {
      $responsive_values[$breakpoint_index] = $values[$param_name . '__' . $breakpoint_name];
    }
  }

  return $responsive_values;
}
