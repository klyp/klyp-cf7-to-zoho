<?php
/**
 * Plugin Name: Klyp Contact Form 7 to ZOHO
 * Plugin URI: https://github.com/klyp/klyp-cf7-to-zoho
 * Description: This plugin allows you to map CF7 fields to ZOHO form fields.
 * Version: 1.0.0
 * Author: Klyp
 * Author URI: https://klyp.co
 * License: GPL2
 */

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');


if (! class_exists('klypCF7ToZoho')) {

    class klypCF7ToZoho
    {
        /**
         * Construct
         * @return void
         */
        public function __construct()
        {
            // Settings
            require_once(sprintf("%s/inc/settings.php", dirname(__FILE__)));
            require_once(sprintf("%s/inc/cf7.php", dirname(__FILE__)));
            require_once(sprintf("%s/inc/zoho.php", dirname(__FILE__)));
            require_once(sprintf("%s/inc/zoho-api.php", dirname(__FILE__)));
        }

        /**
         * Hook into the WordPress activate hook
         * @return void
         */
        public static function activate()
        {
        }

        /**
         * Hook into the WordPress deactivate hook
         * @return void
         */
        public static function deactivate()
        {
        }
    }
}

if (class_exists('klypCF7ToZoho')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('klypCF7ToZoho', 'activate'));
    register_deactivation_hook(__FILE__, array('klypCF7ToZoho', 'deactivate'));

    // instantiate the plugin class
    $plugin = new klypCF7ToZoho();
}