<?php
namespace ElementorLightXWidgets\Handlers;

class TsitutimHandler {
    public static $actions = [
        'save_bookmark' => 'saveBookmark',
        'get_bookmark' => 'getBookmark',
        'get_quota' => 'getQuota'
    ];

    private static $nonces = [
        'general' => 'tsitutim'
    ];

    public function register($child) {
        $this->registerDb();
        $this->registerActions();

        switch ($child) {
            case 'utterances':
                new UtterancesHandler();
                break;
            case 'responses':
                new ResponsesHandler();
                break;
        }
    }

    public function registerActions() {
        // Styles
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] );
        
        // Scripts
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

        // Posts
        add_action( 'init', [$this, 'registerCustomPosts'], 10 );

        // Hooks
        add_action( 'save_post', [$this, 'saveItemMeta'], 10, 3 );
        add_action( 'wp_trash_post', [$this, 'deleteItemMeta'], 10, 1 );

        // API
        add_action('wp_ajax_' . TsitutimHandler::$actions['save_bookmark'] , [$this, 'saveItemBookmarkToDB']); 
        add_action('wp_ajax_' . TsitutimHandler::$actions['get_bookmark'] , [$this, 'getItemBookmarkFromDB']); 
    }

    function registerCustomPosts() {
        register_post_type( 'response', array(
                'labels' => array(
                    'name' => 'תגובות',
                    'singular_name' => 'תגובה',
                ),
                'description' => 'תגובות',
                'public' => true,
                'menu_position' => 20,
                'supports' => array( 'title', 'editor', 'custom-fields' )
            )
        );
        
        register_post_type( 'utterance', array(
                'labels' => array(
                    'name' => 'אמרות',
                    'singular_name' => 'אמרה',
                ),
                'description' => 'אמרות',
                'public' => true,
                'menu_position' => 20,
                'supports' => array( 'title', 'editor', 'custom-fields' )
            )
        );
    }

    public function saveItemBookmarkToDB() {
        global $wpdb;
        $itemName = $_GET['itemName'];

        $table_name = $wpdb->prefix . "items_bookmark";
        $sql = "INSERT INTO $table_name (user_id, " . $itemName . "_id) VALUES (%d, %d) ON DUPLICATE KEY UPDATE " . $itemName . "_id = %d;";
        $sql = $wpdb->prepare($sql, $_GET['userId'], $_GET['itemId'], $_GET['itemId'] ?: 0);
        
        wp_send_json($wpdb->query($sql));
        wp_die();
    }

    public function getItemBookmarkFromDB() {
        global $wpdb;
        $table_name = $wpdb->prefix . "items_bookmark";

        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d;", $_GET['userId']);
        $result = $wpdb->get_row( $sql );
        wp_send_json($result);
        wp_die();
    }

    /**
     *  register database
     *
     * Register plugin database
     *
     * @since 1.2.2
     * @access public
     */
    public function registerDb() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . "items_bookmark";
        
        $createTable = "CREATE TABLE IF NOT EXISTS $table_name (
            user_id INTEGER NOT NULL,
            response_id int,
            utterance_id int,
            PRIMARY KEY (user_id)
            ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($createTable);
    }

    public function saveItemMeta( $post_id, $post, $update ) {
        global $wpdb;
        $table_name = $wpdb->prefix . "postmeta";

        $post_type = strtolower(get_post_type($post_id));

        if ( "response" != $post_type && "utterance" != $post_type || $update ) return;

        $itemIdName = $post_type . 'Id';

        $selectMaxQuery = $wpdb->prepare("SELECT meta_value FROM $table_name WHERE CONVERT(meta_value, SIGNED) = 
            ( SELECT MAX(CONVERT(meta_value, SIGNED)) FROM $table_name WHERE meta_key = %s ) AND meta_key = %s; ", 
            $itemIdName, $itemIdName);
        
        $biggestIdResult = $wpdb->get_row($selectMaxQuery);
        $newItemId = $biggestIdResult ? $biggestIdResult->meta_value + 1 : 1;

        update_post_meta( $post_id, $itemIdName, $newItemId);
    }

    public function deleteItemMeta( $post_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . "postmeta";

        $post_type = strtolower(get_post_type($post_id));

        if ( "response" != $post_type && "utterance" != $post_type ) return;

        $itemIdName = $post_type . 'Id';

        $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE post_id = %d AND meta_key = %s", $post_id, $itemIdName ) );
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
        wp_enqueue_script( 'elementor-lightx-widgets_widgets-js', plugins_url( 'widgets.js', __FILE__ ), [ 'jquery' ], false, true );

        $ajax_data = [
            'url' => admin_url('admin-ajax.php'),
            'actions' => TsitutimHandler::$actions,
            'nonce' => wp_create_nonce(self::$nonces['general'])
        ];
        wp_localize_script( 'elementor-lightx-widgets_widgets-js', 'admin_ajax', $ajax_data );
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
        wp_enqueue_style( 'elementor-lightx-widgets_widgets-css', plugins_url( 'widgets.css', __FILE__));
    }
}