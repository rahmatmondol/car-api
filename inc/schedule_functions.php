<?php

// Define a custom every minute schedule
add_filter('cron_schedules', 'add_every_minute_schedule');

function add_every_minute_schedule($schedules)
{
    $schedules['every_minute'] = array(
        'interval' => 60, // 60 seconds = 1 minute
        'display'  => __('Every Minute')
    );
    return $schedules;
}

// Schedule an event to run every minute
add_action('wp', 'my_custom_schedule');

function my_custom_schedule()
{
    // Check if the event is already scheduled
    if (!wp_next_scheduled('my_custom_event')) {
        // Schedule the event to run every minute
        wp_schedule_event(time(), 'every_minute', 'my_custom_event');
    }
}

// Hook to run your custom function when the scheduled event is triggered
add_action('my_custom_event', 'my_custom_function');

function my_custom_function()
{
    // update_option('inventory_per_page', 2);

    $per_page   = get_option('inventory_per_page') == '' ? 1 : get_option('inventory_per_page');
    $page       = get_option('inventory_page') == '' ? 1 : get_option('inventory_page');

    $url = 'https://dropshop.com.bd/wp-json/wc/store/products?per_page=' . $per_page . '&page=' . $page;
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
        add_product($value);
    }

    $to = 'recipient@example.com';
    $subject = 'Test Email';
    $message = $value['name'] . ' product added';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send the email
    $success = wp_mail($to, $subject, $message, $headers);

    update_option('inventory_page',  $page + 1);
}


//add product
function add_product($value)
{
    $product = new WC_Product();
    // Set product data
    $product->set_name($value['name']);
    $product->set_regular_price(intval($value['prices']['price'] / 100));
    $product->set_short_description($value['short_description']);
    $product->set_description($value['description']);
    $product->set_sku($value['id']);
    $product->set_stock_status($value['add_to_cart']['maximum'] !== 9999 ? 'instock' : 'outofstock');
    $product->set_manage_stock(true);
    $product->set_stock_quantity($value['add_to_cart']['maximum']);
    // Save the product
    $product_id = $product->save();

    $attachment_ids = [];
    foreach ($value['images'] as $image) {
        $attachment_ids[] = upload_image_from_url($image['src']);
    }
    $product->set_image_id($attachment_ids[0]);
    $product->set_gallery_image_ids($attachment_ids);

    $product->save();

    // // Optional: Assign product to a category
    // $category_ids = array(23); // Change 23 to your category ID
    // wp_set_post_terms($product_id, $category_ids, 'product_cat');

    // // Optional: Set product tags
    // $tag_ids = array(45, 56); // Change 45, 56 to your tag IDs
    // wp_set_post_terms($product_id, $tag_ids, 'product_tag');

    // Optional: Set product images

}




/**
 * Upload image from URL
 *
 * @param string $image_url The URL of the image to upload.
 * @param int $post_id Optional. Post ID to attach the image to.
 * @return int|WP_Error The attachment ID on success, or a WP_Error object on failure.
 */
function upload_image_from_url($image_url, $post_id = 0)
{
    // Check if the URL is valid
    if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        return new WP_Error('invalid_image_url', 'Invalid image URL.');
    }

    // Download the image from the URL
    $image_data = file_get_contents($image_url);

    // Check if the image data was retrieved successfully
    if (!$image_data) {
        return new WP_Error('download_failed', 'Failed to download image from URL.');
    }

    // Get the filename from the URL
    $filename = basename($image_url);

    // Upload the image data to the WordPress uploads directory
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . '/';
    $new_image_path = $upload_path . $filename;

    // Check if the file already exists
    if (file_exists($new_image_path)) {
        return new WP_Error('file_exists', 'File already exists in uploads directory.');
    }

    // Write the image data to the uploads directory
    if (!file_put_contents($new_image_path, $image_data)) {
        return new WP_Error('write_failed', 'Failed to write image data to uploads directory.');
    }

    // Prepare the attachment data
    $attachment = array(
        'post_title'     => sanitize_file_name($filename),
        'post_mime_type' => wp_check_filetype($filename)['type'],
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    // Insert the attachment into the media library
    $attachment_id = wp_insert_attachment($attachment, $new_image_path, $post_id);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    // Generate attachment metadata and update the attachment
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $new_image_path);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    return $attachment_id;
}
