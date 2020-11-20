<?php
/*
Plugin Name: QQWorld Speed for China
Plugin URI: https://wordpress.org/plugins/qqworld-speed-4-china/
Description: If your host is in china, you might need this plugin to make your website that running faster.
Version: 1.6.6.3
Author: Michael Wang
Author URI: http://www.qqworld.org
Text Domain: qqworld-speed-4-china
*/

define('QQWORLD_SPEED4CHINA_DIR', __DIR__);
define('QQWORLD_SPEED4CHINA_URL', plugin_dir_url(__FILE__));

class qqworld_speed4china {
	var $text_domain = 'qqworld-speed-4-china';
	var $value;
	var $using_google_fonts;
	var $using_gravatar;
	var $default_avatar;
	var $local_avatar;
	var $auto_update_core;
	var $auto_update_plugins;
	var $auto_update_themes;
	var $advanced_speed_up;
	public function __construct() {
		add_action( 'admin_menu', array($this, 'create_menu') );
		add_action( 'admin_init', array($this, 'register_setting') );
		add_filter( 'plugin_row_meta', array($this, 'registerPluginLinks'),10,2 );
		add_action( 'plugins_loaded', array($this, 'load_language') );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );
		$this->get_value();
		$this->speed_up();

		// 实验接口
		//add_action( 'http_api_curl', array($this, 'http_api_curl') ); // 将代理信息接入HTTP API接口，可用于升级内核、插件和主题
	}

	public function http_api_curl($handle) {
		//Don't verify SSL certs
		//curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($handle, CURLOPT_SSLVERSION, 3); //设置SSL协议版本号

		// 默认使用HTTP代理
		$proxy = explode(':', '127.0.0.1:8100');
		curl_setopt($handle, CURLOPT_PROXY, $proxy[0]);
		curl_setopt($handle, CURLOPT_PROXYPORT, $proxy[1]);
		//error_log(print_r($proxy, true)."\n", 3, 'e:\log.txt');

		// 如果支持 CURLOPT_PROXYTYPE 才可以使用 socks5 等代理
		$this->qc_proxy_type = 'CURLPROXY_SOCKS5';
		switch ($this->qc_proxy_type) {
			case 'CURLPROXY_SOCKS4':
				$proxy_type = CURLPROXY_SOCKS4;
				break;
			case 'CURLPROXY_SOCKS5':
				$proxy_type = CURLPROXY_SOCKS5;
				break;
			case 'CURLPROXY_SOCKS4A':
				$proxy_type = CURLPROXY_SOCKS4A;
				break;
			case 'CURLPROXY_SOCKS5_HOSTNAME':
				$proxy_type = CURLPROXY_SOCKS5_HOSTNAME;
				break;
			case 'CURLPROXY_HTTP':
				$proxy_type = CURLPROXY_HTTP;
				break;
		}
		curl_setopt($handle, CURLOPT_PROXYTYPE, $proxy_type);

		//error_log(print_r($GLOBALS['PROXY_ENABLED'], true)."\n", 3, 'e:\log.txt');
		//error_log(print_r($GLOBALS['PROXY_ADDRESS'], true)."\n", 3, 'e:\log.txt');
		//error_log(print_r($proxy_type, true)."\n", 3, 'e:\log.txt');
	}

	public function outside_language() {
		__('Michael Wang', $this->text_domain);
	}

	public function get_value() {
		$this->default_avatar = QQWORLD_SPEED4CHINA_URL . 'images/avatar_256x256.png';
		$this->values = get_option($this->text_domain);

		$this->google_font_url = 'googleapis.com';
		$this->google_font = isset($this->values['google-font']) ? $this->values['google-font'] : array();
		$this->google_font_frontend = isset($this->google_font['frontend']) ? $this->google_font['frontend'] : array();
		$this->google_font_frontend_type = isset($this->google_font_frontend['type']) ? $this->google_font_frontend['type'] : 'enabled';
		$this->google_font_frontend_to = isset($this->google_font_frontend['to']) ? $this->google_font_frontend['to'] : 'bootcdn.cn';
		$this->google_font_backend = isset($this->google_font['backend']) ? $this->google_font['backend'] : array();
		$this->google_font_backend_type = isset($this->google_font_backend['type']) ? $this->google_font_backend['type'] : 'enabled';
		$this->google_font_backend_to = isset($this->google_font_backend['to']) ? $this->google_font_backend['to'] : 'bootcdn.cn';

		$this->using_gravatar = isset($this->values['using-gravatar']) ? $this->values['using-gravatar'] : 'enabled';
		$this->local_avatar = isset($this->values['local-avatar']) && !empty($this->values['local-avatar']) ? $this->values['local-avatar'] : $this->default_avatar;

		$this->disable_emoji = isset($this->values['disable-emoji']) ? $this->values['disable-emoji'] : 'no';
		$this->local_emoji = isset($this->values['local-emoji']) ? $this->values['local-emoji'] : 'no';

		$this->remove_dashbaord_widgets = isset($this->values['remove-dashboard-widgets']) ? $this->values['remove-dashboard-widgets'] : array();

		$this->auto_update_core = isset($this->values['auto-update-core']) ? $this->values['auto-update-core'] : 'disabled';
		$this->auto_update_plugins = isset($this->values['auto-update-plugins']) ? $this->values['auto-update-plugins'] : 'disabled';
		$this->auto_update_themes = isset($this->values['auto-update-themes']) ? $this->values['auto-update-themes'] : 'disabled';
		$this->advanced_speed_up = isset($this->values['advanced-speed-up']) ? $this->values['advanced-speed-up'] : 'disabled';
		$this->update_plugins_ids = isset($this->values['update_plugins_ids']) ? $this->values['update_plugins_ids'] : array();
		$this->update_themes_ids = isset($this->values['update_themes_ids']) ? $this->values['update_themes_ids'] : array();
		$this->update_core_ids = isset($this->values['update_core_ids']) ? $this->values['update_core_ids'] : array();
		$this->update_plugins_roles_ids = isset($this->values['update_plugins_roles_ids']) ? $this->values['update_plugins_roles_ids'] : array();
		$this->update_themes_roles_ids = isset($this->values['update_themes_roles_ids']) ? $this->values['update_themes_roles_ids'] : array();
		$this->update_core_roles_ids = isset($this->values['update_core_roles_ids']) ? $this->values['update_core_roles_ids'] : array();
		$this->extend_the_time_of_the_upgrade = isset($this->values['extend-the-time-of-the-upgrade']) ? $this->values['extend-the-time-of-the-upgrade'] : 'disabled';
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ($screen->id == 'settings_page_qqworld-speed-4-china') {
			//for 3.5+ uploader
			wp_enqueue_media();
		}
	}

	function cdn_callback($buffer) {
		if (is_admin()) {
			if ($this->google_font_backend_type == 'enabled') return str_replace($this->google_font_url, $this->google_font_backend_to, $buffer);
			else return preg_replace('/<link[^<]*googleapis\.com[^>]*>/', '', $buffer);
		} else {
			if ($this->google_font_frontend_type == 'enabled') return str_replace($this->google_font_url, $this->google_font_frontend_to, $buffer);
			else return preg_replace('/<link[^<]*googleapis\.com[^>]*>/', '', $buffer);
		}
	}
	function buffer_start() {
		ob_start(array($this, "cdn_callback"));
	}
	function buffer_end() {
		@ob_end_flush();
	}

	public function return_null(){
		return null;
	}

	public function speed_up() {
		if (is_admin()) {
			if ( in_array($this->google_font_backend_type, array('enabled', 'delete')) ) {
				add_action('init', array($this, 'buffer_start') );
				add_action('shutdown', array($this, 'buffer_end') );
			}
		} else {
			if ( in_array($this->google_font_frontend_type, array('enabled', 'delete')) ) {
				add_action('init', array($this, 'buffer_start') );
				add_action('shutdown', array($this, 'buffer_end') );
			}
		}

		if ($this->using_gravatar == 'disabled') {
			add_filter( 'get_avatar', array($this, 'get_avatar'), 11, 5 );
		} elseif ($this->using_gravatar == 'v2ex') {
			add_filter('get_avatar', array($this, 'getV2exAvatar') );
		}

		if ($this->disable_emoji == 'yes') {
			add_action( 'init', array($this, 'disable_emojis') );
		}

		if ($this->auto_update_core == 'disabled') {
			add_filter( 'pre_site_transient_update_core', array($this, 'return_null') );
			remove_action( 'admin_init', '_maybe_update_core');
			remove_action( 'wp_version_check', 'wp_version_check' );
			remove_action( 'upgrader_process_complete', 'wp_version_check', 10, 0 );
			$this->remove_auto_update();
		}

		if ($this->auto_update_plugins == 'disabled') {
			add_filter( 'pre_site_transient_update_plugins', array($this, 'return_null') );
			remove_action( 'admin_init', '_maybe_update_plugins');
			remove_action( 'load-plugins.php', 'wp_update_plugins' );
			remove_action( 'load-update.php', 'wp_update_plugins' );
			remove_action( 'load-update-core.php', 'wp_update_plugins' );
			remove_action( 'admin_init', '_maybe_update_plugins' );
			remove_action( 'wp_update_plugins', 'wp_update_plugins' );
			remove_action( 'upgrader_process_complete', 'wp_update_plugins' );

			$timestamp = wp_next_scheduled( 'wp_update_plugins' );
			wp_unschedule_event( $timestamp, 'wp_update_plugins');

			$this->remove_auto_update();
		}

		if ($this->auto_update_themes == 'disabled') {
			add_filter( 'pre_site_transient_update_themes', array($this, 'return_null') );
			remove_action( 'admin_init', '_maybe_update_themes');
			remove_action( 'load-themes.php', 'wp_update_themes' );
			remove_action( 'load-update.php', 'wp_update_themes' );
			remove_action( 'load-update-core.php', 'wp_update_themes' );
			remove_action( 'wp_update_themes', 'wp_update_themes' );
			remove_action( 'upgrader_process_complete', 'wp_update_themes' );

			$timestamp = wp_next_scheduled( 'wp_update_themes' );
			wp_unschedule_event( $timestamp, 'wp_update_themes');

			$this->remove_auto_update();
		}

		if ($this->advanced_speed_up == 'enabled') {
			add_filter( 'user_has_cap', array($this, 'user_has_cap') );
			add_filter( 'pre_http_request', array($this, 'pre_http_request'), 10, 3 );
		}

		if ($this->extend_the_time_of_the_upgrade == 'enabled') {
			add_action( 'admin_init', array($this, 'update_no_limit') );
		}

		if (!empty($this->remove_dashbaord_widgets)) {
			add_action( 'wp_dashboard_setup', array($this, 'remove_dashboard_widgets') );
		}
	}

	public function remove_dashboard_widgets() {
		global $wp_meta_boxes;
		foreach ($this->remove_dashbaord_widgets as $slug => $enabled) {
			switch ($slug) {
				case 'dashboard_quick_press':
					// 以下这一行代码将删除 "快速发布" 模块
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
					break;
				case 'dashboard_plugins':
					// 以下这一行代码将删除 "插件" 模块
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
					break;
				case 'dashboard_recent_comments':
					// 以下这一行代码将删除 "近期评论" 模块
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
					break;
				case 'dashboard_recent_drafts':
					// 以下这一行代码将删除 "近期草稿" 模块
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
					break;
				case 'dashboard_primary':
					// 以下这一行代码将删除 "WordPress新闻" 模块
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
					break;
				case 'dashboard_secondary':
					// 以下这一行代码将删除 "其它 WordPress 新闻" 模块
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
					break;
				case 'dashboard_right_now':
					// 以下这一行代码将删除 "概况" 模块
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
					break;
			}
		}
	}

	public function update_no_limit() {
		set_time_limit(0);
	}

	/**
	 * Disable the emoji's
	 */
	public function disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );    
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', array($this, 'disable_emojis_tinymce') );
	}

	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 * 
	 * @param    array  $plugins  
	 * @return   array             Difference betwen the two arrays
	 */
	public function disable_emojis_tinymce( $plugins ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}

	public function user_has_cap($allcaps) {
		if (isset($allcaps['update_plugins'])) unset($allcaps['update_plugins']);
		if (isset($allcaps['update_themes'])) unset($allcaps['update_themes']);
		if (isset($allcaps['update_core'])) unset($allcaps['update_core']);
		return $allcaps;
	}

	// 所有的http请求如果包含api.wordpress.org，则禁止运行
	public function pre_http_request($pre, $r, $url) {
		if (preg_match('/api\.wordpress\.org|update\-check|subscription/i', $url)) {
			return true;
		} else {
			return false;
		}
	}

	public function get_avatar($avatar, $id_or_email, $size, $default, $alt) {
		$url = is_numeric( $this->local_avatar ) ? wp_get_attachment_url( $this->local_avatar ) : $this->local_avatar;
		return '<img src="'.$url.'" class="avatar avatar-'.$size.' height="'.$size.'" width="'.$size.'" alt="'.$alt.'" />';
	}

	/*替换为v2ex的Gravatar CDN*/
	public function getV2exAvatar($avatar) {
		$avatar = str_replace(array("www.gravatar.com/avatar", "0.gravatar.com/avatar", "1.gravatar.com/avatar", "2.gravatar.com/avatar"), "cdn.v2ex.com/gravatar", $avatar);
		return $avatar;
	} 

	public function remove_auto_update() {
		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'init', 'wp_schedule_update_checks' );
	}

	public function wp_default_styles(&$styles) {
		$styles->remove('open-sans');
		$styles->add( 'open-sans', QQWORLD_SPEED4CHINA_URL . 'opensans.css' );
	}

	public function wp_default_styles_360_cdn(&$styles) {
		$styles->remove('open-sans');
		$open_sans_font_url = '';

		/* translators: If there are characters in your language that are not supported
		 * by Open Sans, translate this to 'off'. Do not translate into your own language.
		 */
		if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) {
			$subsets = 'latin,latin-ext';

			/* translators: To add an additional Open Sans character subset specific to your language,
			 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
			 */
			$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

			if ( 'cyrillic' == $subset ) {
				$subsets .= ',cyrillic,cyrillic-ext';
			} elseif ( 'greek' == $subset ) {
				$subsets .= ',greek,greek-ext';
			} elseif ( 'vietnamese' == $subset ) {
				$subsets .= ',vietnamese';
			}

			// Hotlink Open Sans, for now
			$open_sans_font_url = "//fonts.useso.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=$subsets";
		}
		$styles->add( 'open-sans', $open_sans_font_url );
	}

	public function load_language() {
		load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	public function registerPluginLinks($links, $file) {
		$base = plugin_basename(__FILE__);
		if ($file == $base) {
			$links[] = '<a href="' . menu_page_url( $this->text_domain, 0 ) . '">' . __('Settings') . '</a>';
		}
		return $links;
	}

	public function register_setting() {
		register_setting($this->text_domain, $this->text_domain);
	}

	public function create_menu() {
		add_submenu_page('options-general.php', __('QQWorld Speed for China', $this->text_domain), __('QQWorld Speed for China', $this->text_domain), 'administrator', $this->text_domain, array($this, 'page') );
	}

	public function page() {
?>
	<style>
	#banner {
		max-width: 100%;
		display: block;
		margin: 20px 0;
		border: 10px solid #fff;
		box-sizing: border-box;
		box-shadow: 3px 3px 5px rgba(0,0,0,.1);
	}
	#local-avatar {
		cursor: pointer;
	}
	@media screen and ( max-width: 1000px ) {
		#banner {
			height: auto;
		}
	}
	@media screen and ( max-width: 640px ) {
		#banner {
			border-width: 5px;
		}
	}

	#qqworld-speed-4-china-tabs {
	height: 40px;
	margin-top: 35px;
	border-bottom: 1px solid #aaa;
	box-shadow: inset 0 -7px 10px rgba(0,0,0,.05);
	}
	#qqworld-speed-4-china-tabs li {
		float: left;
		height: 39px;
		line-height: 39px;
		font-size: 24px;
		padding: 0 40px;
		border: 1px solid #aaa;
		margin: 0 5px;
		cursor: pointer;
		border-radius: 5px 5px 0 0;
		color: #aaa;
		-webkit-transition: width .25s, height .25s, background .25s, color .25s, box-shadow .25s;
		-moz-transition: width .25s, height .25s, background .25s, color .25s, box-shadow .25s;
		-o-transition: width .25s, height .25s, background .25s, color .25s, box-shadow .25s;
		-ms-transition: width .25s, height .25s, background .25s, color .25s, box-shadow .25s;
		transition: width .25s, height .25s, background .25s, color .25s, box-shadow .25s;
	}
	#qqworld-speed-4-china-tabs li:hover {
		color: #666;
		background: #fff;
		box-shadow: inset 0 -7px 10px rgba(0,0,0,.05);
	}
	#qqworld-speed-4-china-tabs li.current {
		color: #333;
		position: relative;
		top: 1px;
		height: 43px;
		line-height: 40px;
		border-bottom: none;
		background: #f1f1f1;
		box-shadow: none;
	}

	@media screen and ( max-width: 1320px ) {
		#qqworld-speed-4-china-tabs li {
			font-size: 24px;
			padding: 0 26px;
		}
	}
	@media screen and ( max-width: 1132px ) {
		#qqworld-speed-4-china-tabs li {
			font-size: 20px;
			padding: 0 22px;
		}
	}
	@media screen and ( max-width: 1000px ) {
		#banner {
			height: auto;
		}
		#qqworld-speed-4-china-tabs li {
			font-size: 18px;
			padding: 0 18px;
		}
	}
	@media screen and ( max-width: 728px ) {
		#qqworld-speed-4-china-tabs li {
			font-size: 14px;
			padding: 0 10px;
			margin: 0 3px;
		}
	}
	@media screen and ( max-width: 640px ) {
		#qqworld-speed-4-china-tabs li {
			font-size: 12px;
			padding: 0 8px;
			margin: 0 2px;
		}
	}

	.tab-content.hidden {
		display: none;
	}

	#products {

	}

	#products > li {
		width: 33.3333%;
		float: left;
		padding: 50px;
		box-sizing: border-box;
		-webkit-transition: transform .5s cubic-bezier(0.175,0.885,0.320,1.275);
		-moz-transition: transform .5s cubic-bezier(0.175,0.885,0.320,1.275);
		-o-transition: transform .5s cubic-bezier(0.175,0.885,0.320,1.275);
		-ms-transition: transform .5s cubic-bezier(0.175,0.885,0.320,1.275);
		transition: transform .5s cubic-bezier(0.175,0.885,0.320,1.275);
	}
	#products > li:hover {
		transform: scale(1.05, 1.05);
	}
	#products > li p {
		max-width: 300px;
	}

	#contact-table .contact-qrcode {
		border: 5px solid #fff;
		width: 150px;
		height: auto;
		transition: width .5s;
	}
	#contact-table .contact-qrcode:hover {
		width: 250px;
	}
	</style>
	<div class="wrap">
		<h2><?php _e('QQWorld Speed for China', $this->text_domain); ?></h2>
		<p><?php _e('If your host is in china, you might need this plugin to make your website that running faster.', $this->text_domain); ?></p>
		<p><img src="<?php echo QQWORLD_SPEED4CHINA_URL; ?>images/banner-772x250.jpg" width="772" height="250" id="banner" /></p>
		<form method="post" action="options.php">
			<input type="hidden" name="qqworld-speed-4-china[update_plugins_roles_ids]" value="" />
			<input type="hidden" name="qqworld-speed-4-china[update_themes_roles_ids]" value="" />
			<input type="hidden" name="qqworld-speed-4-china[update_core_roles_ids]" value="" />
			<?php settings_fields($this->text_domain); ?>

			<ul id="qqworld-speed-4-china-tabs">
				<li class="current"><?php _e('Settings', $this->text_domain); ?></li>
				<li><?php _e('Contact', $this->text_domain); ?></li>
			</ul>

			<div class="tab-content">
				<h3><?php _e('Google Fonts', $this->text_domain); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="use-360-cdn"><?php _e('Front-End', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="use-custom-cdn-yes" name="qqworld-speed-4-china[google-font][frontend][type]" value="enabled" <?php checked($this->google_font_frontend_type, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label><br />
									<?php _e('Replace to', $this->text_domain); ?>
									<input type="text" name="qqworld-speed-4-china[google-font][frontend][to]" placeholder="<?php _e('Such as: ', $this->text_domain); ?>useso.com" value="<?php echo $this->google_font_frontend_to; ?>" /><br />
									<label><input type="radio" id="use-custom-cdn-no" name="qqworld-speed-4-china[google-font][frontend][type]" value="disabled" <?php checked($this->google_font_frontend_type, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="use-custom-cdn-delete" name="qqworld-speed-4-china[google-font][frontend][type]" value="delete" <?php checked($this->google_font_frontend_type, 'delete'); ?> /> <?php _e('Delete Google Font', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="use-google-fonts"><?php _e('Admin Page', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="use-custom-cdn-yes" name="qqworld-speed-4-china[google-font][backend][type]" value="enabled" <?php checked($this->google_font_backend_type, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label><br />
									<?php _e('Replace to', $this->text_domain); ?>
									<input type="text" name="qqworld-speed-4-china[google-font][backend][to]" placeholder="<?php _e('Such as: ', $this->text_domain); ?>useso.com" value="<?php echo $this->google_font_backend_to; ?>" /><br />
									<label><input type="radio" id="use-custom-cdn-no" name="qqworld-speed-4-china[google-font][backend][type]" value="disabled" <?php checked($this->google_font_backend_type, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="use-custom-cdn-delete" name="qqworld-speed-4-china[google-font][backend][type]" value="delete" <?php checked($this->google_font_backend_type, 'delete'); ?> /> <?php _e('Delete Google Font', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><?php _e('Gravatar', $this->text_domain); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="using-gravatar"><?php _e('Using Gravatar', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="using-gravatar-yes" name="qqworld-speed-4-china[using-gravatar]" value="enabled" <?php checked($this->using_gravatar, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="using-gravatar-v2ex" name="qqworld-speed-4-china[using-gravatar]" value="v2ex" <?php checked($this->using_gravatar, 'v2ex'); ?> /> <?php _e('V2ex CDN', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label><br />
									<label><input type="radio" id="using-gravatar-no" name="qqworld-speed-4-china[using-gravatar]" value="disabled" <?php checked($this->using_gravatar, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
						<tr valign="top" id="local-avatar-row"<?php if ($this->using_gravatar == 'enabled') echo ' class="hidden"'; ?>>
							<th scope="row">
								<label for="local-avatar"><?php _e('Local Avatar', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
								<?php
								if ( is_numeric($this->local_avatar) ) {
									$id = $this->local_avatar;
									$url = wp_get_attachment_url( $id );
								} else {
									$id = '';
									$url = $this->local_avatar;
								}
								?>
								<div id="local-avatar"><img src="<?php echo $url; ?>" width="80" height="80" default-avatar="<?php echo $this->default_avatar; ?>" title="<?php _e('Insert Avatar', $this->text_domain);?>" /></div>
								<input type="hidden" id="upload-avatar" name="qqworld-speed-4-china[local-avatar]" value="<?php echo $this->local_avatar; ?>" />
								<input type="button" class="button<?php if ( !is_numeric($this->local_avatar) ) echo ' hidden'; ?>" id="using-default-avatar" value="<?php _e('Using Default Avatar', $this->text_domain); ?>" />
								</aside>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><?php _e('Emoji', $this->text_domain); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="disable-emoji"><?php _e('Disabled', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="checkbox" id="disable-emoji" name="qqworld-speed-4-china[disable-emoji]" value="yes" <?php checked($this->disable_emoji, 'yes'); ?> /></label>
								</aside>
							</td>
						</tr>
						<!--
						<tr valign="top">
							<th scope="row">
								<label for="local-emoji"><?php _e('Local Emoji', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="checkbox" id="local-emoji" name="qqworld-speed-4-china[local-emoji]" value="yes" <?php checked($this->local_emoji, 'yes'); ?> /></label>
									<p><?php _e('Use local emoji image files.', $this->text_domain); ?></p>
								</aside>
							</td>
						</tr>-->
					</tbody>
				</table>
				<h3><?php _e('Dashboard', $this->text_domain); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="remove-dashboard-metas"><?php _e('Remove Dashboard Widgets', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
								<?php
								$widgets = array(
									'dashboard_quick_press' => __('Quick Draft'),
									'dashboard_plugins' => __('Plugins'),
									'dashboard_recent_comments' => __('Recent Comments', $this->text_domain),
									'dashboard_recent_drafts' => __('Recent Drafts', $this->text_domain),
									'dashboard_primary' => _x('Primary', 'widgets', $this->text_domain),
									'dashboard_secondary' => _x('Secondary', 'widgets', $this->text_domain),
									'dashboard_activity' => _x('Activity', 'widgets', $this->text_domain),
									'dashboard_right_now' => _x('Right now', 'widgets', $this->text_domain),
								);
								foreach ($widgets as $slug => $label) :
								?>
									<label><input type="checkbox" name="qqworld-speed-4-china[remove-dashboard-widgets][<?php echo $slug; ?>]" value="yes" <?php if (isset($this->remove_dashbaord_widgets[$slug])) echo ' checked'; ?> /> <?php echo $label; ?></label><br />
								<?php endforeach; ?>
								</aside>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><?php _e('Upgrade', $this->text_domain); ?></h3>
				<p><?php _e("If you want to update, don't forget temporarily enable these options.", $this->text_domain); ?></p>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="auto-update-core"><?php _e('Auto Update Core', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="auto-update-core-yes" name="qqworld-speed-4-china[auto-update-core]" value="enabled" <?php checked($this->auto_update_core, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="auto-update-core-no" name="qqworld-speed-4-china[auto-update-core]" value="disabled" <?php checked($this->auto_update_core, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="auto-update-plugins"><?php _e('Auto Update Plugins', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="auto-update-plugins-yes" name="qqworld-speed-4-china[auto-update-plugins]" value="enabled" <?php checked($this->auto_update_plugins, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="auto-update-plugins-no" name="qqworld-speed-4-china[auto-update-plugins]" value="disabled" <?php checked($this->auto_update_plugins, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="auto-update-themes"><?php _e('Auto Update Themes', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="auto-update-themes-yes" name="qqworld-speed-4-china[auto-update-themes]" value="enabled" <?php checked($this->auto_update_themes, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="auto-update-themes-no" name="qqworld-speed-4-china[auto-update-themes]" value="disabled" <?php checked($this->auto_update_themes, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label>
								</aside>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="auto-update-themes"><?php _e('Advanced Speed Up', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="advanced-speed-up-yes" name="qqworld-speed-4-china[advanced-speed-up]" value="enabled" <?php checked($this->advanced_speed_up, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain);_e('(Speed up)', $this->text_domain); ?></label><br />
									<label><input type="radio" id="advanced-speed-up-no" name="qqworld-speed-4-china[advanced-speed-up]" value="disabled" <?php checked($this->advanced_speed_up, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain); ?></label>
								</aside>
								<p class="description"><?php _e('If enabled this option, all update action will be disabled.', $this->text_domain); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="auto-update-themes"><?php _e('Extend the Time of the Upgrade', $this->text_domain); ?></label>
							</th>
							<td>
								<aside class="admin_box_unit">
									<label><input type="radio" id="extend-the-time-of-the-upgrade-yes" name="qqworld-speed-4-china[extend-the-time-of-the-upgrade]" value="enabled" <?php checked($this->extend_the_time_of_the_upgrade, 'enabled'); ?> /> <?php _e('Enabled', $this->text_domain); ?></label><br />
									<label><input type="radio" id="extend-the-time-of-the-upgrade-no" name="qqworld-speed-4-china[extend-the-time-of-the-upgrade]" value="disabled" <?php checked($this->extend_the_time_of_the_upgrade, 'disabled'); ?> /> <?php _e('Disabled', $this->text_domain); ?></label>
								</aside>
								<p class="description"><?php _e('If enabled this option, will never timeout when upgrading.', $this->text_domain); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- Contact -->
			<div class="tab-content hidden">
				<table id="contact-table" class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for=""><?php _ex('Developer', 'contact', $this->text_domain); ?></label></th>
							<td><?php _e('Michael Wang', $this->text_domain); ?></td>
						</tr>
						<tr>
							<th scope="row"><label for=""><?php _e('Official Website', $this->text_domain); ?></label></th>
							<td><a href="https://www.qqworld.org" target="_blank">www.qqworld.org</a></td>
						</tr>
						<tr>
							<th scope="row"><label for=""><?php _e('Email'); ?></label></th>
							<td><a href="mailto:<?php _e('Michael Wang', $this->text_domain); ?> <admin@qqworld.org>">admin@qqworld.org</a></td>
						</tr>
						<tr>
							<th scope="row"><label for=""><?php _e('Tencent QQ', $this->text_domain); ?></label></th>
							<td><a href="http://wpa.qq.com/msgrd?v=3&uin=172269588&site=qq&menu=yes" target="_blank">172269588</a> (<?php printf(__('%s: ', $this->text_domain), __('QQ Group', $this->text_domain)); ?>3372914)</td>
						</tr>
						<tr>
							<th scope="row"><label for=""><?php _e('Wechat', $this->text_domain); ?></label></th>
							<td><img src="<?php echo QQWORLD_SPEED4CHINA_URL; ?>images/wechat-qrcode.png" class="contact-qrcode" />
							<p><?php _e('Please use the WeChat APP to scan the QR code.', $this->text_domain); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for=""><?php _e('Cellphone', $this->text_domain); ?></label></th>
							<td><a href="tel:13294296711">13294296711</a></td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php submit_button(); ?>
		</form>
		<script>
		var wpqs4c = {};
		wpqs4c.speed4china = function() {
			var $ = jQuery, _this = this;
			$(document).on('click', '#using-gravatar-yes', function() {
				$('#local-avatar-row').fadeOut('normal');
			}).on('click', '#using-gravatar-no', function() {
				$('#local-avatar-row').fadeIn('normal');
			}).on('click', '#local-avatar-row label, #local-avatar', function() {
				event.preventDefault();
				var title = $('#local-avatar img').attr('title');
				if ( typeof _this.file_frame == 'object' ) {
					_this.file_frame.open();
					return;
				}
				_this.file_frame = wp.media.frames.file_frame = wp.media({
					title: title,
					button: {
						text: title,
					},
					multiple: false
				});
				_this.file_frame.on( 'open', function() {
					var selection = _this.file_frame.state().get('selection');
					var attachment_id = $('#upload-avatar').val();
					if (attachment_id) {
						var attachment = wp.media.attachment(attachment_id);
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					}
				});
				_this.file_frame.on('select', function() {
					var attachment = _this.file_frame.state().get('selection').first().toJSON();
					var id = attachment.id;
					var url = attachment.url;
					$('#local-avatar img').attr('src', url);
					$('#upload-avatar').val(id).attr({
						'type': 'hidden',
						'name': 'qqworld-speed-4-china[local-avatar]'
					});
					$('#using-default-avatar').slideDown('normal');
				});
				_this.file_frame.open();
			}).on('click', '#using-default-avatar', function() {
				$('#upload-avatar').val('');
				$('#local-avatar img').attr('src', $('#local-avatar img').attr('default-avatar'));
				$(this).slideUp('normal');
			});

			$(document).on('click', '#qqworld-speed-4-china-tabs li', function() {
				if (!$(this).hasClass('current')) {
					var index = $('#qqworld-speed-4-china-tabs li').index(this);
					$('#qqworld-speed-4-china-tabs li').removeClass('current');
					$(this).addClass('current');
					$('.tab-content').hide().eq(index).fadeIn('normal');
				}
				return false;
			});
		}
		wpqs4c.speed4china();
		</script>
	</div>
<?php
	}
}
new qqworld_speed4china;
?>