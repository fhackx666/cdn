<?php
/**
* Plugin Name: Elementor Olympus Widgets
* Description: Olympus widgets for Elemetor
* Plugin URI:  https://crumina.net/
* Version:     1.2.0
* Author:      
* Author URI:  https://crumina.net/
* Text Domain: elementor-olympus
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'ES_PLUGIN_FILE' ) ) {
	define( 'ES_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'ES_PLUGIN_URL' ) ) {
	define( 'ES_PLUGIN_URL', untrailingslashit( plugins_url( '/', ES_PLUGIN_FILE ) ) );
}

if ( ! defined( 'ES_ABSPATH' ) ) {
	define( 'ES_ABSPATH', dirname( ES_PLUGIN_FILE ) . '/' );
}

/**
 * Main Elementor Olympus Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Elementor_Olympus {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Elementor_Olympus The single instance of the class.
	 */
	private static $_instance = null;

	private $_is_wc_active = false;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Olympus An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// @todo Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		load_textdomain( 'elementor-olympus', WP_LANG_DIR . '/elementor-olympus/elementor-olympus-' . $locale . '.mo' );
		load_plugin_textdomain( 'elementor-olympus', false, plugin_basename( dirname( ES_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}


		// Add widget icons
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_widget_icons']);

		// Add Olympus Theme Icon pack
		add_filter( 'elementor/icons_manager/native', [ $this, 'add_olympus_icons_to_icon_manager']);

		// Checks if WooCommerce is enabled
		if ( true === in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			// $this->_is_wc_active = true;
		}

		// Add Plugin actions
		add_action( 'elementor/elements/categories_registered', [ $this, 'init_categories' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );

		// Resize
		require ES_ABSPATH . 'includes/class-es-resize.php';
		
		// Include functions (available in both admin and frontend).
		require ES_ABSPATH . 'includes/conditional-functions.php';
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-olympus' ),
			'<strong>' . esc_html__( 'Elementor Olympus', 'elementor-olympus' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-olympus' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-olympus' ),
			'<strong>' . esc_html__( 'Elementor Olympus', 'elementor-olympus' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-olympus' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-olympus' ),
			'<strong>' . esc_html__( 'Elementor Olympus', 'elementor-olympus' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-olympus' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Add element category.
	 *
	 * Register new category for the element.
	 *
	 * @since 1.7.12
	 * @access public
	 *
	 * @param string $category_name       Category name.
	 * @param array  $category_properties Category properties.
	 */
	function init_categories( $elements_manager ) {
	    $elements_manager->add_category(
	        'elementor-olympus',
	        [
				'title' => esc_html__( 'Olympus', 'elementor-olympus' ),
	        ]
	    );

	    if ( $this->_is_wc_active ) {
		    $elements_manager->add_category(
		        'elementor-olympus-wc',
		        [
					'title' => esc_html__( 'Olympus WooCommerce', 'elementor-olympus' ),
		        ]
		    );
		}
	}

	/**
	 * Enqueue icons
	 *
	 * Load icons stylesheet for use it in our widgets
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function enqueue_widget_icons() {
		wp_enqueue_style(
			'crum-el-widget-icons',
			ES_PLUGIN_URL . '/assets/css/widget-icons.css',
			array(),
			Elementor_Olympus::VERSION
		);
	}

	public function add_olympus_icons_to_icon_manager( $settings ) {
		$json_url = get_template_directory_uri()  . '/fonts/olympus-icons/olympus.json';

		$settings['olympus-icons-font'] = [
			'name'          => 'olympus-icons-font',
			'label'         => esc_html__( 'Olympus', 'elementor-olympus' ),
			'url'           => get_template_directory_uri() . '/css/olympus-icons-font.css',
			'enqueue'       => false,
			'prefix'        => 'olympus-icon-',
			'displayPrefix' => 'olympus-icon',
			'labelIcon'     => 'olympus-icon-Heart-Icon',
			'ver'           => '1',
			'fetchJson'     => $json_url,
			'native'        => false,
		];

		return $settings;
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {
		$widgets_names = [
			'olympus_title',
			'olympus_button',
			'olympus_counter',
			'olympus_clients_slider',
			'olympus_maps',
			'olympus_fw_form',
			'olympus_team',
			'olympus_testimonial',
			'olympus_news_grid',
			'olympus_register_form',
		];

		if ( $this->_is_wc_active ) {
			array_push( $widgets_names,
				'olympus_wc_add_to_cart',
				'olympus_wc_elements',
				'olympus_wc_product',
				'olympus_wc_product_categories',
				'olympus_wc_product_category',
				'olympus_wc_products'
			);
		}

		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		foreach ( $widgets_names as $widget_id ) {
			$file = ES_ABSPATH . 'widgets/' . $widget_id . '.php';
			
			$class_name = 'Elementor_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $widget_id ) ) );

			if ( file_exists( $file ) && ! class_exists( $class_name ) ) {
				// Include Widget file
				require_once( $file );
				
				// Register widget
				if ( class_exists( $class_name ) ) {
					$widgets_manager->register_widget_type( new $class_name() );
				}
			}
		}
	}

	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_controls() {
		$controls_names = [
		];
		
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		foreach ( $controls_names as $control_id ) {
			$file = ES_ABSPATH . 'controls/' . $control_id . '.php';
			
			$class_name = 'Control_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $control_id ) ) );

			if ( file_exists( $file ) && ! class_exists( $class_name ) ) {
				// Include Control file
				require_once( $file );
				
				// Register control
				if ( class_exists( $class_name ) ) {
					$controls_manager->register_control( $control_id, new $class_name() );
				}
			}
		}
	}

}

Elementor_Olympus::instance();