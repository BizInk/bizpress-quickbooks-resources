<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function quickbooks_settings_fields( $fields, $section ) {

	//if ( 'bizink-client_basic' != $section['id'] ) return $fields;
	
	if('bizink-client_basic' == $section['id']){
		$fields['quickbooks_content_page'] = array(
			'id'      => 'quickbooks_content_page',
			'label'     => __( 'Bizink Client Quickbooks', 'bizink-client' ),
			'type'      => 'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizink-content]</code> shortcode.', 'bizink-client' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			// 'chosen'	=> true,
			'required'	=> true,
		);
	}
	
	if('bizink-client_content' == $section['id']){
		$fields['quickbooks_label'] = array(
			'id' => 'quickbooks',
	        'label'	=> __( 'Bizink Client Quickbooks', 'bizink-client' ),
	        'type' => 'divider'
		);
		$fields['quickbooks_title'] = array(
			'id' => 'quickbooks_title',
			'label'     => __( 'Quickbooks Title', 'bizink-client' ),
			'type'      => 'text',
			'default'   => __( 'Quickbooks Resources', 'bizink-client' ),
			'required'	=> true,
		);
		$fields['quickbooks_desc'] = array(
			'id'      	=> 'quickbooks_desc',
			'label'     => __( 'Quickbooks Description', 'bizink-client' ),
			'type'      => 'textarea',
			'default'   => __( 'Free resources to help you use Quickbooks.', 'bizink-client' ),
			'required'	=> true,
		);
	}

	return $fields;
}
add_filter( 'cx-settings-fields', 'quickbooks_settings_fields', 10, 2 );

function quickbooks_content( $types ) {
	$types[] = [
		'key' 	=> 'quickbooks_content_page',
		'type'	=> 'quickbooks-content'
	];

	return $types;
}
add_filter( 'bizink-content-types', 'quickbooks_content' );