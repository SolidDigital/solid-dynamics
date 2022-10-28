<?php
namespace Solid;

class Settings {
  function __construct() {
    require_once( __DIR__ . "/settings-page.php" );
    new SettingsPage();

    include_once( __DIR__ . "/elementor-back-to-wp-editor-button.php" );
    new ElementorBackToWPEditorButton();

    include_once( __DIR__ . "/hello-elementor-page-title.php" );
    new HelloElementorPageTitle();

    include_once( __DIR__ . "/elementor-wrap-content.php" );
    new ElementorWrapContent();
  }
}
