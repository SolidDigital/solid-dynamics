<?php
namespace Solid;

class ElementorDynamicTags {
    function __construct() {
        add_action( 'elementor/dynamic_tags/register_tags', [$this, 'elementor_dynamic_tags_register_tags'] );
    }

    function elementor_dynamic_tags_register_tags( $dynamic_tags ) {
        \Elementor\Plugin::$instance->dynamic_tags->register_group( 'solid-dynamics', [
            'title' => 'Solid Dynamics'
        ] );

        include_once( __DIR__ . "/parent-meta.php" );
        include_once( __DIR__ . "/parent-meta-image.php" );
        include_once( __DIR__ . "/custom-callback.php" );
        include_once( __DIR__ . "/menu.php" );

        $dynamic_tags->register_tag( '\Solid\ParentMeta' );
        $dynamic_tags->register_tag( '\Solid\ParentMetaImage' );
        $dynamic_tags->register_tag( '\Solid\CustomCallback' );
        $dynamic_tags->register_tag( '\Solid\Menu' );
    }
}
