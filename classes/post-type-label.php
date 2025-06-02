<?php
namespace Solid;

class PostTypeLabel extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'post-type-label';
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        ];
    }

    public function get_group() {
        return 'solid-dynamics';
    }

    public function get_title() {
        return __('Post Type Label', 'solid-dynamics');
    }

    protected function _register_controls() {
        $this->add_control(
            'label',
            [
                'label' => __( 'Label', 'solid-dynamics' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'singular',
                'options' => [
                    'singular' => __( 'Singular', 'solid-dynamics' ),
                    'plural' => __( 'Plural', 'solid-dynamics' ),
                ],
            ]
        );
    }

    public function render() {
        global $post;
        $label = $this->get_settings( 'label' );
        $post_type_object = get_post_type_object($post->post_type);

        if (!$post_type_object) {
            return;
        }

        switch ($label) {
            case 'singular':
                echo $post_type_object->labels->singular_name;
                break;
            case 'plural':
                echo $post_type_object->labels->name;
                break;
        }
    }
}
