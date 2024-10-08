<?php
function front_register_open_apis()
{
    register_rest_route('open/v1', '/products/', array(
        'methods' => 'GET',
        'callback' => 'front_get_products',
    ));

    register_rest_route('open/v1', '/category/', array(
        'methods' => 'GET',
        'callback' => 'bulk_category',
    ));
}

add_action('rest_api_init', 'front_register_open_apis');

//get all products by category id
function front_get_products($data)
{
    if (isset($data['category'])) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => isset($data['per_page']) ? $data['per_page'] : -1,
            'tax_query'      => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $data['category'],
                    'operator' => 'IN',
                ]
            ]
        );
    } else {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => isset($data['per_page']) ? $data['per_page'] : -1,
        );
    }
    $products_query = new WP_Query($args);

    // Initialize an array to store product data
    $products_data = array();

    // Loop through each product
    while ($products_query->have_posts()) {
        $products_query->the_post();

        $product_id     = get_the_ID();
        $product_name   = get_the_title();
        $sale_price     = wc_get_product($product_id)->get_sale_price();
        $regular_price  = wc_get_product($product_id)->get_regular_price();
        $average_rating = wc_get_product($product_id)->get_average_rating();

        $thumnail       = get_the_post_thumbnail_url($product_id, [300, 300]);
        $slug           = get_post_field('post_name', $product_id);

        $products_data[] = array(
            'id'    => $product_id,
            'name'  => $product_name,
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            'average_rating' => $average_rating,
            'image' => $thumnail,
            'slug' => $slug,
        );
    }
    $info = array(
        'total' => $products_query->found_posts,
        'pages' => $products_query->max_num_pages,
    );
    // Reset post data
    wp_reset_postdata();

    // Return product data in JSON format
    wp_send_json(['data' => $products_data, 'info' => $info]);
}

function bulk_category($data)
{
    $url = 'https://dropshop.com.bd/wp-json/wc/store/products/categories';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Cookie: wordpress_logged_in_10817d619b545fe58414874e7acf0c9e=royalstyle24%7C1734383398%7CJK7nIV5grQPlEKAl4Vq9zurHxqAwzUPQDrhIcLfFt1m%7C6da8cd748acc48f36a00f62c31a0f5ff40ec45b1c56cc67e6e9f4cdda8f9a0cc;'
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    foreach ($data as $value) {
        $category = wp_insert_term(
            $value['name'],
            'product_cat',
            [
                'slug' => $value['slug'],
                'description' => $value['description'],
            ]
        );
    }


    return 'ok';
}


