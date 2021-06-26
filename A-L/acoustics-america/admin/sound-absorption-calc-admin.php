<?php
/*
@since ver: 1.0.0
Author: Tradesouthwest
Author URI: http://tradesouthwest.com
@package sound_absorption_calc
@subpackage admin/sound-absorption-calc-admin
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include 'sound-absorption-adminpage.php'; 
include 'sound-absorption-calc-metabox.php'; 
add_action( 'add_meta_boxes', 'sound_absorption_calc_product_metabox' );
add_action( 'save_post', 'sound_absorption_calc_productmeta_save' );