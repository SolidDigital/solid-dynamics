<?php
namespace Solid;

class ElementorDisplayConditions {
    function __construct() {
        add_action( 'elementor/display_conditions/register', [$this, 'elementor_display_conditions_register_conditions'] );
    }

    function elementor_display_conditions_register_conditions($conditions_manager) {
        require_once( __DIR__ . "/display-condition-solid-dynamics-macro.php" );

        $conditions_manager->register_condition_instance( new DisplayConditionSolidDynamicsMacro() );
    }
}
