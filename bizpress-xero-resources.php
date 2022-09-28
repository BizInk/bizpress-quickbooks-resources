<?php
/**
 * Plugin Name: BizPress Xero Resources
 * Description: Show Xero resources on your site. Automatically updated by the Bizink team.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.1
 * Text Domain: bizink-client-xero
 * Domain Path: /languages
 */

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/BizInk/bizpress-xero-resources',__FILE__,'bizpress-xero-resources');
// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
// Using a private repository, specify the access token 
$myUpdateChecker->setAuthentication('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');

function xero_settings_fields( $fields, $section ) {

	//if ( 'bizink-client_basic' != $section['id'] ) return $fields;
	
	if('bizink-client_basic' == $section['id']){
		$fields['xero_content_page'] = array(
			'id'      => 'xero_content_page',
			'label'     => __( 'Bizink Client Xero', 'bizink-client' ),
			'type'      => 'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizink-content]</code> shortcode.', 'bizink-client' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			// 'chosen'	=> true,
			'required'	=> true,
		);
	}
	
	if('bizink-client_content' == $section['id']){
		$fields['xero_label'] = array(
			'id' => 'xero',
	        'label'	=> __( 'Bizink Client Xero', 'bizink-client' ),
	        'type' => 'divider'
		);
		$fields['xero_title'] = array(
			'id' => 'xero_title',
			'label'     => __( 'Xero Title', 'bizink-client' ),
			'type'      => 'text',
			'default'   => __( 'Xero Resources', 'bizink-client' ),
			'required'	=> true,
		);
		$fields['xero_desc'] = array(
			'id'      	=> 'xero_desc',
			'label'     => __( 'Xero Description', 'bizink-client' ),
			'type'      => 'textarea',
			'default'   => __( 'Free resources to help you use Xero.', 'bizink-client' ),
			'required'	=> true,
		);
	}

	return $fields;
}
add_filter( 'cx-settings-fields', 'xero_settings_fields', 10, 2 );

function xero_content( $types ) {
	$types[] = [
		'key' 	=> 'xero_content_page',
		'type'	=> 'xero-content'
	];

	return $types;
}
add_filter( 'bizink-content-types', 'xero_content' );
