<?php

// get all models_years
add_action('wp_ajax_get_all_models', 'get_all_models');
add_action('wp_ajax_nopriv_get_all_models', 'get_all_models');

function get_all_models()
{
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $args = array(
        'post_type'      => 'models',
        'posts_per_page' => -1,
        'meta_key' => 'year_id',
        'meta_value' => $id,
    );

    $response = new WP_Query($args);

    if ($response->posts) {
        $result = '';
        foreach ($response->posts as $post) {
            $year = get_post_meta($post->ID, 'year_name', true);
            $year_id = get_post_meta($post->ID, 'year_id', true);

            $brand_name = get_post_meta($post->ID, 'brand_name', true);
            $brand_id = get_post_meta($post->ID, 'brand_id', true);

            $result .= '<tr>
                <td>' . $post->ID . '</td>
                <td>' . $brand_name . '</td>
                <td>' . $year . '</td>
                <td>' . $post->post_title . '</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
                        <button type="button" data-brand="' . $brand_id . '" data-year="' . $year_id . '" data-id="' . $post->ID . '" data-name="' . $post->post_title . '" id="year-edit-button" class="btn btn-outline-primary">Edit</button>
                        <button type="button" data-id="' . $post->ID . '" id="year-delete-button" class="btn btn-outline-danger">Delete</button>
                    </div>
                </td>
            </tr>';
        }
        $big = 999999999; // need an unlikely integer
        $pagination_links = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, $paged),
            'total' => $response->max_num_pages,
            'type' => 'array',
            'prev_text' => __('  <span aria-hidden="true">&laquo;</span>'),
            'next_text' => __('<span aria-hidden="true">&raquo;</span>'),
        ));

        if (!empty($pagination_links)) {
            $pagination = '<ul class="pagination pt-2">';
            foreach ($pagination_links as $link) {
                $class = strpos($link, 'current') !== false ? 'active' : '';
                $pagination .= '<li class="page-item ' . $class . '">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            }
            $pagination .= '</ul>';
        }
        $response = array(
            'success' => true,
            'result' => $result,
            'pagination' => $pagination,
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

// get model options
add_action('wp_ajax_get_all_model_options', 'get_all_model_options');
add_action('wp_ajax_nopriv_get_all_model_options', 'get_all_model_options');

function get_all_model_options()
{
    $args = array(
        'post_type'      => 'models',
        'posts_per_page' => -1,
    );

    $response = new WP_Query($args);

    if ($response->posts) {
        $result = '<option selected="true" value="">All models</option>';
        foreach ($response->posts as $post) {
            $result .= '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
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


// add models
add_action('wp_ajax_add_model', 'add_model');
add_action('wp_ajax_nopriv_add_model', 'add_model');

function add_model()
{
    $name = isset($_POST['data']['name']) ? $_POST['data']['name'] : '';
    $year_name = isset($_POST['data']['year_name']) ? $_POST['data']['year_name'] : '';
    $year_id = isset($_POST['data']['year_id']) ? $_POST['data']['year_id'] : '';

    $brand_name = isset($_POST['data']['brand_name']) ? $_POST['data']['brand_name'] : '';
    $brand_id = isset($_POST['data']['brand_id']) ? $_POST['data']['brand_id'] : '';

    $post_data = array(
        'post_title'    => $name,
        'post_status'   => 'publish',
        'post_type'     => 'models',
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    if (!is_wp_error($post_id)) {

        update_post_meta($post_id, 'year_name', $year_name);
        update_post_meta($post_id, 'year_id', $year_id);

        update_post_meta($post_id, 'brand_name', $brand_name);
        update_post_meta($post_id, 'brand_id', $brand_id);

        $response = array(
            'success' => true,
            'message' => 'model inserted successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting model: ' . $post_id->get_error_message(),
        );
    }
    wp_send_json($response);
    wp_die();
}

// update models_years
add_action('wp_ajax_update_model', 'update_model');
add_action('wp_ajax_nopriv_update_model', 'update_model');

function update_model()
{
    $name = isset($_POST['data']) ? $_POST['data']['name'] : '';
    $id = isset($_POST['data']['id']) ? $_POST['data']['id'] : '';

    $year_name = isset($_POST['data']) ? $_POST['data']['year_name'] : '';
    $year_id = isset($_POST['data']) ? $_POST['data']['year_id'] : '';

    $brand_name = isset($_POST['data']['brand_name']) ? $_POST['data']['brand_name'] : '';
    $brand_id = isset($_POST['data']['brand_id']) ? $_POST['data']['brand_id'] : '';

    $post_data = array(
        'ID'           => $id,
        'post_title'   => $name,
    );

    // Update the post
    $post_id = wp_update_post($post_data);

    if (!is_wp_error($post_id)) {

        update_post_meta($post_id, 'year_name', $year_name);
        update_post_meta($post_id, 'year_id', $year_id);

        update_post_meta($post_id, 'brand_name', $brand_name);
        update_post_meta($post_id, 'brand_id', $brand_id);

        $response = array(
            'success' => true,
            'message' => 'model updated successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting model: ' . $post_id->get_error_message(),
        );
    }
    wp_send_json($response);
    wp_die();
}



// delete models_years
add_action('wp_ajax_delete_model', 'delete_model');
add_action('wp_ajax_nopriv_delete_model', 'delete_model');

function delete_model()
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
