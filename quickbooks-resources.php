<?php
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'display_post_states', 'bizpress_quickbooks_post_states', 10, 2 );
function bizpress_quickbooks_post_states( $post_states, $post ) {
	$quickbooksPageID =  cxbc_get_option( 'bizink-client_basic', 'quickbooks_content_page' );
    if ( $quickbooksPageID == $post->ID ) {
        $post_states['bizpress_quickbooks'] = __('BizPress Quickbooks Resources','bizink-client');
    }
    return $post_states;
}

function quickbooks_settings_fields( $fields, $section ) {
	$pageselect = false;
	if(defined('CXBPC')){
		$bizpress = get_plugin_data( CXBPC );
		$v = intval(str_replace('.','',$bizpress['Version']));
		if($v >= 151){
			$pageselect = true;
		}
	}
	
	if('bizink-client_basic' == $section['id']){
		$fields['quickbooks_content_page'] = array(
			'id'      => 'quickbooks_content_page',
			'label'     => __( 'Quickbooks Resources', 'bizink-client' ),
			'type'      => $pageselect ? 'pageselect':'select',
			'desc'      => __( 'Select the page to show the content. This page must contain the <code>[bizpress-content]</code> shortcode.', 'bizink-client' ),
			'options'	=> cxbc_get_posts( [ 'post_type' => 'page' ] ),
			'required'	=> false,
			'default_page' => [
				'post_title' => 'Quickbooks Resources',
				'post_content' => '[bizpress-content]',
				'post_status' => 'publish',
				'post_type' => 'page'
			]
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
		
		add_rewrite_tag('%quickbooks_resources.xml%', '([^&]+)', 'bizpressxml=');
		add_rewrite_rule('^(quickbooks_resources\.xml)?$','index.php?bizpressxml=quickbooks_resources','top');

		if(get_option('bizpress_quickbooks_flush_update',0) < 1){
			flush_rewrite_rules();
			update_option('bizpress_quickbooks_flush_update',1);
		}
	}
}

add_action('parse_request','bizpress_quickbooksxml_request', 10, 1);
function bizpress_quickbooksxml_request($wp){
	if ( array_key_exists( 'bizpressxml', $wp->query_vars ) && $wp->query_vars['bizpressxml'] == 'quickbooks_resources'){
		$post = bizink_get_quickbooks_page_object();
		if( is_object( $post ) && get_post_type( $post ) == "page" ){
			$data = get_transient("bizinktype_".md5('quickbooks-content'));
			if(empty($data)){
				$data = bizink_get_content('quickbooks-content', 'topics');
				set_transient( "bizinktype_".md5('quickbooks-content'), $data, (DAY_IN_SECONDS * 2) );
			}
			header('Content-Type: text/xml; charset=UTF-8');
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			$url = get_home_url();
			$url = str_replace('https:','',$url);
			$url = str_replace('http:','',$url);
			echo '<?xml-stylesheet type="text/xsl" href="//'.$url.'/wp-content/plugins/wordpress-seo/css/main-sitemap.xsl"?>';
			echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			echo '<url>';
			echo '<loc>'.get_home_url().'/'.$post->post_name.'</loc>';
			echo '</url>';
			
			if(empty($data->posts) == false){
				foreach($data->posts as $item){
					echo '<url>';
					echo '<loc>'.get_home_url().'/'.$post->post_name.'/'. $item->slug .'</loc>';
					if($item->thumbnail){
						echo '<image:image>';
						echo '<image:loc>'. $item->thumbnail .'</image:loc>';
						echo '</image:image>'; 
					}
					echo '</url>';
				}
			}
			echo '</urlset>';
		}
		die();
	}
}

add_filter('query_vars', 'bizpress_quickbooks_qurey');
function bizpress_quickbooks_qurey($vars) {
    $vars[] = "bizpress";
    return $vars;
}

add_filter('query_vars', 'bizpress_quickbooksxml_query');
function bizpress_quickbooksxml_query($vars) {
    $vars[] = "bizpressxml";
    return $vars;
}

function bizpress_quickbooks_sitemap_custom_items( $sitemap_custom_items ) {
    $sitemap_custom_items .= '
	<sitemap>
		<loc>'.get_home_url().'/quickbooks_resources.xml</loc>
	</sitemap>';
    return $sitemap_custom_items;
}

add_filter( 'wpseo_sitemap_index', 'bizpress_quickbooks_sitemap_custom_items' );