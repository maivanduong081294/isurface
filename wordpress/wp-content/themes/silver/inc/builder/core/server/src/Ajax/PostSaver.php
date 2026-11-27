<?php

namespace UxBuilder\Ajax;

use UxBuilder\Options\Options;
use UxBuilder\Transformers\ArrayToString;

class PostSaver {

  public function save() {
    define( 'UX_BUILDER_SAVING', true );
    $data    = json_decode( stripslashes( isset( $_POST['data'] ) ? (string) $_POST['data'] : '' ), true );
    $data    = is_array( $data ) ? $data : array();
    $post_id = isset( $data['id'] ) ? (int) $data['id'] : 0;

    // Return an error if nonce is invalid.
    check_ajax_referer( 'ux-builder-' . $post_id, 'security' );

    $post        = $post_id ? get_post( $post_id ) : null;
    $can_edit    = $post && current_user_can( 'edit_post', $post_id );
    $can_publish = $post && current_user_can( 'publish_post', $post_id );
    $can_save    = apply_filters( 'ux_builder_can_save', '__return_true', $data );

    // Stop here if user is not allowed to edit post.
    if ( ! $can_edit or ! $can_save ) {
      return wp_send_json_error();
    }

    $post_content = '';

    if ( isset( $data['content'] ) && is_array( $data['content'] ) && array_key_exists( 'children', $data['content'] ) ) {
      $elements = ux_builder( 'elements' );
      $use_blocks = function_exists( 'use_block_editor_for_post' )
        ? use_block_editor_for_post( $post )
        : false;

      $stringify = new ArrayToString( $elements, $use_blocks );

      $post_content = $stringify->transform( $data['content']['children'] );
    }

    // Status: publishing (or scheduling, private) needs the right to publish;
    // without it the post goes to review, as in the WordPress editor.
    $post_status = isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft';
    if ( ! in_array( $post_status, array( 'publish', 'future', 'private', 'pending', 'draft' ), true ) ) {
      $post_status = 'draft';
    }
    if ( ! $can_publish && in_array( $post_status, array( 'publish', 'future', 'private' ), true ) ) {
      $post_status = 'pending';
    }

    if ( $can_publish && $post_status == 'publish' ) {
      $save_button = __( 'Update', 'wordpress' );
    } else {
      $save_button = $post_status == 'pending'
        ? __( 'Submit for Review', 'wordpress' )
        : __( 'Publish', 'wordpress' );
    }

    // Only the fields the builder shows for this post (title, excerpt,
    // parent, its meta options), never other columns (author, type...) or
    // other meta keys.
    $attribute_keys = array_keys( (array) apply_filters( 'ux_builder_post_options', array(), $post ) );
    $meta_keys      = array_keys( (array) apply_filters( 'ux_builder_meta_options', array(), $post ) );
    $attributes     = isset( $data['attributes'] ) && is_array( $data['attributes'] ) ? $data['attributes'] : array();
    $meta           = isset( $data['meta'] ) && is_array( $data['meta'] ) ? $data['meta'] : array();
    $attributes     = array_intersect_key( $attributes, array_flip( $attribute_keys ) );
    $meta           = array_intersect_key( $meta, array_flip( $meta_keys ) );

    $post = apply_filters( 'ux_builder_save_post', array_merge( $attributes, array(
      'ID' => $post_id,
      'meta_input' => $meta,
      'post_content' => trim( $post_content ),
      'post_status' => $post_status,
    ) ) );

    if ( wp_update_post( $post ) ) {
      return wp_send_json_success( compact( 'post', 'save_button' ) );
    }

    return wp_send_json_error();
  }
}
