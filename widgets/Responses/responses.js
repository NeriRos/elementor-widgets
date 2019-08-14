// widgetName = "response";
var quota = 0;
var quotaPreText = "תגובות:";
var quotaSelector = "#responseForm input#form-field-quota";
var userIdSelector = "#responseForm input#form-field-userId";
var nonceSelector = "#responseForm input#form-field-nonce";

jQuery(document).ready(function($) {
    $("#respond-popup-open-button").click(() => {
        setTimeout(() => {
            var quotaElement = $(quotaSelector);
            var nonceElement = $(nonceSelector);
            var userIdElement = $(userIdSelector);
    
            userIdElement.val(userId);
            nonceElement.val(admin_ajax.nonce_create);
            quotaElement.attr('readonly', true);

            $('#responseForm').submit(function(e) {
                e.preventDefault(); // cancel default submit

                if(quota == 0) {
                    this.action = '/product/response';
                    this.method = 'GET';
                    this.submit();
                }
                // TODO : BACKEND GET SEARCH PARAMS AND SAVE FOR POSTING AFTER PURCHASE

                if (this.checkValidity() == false) {
                    return false;
                }

                var form = $(this);
                $.post(admin_ajax.url, form.serialize() + "&action=elementor_pro_forms_send_form", function(data) 
                {}).done(function(response) {
                    var jsonData = (response.data);
                    if(response.success)
                        setQuota(jsonData.data.newQuota);
                }).fail(function(data) {
                    console.log(data.responseText);
                });
            });
        });
        
        setQuota(quota);
    });
});

jQuery.ajax({
    url: admin_ajax.url,
    type: `get`,
    dataType: `json`,
    data: {
        userId,
        nonce: admin_ajax.nonce,
        action: admin_ajax.actions.get_quota
    },
    success: function(quotaResponse) {
        if(quotaResponse.error) {
            alert("there was a problem with getting the quota, please try again later.");
            console.log(quotaResponse);
            return;
        }

        //quota = quotaResponse.quota;
        setTimeout(() => setQuota(quota), 2000);
    },
    error: function(xhr) {
        console.log(xhr.responseText);
    }, done: function() {       
    }
});


function setQuota(quota, element) {
    setTimeout(() => {
        var quotaElement = (element ? element : jQuery(quotaSelector));
        if (quota == 0) {
            // quotaElement.after("<a");
            jQuery('.elementor-field-type-submit button').text("רכוש עוד תגובות");
        } else {
            quotaElement.val(quotaPreText + ' ' + quota);
        }
        

    });    
}