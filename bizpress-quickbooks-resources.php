<?php
/**
 * Plugin Name: BizPress Quickbooks Resources
 * Description: Show Quickbooks resources on your site. Automatically updated by the Bizink team.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.3.6
 * Text Domain: bizink-client-quickbooks
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
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker('https://github.com/BizInk/bizpress-quickbooks-resources',__FILE__,'bizpress-quickbooks-resources');
$myUpdateChecker->setBranch('master');
$myUpdateChecker->setAuthentication('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');

if(is_plugin_active("bizpress-client/bizink-client.php")){
	require 'quickbooks-resources.php';
}