<?php
namespace Solid;

class JetEngineMacros {
  function __construct() {
    add_filter('jet-engine/listings/macros-list', [$this, 'macros_list']);
  }

  function macros_list($macros_list) {
    $macros_list['custom_callback'] = [
      'label' => __( 'Custom Callback', 'solid-dynamics' ),
      'cb'    => [ $this, 'custom_callback' ],
      'args'  => array(
        'callback' => array(
          'label'   => __( 'Callback', 'solid-dynamics' ),
          'type'    => 'text',
          'default' => '',
        ),
      ),
    ];

    return $macros_list;
  }

  function custom_callback($field_value, $callback) {
    global $post;

    if (is_callable($callback)) {
      return $callback($post);
    }

    return $field_value;
  }
}
