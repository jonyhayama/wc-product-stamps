<?php
/*
Plugin Name: WooCommerce Product Stamps
Plugin URI: 
Description: WooCommerce Product Stamps
Version: 0.0.2
Author: Jony Hayama
Author URI: https://jony.dev
*/

define( 'WC_PRODUCT_STAMPS_PLUGIN_FILE', __FILE__ );
define( 'WC_PRODUCT_STAMPS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_PRODUCT_STAMPS_PLUGIN_URL', plugins_url('', __FILE__ ) );
define( 'WC_PRODUCT_STAMPS_ASSETS_URL', WC_PRODUCT_STAMPS_PLUGIN_URL . '/app/assets' );
define( 'WC_PRODUCT_STAMPS_APP_PATH', WC_PRODUCT_STAMPS_DIR_PATH . 'app' . DIRECTORY_SEPARATOR );

require_once( WC_PRODUCT_STAMPS_APP_PATH . 'application.class.php' );

function wc_product_stamps( $module = '' ){
	static $_wProductStamps_obj = null;
	if( !$_wProductStamps_obj ){
		$_wProductStamps_obj = new wcProductStamps();
	} 
	if( $module ){
		return $_wProductStamps_obj->getModule( $module );
	}
	return $_wProductStamps_obj;
}
wc_product_stamps();


require 'lib/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonyhayama/wc-product-stamps',
	__FILE__,
	'wc-product-stamps'
);

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('production');