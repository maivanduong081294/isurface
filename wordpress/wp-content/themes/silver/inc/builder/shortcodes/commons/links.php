<?php
/**
 * Nhóm option "Link" dùng chung cho các shortcode trong UX Builder.
 *
 * File này được require ở nhiều shortcode, nên phần dựng option được đưa vào
 * một hàm có cache để chỉ truy vấn post type / taxonomy đúng một lần mỗi request.
 * Giá trị lưu ra shortcode được đổi thành URL bởi silver_get_link_data().
 */

if (! function_exists('silver_ux_builder_link_options')) {
  function silver_ux_builder_link_options()
  {
    static $group = null;

    if ($group !== null) return $group;

    // Các post type public (trừ attachment): page, post, product, CPT của bạn...
    $post_types = array_values(array_diff(
      get_post_types(array('public' => true)),
      array('attachment')
    ));

    // CPT có trang archive.
    $archive_options = array('' => __('Select...'));
    foreach (get_post_types(array('public' => true, 'has_archive' => true), 'objects') as $pt) {
      $archive_options[$pt->name] = $pt->labels->name;
    }

    // Taxonomy public + 1 field chọn term cho mỗi taxonomy.
    $taxonomies = get_taxonomies(array('public' => true), 'objects');
    unset($taxonomies['post_format']);

    $tax_options = array();
    $term_fields = array();
    foreach ($taxonomies as $tax) {
      $tax_options[$tax->name] = $tax->labels->name;

      $term_fields['link_term_' . $tax->name] = array(
        'type'       => 'select',
        'heading'    => $tax->labels->singular_name,
        'conditions' => 'link_type === "term" && link_taxonomy === "' . $tax->name . '"',
        'config'     => array(
          'placeholder' => __('Select...'),
          'termSelect'  => array(
            'taxonomies' => $tax->name,
          ),
        ),
      );
    }

    $group = array(
      'type'    => 'group',
      'heading' => __('Link'),
      'options' => array_merge(
        array(
          'link_type' => array(
            'type'    => 'select',
            'heading' => __('Link Type'),
            'default' => 'custom',
            'options' => array(
              'custom'  => __('Custom link'),
              'post'    => __('Post'),
              'term'    => __('Taxonomy'),
              'archive' => __('Archive'),
            ),
          ),
          'link' => array(
            'type'       => 'textfield',
            'heading'    => __('Link'),
            'conditions' => 'link_type === "custom"',
          ),
          'link_post' => array(
            'type'       => 'select',
            'heading'    => __('Post'),
            'conditions' => 'link_type === "post"',
            'config'     => array(
              'placeholder' => __('Select...'),
              'postSelect'  => array(
                'post_type' => $post_types,
              ),
            ),
          ),
          'link_taxonomy' => array(
            'type'       => 'select',
            'heading'    => __('Taxonomy'),
            // Giá trị mặc định không được lưu ra shortcode, nên phải khớp với
            // taxonomy mặc định trong silver_get_link_data().
            'default'    => 'category',
            'conditions' => 'link_type === "term"',
            'options'    => $tax_options,
          ),
        ),
        $term_fields,
        array(
          'link_archive' => array(
            'type'       => 'select',
            'heading'    => __('Post type'),
            'default'    => '',
            'conditions' => 'link_type === "archive"',
            'options'    => $archive_options,
          ),
          'target' => array(
            'type'    => 'select',
            'heading' => __('Target'),
            'default' => '',
            'options' => array(
              ''       => __('Same window'),
              '_blank' => __('New window'),
            ),
          ),
          'rel' => array(
            'type'    => 'textfield',
            'heading' => __('Rel'),
          ),
        )
      ),
    );

    return $group;
  }
}

return silver_ux_builder_link_options();
