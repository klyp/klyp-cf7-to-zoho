<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

/**
 * Submit to zoho
 * @return void
 */
function klypZhCf7SendToZoho()
{
    // form options
    $cf7FormId          = intval(sanitize_key($_POST['_wpcf7']));
    $cf7FormFields      = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-cf-map-fields', true);
    $zhFormLeadSource   = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-leadsource', true);
    $zhFormFields       = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-zh-map-fields', true);
    $zhFormMethod       = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-method', true);

    // start zoho
    $zoho = new klypZoho();
    $zoho->cf7FormId        = $cf7FormId;
    $zoho->cf7FormFields    = $cf7FormFields;
    $zoho->zhFormMethod     = $zhFormMethod;
    $zoho->zhFormLeadSource = $zhFormLeadSource;
    $zoho->zhFormFields     = $zhFormFields;
    $zoho->postedData       = klypCF7ToZohoSanitizeInput($_POST);

    // create contact
    if ($zhFormMethod == 'API') {
        $zoho->zhObject      = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-object', true);
        $zoho->zhPrimaryKey  = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-primary-key', true);
        $zohoReturn          = $zoho->upsert();
    } elseif ($zhFormMethod == 'Webform') {
        $zoho->actionType    = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-method-actionType', true);
        $zoho->xnQsjsdp      = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-method-xnQsjsdp', true);
        $zoho->xmIwtLD       = get_post_meta($cf7FormId, '_klyp-cf7-to-zoho-method-xmIwtLD', true);
        $zohoReturn          = $zoho->webform();
    }
}

/**
 * If submission is spam
 * @param array
 * @param array
 * @return void
 */
function klypZhCf7CatchSpam($spam, $instance)
{
    if ($spam) {
        return $spam;
    }

    return klypZhCf7SendToZoho();
}
add_filter('wpcf7_spam', 'klypZhCf7CatchSpam', 10, 2);

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
        return $result;
    }

    return $result;
}
add_filter('wpcf7_validate', 'klypZhCf7CatchSubmission', 10, 2);
