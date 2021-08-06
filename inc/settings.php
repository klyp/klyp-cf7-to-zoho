<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

/**
 * Create menu under settings
 *
 * @return void
 */
function klypCF7ToZohoMenu()
{
    add_options_page('Klyp CF7 to Zoho', 'Klyp CF7 to Zoho', 'manage_options', 'klyp-cf7-to-zoho', 'klypCF7ToZohoSettings');
}
add_action('admin_menu', 'klypCF7ToZohoMenu');

/**
 * Create the settings page
 *
 * @return void
 */
function klypCF7ToZohoSettings()
{
    require_once(sprintf("%s/settings-page.php", dirname(__FILE__)));
}

/**
 * Register Plugin settings
 *
 * @return void
 */
function klypCF7ToZohoRegisterSettings()
{
    //register our settings
    define('KlypCF7TOZoho', 'klyp-cf7-to-zoho');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_client_id');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_client_secret');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_data_centre');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_auth_code');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_access_token');
    register_setting(KlypCF7TOZoho, 'klyp_cf7tozoho_refresh_token');
}
add_action('admin_init', 'klypCF7ToZohoRegisterSettings');

/**
 * Sanitize input
 *
 * @param string/array
 * @return string/array
 */
function klypCF7ToZohoSanitizeInput($input)
{
    if (is_array($input)) {
        $return = array ();

        foreach ($input as $key => $value) {
            $return[$key] = is_array($value) ? $value : sanitize_text_field($value);
        }

        return $return;
    } else {
        return sanitize_text_field($input);
    }
}

/**
 * Load JS
 *
 * @param string
 * @return void
 */
function klypCF7ToZohoLoadJS($hook)
{
    // only fire up when we are editing contact
    if ($hook == 'toplevel_page_wpcf7' || $hook == 'settings_page_klyp-cf7-to-zoho') {
        wp_enqueue_script('klyp-cf7-to-zoho-js', plugins_url('/assets/js/main.js', dirname(__FILE__)));
    }
}
add_action('admin_enqueue_scripts', 'klypCF7ToZohoLoadJS');

/**
 * Get Token Access
 *
 * @return JSON
 */
function klypCF7ToZohoGetTokenAccess()
{
    if (! wp_verify_nonce($_REQUEST['nonce'], 'klypCF7ToZoho')) {
        $return['message'] = esc_html__('Invalid request.');
        wp_send_json_error($return);
        die();
    }

    // set the params
    $params['client_id']        = klypCF7ToZohoSanitizeInput($_POST['client_id']);
    $params['client_secret']    = klypCF7ToZohoSanitizeInput($_POST['client_secret']);
    $params['redirect_uri']     = klypCF7ToZohoSanitizeInput($_POST['redirect_uri']);
    $params['code']             = klypCF7ToZohoSanitizeInput($_POST['code']);
    $params['grant_type']       = 'authorization_code';
    $url                        = 'https://accounts.zoho.' . klypCF7ToZohoSanitizeInput($_POST['data_centre']) . '/oauth/v2/token';

    // get response
    $response = wp_remote_post(
        $url . '?' . http_build_query($params),
        array (
            'method'  => 'POST',
            'headers' => array (
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
        ),
        $params
    );

    $body = wp_remote_retrieve_body($response);

    if (defined('WP_DEBUG') && true === WP_DEBUG) {
        error_log('Start of getting token: ' . date('l jS \of F Y h:i:s A'));
        error_log('URL: ' . $url);
        error_log('Response: ' . print_r($response, true));
        error_log('Body: ' . print_r($body, true));
        error_log('End of getting token: ' . date('l jS \of F Y h:i:s A'));
    }
    
    echo $body;
    die();
}
add_action('wp_ajax_klypCF7ToZohoGetTokenAccess', 'klypCF7ToZohoGetTokenAccess');
