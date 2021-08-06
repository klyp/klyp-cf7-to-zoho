<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

$redirectURI = admin_url('options-general.php?page=klyp-cf7-to-zoho');
?>

<div class="wrap">
     
    <h2>Klyp CF7 to Zoho - Settings</h2>

    <h2 class="nav-tab-wrapper">
        <a href="?page=klyp-cf7-to-zoho" class="nav-tab nav-tab-active">Settings</a>
    </h2>

    <section>
        <form method="post" action="<?= admin_url('options.php'); ?>">
            <?php
                settings_fields(KlypCF7TOZoho);
                do_settings_sections(KlypCF7TOZoho);
            ?>

            <table style="width:100%;">
                <tr>
                    <td style="width:50%; vertical-align:top">
                        <div id="klyp-cf7-to-zoho-client-id" class="klyp-cf7-to-zoho-client-id">
                            <h2 class="title">Zoho Client ID</h2>
                            <p>Click <a href="https://www.zoho.com/writer/help/api/v1/oauth-step1.html" target="_blank">here</a> on how to access your client id</p>
                        </div>
                        <input type="text" name="klyp_cf7tozoho_client_id" id="klyp_cf7tozoho_client_id" class="large-text code" value="<?= esc_attr(get_option('klyp_cf7tozoho_client_id')); ?>">
                    </td>
                    <td style="width:50%; vertical-align:top">
                        <div id="klyp-cf7-to-zoho-client-secret" class="klyp-cf7-to-zoho-client-secret">
                            <h2 class="title">Zoho Client Secret</h2>
                            <p>Click <a href="https://www.zoho.com/writer/help/api/v1/oauth-step1.html" target="_blank">here</a> on how to get your client secret.</p>
                            <input type="text" name="klyp_cf7tozoho_client_secret" id="klyp_cf7tozoho_client_secret" class="large-text code" value="<?= esc_attr(get_option('klyp_cf7tozoho_client_secret')); ?>">
                            <p>Make sure to enter <strong><?= $redirectURI; ?></strong> as your Authorized Redirect URIs;</p>
                        </div>
                    </td>
                </tr>
            </table>

            <?php if (get_option('klyp_cf7tozoho_client_id') != '' && get_option('klyp_cf7tozoho_client_secret') != '') { ?>
                <table style="width:100%;">
                    <tr>
                        <td colspan="3">
                            <div id="klyp-cf7-to-zoho-data=centre" class="klyp-cf7-to-zoho-data=centre">
                                <h2 class="title">Zoho Data Centre</h2>
                                <p>Select the data centre</p>
                                <select name="klyp_cf7tozoho_data_centre" id="klyp_cf7tozoho_data_centre" class="large-text code">
                                    <option value="com" <?= (esc_attr(get_option('klyp_cf7tozoho_data_centre')) == 'com' ? 'selected' : ''); ?>>zoho.com (Global - USA)</option>
                                    <option value="eu" <?= (esc_attr(get_option('klyp_cf7tozoho_data_centre')) == 'eu' ? 'selected' : ''); ?>>zoho.eu (Europe)</option>
                                    <option value="in" <?= (esc_attr(get_option('klyp_cf7tozoho_data_centre')) == 'in' ? 'selected' : ''); ?>>zoho.in (India)</option>
                                    <option value="com.cn" <?= (esc_attr(get_option('klyp_cf7tozoho_data_centre')) == 'com.cn' ? 'selected' : ''); ?>>zoho.com.cn (China)</option>
                                    <option value="com.au" <?= (esc_attr(get_option('klyp_cf7tozoho_data_centre')) == 'com.au' ? 'selected' : ''); ?>>zoho.com.au (Australia)</option>
                                </select>

                                <input type="hidden" name="klyp_cf7tozoho_redirect_uri" id="klyp_cf7tozoho_redirect_uri" value="<?= $redirectURI; ?>">
                            </div>
                        </td>
                    </tr>

                    <?php if (get_option('klyp_cf7tozoho_data_centre') != '') { ?>
                    <tr>
                        <td style="width:33%; vertical-align:top">
                            <div id="klyp-cf7-to-zoho-auth-code" class="klyp-cf7-to-zoho-auth-code">
                                <h2 class="title">Zoho Auth Code</h2>
                                <p>Click 
                                    <a href="https://accounts.zoho.<?= esc_attr(get_option('klyp_cf7tozoho_data_centre')); ?>/oauth/v2/auth?scope=ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.Read,ZohoCRM.files.CREATE&response_type=code&client_id=<?= esc_attr(get_option('klyp_cf7tozoho_client_id')); ?>&access_type=offline&redirect_uri=<?= admin_url('options-general.php?page=klyp-cf7-to-zoho'); ?>">here</a> 
                                    to get your access token.
                                </p>
                                <input type="text" name="klyp_cf7tozoho_auth_code" id="klyp_cf7tozoho_auth_code" class="large-text code" value="<?= isset($_GET['code']) ? $_GET['code'] : esc_attr(get_option('klyp_cf7tozoho_auth_code')); ?>">
                            </div>
                        </td>
                        <td style="width:33%; vertical-align:top">
                            <div id="klyp-cf7-to-zoho-access-token" class="klyp-cf7-to-zoho-access-token">
                                <h2 class="title">Zoho Access Token</h2>
                                <p>Click <a href="#" id="klyp-cf7-to-zoho-access-token-get" class="klyp-cf7-to-zoho-access-token-get" data-admin-url="<?= admin_url('admin-ajax.php'); ?>" data-nonce="<?= wp_create_nonce('klypCF7ToZoho'); ?>">here</a> to get your access token.</p>
                                <input type="text" name="klyp_cf7tozoho_access_token" id="klyp_cf7tozoho_access_token" class="large-text code" value="<?= esc_attr(get_option('klyp_cf7tozoho_access_token')); ?>">
                            </div>
                        </td>
                        <td style="width:33%; vertical-align:top">
                            <div id="klyp-cf7-to-zoho-refresh-token" class="klyp-cf7-to-zoho-refresh-token">
                                <h2 class="title">Zoho Refresh Token</h2>
                                <p>Click <a href="#" id="klyp-cf7-to-zoho-access-refresh-token-get" class="klyp-cf7-to-zoho-access-token-get" data-admin-url="<?= admin_url('admin-ajax.php'); ?>" data-nonce="<?= wp_create_nonce('klypCF7ToZoho'); ?>">here</a> to get your access token.</p>
                                <input type="text" name="klyp_cf7tozoho_refresh_token" id="klyp_cf7tozoho_refresh_token" class="large-text code" value="<?= esc_attr(get_option('klyp_cf7tozoho_refresh_token')); ?>">
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            <?php } ?>
            <input type="hidden" name="_wp_http_referer" value="<?= $redirectURI; ?>" />
            <?= submit_button('Save Settings'); ?>
        </form>
    </section>
</div>
