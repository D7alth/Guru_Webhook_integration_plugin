<?php
/**
 * Plugin Name: Guru WebHooks Integration
 * Plugin URI:
 * Description: Create a custom route to handle guru webhook, create users by webhook events
 * Version: 1.0.7.1
 * Author: Alberth henrique
 * Author URI: https://github.com/D7alth/
 */

if(!defined('ABSPATH')){
    die();
}

add_action( 'rest_api_init', 'create_guru_user_endpoint' );
function create_guru_user_endpoint() {
    register_rest_route( 'webhook/guru/v1', '/create', 
    array(
            'methods' => 'POST',
            'callback' => 'create_guru_user',
        )   
    );
}

function create_guru_user( $request ) {
    $data = array();
    $params = $request->get_params();
    // split params into wp create users params   
    $name = $params['subscriber']['name'];
    $email = $params['subscriber']['email'];
    //$pass = $email;

    $pass = wp_generate_password( 12, true );

    // santize fields
    $name_sanitize = sanitize_text_field($name);
    $email_sanitize = sanitize_email($email);

    $name_sanitize .= ' - ' . rand(1, 9999);

    // create user
    $new_user_id = wp_create_user($name_sanitize, $email_sanitize, $email_sanitize);
    
    if(is_wp_error( $new_user_id )){
         
        $data['status'] = 'ERROR';
        
        $data['message'] = "Error when trying to create user : " . $new_user_id->get_error_message(); 
        http_response_code(500); 
        return $data;
    }else{
        
        $data['received_data'] = array(
            'name' => $name_sanitize, 
            'email' => $email_sanitize
            );
            
        $data['message'] = "User successfully registered! : " . $new_user_id;
        http_response_code(201);
        return $data;
    }
}

add_action('rest_api_init', 'delete_guru_user_endpoint');
function delete_guru_user_endpoint() {
    register_rest_route('webhook/guru/v1', '/delete', 
    array(
            'methods' => 'DELETE',
            'callback' => 'delete_guru_user'
        )
    );
}

function delete_guru_user( $request ) {
    $data = array();
    $params = $request->get_params();
    $email  = $params['contact']['email'];    

    $id = get_user_by( 'email',  $email);

    $delete_result = wp_delete_user( $id->ID, null );
    
    if($delete_result == true){
        $data['status'] = 'OK';
        $data['message'] = "User successfully deleted! : ". $id;
    }else{
        $data['status'] = 'ERROR';
        $data['message'] = "Error when trying to delete user: ". $id;
    }

}
?>