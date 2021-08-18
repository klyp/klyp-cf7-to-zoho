<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

class klypZoho
{
    private $clientId;
    private $clientSecret;
    public $token;
    public $refreshToken;
    public $dataCentre;

    public $cf7FormId;
    public $cf7FormFields;
    public $zhFormMethod;
    public $zhFormLeadSource;
    public $zhFormFields;
    public $zhObject;

    public $xnQsjsdp;
    public $xmIwtLD;

    public $data = array ();
    public $postedData = array ();

    public function __construct()
    {
        $this->clientId     = get_option('klyp_cf7tozoho_client_id');
        $this->clientSecret = get_option('klyp_cf7tozoho_client_secret');
        $this->token        = get_option('klyp_cf7tozoho_access_token');
        $this->refreshToken = get_option('klyp_cf7tozoho_refresh_token');
        $this->dataCentre   = get_option('klyp_cf7tozoho_data_centre');
    }

    private function remotePost($url, $method = 'POST', $body, $headers = array())
    {
        $response = wp_remote_post(
            $url,
            array (
                'method'  => $method,
                'body'    => wp_json_encode($body),
                'headers' => $headers
            )
        );

        return $response;
    }

    private function remoteGet($url, $headers)
    {
        $response = wp_remote_get(
            $url,
            array (
                'headers' => $headers
            )
        );

        return $response;
    }

    public function remoteStatus($response)
    {
        if (is_wp_error($response)) {
            $status = $response->get_error_code();
        } else {
            $status = wp_remote_retrieve_response_code($response);
        }

        return $status;
    }

    private function processData()
    {
        // add lead source
        if ($this->zhFormLeadSource != '') {
            $this->data += array(($this->zhFormMethod == 'Webform' ? 'Lead source' : 'Lead_Source') => $this->zhFormLeadSource);
        }

        for ($i = 0; $i <= count($this->zhFormFields); $i++) {
            if ($this->zhFormFields[$i] != '') {
                list($apiField, $webFormField) = explode('|', $this->zhFormFields[$i]);
                $theField = ($this->zhFormMethod == 'Webform' ? $webFormField : $apiField);
                $this->data += array($theField => $this->postedData[$this->cf7FormFields[$i]]);
            }
        }

        if ($this->zhFormMethod == 'Webform') {
            $this->data += array('actionType' => $this->actionType);
            $this->data += array('xnQsjsdp' => $this->xnQsjsdp);
            $this->data += array('xmIwtLD' => $this->xmIwtLD);
        }

        return $this->data;
    }

    public function refreshAccessToken()
    {
        $params['refresh_token']    = $this->refreshToken;
        $params['client_id']        = $this->clientId;
        $params['client_secret']    = $this->clientSecret;
        $params['grant_type']       = 'refresh_token';
        $url                        = 'https://accounts.zoho.' . $this->dataCentre . '/oauth/v2/token';

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

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            // log
            error_log('Start of refreshing token: ' . date('l jS \of F Y h:i:s A'));
            error_log('URL: ' . $url);
            error_log('Response: ' . print_r($response, true));
            error_log('End of refreshing token: ' . date('l jS \of F Y h:i:s A'));
        }

        $body = wp_remote_retrieve_body($response);

        if ($body) {
            $body = json_decode($body);

            if (isset($body->error)) {
                die($body->error);
            }

            //update access token
            $newToken = $body->access_token;
            update_option('klyp_cf7tozoho_access_token', $newToken);

            return $newToken;
        } else {
            die('Error refreshing token');
        }
    }

    public function getFormFields()
    {
        $url        = 'https://www.zohoapis.' . $this->dataCentre . '/crm/v2/settings/fields?module=Leads';
        $response   = $this->remoteGet($url, array('Authorization' => 'Zoho-oauthtoken ' . $this->token));
        $status     = $this->remoteStatus($response);

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            // log
            error_log('Start of getting fields: ' . date('l jS \of F Y h:i:s A'));
            error_log('URL: ' . $url);
            error_log('Response: ' . print_r($response, true));
            error_log('End of getting fields: ' . date('l jS \of F Y h:i:s A'));
        }

        if ($status == 200) {
            $body = wp_remote_retrieve_body($response);

            if ($body) {
                $body = json_decode($body);
            }

            return $body;
        } else { // refresh token
            $newRefreshToken = $this->refreshAccessToken();
            $this->token = $newRefreshToken;

            if (defined('WP_DEBUG') && true === WP_DEBUG) {
                error_log('New token generated: ' . $newRefreshToken . ' - ' . date('l jS \of F Y h:i:s A'));
            }

            return $this->getFormFields();
        }
    }

    public function webform()
    {
        $url        = 'https://crm.zoho.com.au/crm/WebToLeadForm';
        $data       = $this->processData();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }

    public function upsert()
    {
        $url        = 'https://www.zohoapis.' . $this->dataCentre . '/crm/v2/' . $this->zhObject . '/upsert';
        $data       = array('data' => array($this->processData()), 'duplicate_check_fields' => array($this->zhPrimaryKey));
        $response   = $this->remotePost($url, 'POST', $data, array('Authorization' => 'Zoho-oauthtoken ' . $this->token));
        $status     = $this->remoteStatus($response);

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            // start of log
            error_log('Start of upsert: ' . date('l jS \of F Y h:i:s A'));
            error_log('URL: ' . $url);
            error_log('Data: ' . print_r($data, true));
            error_log('Response: ' . print_r($response, true));
        }

        if ($status == 200) {
            $body = wp_remote_retrieve_body($response);

            if ($body) {
                $body = json_decode($body);
            }

            if ($body->data[0]->status == 'success') {
                $return = array (
                    'success'   => true,
                    'message'   => ''
                );
            } else {
                $return = array (
                    'success'   => false,
                    'message'   => $body->data[0]->message
                );
            }
        } else { // refresh token
            $newRefreshToken = $this->refreshAccessToken();
            $this->token = $newRefreshToken;

            if (defined('WP_DEBUG') && true === WP_DEBUG) {
                error_log('New token generated: ' . date('l jS \of F Y h:i:s A'));
            }

            $this->upsert();
        }

        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            error_log('Return: ' . print_r($return, true));
            error_log('End of upsert: ' . date('l jS \of F Y h:i:s A'));
        }

        return $return;
    }
}
