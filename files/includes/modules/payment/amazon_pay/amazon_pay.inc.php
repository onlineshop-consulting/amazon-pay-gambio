<?php
require_once __DIR__ . '/vendor/autoload.php';
$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('amazon_pay', $_SESSION['languages_id']);

if (!class_exists(\OncoAmazonPay\Utils::class)) {
    foreach (glob(__DIR__ . '/classes/*.php') as $file) {
        require_once $file;
    }
    foreach (glob(__DIR__ . '/classes/*/*.php') as $file) {
        require_once $file;
    }
}