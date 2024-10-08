<?php

// get all brands
add_action('wp_ajax_get_all_brands', 'get_all_brands');
add_action('wp_ajax_nopriv_get_all_brands', 'get_all_brands');

function get_all_brands()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $args = array(
        'post_type'      => 'brands',
        'posts_per_page' => -1,
    );

    $response = new WP_Query($args);

    if ($response->posts) {
        $result = '';
        foreach ($response->posts as $post) {
            $result .= '<tr>
                <td><div class="form-check mt-2">
                <input class="form-check-input all_check" type="checkbox" value="' . $post->ID . '" id="' . $post->ID . '">
                <label class="form-check-label" for="' . $post->ID . '"></label></div></td>
                <td>' . $post->ID . '</td>
                <td>' . $post->post_title . '</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
                        <button type="button" data-id="' . $post->ID . '" data-name="' . $post->post_title . '" id="brand-edit-button" class="btn btn-outline-primary">Edit</button>
                        <button type="button" data-id="' . $post->ID . '" id="brand-delete-button" class="btn btn-outline-danger">Delete</button>
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

// get all brands
add_action('wp_ajax_get_all_brands_options', 'get_all_brands_options');
add_action('wp_ajax_nopriv_get_all_brands_options', 'get_all_brands_options');

function get_all_brands_options()
{
    $args = array(
        'post_type'      => 'brands',
        'posts_per_page' => -1,
    );

    $response = new WP_Query($args);

    if ($response->posts) {
        $result = '<option selected="true" value="">All Brands</option>';
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


// add brands
add_action('wp_ajax_brand_add', 'add_brand');
add_action('wp_ajax_nopriv_brand_add', 'add_brand');


function add_brand()
{
    $name = isset($_POST['data']) ? $_POST['data']['name'] : '';

    if (!empty($_FILES['json_file']['tmp_name'])) {
        $file_path = $_FILES['json_file']['tmp_name'];

        $file_contents = file_get_contents($file_path);
        $data = json_decode($file_contents, true);

        foreach ($data as $key => $value) {
            $post_data = array(
                'post_title'    => $value['name'],
                'post_status'   => 'publish',
                'post_type'     => 'brands',
            );

            $post_id = wp_insert_post($post_data);
        }

        $response = array(
            'success' => true,
            'message' => 'Brand inserted successfully',
        );
    } else {
        $post_data = array(
            'post_title'    => $name,
            'post_status'   => 'publish',
            'post_type'     => 'brands',
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            $response = array(
                'success' => true,
                'message' => 'Brand inserted successfully',
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error inserting brand: ' . $post_id->get_error_message(),
            );
        }
    }

    wp_send_json($response);
    wp_die();
}

// update brands
add_action('wp_ajax_update_brand', 'update_brand');
add_action('wp_ajax_nopriv_update_brand', 'update_brand');

function update_brand()
{
    $name = isset($_POST['data']) ? $_POST['data']['name'] : '';
    $id = isset($_POST['data']) ? $_POST['data']['id'] : '';
    $post_data = array(
        'ID'           => $id,
        'post_title'   => $name,
    );

    // Update the post
    $updated_post_id = wp_update_post($post_data);

    if (!is_wp_error($updated_post_id)) {
        $response = array(
            'success' => true,
            'message' => 'Brand updated successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting brand: ' . $updated_post_id->get_error_message(),
        );
    }
    wp_send_json($response);
    wp_die();
}



// delete brands
add_action('wp_ajax_delete_brand', 'delete_brand');
add_action('wp_ajax_nopriv_update_brand', 'delete_brand');

function delete_brand()
{
    $ids = isset($_POST['ids']) ? $_POST['ids'] : '';

    foreach ($ids as $id) {
        $deleted = wp_delete_post($id);
    }

    if (!is_wp_error($deleted)) {
        $response = array(
            'success' => true,
            'message' => 'Brand deleted successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error inserting brand: '
        );
    }
    wp_send_json($response);
    wp_die();
}
