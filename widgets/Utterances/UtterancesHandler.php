<?php
namespace ElementorLightXWidgets\Widgets\UtterancesHandler;

class UtterancesHandler {
  public function register()
  {
    $this->registerActions();
  }

  public function registerActions() {
    // Styles
    add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] );
    
    // Scripts
    add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
  }

  /**
   * widget_scripts
   *
   * Load required plugin core files.
   *
   * @since 1.2.0
   * @access public
   */
  public function widget_scripts() {
    wp_enqueue_script( 'elementor-lightx-widgets-utternaces-js', plugins_url( 'utterances.js', __FILE__ ), [ 'jquery' ], false, true );
    $ajax_data = [
      'url' => admin_url('admin-ajax.php'),
      'actions' => WidgetHandler::$actions,
      'nonce' => wp_create_nonce('utterances')
    ];
    wp_localize_script( 'elementor-lightx-widgets-utternaces-js', 'admin_ajax', $ajax_data );

    add_action('wp_admin_' . WidgetHandler::$actions['save_bookmark'], array($this, 'save_bookmark'));
  }
 
  /**
   * widget_styles
   *
   * Load required plugin core files.
   *
   * @since 1.2.1
   * @access public
   */
  public function widget_styles() {
    wp_enqueue_style( 'elementor-lightx-widgets-utterances-css', plugins_url( 'utterances.css', __FILE__));
  }
}