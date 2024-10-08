<?php

// get all brands
add_action('wp_ajax_inventory_settings_save', 'inventory_settings_save');
add_action('wp_ajax_nopriv_inventory_settings_save', 'inventory_settings_save');

function inventory_settings_save()
{
    $api_url    = isset($_POST['data']) ? $_POST['data']['url'] : '';
    $user_name  = isset($_POST['data']) ? $_POST['data']['user_name'] : '';
    $password   = isset($_POST['data']) ? $_POST['data']['password'] : '';
    $list_price   = isset($_POST['data']) ? $_POST['data']['list_price'] : false;

    update_option('api_url', $api_url);
    update_option('api_user_name', $user_name);
    update_option('api_password', $password);
    if ($list_price) {
        update_option('list_price', $list_price);
    }

    // Data to be sent in the POST request
    $data = array(
        'UserName' => $user_name,
        'Password' =>  $password
    );

    $ch = curl_init($api_url . '/Authentication/token');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    update_option('api_token', $response_data['Token']);
    curl_close($ch);
    $response = array(
        'success' => true,
        'message' => 'settings saved successfully',
        'token' => $response_data,
    );
    wp_send_json($response);
    wp_die();
}

// get all brands
add_action('wp_ajax_inventory_token_ganarate', 'inventory_token_ganarate');
add_action('wp_ajax_nopriv_inventory_token_ganarate', 'inventory_token_ganarate');

//token ganarate
function inventory_token_ganarate()
{

    $api_url    = get_option('api_url');
    $user_name  = get_option('api_user_name');
    $password   = get_option('api_password');

    // Data to be sent in the POST request
    $data = array(
        'UserName' => $user_name,
        'Password' =>  $password
    );

    // Initialize cURL
    $ch = curl_init($api_url . '/token');

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return false;
    } else {
        $response_data = json_decode($response, true);
        update_option('api_token', $response_data['Token']);
        update_option('token_date', $response_data['Token']);
        return $response_data['Token'];
    }
    curl_close($ch);
}

//token validate
function inventory_token_validator()
{
    $api_url    = get_option('api_url');
    $api_token  = get_option('api_token');
    if ($api_token == '') {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url . '/Authentication/validateToken?token=' . $api_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $response_data = json_decode($response, true);
        if ($response_data['IsValid']) {
            return $api_token;
        } else {
            return inventory_token_ganarate();
        }
    } else {
        return inventory_token_ganarate();
    }
}

//brnads sinc
function all_brands_sync()
{
    $api_url    = get_option('api_url');
    $all_brands = get_option('brands');

    if ($all_brands) {
        return $all_brands;
    } else {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url . '/Products/brands?ProductGroup=Any&MinQuantity=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);
        update_option('brands', $response_data);
        return $response_data;
    }
}
