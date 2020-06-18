<?php
/*
Plugin Name: WPCasa
Plugin URI: https://wpcasa.com
Description: Flexible WordPress plugin to create professional real estate websites and manage property listings with ease.
Version: 1.3
Author: WPSight
Author URI: http://wpsight.com
Requires at least: 4.0
Tested up to: 5.4
Text Domain: wpcasa
Domain Path: /languages

	Copyright: 2015 Simon Rimkus
	License: GNU General Public License v2.0 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';

/**
 * WPSight_Framework class
 */
class WPSight_Framework {

	/**
	 *	Constructor - get the plugin hooked in and ready
	 */
	public function __construct() {

		// Define constants

		if ( ! defined( 'WPSIGHT_NAME' ) )
			define( 'WPSIGHT_NAME', 'WPCasa' );

		if ( ! defined( 'WPSIGHT_DOMAIN' ) )
			define( 'WPSIGHT_DOMAIN', 'wpcasa' );

		if ( ! defined( 'WPSIGHT_SHOP_URL' ) )
			define( 'WPSIGHT_SHOP_URL', 'https://wpcasa.com' );

		if ( ! defined( 'WPSIGHT_AUTHOR' ) )
			define( 'WPSIGHT_AUTHOR', 'WPSight' );

		define( 'WPSIGHT_VERSION', '1.3' );
		define( 'WPSIGHT_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'WPSIGHT_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		// Cookie constants

		define( 'WPSIGHT_COOKIE_SEARCH_QUERY', WPSIGHT_DOMAIN . '_search_query' );
		define( 'WPSIGHT_COOKIE_SEARCH_MAP', WPSIGHT_DOMAIN . '_search_map' );

		// Include functions

		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-general.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-template.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-listings.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-agents.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-search.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-helpers.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-admin.php' );
		include( WPSIGHT_PLUGIN_DIR . '/functions/wpsight-meta-boxes.php' );

		// Include classes

		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-post-types.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-api.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-listings.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-agents.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-general.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-helpers.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-search.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-meta-boxes.php' );
		include( WPSIGHT_PLUGIN_DIR . '/includes/class-wpsight-template.php' );

		// Include shortcodes
		include( WPSIGHT_PLUGIN_DIR . '/includes/shortcodes/class-wpsight-shortcodes.php' );

		// Include admin class
		include( WPSIGHT_PLUGIN_DIR . '/includes/admin/class-wpsight-admin.php' );

        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        // Include wpcasa polylang
//        if ( is_plugin_active( 'wpcasa-polylang/wpcasa-polylang.php' )  ) {
//            deactivate_plugins( '/wpcasa-polylang/wpcasa-polylang.php' );
//        }
//
//        if ( !is_plugin_active( 'wpcasa-polylang/wpcasa-polylang.php' )  ) {
//            include( WPSIGHT_PLUGIN_DIR . '/includes/wpcasa-polylang/wpcasa-polylang.php' );
//        }


//      TODO: delete till wpcasa 2.0
        if ( is_plugin_active( 'wpcasa-admin-map-ui/wpcasa-admin-map-ui.php' )  ) {
            deactivate_plugins( '/wpcasa-admin-map-ui/wpcasa-admin-map-ui.php' );
        }

        if ( wpsight_get_option( 'listings_map_provider' ) == 'leaflet') {
            include_once( WPSIGHT_PLUGIN_DIR . '/includes/admin/maps/leaflet/cmb2-field-leaflet-map.php' );
        } else {
//           TODO: delete check till wpcasa 2.0
            if ( !is_plugin_active( 'wpcasa-admin-map-ui/wpcasa-admin-map-ui.php' )  ) {
                if ( !class_exists('CMB2_Field_Google') ) {
                    include_once( WPSIGHT_PLUGIN_DIR . '/includes/admin/maps/google/cmb2-field-google-map.php' );
                }
            }
        }

//      TODO: delete till wpcasa 2.0
        if ( is_plugin_active( 'wpcasa-listings-map/wpcasa-listings-map.php' )  ) {
            deactivate_plugins( '/wpcasa-listings-map/wpcasa-listings-map.php' );
        }

//      TODO: delete check till wpcasa 2.0
        if ( !is_plugin_active( 'wpcasa-listings-map/wpcasa-listings-map.php' )  ) {
            if ( !class_exists('WPSight_Listings_Map') ) {
                include(WPSIGHT_PLUGIN_DIR . '/includes/wpcasa-listings-map/wpcasa-listings-map.php');
            }
        }

		// Only instantiate admin class when in admin area
		if ( is_admin() )
			$this->admin = new WPSight_Admin();

		// Init classes
		$this->post_types = new WPSight_Post_Type_Listing();
		$this->agents     = new WPSight_Agents();
		$this->general    = new WPSight_General();
		$this->helpers    = new WPSight_Helpers();
		$this->search     = new WPSight_Search();
		$this->meta_boxes = new WPSight_Meta_Boxes();

		// Activation

		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this->post_types, 'register_post_type_listing' ), 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), function() { include_once('includes/class-wpsight-install.php'); }, 10 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), 'flush_rewrite_rules', 15 );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activation' ) );
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'map_activation' ) );

		// Actions

		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'switch_theme', array( $this->post_types, 'register_post_type_listing' ), 10 );
		add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
        add_action( 'wp_loaded', array( $this, 'register_front_and_admin_scripts' ) );

		// Init action for add-ons to hook in
		do_action_ref_array( 'wpsight_init', array( &$this ) );

	}

    /**
     *	register_front_and_admin_scripts()
     *
     *	One leaflet core for all wpcasa products
     *
     *	@uses	wp_register_script()
     *	@uses	wp_register_style()
     *
     *	@since 1.3.0
     */
    public function register_front_and_admin_scripts() {
        wp_register_script( 'cmb2-leaflet-core', '//unpkg.com/leaflet/dist/leaflet-src.js', [ 'jquery' ], WPSIGHT_VERSION );
        wp_register_style( 'cmb2-leaflet-core', '//unpkg.com/leaflet/dist/leaflet.css', [], WPSIGHT_VERSION );

        if (wpsight_get_option('listings_map_provider') == 'leaflet') {
            wp_enqueue_style('cmb2-leaflet-core');
            wp_enqueue_script('cmb2-leaflet-core');
        }

    }

	/**
	 *	load_plugin_textdomain()
	 *
	 *	Set up the text domain for the plugin
	 *	and load language files.
	 *
	 *	@uses	plugin_basename()
	 *	@uses	load_plugin_textdomain()
	 *
	 *	@since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wpcasa', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 *	frontend_scripts()
	 *
	 *	Register and enqueue scripts and css.
	 *
	 *	@uses	wp_enqueue_script()
	 *	@uses	wp_localize_script()
	 *	@uses	wp_enqueue_style()
	 *	@uses	wpsight_get_option()
	 *
	 *	@since 1.0.0
	 */
	public function frontend_scripts() {
		// Enqueue jQuery
		wp_enqueue_script( 'jquery' );

		// Script debugging?
		$suffix = SCRIPT_DEBUG ? '' : '.min';


		wp_enqueue_script( 'jquery-tiptip', WPSIGHT_PLUGIN_URL . '/assets/js/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WPSIGHT_VERSION, true );
		wp_enqueue_script( 'jquery-cookie', WPSIGHT_PLUGIN_URL . '/assets/js/jquery.cookie.js', array( 'jquery' ), WPSIGHT_VERSION, true );
		wp_enqueue_script( 'wpsight-listings-search', WPSIGHT_PLUGIN_URL . '/assets/js/wpsight-listings-search.js', array( 'jquery' ), WPSIGHT_VERSION, true );

		// Localize scripts

		$data = array(
			'cookie_path'			=> COOKIEPATH,
			'cookie_search_query'	=> WPSIGHT_COOKIE_SEARCH_QUERY
		);

		wp_localize_script( 'wpsight-listings-search', 'wpsight_localize', $data );

		// Enqueue Google Maps (optionally with API key)

		if( true == apply_filters( 'wpsight_google_maps', true ) ) {

			$api_key = wpsight_get_option( 'google_maps_api_key' );

			$api_url = $api_key ? add_query_arg( array( 'key' => $api_key ), '//maps.googleapis.com/maps/api/js' ) : '//maps.googleapis.com/maps/api/js';

			wp_enqueue_script( 'wpsight-map-googleapi', apply_filters( 'wpsight_google_maps_endpoint', esc_url( $api_url ), $api_key ), null, WPSIGHT_VERSION );

		}

		if( true == apply_filters( 'wpsight_css', true ) && wpsight_get_option( 'listings_css' ) ) {

			wp_enqueue_style( 'wpsight', WPSIGHT_PLUGIN_URL . '/assets/css/wpsight' . $suffix . '.css' );

			if ( is_rtl() )
				wp_enqueue_style( 'wpsight-rtl', WPSIGHT_PLUGIN_URL . '/assets/css/wpsight-rtl' . $suffix . '.css' );

		}

	}

	/**
	 *	activation()
	 *
	 *	Callback for register_activation_hook
	 *	to create a default listings page with
	 *	the [wpsight_listings] shortcode and
	 *	to create some default options to be
	 *	used by this plugin.
	 *
	 *	@uses	wpsight_get_option()
	 *	@uses	wp_insert_post()
	 *	@uses	wpsight_add_option()
	 *
	 *	@since 1.0.0
	 */

	public function activation() {

		// Create listings page

		$page_data = array(
			'post_title'     => _x( 'Listings', 'listings page title', 'wpcasa' ),
			'post_content'   => '[wpsight_listings]',
			'post_type'      => 'page',
			'post_status'	 => 'publish',
			'comment_status' => 'closed',
			'ping_status'	 => 'closed'
		);

		$page_id = ! wpsight_get_option( 'listings_page' ) ? wp_insert_post( $page_data ) : false;

		// Add some default options

		$options = array(
			'listings_page'			=> $page_id,
			'listing_id'			=> __( 'ID-', 'wpcasa' ),
			'measurement_unit'		=> 'm2',
			'currency'				=> 'usd',
			'currency_symbol'		=> 'before',
			'currency_separator'	=> 'comma',
			'date_format'			=> get_option( 'date_format' ),
			'listings_css'			=> '1'
		);

		// Add default standard features
		foreach ( wpsight_details() as $option => $value )
			$options[ $option ] = array( 'label' => $value['label'], 'unit' => $value['unit'] );

		// Add default rental periods
		foreach ( wpsight_rental_periods() as $option => $value )
			$options[ $option ] = $value;

		foreach( $options as $option => $value ) {

			if( wpsight_get_option( $option ) )
				continue;

			wpsight_add_option( $option, $value );

		}
	}


    /**
     *	map_activation()
     *
     *	Callback for register_activation_hook
     *	to create a default map settings
     *
     *	@uses	wpsight_get_option()
     *	@uses	wpsight_add_option()
     *
     *	@since 1.0.0
     */
    public static function map_activation() {

        // Create map page

        $page_data = array(
            'post_title'		=> _x( 'Listings Map', 'listings map page title', 'wpsight-listings-map' ),
            'post_content'		=> '[wpsight_listings_map]',
            'post_type'			=> 'page',
            'post_status'		=> 'publish',
            'comment_status'	=> 'closed',
            'ping_status'		=> 'closed'
        );

        $page_id = ! wpsight_get_option( 'listings_map_page' ) ? wp_insert_post( $page_data ) : wpsight_get_option( 'listings_map_page' );

        // Add some default options

        $options = array(
            'listings_map_page'				=> $page_id,
            'listings_map_panel'			=> '1',
            'listings_map_panel_link'		=> __( 'Toggle Map', 'wpcasa-listings-map' ),
            'listings_map_nr'				=> 50,
            'listings_map_width'			=> '100%',
            'listings_map_height'			=> '800px',
            'listings_map_type'				=> 'ROADMAP',
            'listings_map_control_type'		=> '1',
            'listings_map_control_nav'		=> '1',
            'listings_map_scrollwheel'		=> '0',
            'listings_map_streetview'		=> '1',
            'listings_map_infobox_event'	=> 'mouseover',
            'listings_map_infobox_close'	=> '1'
        );

        foreach ( $options as $option => $value ) {

            if ( wpsight_get_option( $option ) )
                continue;

            wpsight_add_option( $option, $value );

        }
    }
}

/**
 *  wpsight()
 *
 *  The main function responsible for returning the one true wpsight Instance to functions everywhere.
 *  Use this function like you would use a global variable, except without needing to declare the global.
 *
 *  Example: <?php $wpsight = wpsight(); ?>
 *
 *  @return  object $wpsight
 */
function wpsight() {
	global $wpsight;

	// Don't activate plugin add-ons if theme still active

	if( wp_get_theme()->template == 'wpcasa' ) {
		remove_all_actions( 'wpsight_init' );
		return false;
	}

	if ( ! isset( $wpsight ) )
		$wpsight = new WPSight_Framework();

	return $wpsight;
}

wpsight();

/**
 *	wpsight_admin_notice_wpcasa()
 *
 *	Make sure users first deactivate
 *	the old WPCasa theme version.
 *	Display error message if it is
 *	still activated.
 *
 *	@uses	wp_get_theme()
 *
 *	@since 1.0.0
 */
add_action( 'admin_notices', 'wpsight_admin_notice_wpcasa' );

function wpsight_admin_notice_wpcasa() {

	if( wp_get_theme()->template != 'wpcasa' )
		return;

	echo '<div class="error"><p>' . __( 'Please make sure to <strong>deactivate the WPCasa theme</strong> in order to use the WPCasa plugin version. For more information about how to switch please <a href="http://docs.wpsight.com/article/switching-from-theme-version/" target="_blank">read our docs</a>.', 'wpcasa' ) . '</p></div>';
}

/**
 *	wpsight_admin_plugins_delete_notice()
 *
 *	Make sure users awera
 *	that WPCasa Listing Map and
 *	WPCasa admin map ui plugins
 *	can be deleted
 *
 *	@since 1.2.0
 */
//TODO: delete till wpcasa 2.0
add_action( 'admin_notices', 'wpsight_admin_plugins_delete_notice' );
function wpsight_admin_plugins_delete_notice() {
 $admin_map_ui_file = WP_PLUGIN_DIR . '/wpcasa-admin-map-ui/wpcasa-admin-map-ui.php';
 $listing_map_file = WP_PLUGIN_DIR . '/wpcasa-listings-map/wpcasa-listings-map.php';

  if ( file_exists($admin_map_ui_file) || file_exists($listing_map_file) )  {
      echo '<div class="notice notice-warning my-dismiss-notice is-dismissible"><p>
          ' . __( '<strong>WPCasa Admin Map UI</strong> and <strong>WPCasa Listings Map</strong> has been discontinued. 
          Functionality of both plugins has been integrated in WPCasa as of 1.2.0. </br>
          Feel free to remove both of those plugins.', 'wpcasa' ) . '
      </p></div>';
  }
}


/**
 *
 *	Redirect after single wpcasa activatation
 *	Prevent multiple activation redirect
 *
 *	@since 1.2.0
 */
register_activation_hook(__FILE__, 'wpcasa_plugin_activate');
add_action('admin_init', 'wpcasa_activation_redirect');

function wpcasa_plugin_activate() {
    add_option('wpcasa_do_activation_redirect', true);
}

function wpcasa_activation_redirect() {
    if (get_option('wpcasa_do_activation_redirect', false)) {
        delete_option('wpcasa_do_activation_redirect');
        if ( !isset($_GET['activate-multi']) ) {
            wp_safe_redirect( admin_url( 'index.php?page=wpsight-settings' ));
            exit();
        }
    }
}
