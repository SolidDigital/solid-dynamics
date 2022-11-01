<?php
namespace Solid;

Class ListPluck extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'list_pluck';
    }

    public function get_title() {
        return __( 'List Pluck', 'solid-dynamics' );
    }

    public function get_group() {
        return 'solid-dynamics';
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY
        ];
    }

    protected function _register_controls() {
        $this->add_control(
			'source',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Source', 'solid-dynamics' ),
				'options' => [
                    'meta' => 'Meta',
                    'option' => 'Site Option'
                ],
                'default' => 'meta'
			]
		);

        $this->add_control(
			'list',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'List', 'solid-dynamics' )
			]
		);

        $this->add_control(
			'field',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Field', 'solid-dynamics' )
			]
		);

        $this->add_control(
			'sep',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Separator', 'solid-dynamics' ),
                'default' => ','
			]
		);
    }

    public function render() {
        $source = $this->get_settings( 'source' );
        $list = $this->get_settings( 'list' );
        $field = $this->get_settings( 'field' );
        $sep = $this->get_settings( 'sep' );

        if ( empty($source) || empty($list) || empty($field) || empty($sep) ) {
            error_log('Solid Dynamics: The List Pluck dynamic tag requires source, list, field, and seperator to be set.');
            return;
        }

        switch ($source) {
            case "meta":
                global $post;

                $the_list = get_post_meta( $post->ID, $list, true );
                break;
            case "option":
                $the_list = get_option($list);
                break;
        }

        $ids = wp_list_pluck( $the_list, $field );

        echo wp_kses_post(join($sep, $ids));
    }
}
