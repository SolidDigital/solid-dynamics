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
			'option',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Option (optional)', 'solid-dynamics' ),
                'condition' => [
                    'source' => 'option'
                ]
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
        $option = $this->get_settings( 'option' );
        $list = $this->get_settings( 'list' );
        $field = $this->get_settings( 'field' );
        $sep = $this->get_settings( 'sep' );

        if ( empty($source) || empty($list) || empty($field) || empty($sep) ) {
            error_log('Solid Dynamics: The List Pluck dynamic tag requires source, list, field, and seperator to be set.');
            return;
        }

        switch ($source) {
            case "meta":
                // Jet engine listing grid sets $wp_query->queried_object to the current object as it iterates through posts, terms, or users.
                // So this will give us the most "local" context when it is called.
                $object = get_queried_object();

                if (!is_object($object)) break;

                switch (get_class($object)) {
                    case "WP_Post":
                        $the_list = get_post_meta( $object->ID, $list, true );
                        break;
                    case "WP_Term":
                        $the_list = get_term_meta( $object->term_id, $list, true );
                        break;
                    case "WP_User":
                        $the_list = get_user_meta( $object->ID, $list, true );
                        break;
                    default:
                        $the_list = [];
                }
                break;
            case "option":
                if ($option) {
                    $the_option = get_option($option);
                    $the_list = isset($the_option[$list]) ? $the_option[$list] : [];
                } else {
                    $the_list = get_option($list);
                }
                break;
        }

        $ids = wp_list_pluck( $the_list, $field );

        echo wp_kses_post(join($sep, $ids));
    }
}
