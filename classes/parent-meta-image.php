<?php
namespace Solid;
class ParentMetaImage extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() {
        return 'parent-meta-image';
    }

    public function get_title() {
        return __( 'Parent Meta Image', 'solid-dynamics' );
    }

    public function get_group() {
        return 'solid-dynamics';
    }

    public function get_categories() {
        $cats = [
            \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
            \Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY,
        ];

        if (class_exists('Jet_Engine_Dynamic_Tags_Module')) {
            $cats[] = \Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY;
        }

        return $cats;
    }

    protected function _register_controls() {

        $this->add_control(
            'parent_meta',
            [
                'label' => __( 'Parent Meta Key', 'solid-dynamics' ),
                'type' => 'text',
            ]
        );
    }

    public function get_value( array $options = array() ) {
        $meta_key = $this->get_settings( 'parent_meta' );

        if ( ! $meta_key ) {
            return [];
        }

        $current = get_post();
        $attachment_id = null;
        $url = null;
        if ($current && $parent = get_post_parent($current)) {
            $attachment_id = get_post_meta($parent->ID, $meta_key, true);
            $url = wp_get_attachment_image_src($attachment_id);
        }

        return [
            "id" => $attachment_id,
            "url" => $url
        ];
    }
}
