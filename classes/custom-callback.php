<?php
namespace Solid;

class CustomCallback extends \Elementor\Core\DynamicTags\Tag {

  public function get_name() {
      return 'custom-callback';
  }

  public function get_categories() {
      return [
        \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
      ];
  }

  public function get_group() {
      return 'solid-dynamics';
  }

  public function get_title() {
      return __('Custom Callback', 'solid-dynamics');
  }

  protected function _register_controls() {
      $this->add_control(
          'callback',
          [
              'label' => __( 'Callback', 'solid-dynamics' ),
              'type' => \Elementor\Controls_Manager::TEXT,
          ]
      );
  }
  public function render() {
      global $post;

      $callback = $this->get_settings( 'callback' );

      if (is_callable($callback)) {
          echo wp_kses_post($callback($post));
      }
  }
}
