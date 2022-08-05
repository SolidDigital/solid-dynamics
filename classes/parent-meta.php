<?php
namespace Solid;

Class ParentMeta extends \Elementor\Core\DynamicTags\Tag {

    /**
     * Get Name
     *
     * Returns the Name of the tag
     *
     * @since 2.0.0
     * @access public
     *
     * @return string
     */
    public function get_name() {
        return 'parent-meta';
    }

    /**
     * Get Title
     *
     * Returns the title of the Tag
     *
     * @since 2.0.0
     * @access public
     *
     * @return string
     */
    public function get_title() {
        return __( 'Parent Meta', 'solid-dynamics' );
    }

    /**
     * Get Group
     *
     * Returns the Group of the tag
     *
     * @since 2.0.0
     * @access public
     *
     * @return string
     */
    public function get_group() {
        return 'solid-dynamics';
    }

    /**
     * Get Categories
     *
     * Returns an array of tag categories
     *
     * @since 2.0.0
     * @access public
     *
     * @return array
     */
    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            \Elementor\Modules\DynamicTags\Module::URL_CATEGORY
        ];
    }

    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function _register_controls() {

        $this->add_control(
            'parent_meta',
            [
                'label' => __( 'Parent Meta Key', 'solid-dynamics' ),
                'type' => 'text',
            ]
        );
    }

    /**
     * Render
     *
     * Prints out the value of the Dynamic tag
     *
     * @since 2.0.0
     * @access public
     *
     * @return void
     */
    public function render() {
        $meta_key = $this->get_settings( 'parent_meta' );

        if ( ! $meta_key ) {
            return;
        }

        $current = get_post();
        if ($current && $parent = get_post_parent($current)) {
            echo wp_kses_post(implode(",", get_post_meta($parent->ID, $meta_key)));
        }
    }
}
