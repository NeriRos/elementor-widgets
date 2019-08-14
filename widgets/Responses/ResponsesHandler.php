<?php
namespace ElementorLightXWidgets\Widgets\Responses;
class ResponsesHandler
{
    private $quota_product_id = 293;

    public function register()
    {
        $this->registerActions();
    }

    public function registerActions()
    {
        // Styles
        add_action('elementor/frontend/after_register_styles', [$this, 'widget_styles']);

        // Scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);

        // Forms
        add_action('elementor_pro/forms/new_record', [$this, 'save_new_response'], 10, 2);

        // Ajax
        add_action('wp_ajax_' . WidgetHandler::$actions['get_quota'] , [$this, 'getQuota']);

        // Hooks
        add_action( 'woocommerce_order_status_completed', [$this, 'payment_complete'], 10, 1 );
        add_action('woocommerce_thankyou', [$this, 'action_woocommerce_thankyou'], 10, 1);
    }
    
    function getQuota() {
        wp_send_json(["quota" => $this->get_user_quota(get_current_user_id())]);
    }
    
    function action_woocommerce_thankyou($order_id)
    {
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order($order_id);
        $order->update_status( 'completed' );
    }
    
    public function save_new_response($record, $handler)
    {
        $form_name = $record->get_form_settings('form_name');

        if ('New Response' !== $form_name)
            return;
        
        $user_id = get_current_user_id();
        $raw_fields = $record->get('fields');
        $fields = [];
        
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        // Verify nonce    
        if(!wp_verify_nonce($fields['nonce'], 'createNewResponse'))
            $handler->add_error( "nonce", "security fault" );

        $this->reduce_quota($user_id);

        // create post
        $postData = array(
            'post_author' => $fields['userId'],
            'post_content' => $fields['response'],
            'post_title' => 'User response',
            'post_status' => 'publish',
            'post_type' => 'response'
        );

        // execurte 
        $post_id = wp_insert_post($postData);

        // return confiration
        if(is_wp_error($post_id))
            return $handler->add_error("response", "was not saved, due to an error." );

        // decrese quota
        $newQuota = $this->reduce_quota($user_id);

        if(isset($newQuota['error']) && $newQuota['error']) {
            wp_trash_post($post_id);
            $handler->add_error( "userId", $newQuota['message'] . " post trashed for now, you can recover it later." );
            return;
        }

        $handler->add_response_data('newQuota', $newQuota);
    }

    function payment_complete( $order_id ){
        $order = wc_get_order( $order_id );
        $user = $order->get_user();
        $user_id = $user->ID;
        $quota = 0;

        if( $user ){
            $quota = $this->get_user_quota($user_id);

            foreach ($order->get_items() as $item_id => $item_data) {
                $product = $item_data->get_product();
                $product_id = $product->get_id();
                
                if($product_id == $this->quota_product_id) {
                    $quota += $item_data->get_quantity();
                }
            }

            $this->set_user_quota($user_id, $quota);
            $this->set_order_quota($order_id, $quota);
            
            $order->add_order_note("the user has: $quota quota.");
        } else
            wp_send_json(['error' => true, 'message' => "no user found for id: $user_id"]);


        return $quota;
    }

    function reduce_quota($user_id) {
        if( $user_id ){
            $quota = $this->get_user_quota($user_id);

            if($quota > 0)  {
                $newQuota = ((int)$quota) - 1;
                $updated_user__id = $this->set_user_quota($user_id, $newQuota);
                
                if(is_wp_error($updated_user__id))
                    return $updated_user__id;
                else
                    return $newQuota;
            }
            else {
                return ['error' => true, 'message' => 'no user quota to decrease from.'];
            }
        }

        return ['error' => true, 'message' => 'no userid.'];
    }

    function get_user_quota($user_id) {
        $quota = 0;

        if(get_user_by('id', $user_id)) {
            $quota = get_user_meta($user_id, 'responses_quota', true);

            if(!$quota)
                $quota = 0;
        } else
            return wp_send_json(['error' => true, 'message' => "user id: $user_id was not found"]);

        return $quota;
    }

    function set_user_quota($user_id, $quota) {
        if (!$quota) 
            $quota = 0;
        $updated_user__id = update_user_meta($user_id, 'responses_quota', $quota);

        return $updated_user__id;
    }


    function set_order_quota($order_id, $quota) {
        if (!$quota) 
            $quota = 0;

        update_post_meta($order_id, 'responses_quota_on_creation', $quota);
    }


    /**
     * widget_scripts
     *
     * Load required plugin core files.
     *
     * @since 1.2.0
     * @access public
     */
    public function widget_scripts()
    {
        wp_enqueue_script('elementor-lightx-widgets-responses-js', plugins_url('responses.js', __FILE__), ['jquery'], false, true);
        $ajax_data = [
            'url' => admin_url('admin-ajax.php'),
            'actions' => WidgetHandler::$actions,
            'nonce' => wp_create_nonce('responses'),
            'nonce_create' => wp_create_nonce('createNewResponse')
        ];
        wp_localize_script( 'elementor-lightx-widgets-responses-js', 'admin_ajax', $ajax_data );
    }

    /**
     * widget_styles
     *
     * Load required plugin core files.
     *
     * @since 1.2.1
     * @access public
     */
    public function widget_styles()
    {
        wp_enqueue_style('elementor-lightx-widgets-responses-css', plugins_url('responses.css', __FILE__));
    }
}
