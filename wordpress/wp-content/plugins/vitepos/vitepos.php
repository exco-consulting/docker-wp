<?php
/**
 * Plugin Name: Vitepos Pro
 * Plugin URI: http://appsbd.com
 * Description: It's a  Point of Sale plugin for Woocommerce , so fast and easy.
 * Version: 2.0.2
 * Author: appsbd
 * Author URI: http://www.appsbd.com
 * Text Domain: vitepos
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * wc require:3.2.0
 * @package Vitepos
 */




require_once 'vitepos/helper/plugin-helper.php';
require_once 'vitepos/core/class-vitepos.php';
require_once 'vitepos/libs/class-vitepos-dependency.php';
use VitePos\Core\VitePos;

if ( true === \VitePos\Libs\Vitepos_Dependency::is_met_requirements(__FILE__ ) ) {




	$vtpos = new VitePos( __FILE__ );


	$vtpos->start_plugin();
}


