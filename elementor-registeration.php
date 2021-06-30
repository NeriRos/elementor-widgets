<?php
/**
 * Plugin Name: Elementor Registeration Form
 * Description: Create a new user using elementor form
 * Author:      LightX - Neriya Rosner
 * Author URI:  https://wwww.lightx.co.il
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action( 'elementor_pro/forms/new_record',  'elementor_registeration_form', 10, 2 );

function elementor_registeration_form($record, $ajax_handler)
{
    $form_name = $record->get_form_settings('form_name');

    if ('Create New User' == $form_name)
        registerNewUser( $record->get_formatted_data(), $ajax_handler );
    else if ('Create New Guest' == $form_name)
        registerNewGuest( $ajax_handler );
}

function registerNewUser($form_data, $ajax_handler) {
    $username=$form_data['User Name']; //Get tne value of the input with the label "User Name"
    $password = $form_data['Password']; //Get tne value of the input with the label "Password"
    $email=$form_data['Email'];  //Get tne value of the input with the label "Email"
    $newslatter=strpos($form_data['Suggestions'], 'newslatter') !== false;
    $advertisements=strpos($form_data['Suggestions'], 'advertisements') !== false;
    
    $user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object
    if (is_wp_error($user)){ // if there was an error creating a new user
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
        $ajax_handler->is_success = false;
        return;
    }
    $first_name=$form_data["First Name"]; //Get tne value of the input with the label "First Name"
    $last_name=$form_data["Last Name"]; //Get tne value of the input with the label "Last Name"
    wp_update_user(array(
        "ID"=>$user,
        "first_name"=>$first_name,
        "last_name"=>$last_name,
    )); // Update the user with the first name and last name
    update_user_meta($user, 'newslatter', $newslatter);
    update_user_meta($user, 'advertisements', $advertisements);

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array( // credientials for newley created user
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>true
    );
    $signon = wp_signon($creds); //sign in the new user
    if ($signon)
        $ajax_handler->add_response_data( 'redirect_url', get_home_url() ); // optinal - if sign on succsfully - redierct the user to the home page

}

function registerNewGuest($ajax_handler) {
    $username = 'guest' . mt_rand(100000, 999999); 
    $password = randomPassword(8);
    $email = "$username@temp.com";
    $user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object
  
    if (is_wp_error($user)){ // if there was an error creating a new user
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
        $ajax_handler->is_success = false;
        return;
    }

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array( // credientials for newley created user
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>false
    );
    $signon = wp_signon($creds); //sign in the new user

    if ($signon)
        $ajax_handler->add_response_data( 'redirect_url', get_home_url() ); // optinal - if sign on succsfully - redierct the user to the home page
    else 
        $ajax_handler->add_response_data( 'redirect_url', get_home_url() . "#FAILED" ); // optinal - if sign on succsfully - redierct the user to the home page

}

function randomPassword($length) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 
}
