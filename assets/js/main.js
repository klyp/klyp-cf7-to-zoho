(function($)
{
    'use strict';
    $(function()
    {
        $('.klyp-cf7-to-zoho-access-token-get').on('click', function(e) {
            e.preventDefault();
            var nonce = $(this).data('nonce'),
                adminUrl = $(this).data('admin-url'),
                client_id = $('#klyp_cf7tozoho_client_id').val(),
                client_secret = $('#klyp_cf7tozoho_client_secret').val(),
                redirect_uri = $('#klyp_cf7tozoho_redirect_uri').val(),
                code = $('#klyp_cf7tozoho_auth_code').val(),
                data_centre = $('#klyp_cf7tozoho_data_centre').val();

            $.ajax({
                type: 'post',
                url: adminUrl,
                data: {
                    client_id: client_id,
                    client_secret: client_secret,
                    redirect_uri: redirect_uri,
                    code: code,
                    data_centre: data_centre,
                    action: 'klypCF7ToZohoGetTokenAccess',
                    nonce: nonce
                },
                success: function(response) {
                    response = $.parseJSON(response);

                    if (response.error) {
                        alert('Error: ' + response.error + '. Please try generate a new Auth Code.');
                    } else if(response.access_token != '') {
                        $('#klyp_cf7tozoho_access_token').val(response.access_token);
                        $('#klyp_cf7tozoho_refresh_token').val(response.refresh_token);
                    } else {
                        alert('Error while trying to get access token, please try again.')
                    }
                }
            });
        });

        $('#klyp-cf7-to-zoho-map-add-new-map').on('click', function(e) {
            e.preventDefault();
            let tfoot = $('#klyp-cf7-to-zoho-tfoot-map').html();
            $('#klyp-cf7-to-zoho-tbody-map').prepend(tfoot);
        });

        $('body').on('click', '.klyp-cf7-to-zoho-cf-remove-map', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    });
})(jQuery);
