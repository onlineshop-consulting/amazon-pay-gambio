<?php
$t_language_text_section_content_array = [
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_DESCRIPTION' => 'Amazon Pay',
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_TITLE' => 'Amazon Pay',
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_INFO' => '',
    'MODULE_PAYMENT_AMAZON_PAY_STATUS_TITLE' => 'Activate Amazon Pay',
    'MODULE_PAYMENT_AMAZON_PAY_STATUS_DESC' => 'Do you want to accept payments via Amazon Pay?',
    'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER_TITLE' => 'Display order',
    'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER_DESC' => 'Order of display. The smallest number is displayed first.',
    'MODULE_PAYMENT_AMAZON_PAY_ZONE_TITLE' => 'Payment zone',
    'MODULE_PAYMENT_AMAZON_PAY_ZONE_DESC' => 'If a zone is selected, the payment method only applies to this zone.',
    'MODULE_PAYMENT_AMAZON_PAY_ALLOWED_TITLE' => 'Allowed zones',
    'MODULE_PAYMENT_AMAZON_PAY_ALLOWED_DESC' => 'Enter <b>individually</b> the zones which should be allowed for this module. (e.g., AT, DE (if empty, all zones will be allowed))',

    //config texts
    'APC_CONFIGURATION_TITLE' => 'Amazon Pay Configuration',
    'APC_CONFIGURATION_SAVED' => 'Amazon Pay configuration saved',
    'APC_CONFIGURATION_INTRO' => 'Here you can make the Amazon Pay configuration.',
    'APC_CONFIGURATION_HELP_LINK_CAPTION' => 'Help with setup',

    'APC_CONFIGURATION_CREDENTIALS_HEADING' => 'Credentials',
    'APC_CONFIGURATION_CREDENTIALS_INTRO' => 'The credentials must be filled in completely so that your shop can communicate with Amazon Pay and process payments.',
    'APC_MERCHANT_ID_TITLE' => 'Amazon Merchant ID',
    'APC_CLIENT_ID_TITLE' => 'Amazon Store ID',
    'APC_PUBLIC_KEY_ID_TITLE' => 'Amazon Public Key ID',
    'APC_RESET_KEY_TITLE' => 'Reset key',
    'APC_PUBLIC_KEY_TITLE' => 'My Public Key',

    'APC_CONFIGURATION_TESTING_HEADING' => 'Status and Logging',
    'APC_CONFIGURATION_TESTING_INTRO' => 'Settings for testing the connection',
    'APC_STATUS_TITLE' => 'Activate Amazon Pay',
    'APC_IS_SANDBOX_TITLE' => 'Activate sandbox',
    'APC_IS_HIDDEN_TITLE' => 'Hide buttons',
    'APC_LOG_LEVEL_TITLE' => 'Log level',
    'APC_LOG_LEVEL_DEBUG_OPTION_LABEL' => 'Debug - Log everything (for troubleshooting)',
    'APC_LOG_LEVEL_ERROR_OPTION_LABEL' => 'Error - Only log errors',

    'APC_CONFIGURATION_IPN_HEADING' => 'Send payment notification to Gambio',
    'APC_CONFIGURATION_IPN_INTRO' => 'In order for the shop to receive notifications about the current status of the payment, the following URL must be stored in the Seller Central.',
    'APC_IPN_URL_TITLE' => 'URL for Amazon IPN',

    'APC_CONFIGURATION_STYLE_HEADING' => 'Presentation',
    'APC_CONFIGURATION_STYLE_INTRO' => 'Decide here how the Amazon Pay buttons should be displayed in your shop.',
    'APC_CHECKOUT_BUTTON_COLOR_TITLE' => 'Checkout button color',
    'APC_SHOW_BUTTON_ON_PDP' => 'Show button on product page',
    'APC_LOGIN_BUTTON_COLOR_TITLE' => 'Login button color',
    'APC_CHECKOUT_BUTTON_COLOR_GOLD_OPTION_LABEL' => 'Gold',
    'APC_CHECKOUT_BUTTON_COLOR_LIGHTGRAY_OPTION_LABEL' => 'Light gray',
    'APC_CHECKOUT_BUTTON_COLOR_DARKGRAY_OPTION_LABEL' => 'Dark gray',
    'APC_LOGIN_BUTTON_NO_LOGIN_OPTION_LABEL' => 'Do not offer separate login with Amazon',

    'APC_CONFIGURATION_SET_STATUS_HEADING' => 'Set order status',
    'APC_CONFIGURATION_SET_STATUS_INTRO' => 'When the status of the payment changes, the status of the order in the shop can be automatically adjusted.',
    'APC_ORDER_STATUS_AUTHORIZED_TITLE' => 'Status for authorized orders',
    'APC_ORDER_STATUS_FAILED_TITLE' => 'Status for failed orders',
    'APC_ORDER_STATUS_CAPTURED_COMPLETELY_TITLE' => 'Status for orders with captured payment',
    'APC_ORDER_STATUS_CAPTURED_PARTLY_TITLE' => 'Status for orders with partly captured payment',
    'APC_ORDER_STATUS_REFUNDED_COMPLETELY_TITLE' => 'Status for orders with refunded payment',
    'APC_ORDER_STATUS_REFUNDED_PARTLY_TITLE' => 'Status for orders with partly refunded payment',
    'APC_CHECKOUT_ORDER_STATUS_NO_CHANGE' => 'Leave status unchanged',

    'APC_CONFIGURATION_AUTOMATION_HEADING' => 'Payment process',
    'APC_CONFIGURATION_AUTOMATION_INTRO' => 'Here you can determine how payments should be collected or refunded.',
    'APC_CAPTURE_MODE_TITLE' => 'Type of payment collection',
    'APC_CAPTURE_MODE_IMMEDIATELY' => 'Collect payment immediately',
    'APC_CAPTURE_MODE_MANUALLY' => 'Collect payment manually or when status changes',
    'APC_CAN_HANDLE_PENDING' => 'Allow orders for which the payment has not yet been finally confirmed. The final confirmation will be made within 24h.',

    'APC_CONFIGURATION_STATUS_TRIGGER_HEADING' => 'Actions on status change',
    'APC_CONFIGURATION_STATUS_TRIGGER_INTRO' => 'The following actions can be performed when the status of an order changes. Please make sure there are no conflicts with the status changes set in the following section.',
    'APC_ORDER_STATUS_TRIGGER_CAPTURE_TITLE' => 'Collect payment at this order status',
    'APC_ORDER_STATUS_TRIGGER_REFUND_TITLE' => 'Arrange for full refund at this order status',

    'APC_ORDER_STATUS_SHIPPED_TITLE' => 'Status for shipped orders',

    'APC_VALIDATION_SUCCESS' => 'Access data successfully checked',
    'APC_VALIDATION_CREDENTIALS_INCOMPLETE' => 'All fields must be filled out',
    'APC_VALIDATION_INVALID_KEY' => 'No valid key could be found',
    'APC_VALIDATION_INITIALIZE_CLIENT' => 'The API client could not be initialized',
    'APC_VALIDATION_CREATE_SESSION' => 'There was an error',
    'APC_VALIDATION_ADDRESS_RESTRICTION' => 'One of the countries stored in this shop has an incorrect ISO code',

    'APC_CONFIGURATION_SAVE' => 'Save',

    //shop
    'TEXT_AMAZON_PAY_ERROR' => 'Your payment was not successful. Please use a different payment method',
    'TEXT_AMAZON_PAY_PENDING' => 'Your payment with Amazon Pay is currently still under review. Please note that we will contact you shortly by email if there should be any uncertainties.',
];