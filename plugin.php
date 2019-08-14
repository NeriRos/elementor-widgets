<?php
/**
 * @package elemenntor-lightx-widgets
 */

namespace ElementorLightXWidgets;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.0.0
 */
class Plugin {

  /**
   * Instance
   *
   * @since 1.0.0
   * @access private
   * @static
   *
   * @var Plugin The single instance of the class.
   */
  private static $_instance = null;
 
  /**
   * Instance
   *
   * Ensures only one instance of the class is loaded or can be loaded.
   *
   * @since 1.2.0
   * @access public
   *
   * @return Plugin An instance of the class.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
           
    return self::$_instance;
  }
 
  /**
   * Include Widgets files
   *
   * Load widgets files
   *
   * @since 1.2.0
   * @access private
   */
  private function include_widgets_files() {
    require_once( __DIR__ . '/widgets/Utterances.php' );
    require_once( __DIR__ . '/widgets/Responses.php' );
    require_once( __DIR__ . '/widgets/SignInButton.php' );
  }

  // function __autoload($class)
  // {
  //     $parts = explode('\\', $class);
  //     require end($parts) . '.php';
  // }

  // public function register_widgets() {
  //   $dir = __DIR__ . '/widgets/';
  //   $widgets_files = scandir($dir);
  //   if($widgets_files)
  //     foreach ($widgets_files as $key => $value) {
  //       if($this->endsWith($value, '.php')) {
  //         // require_once $dir . $value;

  //         $class = 'Widgets\\' . basename($dir . $value, '.php');
          
  //         echo "sss" . $class;

  //         new \ElementorLightXWidgets\Widgets\Responses;

  //         // Register Widgets
  //         \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class() );    
          
  //         if (class_exists("Widgets\\Responses")) {
  //           echo "Teettet";
  //         } else {
  //           echo "ne class" . $class;
  //         }
  //       }
  //     }
  // }
 
  // function endsWith($haystack, $needle)
  // {
  //     $length = strlen($needle);
  //     if ($length == 0) {
  //         return false;
  //     }
  
  //     return (substr($haystack, -$length) === $needle);
  // }
  
  /**
   * Register Widgets
   *
   * Register new Elementor widgets.
   *
   * @since 1.2.0
   * @access public
   */
  public function register_widgets2() {
    foreach (Init::get_widgets([
      Widgets\Responses,
      Widgets\Utterances,
      Widgets\Responses
    ]) as $key => $value) {
      \Elementor\Plugin::instance()->widgets_manager->register_widget_type( $value );      
    }
  }

  function widget_scripts() {
    wp_enqueue_script( 'elementor-lightx-widgets-js', plugins_url( 'assets/js/index.js', __FILE__ ), [ 'jquery' ], false, true );
  }

  function widget_styles() {
    wp_enqueue_script( 'elementor-lightx-widgets-css', plugins_url( 'assets/css/index.css', __FILE__ ), [ 'jquery' ], false, true );
  }

  /**
   *  Plugin class constructor
   *
   * Register plugin action hooks and filters
   *
   * @since 1.2.0
   * @access public
   */
  public function __construct() {
    // $UtterancesHandler = new Handlers\UtterancesHandler();
    // $ResponsesHandler = new Handlers\ResponsesHandler();
    $ResponsesHandler = new Handlers\WidgetHandler();

    // Styles
    add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] );
    
    // Scripts
    add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

    // Register widgets
    add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets2' ] );
  }
}


// Instantiate Plugin Class
Plugin::instance();