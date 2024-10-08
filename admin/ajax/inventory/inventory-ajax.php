<?php

// get all brands
add_action('wp_ajax_inventory_get_products_form_api', 'inventory_get_products_form_api');
add_action('wp_ajax_nopriv_inventory_get_products_form_api', 'inventory_get_products_form_api');

function inventory_get_products_form_api()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $PageSize = isset($_POST['PageSize']) ? $_POST['PageSize'] : 1000;
    $ProductGroup = isset($_POST['ProductGroup']) ? $_POST['ProductGroup'] : 'Tire';
    $refrash = isset($_POST['refrash']) ? $_POST['refrash'] : false;

    // Get API URL and token from WordPress options

    if ($refrash == "true") {
        $result = get_all_api_products($PageSize, $ProductGroup);
    } else {
        if ($ProductGroup == 'Tire') {
            $result = get_option('api_tire_products');
        } elseif ($ProductGroup == 'Wheel') {
            $result = get_option('api_wheel_products');
        } else {
            $result = get_option('api_accessory_products');
        }
    }

    $in_products = get_option('in_products');

    $response = array(
        'success' => true,
        'result' => $result,
        'in_products' => $in_products,
    );
    wp_send_json($response);
    wp_die();
}

// save products
add_action('wp_ajax_save_products', 'save_products');
add_action('wp_ajax_nopriv_save_products', 'save_products');

function save_products()
{
    $products = isset($_POST['products']) ? $_POST['products'] : '';
    $in_products = [];
    $sku = '';

    foreach ($products as $product_data) {

        //ceate new or existing category
        $category = create_product_category($product_data['ItemGroup']);

        //increase price
        $percentageIncrease = get_option('list_price');
        $price = $product_data['Price'];
        $increaseAmount = ($price * $percentageIncrease) / 100;
        $newPrice = $price + $increaseAmount;

        // Create or update the product
        $product = new WC_Product_Simple();
        $product->set_name($product_data['Description']);
        $product->set_sku($product_data['ProductNumber']);
        $product->set_regular_price($newPrice);
        $product->set_description($product_data['Description']);
        $product->set_short_description($product_data['Description']);
        $product->set_category_ids([$category->term_id]);
        $in_products[] = $product_data['ProductNumber'];
        $sku = $product_data['ProductNumber'];

        if (!empty($product_data['ImageUrl'])) {
            $image_id = attachment_url_to_postid($product_data['ImageUrl']);
            if ($image_id) {
                $product->set_image_id($image_id);
            } else {
                $image_id = upload_image_from_url($product_data['ImageUrl']);
                if (!is_wp_error($image_id)) {
                    $product->set_image_id($image_id);
                }
            }
        }

        // Save the product
        $product_id = $product->save();

        if ($product_id) {
            update_post_meta($product_id, 'Price', $product_data['Price']);
            update_post_meta($product_id, 'PriceClass', $product_data['PriceClass']);
            update_post_meta($product_id, 'ProductNumber', $product_data['ProductNumber']);
            update_post_meta($product_id, 'Brand', $product_data['Brand']);
            update_post_meta($product_id, 'DOT', $product_data['DOT']);
            update_post_meta($product_id, 'Decibel', $product_data['Decibel']);
            update_post_meta($product_id, 'Depth', $product_data['Depth']);
            update_post_meta($product_id, 'Inch', $product_data['Inch']);
            update_post_meta($product_id, 'Width', $product_data['Width']);
            update_post_meta($product_id, 'ExtraLoad', $product_data['ExtraLoad']);
            update_post_meta($product_id, 'FuelRating', $product_data['FuelRating']);
            update_post_meta($product_id, 'GripRating', $product_data['GripRating']);
            update_post_meta($product_id, 'IceTire', $product_data['IceTire']);
            update_post_meta($product_id, 'ItemGroup', $product_data['ItemGroup']);
            update_post_meta($product_id, 'ItemSubGroup', $product_data['ItemSubGroup']);
            update_post_meta($product_id, 'ItemType', $product_data['ItemType']);
            update_post_meta($product_id, 'LoadIndex', $product_data['LoadIndex']);
            update_post_meta($product_id, 'ManufacturerItemNumber', $product_data['ManufacturerItemNumber']);
            update_post_meta($product_id, 'Material', $product_data['Material']);
            update_post_meta($product_id, 'NoiseRating', $product_data['NoiseRating']);
            update_post_meta($product_id, 'OEMark', $product_data['OEMark']);
            update_post_meta($product_id, 'Pattern', $product_data['Pattern']);
            update_post_meta($product_id, 'PriceClass', $product_data['PriceClass']);
            update_post_meta($product_id, 'RimProtection', $product_data['RimProtection']);
            update_post_meta($product_id, 'RunFlat', $product_data['RunFlat']);
            update_post_meta($product_id, 'Season', $product_data['Season']);
            update_post_meta($product_id, 'SevereSnowTire', $product_data['SevereSnowTire']);
            update_post_meta($product_id, 'ShowOnFirstPage', $product_data['ShowOnFirstPage']);
            update_post_meta($product_id, 'SilentTire', $product_data['SilentTire']);
            update_post_meta($product_id, 'SpeedIndex', $product_data['SpeedIndex']);
            update_post_meta($product_id, 'StuddedOrFriction', $product_data['StuddedOrFriction']);
            update_post_meta($product_id, 'TireType', $product_data['TireType']);
            update_post_meta($product_id, 'Warehouse', $product_data['Warehouse']);
        }
    }


    $res_in_pro = [];
    $in = get_option('in_products');
    if (!$in) {
        update_option('in_products', serialize($in_products));
        $res_in_pro = serialize($in_products);
    } else {
        $old = unserialize($in);
        $combined_product_ids = array_merge($old, $in_products);
        update_option('in_products', serialize($combined_product_ids));
        $res_in_pro = serialize($combined_product_ids);
    }

    $response = array(
        'success' => true,
        'result' => 'product import successful',
        'in_products' => $res_in_pro,
        'sku' => $sku,
    );
    wp_send_json($response);
    wp_die();
}

// update products
add_action('wp_ajax_update_products', 'update_products');
add_action('wp_ajax_nopriv_update_products', 'update_products');

function update_products()
{
    $products = isset($_POST['products']) ? $_POST['products'] : '';
    $sku = '';
    $product_id = '';
    foreach ($products as $product_data) {
        //get product id by sku/productNumber
        $product_id = wc_get_product_id_by_sku($product_data['ProductNumber']);
        if ($product_id) {
            $product = wc_get_product($product_id);
            //increase price
            $percentageIncrease = get_option('list_price');
            $price = $product_data['Price'];
            $increaseAmount = ($price * $percentageIncrease) / 100;
            $newPrice = $price + $increaseAmount;

            // Create or update the product
            $product->set_name($product_data['Description']);
            $product->set_sku($product_data['ProductNumber']);
            $product->set_regular_price($newPrice);
            $product->set_description($product_data['Description']);
            $product->set_short_description($product_data['Description']);
            $in_products[] = $product_data['ProductNumber'];
            $sku = $product_data['ProductNumber'];

            if (!empty($product_data['ImageUrl'])) {
                $image_id = attachment_url_to_postid($product_data['ImageUrl']);
                if ($image_id) {
                    $product->set_image_id($image_id);
                } else {
                    $image_id = upload_image_from_url($product_data['ImageUrl']);
                    if (!is_wp_error($image_id)) {
                        $product->set_image_id($image_id);
                    }
                }
            }

            // Save the product
            $product_id = $product->save();

            if ($product_id) {
                update_post_meta($product_id, 'Price', $product_data['Price']);
                update_post_meta($product_id, 'PriceClass', $product_data['PriceClass']);
                update_post_meta($product_id, 'ProductNumber', $product_data['ProductNumber']);
                update_post_meta($product_id, 'Brand', $product_data['Brand']);
                update_post_meta($product_id, 'DOT', $product_data['DOT']);
                update_post_meta($product_id, 'Decibel', $product_data['Decibel']);
                update_post_meta($product_id, 'Depth', $product_data['Depth']);
                update_post_meta($product_id, 'Inch', $product_data['Inch']);
                update_post_meta($product_id, 'Width', $product_data['Width']);
                update_post_meta($product_id, 'ExtraLoad', $product_data['ExtraLoad']);
                update_post_meta($product_id, 'FuelRating', $product_data['FuelRating']);
                update_post_meta($product_id, 'GripRating', $product_data['GripRating']);
                update_post_meta($product_id, 'IceTire', $product_data['IceTire']);
                update_post_meta($product_id, 'ItemGroup', $product_data['ItemGroup']);
                update_post_meta($product_id, 'ItemSubGroup', $product_data['ItemSubGroup']);
                update_post_meta($product_id, 'ItemType', $product_data['ItemType']);
                update_post_meta($product_id, 'LoadIndex', $product_data['LoadIndex']);
                update_post_meta($product_id, 'ManufacturerItemNumber', $product_data['ManufacturerItemNumber']);
                update_post_meta($product_id, 'Material', $product_data['Material']);
                update_post_meta($product_id, 'NoiseRating', $product_data['NoiseRating']);
                update_post_meta($product_id, 'OEMark', $product_data['OEMark']);
                update_post_meta($product_id, 'Pattern', $product_data['Pattern']);
                update_post_meta($product_id, 'PriceClass', $product_data['PriceClass']);
                update_post_meta($product_id, 'RimProtection', $product_data['RimProtection']);
                update_post_meta($product_id, 'RunFlat', $product_data['RunFlat']);
                update_post_meta($product_id, 'Season', $product_data['Season']);
                update_post_meta($product_id, 'SevereSnowTire', $product_data['SevereSnowTire']);
                update_post_meta($product_id, 'ShowOnFirstPage', $product_data['ShowOnFirstPage']);
                update_post_meta($product_id, 'SilentTire', $product_data['SilentTire']);
                update_post_meta($product_id, 'SpeedIndex', $product_data['SpeedIndex']);
                update_post_meta($product_id, 'StuddedOrFriction', $product_data['StuddedOrFriction']);
                update_post_meta($product_id, 'TireType', $product_data['TireType']);
                update_post_meta($product_id, 'Warehouse', $product_data['Warehouse']);
            }
        }
    }

    $response = array(
        'success' => true,
        'result' => 'product update successful',
        'sku' => $sku,
    );
    wp_send_json($response);
    wp_die();
}


// get all api products
function get_all_api_products($PageSize, $ProductGroup)
{
    $api_url = get_option('api_url');
    $api_token = get_option('api_token');

    // Initialize cURL
    $curl = curl_init();
    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url . "/Products?PageIndex=1&PageSize=$PageSize&ProductGroup=$ProductGroup",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_token,
        ),
    ));
    // Execute the request
    $response = curl_exec($curl);
    // Decode the JSON response body
    $result = json_decode($response, true);

    if ($ProductGroup == 'Tire') {
        update_option('api_tire_products', $result);
    } elseif ($ProductGroup == 'Wheel') {
        update_option('api_wheel_products', $result);
    } else {
        update_option('api_accessory_products', $result);
    }

    return $result;
}


// add image
function upload_image_from_url($image_url)
{
    $image_name = basename($image_url);
    $upload_dir = wp_upload_dir();

    $image_data = file_get_contents($image_url);
    if ($image_data === false) {
        return new WP_Error('image_download_failed', 'Failed to download image from URL.');
    }

    $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
    $filename = basename($unique_file_name);

    if (!wp_mkdir_p($upload_dir['path'])) {
        return new WP_Error('upload_dir_creation_failed', 'Failed to create directory.');
    }

    $file_path = $upload_dir['path'] . '/' . $filename;
    file_put_contents($file_path, $image_data);

    $file_type = wp_check_filetype($filename, null);
    if (!$file_type['type']) {
        return new WP_Error('invalid_file_type', 'Invalid file type.');
    }

    $attachment = [
        'post_mime_type' => $file_type['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $file_path);
    if (is_wp_error($attach_id)) {
        return $attach_id;
    }

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}
//search categpory or create new on
function create_product_category($category_name)
{
    // Check if the category already exists
    $existing_category = get_term_by('name', $category_name, 'product_cat');

    // If the category doesn't exist, create a new one
    if (!$existing_category) {
        // Prepare arguments for the new category
        $args = array(
            'description' => '', // Description of the category
            'slug'        => sanitize_title($category_name),
        );

        // Insert the new category
        $result = wp_insert_term($category_name, 'product_cat', $args);

        // Check if category insertion was successful
        if (!is_wp_error($result)) {
            // Category inserted successfully, return the category object
            return get_term($result['term_id'], 'product_cat');
        } else {
            // Category insertion failed, return null or handle the error as needed
            return null;
        }
    } else {
        // Category already exists, return the existing category object
        return $existing_category;
    }
}


add_action('before_delete_post', 'remove_product_id_from_option', 99, 2);
function remove_product_id_from_option($postid, $post)
{
    // Check if the deleted post is a product
    if (get_post_type($postid) === 'product') {
        $product_id = $postid;
        $stored_ids_serialized = get_option('in_products');
        $ProductNumber = get_post_meta($product_id, 'ProductNumber', true);
        $stored_ids = unserialize($stored_ids_serialized);
        $key = array_search($ProductNumber, $stored_ids);

        if ($key !== false) {
            unset($stored_ids[$key]);
            $updated_ids_serialized = serialize($stored_ids);
            update_option('in_products', $updated_ids_serialized);
        }
    }
}
