<?php

// get all types
add_action('wp_ajax_get_all_types', 'get_all_types');
add_action('wp_ajax_nopriv_get_all_types', 'get_all_types');

function get_all_types()
{
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $args = array(
        'post_type'      => 'types',
        'posts_per_page' => -1,
        'meta_key' => 'model_id',
        'meta_value' => $id,
    );

    $response = new WP_Query($args);

    if ($response->posts) {
        $result = '';
        foreach ($response->posts as $post) {
            $barnd      = get_post_meta($post->ID, 'model_name', true);
            $id         = get_post_meta($post->ID, 'model_id', true);
            $depth      = get_post_meta($post->ID, 'depth_name', true);
            $depth_id   = get_post_meta($post->ID, 'depth_id', true);
            $inch       = get_post_meta($post->ID, 'inch_name', true);
            $inch_id    = get_post_meta($post->ID, 'inch_id', true);
            $width      = get_post_meta($post->ID, 'width_name', true);
            $width_id   = get_post_meta($post->ID, 'width_id', true);

            $result .= '<tr>
                <td>' . $post->ID . '</td>
                <td>' . $post->post_title . '</td>
                <td>' . $barnd . '</td>
                <td>' . $width . '</td>
                <td>' . $depth . '</td>
                <td>' . $inch . '</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
                        <button 
                        type="button" 
                        data-model="' . $id . '" 
                        data-width="' . $width_id . '" 
                        data-depth="' . $depth_id . '" 
                        data-inch="' . $inch_id . '" 
                        data-id="' . $post->ID . '" 
                        data-name="' . $post->post_title . '" 
                        id="type-edit-button" 
                        class="btn btn-outline-primary">Edit</button>
                        <button type="button" data-id="' . $post->ID . '" id="type-delete-button" class="btn btn-outline-danger">Delete</button>
                    </div>
                </td>
            </tr>';
        }
        $response = array(
            'success' => true,
            'result' => $result,
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Not Found',
        );
    }
    wp_send_json($response);
    wp_die();
}


// add types_models
add_action('wp_ajax_add_type', 'add_type');
add_action('wp_ajax_nopriv_add_type', 'add_type');

function add_type()
{
    $name       = isset($_POST['data']) ? $_POST['data']['name'] : '';
    $model_name = isset($_POST['data']) ? $_POST['data']['model_name'] : '';
    $model_id   = isset($_POST['data']) ? $_POST['data']['model_id'] : '';
    $depth_name = isset($_POST['data']) ? $_POST['data']['depth_name'] : '';
    $depth_id   = isset($_POST['data']) ? $_POST['data']['depth_id'] : '';
    $width_name = isset($_POST['data']) ? $_POST['data']['width_name'] : '';
    $width_id   = isset($_POST['data']) ? $_POST['data']['width_id'] : '';
    $inch_name  = isset($_POST['data']) ? $_POST['data']['inch_name'] : '';
    $inch_id    = isset($_POST['data']) ? $_POST['data']['inch_id'] : '';


    $post_data = array(
        'post_title'    => $name,
        'post_status'   => 'publish',
        'post_type'     => 'types',
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    if (!is_wp_error($post_id)) {

        update_post_meta($post_id, 'model_name', $model_name);
        update_post_meta($post_id, 'model_id', $model_id);
        update_post_meta($post_id, 'depth_name', $depth_name);
        update_post_meta($post_id, 'depth_id', $depth_id);
        update_post_meta($post_id, 'width_name', $width_name);
        update_post_meta($post_id, 'width_id', $width_id);
        update_post_meta($post_id, 'inch_name', $inch_name);
        update_post_meta($post_id, 'inch_id', $inch_id);

        $response = array(
            'success' => true,
            'message' => 'Type inserted successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting Type: ' . $post_id->get_error_message(),
        );
    }
    wp_send_json($response);
    wp_die();
}

// update types_models
add_action('wp_ajax_update_type', 'update_type');
add_action('wp_ajax_nopriv_update_type', 'update_type');

function update_type()
{
    $name   = isset($_POST['data']) ? $_POST['data']['name'] : '';
    $id         = isset($_POST['data']) ? $_POST['data']['id'] : '';
    $model_name = isset($_POST['data']) ? $_POST['data']['model_name'] : '';
    $model_id   = isset($_POST['data']) ? $_POST['data']['model_id'] : '';
    $depth_name = isset($_POST['data']) ? $_POST['data']['depth_name'] : '';
    $depth_id   = isset($_POST['data']) ? $_POST['data']['depth_id'] : '';
    $width_name = isset($_POST['data']) ? $_POST['data']['width_name'] : '';
    $width_id   = isset($_POST['data']) ? $_POST['data']['width_id'] : '';
    $inch_name  = isset($_POST['data']) ? $_POST['data']['inch_name'] : '';
    $inch_id    = isset($_POST['data']) ? $_POST['data']['inch_id'] : '';

    $post_data = array(
        'ID'           => $id,
        'post_title'   => $name,
    );

    // Update the post
    $post_id = wp_update_post($post_data);

    if (!is_wp_error($post_id)) {

        update_post_meta($post_id, 'model_name', $model_name);
        update_post_meta($post_id, 'model_id', $model_id);
        update_post_meta($post_id, 'depth_name', $depth_name);
        update_post_meta($post_id, 'depth_id', $depth_id);
        update_post_meta($post_id, 'width_name', $width_name);
        update_post_meta($post_id, 'width_id', $width_id);
        update_post_meta($post_id, 'inch_name', $inch_name);
        update_post_meta($post_id, 'inch_id', $inch_id);

        $response = array(
            'success' => true,
            'message' => 'Type updated successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting Type: ' . $post_id->get_error_message(),
        );
    }
    wp_send_json($response);
    wp_die();
}



// delete types_models
add_action('wp_ajax_delete_type', 'delete_type');
add_action('wp_ajax_nopriv_delete_model', 'delete_type');

function delete_type()
{
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $deleted = wp_delete_post($id);
    if (!is_wp_error($deleted)) {
        $response = array(
            'success' => true,
            'message' => 'model deleted successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting model: '
        );
    }
    wp_send_json($response);
    wp_die();
}
