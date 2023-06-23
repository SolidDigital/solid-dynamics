<?php
namespace Solid;

// Example of how to create a custom WPSF type
class SolidWPSF extends \WordPressSettingsFramework {
    public function generate_readonly_field($args) {
        $args['value'] = esc_html( esc_attr( $args['value'] ) );

        echo '<textarea readonly name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" rows="5" cols="60" class="' . esc_attr( $args['class'] ) . '">' . esc_html( $args['value'] ) . '</textarea>';

        $this->generate_description( $args['desc'] );
    }
}
