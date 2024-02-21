var OncoAmazonPay = {
    payButtonCount: 0,
    debugMode: true,
    boot: function () {
        if (AmazonPayConfiguration.localButtonHtml) {
            OncoAmazonPay.registerCheckoutButtons();
            setInterval(function () {
                OncoAmazonPay.registerCheckoutButtons();
            }, 200);
        } else {
            OncoAmazonPay.loadJs(function () {
                OncoAmazonPay.init();
                setInterval(function () {
                    OncoAmazonPay.registerCheckoutButtons();
                }, 200);
            })
        }
        OncoAmazonPay.registerGambioEvents();
    },

    loadJs: function (callback) {
        const script = document.createElement('script');
        script.src = 'https://static-eu.payments-amazon.com/checkout.js';
        script.async = true;
        document.body.appendChild(script);
        script.onload = function () {
            callback();
        };
    },
    init: function () {
        OncoAmazonPay.registerChangeActions();
        OncoAmazonPay.registerCheckoutButtons();
        OncoAmazonPay.registerLoginButtons();
    },
    registerChangeActions: function () {
        if (typeof amazon === 'undefined') {
            return;
        }
        try {
            amazon.Pay.bindChangeAction('#amazon-pay-change-address', {
                amazonCheckoutSessionId: AmazonPayConfiguration.checkoutSessionId,
                changeAction: 'changeAddress'
            });
        } catch (e) {
            if (OncoAmazonPay.debugMode) {
                console.warn(e);
            }
        }
        try {
            amazon.Pay.bindChangeAction('#amazon-pay-change-payment', {
                amazonCheckoutSessionId: AmazonPayConfiguration.checkoutSessionId,
                changeAction: 'changePayment'
            });
        } catch (e) {
            if (OncoAmazonPay.debugMode) {
                console.warn(e);
            }
        }
    },

    registerCheckoutButtons: function () {
        try {
            const buttons = document.querySelectorAll('.amazon-pay-button');
            for (let i = 0; i < buttons.length; i++) {
                const button = buttons[i];
                if (!button.id) {
                    const id = 'amazon-pay-button-' + OncoAmazonPay.payButtonCount++;
                    button.id = id;
                    if (AmazonPayConfiguration.localButtonHtml) {
                        OncoAmazonPay.registerLocalCheckoutButton(button);
                    } else {
                        OncoAmazonPay.registerOriginalCheckoutButton(button);
                    }
                }
            }
        } catch (e) {
            if (OncoAmazonPay.debugMode) {
                console.warn(e);
            }
        }
    },

    registerLocalCheckoutButton: function (button) {
        OncoAmazonPay.addShadowRoot('#' + button.id, AmazonPayConfiguration.localButtonHtml);
        button.addEventListener('click', e => {
            OncoAmazonPay.loadJs(() => {
                const gotoCheckout = () => {
                    const buttonConfigurationData = OncoAmazonPay.getButtonConfigurationData();
                    const checkoutConfiguration = buttonConfigurationData.buttonConfiguration;
                    window.amazon.Pay.initCheckout(checkoutConfiguration);
                };
                if (button.classList.contains('add-to-cart')) {
                    OncoAmazonPay.addToCart($(button).closest('form'), gotoCheckout);
                } else {
                    gotoCheckout();
                }
            });
        })
    },


    registerOriginalCheckoutButton: function (button) {
        const buttonConfigurationData = OncoAmazonPay.getButtonConfigurationData();
        const buttonConfiguration = buttonConfigurationData.buttonConfiguration;
        const createCheckoutSessionConfig = buttonConfigurationData.createCheckoutSessionConfig;
        if (button.getAttribute('data-amount')) {
            buttonConfiguration.estimatedOrderAmount = {
                amount: parseFloat(button.getAttribute('data-amount')).toString(),
                currencyCode: AmazonPayConfiguration.currency
            };
        }

        if (button.classList.contains('add-to-cart')) {
            buttonConfiguration.createCheckoutSession = null;
        }

        const buttonResult = amazon.Pay.renderButton('#' + button.id, buttonConfiguration);

        if (button.classList.contains('add-to-cart')) {
            buttonResult.onClick(function () {
                OncoAmazonPay.addToCart($(button).closest('form'), function () {
                    buttonResult.initCheckout(createCheckoutSessionConfig);
                });
            });
        }

        if (OncoAmazonPay.debugMode) {
            console.log(buttonConfiguration);
        }
    },

    getButtonConfigurationData: function () {
        const createCheckoutSessionConfig = {
            createCheckoutSession: {
                url: AmazonPayConfiguration.createCheckoutSessionUrl
            }
        };

        return {
            createCheckoutSessionConfig: createCheckoutSessionConfig,
            buttonConfiguration: {
                merchantId: AmazonPayConfiguration.merchantId,
                createCheckoutSession: createCheckoutSessionConfig.createCheckoutSession,
                sandbox: AmazonPayConfiguration.isSandbox,
                ledgerCurrency: AmazonPayConfiguration.ledgerCurrency,
                checkoutLanguage: AmazonPayConfiguration.language,
                productType: AmazonPayConfiguration.isPayOnly ? 'PayOnly' : 'PayAndShip',
                placement: 'Cart',
                buttonColor: AmazonPayConfiguration.checkoutButtonColor
            }
        };
    },

    addToCart: function ($form, callback) {
        const $this = $form;
        $this.addClass('loading');
        if ($form.length) {
            const formdata = jse.libs.form.getData($form, null, true);
            formdata.target = 'cart';
            formdata.isProductInfo = $form.hasClass('product-info') ? 1 : 0;
            jse.libs.xhr.post({
                url: 'shop.php?do=Cart/BuyProduct',
                data: formdata
            }, true).done(function (result) {
                try {
                    if (result.success) {
                        callback();
                    }
                } catch (ignore) {
                }
            }).fail(function () {

            }).always(function () {
                $this.removeClass('loading');
            });
        }
    },

    startPurePaymentCheckout: function (createCheckoutSessionConfig) {
        amazon.Pay.initCheckout({
            merchantId: AmazonPayConfiguration.merchantId,
            ledgerCurrency: AmazonPayConfiguration.ledgerCurrency,
            sandbox: AmazonPayConfiguration.isSandbox,
            productType: AmazonPayConfiguration.isPayOnly ? 'PayOnly' : 'PayAndShip',
            placement: 'Other',
            createCheckoutSessionConfig: createCheckoutSessionConfig,
        });
    },

    registerLoginButtons: function () {
        if (AmazonPayConfiguration.loginButtonColor === '') {
            return;
        }
        try {
            const buttons = document.querySelectorAll('.amazon-login-button');
            for (let i = 0; i < buttons.length; i++) {
                const button = buttons[i];
                if (!button.id) {
                    const id = 'amazon-login-button-' + OncoAmazonPay.payButtonCount++;
                    button.id = id;
                    amazon.Pay.renderButton('#' + id, {
                        merchantId: AmazonPayConfiguration.merchantId,
                        sandbox: AmazonPayConfiguration.isSandbox,
                        ledgerCurrency: AmazonPayConfiguration.ledgerCurrency,
                        checkoutLanguage: AmazonPayConfiguration.language,
                        productType: 'SignIn',
                        placement: 'Other',
                        buttonColor: AmazonPayConfiguration.loginButtonColor,
                        signInConfig: {
                            payloadJSON: AmazonPayConfiguration.loginPayload,
                            signature: AmazonPayConfiguration.loginSignature,
                            publicKeyId: AmazonPayConfiguration.publicKeyId
                        }
                    });
                }
            }
        } catch (e) {
            if (OncoAmazonPay.debugMode) {
                console.warn(e);
            }
        }
    },
    registerGambioEvents: function () {
        if(typeof $ === 'undefined') {
            return;
        }
        const handlePdpButtonStatus = function () {
            if ($('.js-btn-add-to-cart').hasClass('inactive')) {
                $('.amazon-pay-button-pdp-container').hide();
            } else {
                $('.amazon-pay-button-pdp-container').show();
            }
        }
        handlePdpButtonStatus();
        $(window).on('STICKYBOX_CONTENT_CHANGE', handlePdpButtonStatus);
    },

    addShadowRoot: function (selector, html) {
        const containers = document.querySelectorAll(selector);
        containers.forEach(container => {
            if (container.attachShadow) {
                // Create a shadow root and append it to the container
                const shadowRoot = container.attachShadow({mode: 'open'});
                // Add content to the shadow root
                shadowRoot.innerHTML = html;
            } else {
                console.error('Shadow DOM is not supported in this browser.');
            }
        });
    }
}

if (typeof AmazonPayConfiguration !== 'undefined'
) {
    OncoAmazonPay.boot();
} else {
    window.onLoadAmazonPayConfiguration = () => {
        OncoAmazonPay.boot();
    }
}