<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

/**
 * Catch contact form 7 submission
 * @param array
 * @param array
 * @return array
 */
function klypZhCf7CatchSubmission($result, $tags)
{
    if (! $result->is_valid()) {
        return $result;
    }

    if (get_option('klyp_cf7tozoho_client_id') == '' && get_option('klyp_cf7tozoho_client_secret') == '') {
        return;
    }

    // form options
    $cf7FormId          = intval(sanitize_key($_POST['_wpcf7']));
    $cf7FormFields      = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-cf-map-fields', true);
    $zhFormFields       = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-zh-map-fields', true);

    // start zoho
    $zoho = new klypZoho();
    $zoho->cf7FormId     = $cf7FormId;
    $zoho->cf7FormFields = $cf7FormFields;
    $zoho->zhFormFields  = $zhFormFields;
    $zoho->zhObject      = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-object', true);
    $zoho->zhPrimaryKey  = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-primary-key', true);
    $zoho->postedData    = klypCF7ToZohoSanitizeInput($_POST);

    // create contact
    $zohoReturn = $zoho->upsert();

    return $result;
}
add_filter('wpcf7_validate', 'klypZhCf7CatchSubmission', 10, 2);
