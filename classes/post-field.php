<?php
namespace Solid;

class PostField extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'post-field';
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
        return __('Post Field', 'solid-dynamics');
    }

    protected function _register_controls() {
        $this->add_control(
            'fieldname',
            [
                'label' => __( 'Callback', 'solid-dynamics' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
    }

    public function render() {
        global $post;
        $field_name = $this->get_settings( 'fieldname' );
        $is_standard_field = property_exists($post,$field_name);

        if($is_standard_field) {
            echo wp_kses_post($post->$field_name);
        } else {
            $field_value = get_post_meta($post->ID, $field_name, true);

            if(empty($field_value)) {
                echo '';
            } else {
                echo wp_kses_post($field_value);
            }
        }
    }
}
