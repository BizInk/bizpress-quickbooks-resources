<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'display_post_states', 'bizpress_quickbooks_post_states', 10, 2 );
function bizpress_quickbooks_post_states( $post_states, $post ) {
	$quickbooksPage = bizink_get_quickbooks_page_object();
    if ( $quickbooksPage->ID === $post->ID ) {
        $post_states['bizpress_quickbooks'] = __('BizPress Quickbooks Resources','bizink-client');
    }
    return $post_states;
}

function quickbooks_settings_fields( $fields, $section ) {

	//if ( 'bizink-client_basic' != $section['id'] ) return $fields;
	
	if('bizink-client_basic' == $section['id']){
		$fields['quickbooks_content_page'] = array(
			'id'      => 'quickbooks_content_page',
			'label'     => __( 'Quickbooks Resources', 'bizink-client' ),
			'type'      => 'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-client' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			// 'chosen'	=> true,
			'required'	=> false,
		);
	}
	
	if('bizink-client_content' == $section['id']){
		$fields['quickbooks_label'] = array(
			'id' => 'quickbooks',
	        'label'	=> __( 'Bizpress Quickbooks Resources', 'bizink-client' ),
	        'type' => 'divider'
		);
		$fields['quickbooks_title'] = array(
			'id' => 'quickbooks_title',
			'label'     => __( 'Quickbooks Resources Title', 'bizink-client' ),
			'type'      => 'text',
			'default'   => __( 'Quickbooks Resources Resources', 'bizink-client' ),
			'required'	=> true,
		);
		$fields['quickbooks_desc'] = array(
			'id'      	=> 'quickbooks_desc',
			'label'     => __( 'Quickbooks Resources Description', 'bizink-client' ),
			'type'      => 'textarea',
			'default'   => __( 'Free resources to help you use Quickbooks Resources.', 'bizink-client' ),
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

if( !function_exists( 'bizink_get_quickbooks_page_object' ) ){
	function bizink_get_quickbooks_page_object(){
		$post_id = cxbc_get_option( 'bizink-client_basic', 'quickbooks_content_page' );
		$post = get_post( $post_id );
		return $post;
	}
}

add_action( 'init', 'bizink_quickbooks_init');
function bizink_quickbooks_init(){
	$post = bizink_get_quickbooks_page_object();
	if( is_object( $post ) && get_post_type( $post ) == "page" ){
		add_rewrite_tag('%'.$post->post_name.'%', '([^&]+)', 'bizpress=');
		add_rewrite_rule('^'.$post->post_name . '/([^/]+)/?$','index.php?pagename=quickbooks-resources&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/([a-z0-9-]+)[/]?$",'index.php?pagename=quickbooks-resources&bizpress=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/topic/([a-z0-9-]+)[/]?$",'index.php?pagename=quickbooks-resources&topic=$matches[1]','top');
		add_rewrite_rule("^".$post->post_name."/type/([a-z0-9-]+)[/]?$" ,'index.php?pagename=quickbooks-resources&type=$matches[1]','top');
		//flush_rewrite_rules();
	}
}

add_filter('query_vars', 'bizpress_quickbooks_qurey');
function bizpress_quickbooks_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}