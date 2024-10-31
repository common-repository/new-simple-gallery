<?php
/*
Plugin Name: New Simple Gallery
Plugin URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Description: Want to display images as an automatic slideshow that can also be explicitly played or paused by the user? then use this New Simple Gallery. <strong>In future back up your existing new simple gallery XML files before update this plugin.</strong> 
Author: Gopi Ramasamy
Version: 8.0
Author URI: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
Donate link: http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: new-simple-gallery
Domain Path: /languages
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb, $wp_version;
define("WP_NSG_TABLE", $wpdb->prefix . "new_simple_gallery");

if ( ! defined( 'WP_NSG_ADMIN_URL' ) )
	define( 'WP_NSG_ADMIN_URL', admin_url() . 'options-general.php?page=new-simple-gallery' );

add_shortcode( 'new-simple-gallery', 'nsg_show_filter_shortcode' );

function nsg_show_filter_shortcode( $atts )
{
	$nsg_pp = "";
	$nsg_package = "";
	$random = "YES";
	
	//[new-simple-gallery group="Group2" width="500" height="300" pause="2500" duration="500" cycles="0" random="YES"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}

	if (isset($atts['group'])) {
		$group 	= $atts['group'];
	}
	else {
		$group 	= "Group1";
	}
	
	if (isset($atts['width'])) {
		$width 	= $atts['width'];
		if(!is_numeric($width)){
			$width = 500;
		} 
	}
	else {
		$width 	= 500;
	}
	
	if (isset($atts['height'])) {
		$height 	= $atts['height'];
		if(!is_numeric($height)){
			$height = 300;
		} 
	}
	else {
		$height 	= 300;
	}
	
	if (isset($atts['pause'])) {
		$pause 	= $atts['pause'];
		if(!is_numeric($pause)){
			$pause = 2500;
		} 
	}
	else {
		$pause 	= 2500;
	}
	
	if (isset($atts['duration'])) {
		$duration 	= $atts['duration'];
		if(!is_numeric($duration)){
			$duration = 500;
		} 
	}
	else {
		$duration 	= 500;
	}
	
	if (isset($atts['cycles'])) {
		$cycles 	= $atts['cycles'];
		if(!is_numeric($cycles)){
			$cycles = 0;
		} 
	}
	else {
		$cycles 	= 0;
	}
	
	if (isset($atts['random'])) {
		$random 	= $atts['random'];
	}
	else {
		$random 	= "YES";
	}
	
	if($group==""){
		$group = "Group1";
	}
	
	$sSql = "select nsg_path, nsg_link, nsg_target, nsg_title from ".WP_NSG_TABLE." where nsg_status='YES' ";
	if($group <> ""){ 
		$sSql = $sSql . " and nsg_type='".$group."'";
	}
	
	if($random == "YES"){ 
		$sSql = $sSql . " ORDER BY RAND()"; 
	}
	else { 
		$sSql = $sSql . " ORDER BY nsg_order"; 
	}
	
	global $wpdb;
	$data = $wpdb->get_results($sSql);

	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$nsg_package = $nsg_package .'["'.$data->nsg_path.'", "'.$data->nsg_link.'", "'.$data->nsg_target.'", "'.$data->nsg_title.'"],';
		}
		$nsg_package = substr($nsg_package,0,(strlen($nsg_package)-1));	
		
		$nsg_wrapperid = str_replace(".","_",$group);
		$nsg_wrapperid = str_replace("-","_",$nsg_wrapperid);
		$nsg_wrapperid = $nsg_wrapperid . rand(10, 99);
		
		$nsg_pp = $nsg_pp . '<script type="text/javascript">';
		$nsg_pp = $nsg_pp . 'var mygallery=new newsimplegallery({wrapperid: "'.$nsg_wrapperid.'", dimensions: ['.$width.', '. $height.'], imagearray: ['. $nsg_package.'],autoplay: [true, "'.$pause.'", "'.$cycles.'"],persist: false, fadeduration: "'.$duration.'",oninit:function(){},onslide:function(curslide, i){}})';
		$nsg_pp = $nsg_pp . '</script>';
		$nsg_pp = $nsg_pp . '<div style="padding-top:5px;"></div>';
		$nsg_pp = $nsg_pp . '<div id="'.$nsg_wrapperid.'"></div>';
		$nsg_pp = $nsg_pp . '<div style="padding-top:5px;"></div>';
	}
	else
	{
		$nsg_pp = __( 'No records found, please check your short code' , 'new-simple-gallery');
	}
		
	return $nsg_pp;
}

function nsg_install()
{
	$pluginsurl = plugins_url( 'images', __FILE__ );
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_NSG_TABLE . "'") != WP_NSG_TABLE) 
	{
		$sSql = "CREATE TABLE IF NOT EXISTS `". WP_NSG_TABLE . "` (";
		$sSql = $sSql . "`nsg_id` INT NOT NULL AUTO_INCREMENT ,";
		$sSql = $sSql . "`nsg_path` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`nsg_link` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "`nsg_target` VARCHAR( 50 ) NOT NULL ,";
		$sSql = $sSql . "`nsg_title` VARCHAR( 1024 ) NOT NULL ,";
		$sSql = $sSql . "`nsg_order` INT NOT NULL ,";
		$sSql = $sSql . "`nsg_status` VARCHAR( 10 ) NOT NULL ,";
		$sSql = $sSql . "`nsg_type` VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "`nsg_date` INT NOT NULL ,";
		$sSql = $sSql . "PRIMARY KEY ( `nsg_id` )";
		$sSql = $sSql . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_NSG_TABLE . "` (nsg_path, nsg_link, nsg_target, nsg_title, nsg_order, nsg_status, nsg_type, nsg_date)"; 
		$sSql = $sSql . "VALUES ('".$pluginsurl."/250x167_1.jpg','#','_parent','New simple gallery wordpress plugin image 1', '1', 'YES', 'Group1', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_NSG_TABLE . "` (nsg_path, nsg_link, nsg_target, nsg_title, nsg_order, nsg_status, nsg_type, nsg_date)"; 
		$sSql = $sSql . "VALUES ('".$pluginsurl."/250x167_2.jpg','#','_parent','New simple gallery wordpress plugin image 2', '2', 'YES', 'Group1', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_NSG_TABLE . "` (nsg_path, nsg_link, nsg_target, nsg_title, nsg_order, nsg_status, nsg_type, nsg_date)"; 
		$sSql = $sSql . "VALUES ('".$pluginsurl."/500x300_1.jpg','#','_parent','New simple gallery wordpress plugin image 3', '3', 'YES', 'Group2', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_NSG_TABLE . "` (nsg_path, nsg_link, nsg_target, nsg_title, nsg_order, nsg_status, nsg_type, nsg_date)"; 
		$sSql = $sSql . "VALUES ('".$pluginsurl."/500x300_2.jpg','#','_parent','New simple gallery wordpress plugin image 4', '4', 'YES', 'Group2', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
	}
}

function nsg_deactivation()
{
	// No action required.
}

function nsg_add_to_menu()
{
	if (is_admin()) 
	{
		add_options_page(__('New simple gallery','new-simple-gallery'),
							__('New simple gallery','new-simple-gallery'), 'manage_options', 
								'new-simple-gallery', 'nsg_admin_option'); 
	}
}

function nsg_admin_option() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/image-management-edit.php');
			break;
		case 'add':
			include('pages/image-management-add.php');
			break;
		case 'set':
			include('pages/image-setting.php');
			break;
		default:
			include('pages/image-management-show.php');
			break;
	}
}

function nsg_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('new-simple-gallery', plugins_url().'/new-simple-gallery/new-simple-gallery.js');
	}	
}

if (is_admin())
{
	add_action('admin_menu', 'nsg_add_to_menu');
}

function nsg_textdomain() 
{
	  load_plugin_textdomain( 'new-simple-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function nsg_adminscripts() 
{
	if( !empty( $_GET['page'] ) ) 
	{
		switch ( $_GET['page'] ) 
		{
			case 'new-simple-gallery':
				wp_register_script( 'nsg-adminscripts', plugins_url( 'pages/setting.js', __FILE__ ), '', '', true );
				wp_enqueue_script( 'nsg-adminscripts' );
				$nsg_select_params = array(
					'nsg_path'   	=> __( 'Please select and upload your image.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_link'   	=> __( 'Please enter the target link.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_target' 	=> __( 'Please select the target option.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_title' 	=> __( 'Please enter the image title.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_order'  	=> __( 'Please enter the display order, only number.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_status' 	=> __( 'Please select the display status.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_type'  	=> __( 'Please select the gallery type.', 'nsg-select', 'new-simple-gallery' ),
					'nsg_delete'	=> __( 'Do you want to delete this record?', 'nsg-select', 'ss' ),
				);
				wp_localize_script( 'nsg-adminscripts', 'nsg_adminscripts', $nsg_select_params );
				break;
		}
	}
}

add_action('plugins_loaded', 'nsg_textdomain');
add_action('init', 'nsg_add_javascript_files');
register_activation_hook(__FILE__, 'nsg_install');
register_deactivation_hook(__FILE__, 'nsg_deactivation');
add_action( 'admin_enqueue_scripts', 'nsg_adminscripts' );
?>