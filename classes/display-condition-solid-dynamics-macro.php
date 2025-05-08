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
        return boolval($value);
    }

    public function get_options() {
        $this->add_control(
            'macro',
            [
                'label' => __( 'Macro', 'solid-dynamics' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
    }
}

function solid_dynamics_macro($macro) {
    $items = explode('|', $macro);
    $type = $items[0] ?? '';
    $field = $items[1] ?? '';
    $meta_key = $items[2] ?? '';

    // TODO: add index for repeater fields.
    // $index = $items[3];

    if (empty($type) || empty($field)) {
        return;
    }

    switch ($type) {
        case 'post':
            global $post;

            if ($field === 'meta' && !empty($meta_key)) {
                return get_post_meta($post->ID, $meta_key, true);
            }

            return $post->$field;
        case 'user':
            $user = wp_get_current_user();

            if ($field === 'meta' && !empty($meta_key)) {
                return get_user_meta($user->ID, $meta_key, true);
            }

            return $user->$field;
        case 'function':
            $function_name = $field;

            if (is_callable($function_name)) {
                return $function_name();
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