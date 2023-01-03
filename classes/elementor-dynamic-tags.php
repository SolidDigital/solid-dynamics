<?php
namespace Solid;

class ElementorDynamicTags {
    function __construct() {
        add_action( 'elementor/dynamic_tags/register', [$this, 'elementor_dynamic_tags_register_tags'] );
    }

    function elementor_dynamic_tags_register_tags( $dynamic_tags ) {
        \Elementor\Plugin::$instance->dynamic_tags->register_group( 'solid-dynamics', [
            'title' => 'Solid Dynamics'
        ] );

        require_once( __DIR__ . "/parent-meta.php" );
        require_once( __DIR__ . "/parent-meta-image.php" );
        require_once( __DIR__ . "/custom-callback.php" );
        require_once( __DIR__ . "/menu.php" );
        require_once( __DIR__ . "/list-pluck.php" );
        require_once( __DIR__ . "/post-field.php" );

        $dynamic_tags->register( new \Solid\ParentMeta() );
        $dynamic_tags->register( new \Solid\ParentMetaImage() );
        $dynamic_tags->register( new \Solid\CustomCallback() );
        $dynamic_tags->register( new \Solid\Menu() );
        $dynamic_tags->register( new \Solid\ListPluck() );
        $dynamic_tags->register( new \Solid\PostField() );
    }
}
