(function ($) {
    window.PostFinanceCheckout = {
        handler: null,
        methodConfigurationId: null,
        running: false,
        loaded: false,
        initCalls: 0,
        initMaxCalls: 10,

        initialized: function () {
            $('#PostFinanceCheckout-iframe-spinner').hide();
            $('#PostFinanceCheckout-iframe-container').show();
            $('#orderConfirmAgbBottom  button[type="submit"]').removeAttr('disabled');
            $('#orderConfirmAgbBottom  button[type="submit"]').click(function (event) {
            	event.preventDefault();
                PostFinanceCheckout.handler.validate();
                $('#orderConfirmAgbBottom  button[type="submit"]').attr('disabled', 'disabled');
                return false;
            });
            this.loaded = true;
            $('[name=PostFinanceCheckout-iframe-loaded').attr('value', 'true');
        },
        
        fallback: function() {
        	$('#PostFinanceCheckout-payment-information').toggle();
        	$('#orderConfirmAgbBottom  button[type="submit"]').removeAttr('disabled');
        },
        
        heightChanged: function () {
        	if(this.loaded && $('#PostFinanceCheckout-iframe-container > iframe').height() == 0) {
        		$('#PostFinanceCheckout-iframe-container').parent().parent().hide();
        	}
        },

        submit: function () {
            if (PostFinanceCheckout.running) {
                return;
            }
            PostFinanceCheckout.running = true;
            var params = '&stoken=' + $('input[name=stoken]').val();
            params += '&sDeliveryAddressMD5=' + $('input[name=sDeliveryAddressMD5]').val();
            params += '&challenge=' + $('input[name=challenge]').val();
            $.getJSON('index.php?cl=order&fnc=pfcConfirm' + params, '', function (data, status, jqXHR) {
                if (data.status) {
                    PostFinanceCheckout.handler.submit();
                }
                else {
                    PostFinanceCheckout.addError(data.message);
                    $('#orderConfirmAgbBottom  button[type="submit"]').removeAttr('disabled');
                }
                PostFinanceCheckout.running = false;
            }).fail((function(jqXHR, textStatus, errorThrown) {
                alert("Something went wrong: " + errorThrown);
            }));
        },

        validated: function (result) {
            if (result.success) {
                PostFinanceCheckout.submit();
            } else {
                if (result.errors) {
                    for (var i = 0; i < result.errors.length; i++) {
                        PostFinanceCheckout.addError(result.errors[i]);
                    }
                }
                $('#orderConfirmAgbBottom  button[type="submit"]').removeAttr('disabled');
            }
        },

        init: function (methodConfigurationId) {
        	this.initCalls++;
            if (typeof window.IframeCheckoutHandler === 'undefined') {
            	if(this.initCalls < this.initMaxCalls) {
	                setTimeout(function () {
	                    PostFinanceCheckout.init(methodConfigurationId);
	                }, 500);
            	} else {
            		this.fallback();
            	}
            } else {
                PostFinanceCheckout.methodConfigurationId = methodConfigurationId;
                PostFinanceCheckout.handler = window
                    .IframeCheckoutHandler(methodConfigurationId);
                PostFinanceCheckout.handler.setInitializeCallback(this.initialized);
                PostFinanceCheckout.handler.setValidationCallback(this.validated);
                PostFinanceCheckout.handler.setHeightChangeCallback(this.heightChanged);
                PostFinanceCheckout.handler.create('PostFinanceCheckout-iframe-container');
            }
        },

        addError: function (message) {
            $('#PostFinanceCheckout-iframe-container').find('div.error').remove();
            $('#PostFinanceCheckout-iframe-container').prepend($("<div class='status error corners'><p style='padding-left:3em;'>" + message + "</p></div>"));
            $('html, body').animate({
                scrollTop: $('#PostFinanceCheckout-iframe-container').find('div.error').offset().top
            }, 200);
        }
    }
})(jQuery);