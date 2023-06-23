<?php

/**
 * The plugin bootstrap file
 *
 * @link            https://pluginsware.com/
 * @since           1.0.0
 * @package         advanced-classifieds-and-directory-pro
 *
 * @wordpress-plugin
 * Plugin Name:     Advanced Classifieds and Directory Pro
 * Plugin URI:      https://pluginsware.com/
 * Description:     Provides an ability to build any kind of business directory site: classifieds, cars, bikes, boats and other vehicles dealers site, pets, real estate portal, wedding site, yellow pages, etc...
 * Version:         2.0.0
 * Author:          PluginsWare
 * Author URI:      https://pluginsware.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     advanced-classifieds-and-directory-pro
 * Domain Path:     /languages
 * 
 */
// Exit if accessed directly
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'acadp_fs' ) ) {
    acadp_fs()->set_basename( false, __FILE__ );
    return;
}


if ( !function_exists( 'acadp_fs' ) ) {
    // Create a helper function for easy SDK access.
    function acadp_fs()
    {
        global  $acadp_fs ;
        
        if ( !isset( $acadp_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_2877_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_2877_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $acadp_fs = fs_dynamic_init( array(
                'id'             => '2877',
                'slug'           => 'advanced-classifieds-and-directory-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_459968d11a1de798088f855a5e5d0',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'           => 'advanced-classifieds-and-directory-pro',
                'override_exact' => true,
                'first-path'     => 'admin.php?page=advanced-classifieds-and-directory-pro',
                'support'        => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $acadp_fs;
    }
    
    // Init Freemius.
    acadp_fs();
    // Signal that SDK was initiated.
    do_action( 'acadp_fs_loaded' );
}

// The unique identifier of this plugin
if ( !defined( 'ACADP_PLUGIN_NAME' ) ) {
    define( 'ACADP_PLUGIN_NAME', 'advanced-classifieds-and-directory-pro' );
}
// The current version of the plugin
if ( !defined( 'ACADP_VERSION_NUM' ) ) {
    define( 'ACADP_VERSION_NUM', '2.0.0' );
}
// Path to the plugin directory
if ( !defined( 'ACADP_PLUGIN_DIR' ) ) {
    define( 'ACADP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
// URL of the plugin
if ( !defined( 'ACADP_PLUGIN_URL' ) ) {
    define( 'ACADP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
// The plugin file name
if ( !defined( 'ACADP_PLUGIN_FILE_NAME' ) ) {
    define( 'ACADP_PLUGIN_FILE_NAME', plugin_basename( __FILE__ ) );
}

if ( !function_exists( 'activate_acadp' ) ) {
    // The code that runs during plugin activation
    function activate_acadp()
    {
        require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-activator.php';
        ACADP_Activator::activate();
    }
    
    register_activation_hook( __FILE__, 'activate_acadp' );
}


if ( !function_exists( 'deactivate_acadp' ) ) {
    // The code that runs during plugin deactivation
    function deactivate_acadp()
    {
        require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-deactivator.php';
        ACADP_Deactivator::deactivate();
    }
    
    register_deactivation_hook( __FILE__, 'deactivate_acadp' );
}


if ( !function_exists( 'run_acadp' ) ) {
    /**
     * Begins execution of the plugin.
     *
     * @since 1.0.0
     */
    function run_acadp()
    {
        require ACADP_PLUGIN_DIR . 'includes/class-acadp.php';
        $plugin = new ACADP();
        $plugin->run();
    }
    
    run_acadp();
}


if ( !function_exists( 'acadp_uninstall' ) ) {
    /**
     * The code that runs during plugin uninstallation.
     * This action is documented in includes/uninstall.php
     */
    function acadp_uninstall()
    {
        require_once ACADP_PLUGIN_DIR . 'includes/uninstall.php';
        ACADP_Uninstall::uninstall();
    }
    
    acadp_fs()->add_action( 'after_uninstall', 'acadp_uninstall' );
}
