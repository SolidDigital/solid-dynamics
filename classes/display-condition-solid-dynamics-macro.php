<?php

namespace Solid;

class DisplayConditionSolidDynamicsMacro extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base {
    public function get_name() {
        return 'solid_dynamics_macro';
    }

    public function get_label() {
        return esc_html__( 'Solid Dynamics Macro', 'solid-dynamics' );
    }

    public function get_group() {
        return 'other';
    }

    public function check( $args ) : bool {
        $value = solid_dynamics_macro($args['macro']);

        if (($args['compare'] ?? '') === 'true') {
            return boolval($value);
        } else {
            return ! boolval($value);
        }
    }

    public function get_options() {
        $this->add_control(
            'macro',
            [
                'label' => __( 'Macro', 'solid-dynamics' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'compare',
            [
                'label' => __( 'Compare', 'solid-dynamics' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'true',
                'options' => [
                    'true' => __( 'Is True', 'solid-dynamics' ),
                    'false' => __( 'Is False', 'solid-dynamics' ),
                    // TODO: add other options, equal, not equal, etc.
                ],
            ]
        );
    }
}

function solid_dynamics_macro($macro) {
    $items = explode('|', $macro);
    $type = $items[0] ?? '';
    $field = $items[1] ?? '';

    // TODO: add index for repeater fields.
    // $index = $items[2];

    if (empty($type) || empty($field)) {
        return;
    }

    switch ($type) {
        case 'post':
            global $post;

            if ($post->$field !== null) {
                return $post->$field;
            }

            return get_post_meta($post->ID, $field, true);
        case 'user':
            $user = wp_get_current_user();

            if ($user->$field !== null) {
                return $user->$field;
            }

            return get_user_meta($user->ID, $field, true);

        case 'function':
            if (is_callable($field)) {
                return $field();
            };

            return;
        // TODO: add option
        // case 'option':
        //     $option = get_option($field);
        //     return $option;
        // TODO: add author
        // case 'author':
        //     $author = get_queried_object();
        //     return $author->name;
        // TODO: add term
        // case 'term':
        //     $term = get_queried_object();
        //     return $term->name;
    }
}