<?php

// See if wordpress is properly installed
defined('ABSPATH') || die('Wordpress is not installed properly.');

/**
 * Add custom zoho settings for form 7
 * @param array
 * @return array
 */
function klypCf7zohoAdditionalSettings($panels)
{
    $panels['klyp-zoho-settings-panel'] = array (
        'title' => 'Zoho Integration',
        'callback' => 'klypCf7zohoAdditionalSettingsTab'
    );

    return $panels;
}
add_filter('wpcf7_editor_panels', 'klypCf7zohoAdditionalSettings');

/**
 * Add custom zoho settings for form 7
 * @param object
 * @return string
 */
function klypCf7zohoAdditionalSettingsTab($post)
{
    $cfFields       = klypCf7ZohoGetCfFormFields($post->id());
    $klypZoho       = new klypZoho();
    $zhFields       = $klypZoho->getFormFields();
    $zhFields       = $zhFields->fields;

    echo '
        <h2>Zoho Settings</h2>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <td>
                        <p class="description">
                            Zoho object
                        </p>
                        <label for="klyp-cf7-to-zoho-object">
                        <select id="klyp-cf7-to-zoho-object" name="klyp-cf7-to-zoho-object" class="large-text code">
                            <option value="Leads" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Leads' ? 'selected' : '') . '>Leads</option>
                            <option value="Contacts" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Contacts' ? 'selected' : '') . '>Contacts</option>
                            <option value="Accounts" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Accounts' ? 'selected' : '') . '>Accounts</option>
                            <option value="Deals" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Deals' ? 'selected' : '') . '>Deals</option>
                            <option value="Activities" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Activities' ? 'selected' : '') . '>Activities</option>
                            <option value="Products" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Products' ? 'selected' : '') . '>Products</option>
                            <option value="Quotes" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Quotes' ? 'selected' : '') . '>Quotes</option>
                            <option value="Sales_Orders" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Sales_Orders' ? 'selected' : '') . '>Sales Orders</option>
                            <option value="Purchase_Orders" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Purchase_Orders' ? 'selected' : '') . '>Purchase Orders</option>
                            <option value="Invoices" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Invoices' ? 'selected' : '') . '>Invoices</option>
                            <option value="Campaigns" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Campaigns' ? 'selected' : '') . '>Campaigns</option>
                            <option value="Vendors" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Vendors' ? 'selected' : '') . '>Vendors</option>
                            <option value="Price_Books" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Price_Books' ? 'selected' : '') . '>Price Books</option>
                            <option value="Cases" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Cases' ? 'selected' : '') . '>Cases</option>
                            <option value="Solutions" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Solutions' ? 'selected' : '') . '>Solutions</option>
                            <option value="Calls" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Calls' ? 'selected' : '') . '>Calls</option>
                            <option value="Tasks" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Tasks' ? 'selected' : '') . '>Tasks</option>
                            <option value="Events" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Events' ? 'selected' : '') . '>Meetings</option>
                            <option value="Notes" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Notes' ? 'selected' : '') . '>Notes</option>
                            <option value="Projects" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-object', true)) == 'Projects' ? 'selected' : '') . '>Projects</option>
                        </select>
                        </label>
                    </td>
                    <td>
                        <p class="description">
                            Primary Key is used as a unique identifier to determine pre-existing object.
                        </p>
                        <select name="klyp-cf7-to-zoho-primary-key" id="klyp-cf7-to-zoho-primary-key" class="large-text code">
                            <option value="">Please select zoho field to map</option>';

                            foreach ($zhFields as $key => $zhField) {
                                if ($zhField->api_name != '') {
                                    echo '<option value="' . $zhField->api_name . '" ' . (esc_html(get_post_meta($post->id(), '_klyp-cf7-to-zoho-primary-key', true)) == $zhField->api_name ? 'selected' : '') . '>' . $zhField->field_label . '</option>';
                                }
                            }
    echo '
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>';


    echo '<p><hr></p>

        <h2>Form Mapping</h2>
        <p>Map contact form fields to Zoho fields</p>
        <table class="form-table" role="presentation">
            <thead>
                <tr>
                    <th width="45%">
                        Contact Form Field
                    </th>
                    <th width="45%">
                        Zoho Form Field
                    </th>
                    <td width="10%" align="right">
                        <button id="klyp-cf7-to-zoho-map-add-new-map">+</button>
                    </td>
                </tr>
            </thead>
            <tbody id="klyp-cf7-to-zoho-tbody-map">';
                $cfMapFormFields = get_post_meta($post->id(), '_klyp-cf7-to-zoho-cf-map-fields', true);
                $zhMapFormFields = get_post_meta($post->id(), '_klyp-cf7-to-zoho-zh-map-fields', true);

                if (! empty($cfMapFormFields)) {
                    for ($i = 0; $i <= count($cfMapFormFields); $i++) {

                        if (empty($cfMapFormFields[$i]) || empty($zhMapFormFields[$i])) {
                            continue;
                        }

                        echo '
                            <tr>
                                <td>
                                    <select id="klyp-cf7-to-zoho-cf-map-fields" name="klyp-cf7-to-zoho-cf-map-fields[]" class="large-text code">
                                        <option value="">Please select form field to map</option>';

                                        foreach ($cfFields as $key => $cfField) {
                                            if ($cfField->name != '') {
                                                echo '<option value="' . $cfField->name . '" ' . ($cfMapFormFields[$i] == $cfField->name ? 'selected="selected"' : '') . '>' . $cfField->name . '</option>';
                                            }
                                        }
                        echo '
                                    </select>
                                </td>
                                <td>
                                    <select id="klyp-cf7-to-zoho-zh-map-fields" name="klyp-cf7-to-zoho-zh-map-fields[]" class="large-text code">
                                        <option value="">Please select zoho field to map</option>';

                                        foreach ($zhFields as $key => $zhField) {
                                            if ($zhField->api_name != '') {
                                                echo '<option value="' . $zhField->api_name . '" ' . ($zhMapFormFields[$i] == $zhField->api_name ? 'selected="selected"' : '') . '>' . $zhField->field_label . '</option>';
                                            }
                                        }
                        echo '
                                </td>
                                <td align="right">
                                    <button class="klyp-cf7-to-zoho-cf-remove-map">x</button>
                                </td>
                            </tr>';
                    }
                }
        echo '
            </tbody>
            <tfoot id="klyp-cf7-to-zoho-tfoot-map" style="display:none;">
                <tr>
                    <td>
                        <select id="klyp-cf7-to-zoho-cf-map-fields" name="klyp-cf7-to-zoho-cf-map-fields[]" class="large-text code">
                                <option value="">Please select form field to map</option>';

                            foreach ($cfFields as $key => $cfField) {
                                if ($cfField->name != '') {
                                    echo '<option value="' . $cfField->name . '">' . $cfField->name . '</option>';
                                }
                            }
        echo '
                        </select>
                    </td>
                    <td>
                        <select id="klyp-cf7-to-zoho-zh-map-fields" name="klyp-cf7-to-zoho-zh-map-fields[]" class="large-text code">
                            <option value="">Please select zoho field to map</option>';

                            foreach ($zhFields as $key => $zhField) {
                                if ($zhField->api_name != '') {
                                    echo '<option value="' . $zhField->api_name . '">' . $zhField->field_label . '</option>';
                                }
                            }
        echo '          </select>
                    </td>
                    <td align="right">
                        <button class="klyp-cf7-to-zoho-cf-remove-map">x</button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <p><hr></p>';
}

/**
 * On save settings
 * @param object
 * @param array
 * @return void
 */
function klypCf7zohoSaveContactForm($contact_form, $args)
{
    // allowed fields
    $cs7Fields = array (
        'klyp-cf7-to-zoho-object',
        'klyp-cf7-to-zoho-primary-key',
        'klyp-cf7-to-zoho-cf-map-fields',
        'klyp-cf7-to-zoho-zh-map-fields'
    );

    klypCf7zohoSaveSettings($args['id'], $cs7Fields);
}
add_action('wpcf7_save_contact_form', 'klypCf7zohoSaveContactForm', 10 ,2);


/**
 * Save CF7 settings
 * @param int
 * @param array
 */
function klypCf7zohoSaveSettings($contact_form, $cs7Fields)
{
    foreach ($cs7Fields as $key) {
        if (isset($_POST[$key]) && ! is_array($_POST[$key]) && $_POST[$key] == '') {
            delete_post_meta($contact_form, '_' . $key);
        } elseif (isset($_POST[$key]) && $_POST[$key] != null) {
            $sanitizedValue = klypCF7ToZohoSanitizeInput($_POST[$key]);
            update_post_meta($contact_form, '_' . $key, $sanitizedValue);
        }
    }
}

/**
 * Get CF7 form fields
 * @param int
 * @return obj
 */
function klypCf7ZohoGetCfFormFields($formId)
{
    $cf7Form = WPCF7_ContactForm::get_instance($formId);
    return $cf7Form->scan_form_tags();
}
