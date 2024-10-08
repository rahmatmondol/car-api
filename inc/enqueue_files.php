<?php

function enqueue_vuejs()
{
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js');
    wp_enqueue_style('boostrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('boostrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js');
}
add_action('admin_enqueue_scripts', 'enqueue_vuejs');

