<?php
// Add a top-level menu page
function brand_menu_page()
{
    add_menu_page(
        'Inventory',
        'Inventory',
        'manage_options',
        'inventory-Management',
        'product_inventory_page_content',
        'dashicons-store',
        30
    );
    add_submenu_page(
        'inventory-Management',
        'Brands',
        'Brands',
        'manage_options',
        'Brands',
        'brand_page_content'
    );

    add_submenu_page(
        'inventory-Management',
        'model-years',
        'Model Years',
        'manage_options', // Capability
        'model-years', // Menu slug
        'model_years_page_content' // Function to display the page content
    );

    add_submenu_page(
        'inventory-Management',
        'models',
        'Models',
        'manage_options', // Capability
        'models', // Menu slug
        'model_page_content' // Function to display the page content
    );

    add_submenu_page(
        'inventory-Management',
        'types',
        'Types',
        'manage_options', // Capability
        'types', // Menu slug
        'type_page_content' // Function to display the page content
    );

    add_submenu_page(
        'inventory-Management',
        'Width',
        'Width',
        'manage_options', // Capability
        'width', // Menu slug
        'width_page_content' // Function to display the page content
    );

    add_submenu_page(
        'inventory-Management',
        'Depth',
        'Depth',
        'manage_options', // Capability
        'depth', // Menu slug
        'depth_page_content' // Function to display the page content
    );
    add_submenu_page(
        'inventory-Management',
        'Inch',
        'Inch',
        'manage_options', // Capability
        'inch', // Menu slug
        'inch_page_content' // Function to display the page content
    );

    add_submenu_page(
        'inventory-Management',
        'settings',
        'settings',
        'manage_options', // Capability
        'settings', // Menu slug
        'api_settings_page_content' // Function to display the page content
    );
}
add_action('admin_menu', 'brand_menu_page');
