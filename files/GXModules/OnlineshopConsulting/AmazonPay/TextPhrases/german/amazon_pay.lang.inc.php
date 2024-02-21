<?php
$t_language_text_section_content_array = [
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_DESCRIPTION' => 'Amazon Pay',
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_TITLE' => 'Amazon Pay',
    'MODULE_PAYMENT_AMAZON_PAY_TEXT_INFO' => '',
    'MODULE_PAYMENT_AMAZON_PAY_STATUS_TITLE' => 'Amazon Pay aktivieren',
    'MODULE_PAYMENT_AMAZON_PAY_STATUS_DESC' => 'M&ouml;chten Sie Zahlungen per Amazon Pay akzeptieren?',
    'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
    'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
    'MODULE_PAYMENT_AMAZON_PAY_ZONE_TITLE' => 'Zahlungszone',
    'MODULE_PAYMENT_AMAZON_PAY_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist => gilt die Zahlungsmethode nur für diese Zone.',
    'MODULE_PAYMENT_AMAZON_PAY_ALLOWED_TITLE' => 'Erlaubte Zonen',
    'MODULE_PAYMENT_AMAZON_PAY_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an => welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer => werden alle Zonen erlaubt))',

    //config texts
    'APC_CONFIGURATION_TITLE' => 'Amazon Pay Konfiguration',
    'APC_CONFIGURATION_SAVED' => 'Amazon Pay Konfiguration gespeichert',
    'APC_CONFIGURATION_INTRO' => 'Hier können Sie die Amazon Pay Konfiguration vornehmen.',
    'APC_CONFIGURATION_HELP_LINK_CAPTION' => 'Hilfe bei der Einrichtung',

    'APC_CONFIGURATION_CREDENTIALS_HEADING' => 'Zugangsdaten',
    'APC_CONFIGURATION_CREDENTIALS_INTRO' => 'Die Zugangsdaten müssen vollständig ausgefüllt werden, damit Ihr Shop mit Amazon Pay kommunizieren und Zahlungen abwickeln kann.',
    'APC_MERCHANT_ID_TITLE' => 'Amazon Händler-ID',
    'APC_CLIENT_ID_TITLE' => 'Amazon Store-ID',
    'APC_PUBLIC_KEY_ID_TITLE' => 'Amazon Public Key-ID',
    'APC_RESET_KEY_TITLE' => 'Key zurücksetzen',
    'APC_PUBLIC_KEY_TITLE' => 'Mein Public Key',

    'APC_CONFIGURATION_TESTING_HEADING' => 'Status und Logging',
    'APC_CONFIGURATION_TESTING_INTRO' => 'Einstellungen zum Testen der Anbindung',
    'APC_STATUS_TITLE' => 'Amazon Pay aktivieren',
    'APC_IS_SANDBOX_TITLE' => 'Sandbox aktivieren',
    'APC_IS_HIDDEN_TITLE' => 'Buttons verstecken',
    'APC_LOG_LEVEL_TITLE' => 'Log-Level',
    'APC_LOG_LEVEL_DEBUG_OPTION_LABEL' => 'Debug - Alles loggen (für Fehlersuche)',
    'APC_LOG_LEVEL_ERROR_OPTION_LABEL' => 'Error - Nur Fehler loggen',

    'APC_CONFIGURATION_IPN_HEADING' => 'Zahlungsbenachrichtigung an Gambio senden',
    'APC_CONFIGURATION_IPN_INTRO' => 'Damit der Shop Benachrichtigungen über den aktuellen Stand der Zahlung erhalten kann, muss die folgende URL in der Seller Central hinterlegt werden.',
    'APC_IPN_URL_TITLE' => 'URL für Amazon IPN',

    'APC_CONFIGURATION_STYLE_HEADING' => 'Darstellung',
    'APC_CONFIGURATION_STYLE_INTRO' => 'Entscheiden Sie hier, wie die Amazon Pay Buttons in Ihrem Shop dargestellt werden sollen.',
    'APC_CHECKOUT_BUTTON_COLOR_TITLE' => 'Farbe des Checkout-Buttons',
    'APC_SHOW_BUTTON_ON_PDP' => 'Button auf Produktseite anzeigen',
    'APC_LOGIN_BUTTON_COLOR_TITLE' => 'Farbe des Login-Buttons',
    'APC_CHECKOUT_BUTTON_COLOR_GOLD_OPTION_LABEL' => 'Gold',
    'APC_CHECKOUT_BUTTON_COLOR_LIGHTGRAY_OPTION_LABEL' => 'Hellgrau',
    'APC_CHECKOUT_BUTTON_COLOR_DARKGRAY_OPTION_LABEL' => 'Dunkelgrau',
    'APC_LOGIN_BUTTON_NO_LOGIN_OPTION_LABEL' => 'Keinen separaten Login mit Amazon anbieten',


    'APC_CONFIGURATION_SET_STATUS_HEADING' => 'Bestellstatus setzen',
    'APC_CONFIGURATION_SET_STATUS_INTRO' => 'Bei Änderungen am Status der Zahlung kann der Status der Bestellung im Shop automatisch angepasst werden.',
    'APC_ORDER_STATUS_AUTHORIZED_TITLE' => 'Status für autorisierte Bestellungen',
    'APC_ORDER_STATUS_FAILED_TITLE' => 'Status für fehlgeschlagene Bestellungen',
    'APC_ORDER_STATUS_CAPTURED_COMPLETELY_TITLE' => 'Status für Bestellungen mit eingezogener Zahlung',
    'APC_ORDER_STATUS_CAPTURED_PARTLY_TITLE' => 'Status für Bestellungen mit teilweise eingezogener Zahlung',
    'APC_ORDER_STATUS_REFUNDED_COMPLETELY_TITLE' => 'Status für Bestellungen mit erstatteter Zahlung',
    'APC_ORDER_STATUS_REFUNDED_PARTLY_TITLE' => 'Status für Bestellungen mit teilweise erstatteter Zahlung',
    'APC_CHECKOUT_ORDER_STATUS_NO_CHANGE' => 'Status unverändert lassen',

    'APC_CONFIGURATION_AUTOMATION_HEADING' => 'Zahlungsablauf',
    'APC_CONFIGURATION_AUTOMATION_INTRO' => 'Hier können Sie festlegen, wie Zahlungen eingezogen oder erstattet werden sollen.',
    'APC_CAPTURE_MODE_TITLE' => 'Art des Zahlungseinzugs',
    'APC_CAPTURE_MODE_IMMEDIATELY' => 'Zahlung sofort einziehen',
    'APC_CAPTURE_MODE_MANUALLY' => 'Zahlung manuell oder bei Statuswechsel einziehen',
    'APC_CAN_HANDLE_PENDING' => 'Bestellungen zulassen, bei denen die Zahlung noch nicht abschließend bestätigt ist. Die endgültige Bestätigung erfolgt innerhalb von 24h.',


    'APC_CONFIGURATION_STATUS_TRIGGER_HEADING' => 'Aktionen bei Statuswechsel',
    'APC_CONFIGURATION_STATUS_TRIGGER_INTRO' => 'Die folgenden Aktionen können ausgeführt werden, wenn sich der Status einer Bestellung ändert. Bitte stellen Sie sicher, dass es keine Konflikte mit den Statusänderungen gibt, die in der nachfolgenden Sektion eingestellt werden.',
    'APC_ORDER_STATUS_TRIGGER_CAPTURE_TITLE' => 'Zahlung einziehen bei diesem Bestellstatus',
    'APC_ORDER_STATUS_TRIGGER_REFUND_TITLE' => 'Vollständige Rückzahlung veranlassen bei diesem Bestellstatus',

    'APC_ORDER_STATUS_SHIPPED_TITLE' => 'Status für versendete Bestellungen',


    'APC_VALIDATION_SUCCESS' => 'Zugangsdaten erfolgreich überprüft',
    'APC_VALIDATION_CREDENTIALS_INCOMPLETE' => 'Es müssen alle Felder ausgefüllt werden',
    'APC_VALIDATION_INVALID_KEY' => 'Es konnte kein valider Schlüssel gefunden werden',
    'APC_VALIDATION_INITIALIZE_CLIENT' => 'Der API Client konnte nicht initialisiert werden',
    'APC_VALIDATION_CREATE_SESSION' => 'Es gab einen Fehler',
    'APC_VALIDATION_ADDRESS_RESTRICTION' => 'Eines der in diesem Shop gespeicherten Länder hat einen inkorrekten ISO-Code',

    'APC_CONFIGURATION_SAVE' => 'Speichern',

    //shop
    'TEXT_AMAZON_PAY_ERROR' => 'Ihre Zahlung war nicht erfolgreich. Bitte verwenden Sie eine andere Zahlungsart',
    'TEXT_AMAZON_PAY_PENDING' => 'Ihre Zahlung mit Amazon Pay ist derzeit noch in Prüfung. Bitte beachten Sie, dass wir uns mit Ihnen in Kürze per Email in Verbindung setzen werden, falls noch Unklarheiten bestehen sollten.',
];
