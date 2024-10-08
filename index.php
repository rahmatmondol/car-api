<?php
/*
Plugin Name: car api
Description: This plugin is responsible for all functions
Version: 1.0
Author: Rahamt Mondol
Author URI: https://www.fiverr.com/rahmatmondol1
*/

// include all enqueued files
include_once('inc/enqueue_files.php');
include_once('inc/manus.php');

// include files management
include_once('admin/settings.php');
include_once('admin/brand-management.php');
include_once('admin/inventory-managment.php');
include_once('admin/model-years-management.php');
include_once('admin/model-management.php');
include_once('admin/type-management.php');
include_once('admin/dimention/depth.php');
include_once('admin/dimention/inch.php');
include_once('admin/dimention/width.php');

// include ajax request
include_once('admin/ajax/model-ajax.php');
include_once('admin/ajax/model-year-ajax.php');
include_once('admin/ajax/brand-ajax.php');
include_once('admin/ajax/type-ajax.php');
include_once('admin/ajax/depth-ajax.php');
include_once('admin/ajax/inch-ajax.php');
include_once('admin/ajax/width-ajax.php');
include_once('admin/ajax/inventory/settings.php');
include_once('admin/ajax/inventory/inventory-ajax.php');
