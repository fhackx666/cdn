<?php
/*
Plugin Name: WPJAM BASIC
Plugin URI: https://blog.wpjam.com/project/wpjam-basic/
Description: WPJAM 常用的函数和接口，屏蔽所有 WordPress 不常用的功能。
Version: 4.6.3
Author: Denis
Author URI: http://blog.wpjam.com/
*/

if (version_compare(PHP_VERSION, '7.2.0') < 0) {
	include plugin_dir_path(__FILE__).'old/wpjam-basic.php';
}else{
	define('WPJAM_BASIC_PLUGIN_URL', plugins_url('', __FILE__));
	define('WPJAM_BASIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
	define('WPJAM_BASIC_PLUGIN_FILE', __FILE__);

	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-model.php';	// Model 和其操作类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-item.php';		// ITEM 操作类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-util.php';		// 通用工具类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-field.php';	// 字段解析类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-core.php';		// 核心底层类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-api.php';		// 接口路由类
	include WPJAM_BASIC_PLUGIN_DIR.'includes/class-wpjam-notice.php';	// 消息通知类

	if(is_admin()){
		if(!class_exists('WP_List_Table')){
			include ABSPATH.'wp-admin/includes/class-wp-list-table.php';
		}

		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-list-table.php';	
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-posts-list-table.php';
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-terms-list-table.php';
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-users-list-table.php';
		
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-page-action.php';
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-plugin-page.php';
		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-builtin-page.php';

		include WPJAM_BASIC_PLUGIN_DIR.'admin/includes/class-wpjam-chart.php';
		
		include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-load.php';
		include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-verify.php';
	}

	include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-functions.php';	// 常用函数
	include WPJAM_BASIC_PLUGIN_DIR.'public/wpjam-route.php';		// 路由接口

	do_action('wpjam_loaded');
}