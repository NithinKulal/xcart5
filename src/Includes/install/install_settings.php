<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * X-Cart installation settings
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

// Current X-Cart version
define('LC_VERSION', '5.3.2.7');

// Minimum PHP version supported
define('LC_PHP_VERSION_MIN', '5.4.0');

// Maximum PHP version supported (none if empty)
define('LC_PHP_VERSION_MAX', '');

// Minimum memory_limit option value (php.ini)
define('LC_PHP_MEMORY_LIMIT_MIN', PHP_INT_SIZE === 8 ? '256M' : '128M');

// Minimum MySQL version supported
define('LC_MYSQL_VERSION_MIN', '5.0.3');

// Default config file name
define('LC_DEFAULT_CONFIG_FILE', 'config.default.php');

// Config file name
define('LC_CONFIG_FILE', 'config.php');

// Other X-Cart settings
global $lcSettings;

$lcSettings = array(

    // Default language code
    'default_language_code' => XLITE_EDITION_LNG,

    // PHP versions that are not supported
    'forbidden_php_versions' => array(),

    'mustBeWritable' => array(
        'var',
        'images',
        'files',
        'etc' . LC_DS . 'config.php',
        '.htaccess',
    ),

    // The list of directories that should have writeble permissions
    'writable_directories' => array(
        'var',
        'images',
        'files',
    ),

    // The list of directories that should be created by installation script
    'directories_to_create' => array(
    ),

    // The list of files that should be created by installation script
    'files_to_create' => array(),

    // YAML files list
    'yaml_files' => array(
        'base' => array(
            'sql' . LC_DS . 'xlite_data.yaml',
            'sql' . LC_DS . 'xlite_data_lng.yaml',
        ),
        'demo' => XLITE_EDITION_LNG === 'ru' ? array(
            'sql' . LC_DS . 'xlite_demo.yaml',
            'sql' . LC_DS . 'xlite_demo_featured.yaml',
            'sql' . LC_DS . 'xlite_demo_orders_ru.yaml',
            'sql' . LC_DS . 'xlite_demo_ru.yaml',
            'sql' . LC_DS . 'xlite_data_ru.yaml',
            'sql' . LC_DS . 'xlite_demo_sale.yaml',
            'sql' . LC_DS . 'product_attributes.sql',
            'sql' . LC_DS . 'xlite_demo_reviews.sql',
            'sql' . LC_DS . 'xlite_menu.yaml',

        ) : XLITE_EDITION_LNG === 'zh' ? array(
            'sql' . LC_DS . 'xlite_demo_zh.yaml',
            'sql' . LC_DS . 'xlite_data_zh.yaml',
            'sql' . LC_DS . 'product_attributes_zh.sql',
            'sql' . LC_DS . 'xlite_menu_zh.yaml',
            'sql' . LC_DS . 'xlite_demo_sale_zh.yaml',
            'sql' . LC_DS . 'xlite_demo_orders_zh.yaml',

        ) : array(
            'sql' . LC_DS . 'xlite_demo.yaml',
            'sql' . LC_DS . 'xlite_demo_featured.yaml',
            'sql' . LC_DS . 'xlite_demo_orders_en.yaml',
            'sql' . LC_DS . 'xlite_demo_sale.yaml',
            'sql' . LC_DS . 'product_attributes.sql',
            'sql' . LC_DS . 'xlite_demo_reviews.sql',
            'sql' . LC_DS . 'xlite_menu.yaml',
        ),
    ),

    // The list of modules that must be enabled by installation script
    'enable_modules' => array(
        'CDev' => array(
            'AuthorizeNet',
            'Bestsellers',
            'ContactUs',
            'Coupons',
            'FeaturedProducts',
            'FedEx',
            'GoogleAnalytics',
            'GoSocial',
            'ProductAdvisor',
            'Sale',
            'SimpleCMS',
            'SocialLogin',
            'TwoCheckout',
            'UserPermissions',
            'XMLSitemap',
            'VolumeDiscounts'
        ),
        'XC' => array(
    	    'ThemeTweaker',
            'Upselling',
            'Reviews',
            'UPS',
            'News',
            'FreeShipping',
            'UpdateInventory',
            'NewsletterSubscriptions',
            'CrispWhiteSkin',
            'FastLaneCheckout',
            'BulkEditing',
            'FroalaEditor',
            'MailChimp',
            'Concierge'
        ),
        'QSL' => array(
            'CloudSearch',
            'FlyoutCategoriesMenu'
        )
    ),
);

if (XLITE_EDITION_LNG === 'ru') {
    $lcSettings['enable_modules'] = array_merge_recursive(
        $lcSettings['enable_modules'],
        array(
            'CDev' => array(
                // RU edition
                'RuTranslation',
                'Paypal'
            ),
            'XC' => array(
                // RU edition
                'EMS',
                'Robokassa',
                'Qiwi',
                'Webmoney',
                'YandexMoney'
            )
        )
    );
} elseif (XLITE_EDITION_LNG === 'zh') {
    $lcSettings['enable_modules'] = array_merge_recursive(
        $lcSettings['enable_modules'],
        array(
            'XC' => array(
                'ZhTranslation'
            )
        )
    );
} elseif (XLITE_EDITION_LNG === 'en') {
    $lcSettings['enable_modules'] = array_merge_recursive(
        $lcSettings['enable_modules'],
        array(
            'CDev' => array(
                // EN edition
                'Moneybookers',
                'XPaymentsConnector',
                'Quantum',
                'AuthorizeNet',
                'TwoCheckout',
                'USPS',
                'SalesTax',
                'Paypal'
            ),
            'XC' => array(
                // EN edition
                'EPDQ',
                'IdealPayments',
                'Stripe',
                'SagePay',
                'CanadaPost',
                'AuctionInc',
                'PitneyBowes'
            ),
            'Amazon' => array(
                'PayWithAmazon'
            )
        )
    );
} elseif (XLITE_EDITION_LNG === 'gb') {
    $lcSettings['yaml_files']['demo'][] = 'sql' . LC_DS . 'xlite_data_gb.yaml';

    $lcSettings['enable_modules'] = array_merge_recursive(
        $lcSettings['enable_modules'],
        array(
            'CDev' => array(
                // UK edition
                'Moneybookers',
                'XPaymentsConnector',
                'Quantum',
                'AuthorizeNet',
                'TwoCheckout',
                'SalesTax',
                'VAT'
            ),
            'XC' => array(
                // UK edition
                'EPDQ',
                'IdealPayments',
                'Stripe',
                'SagePay',
                'GbTranslation',
                'AuctionInc'
            ),
        )
    );
}

//if (defined('DRUPAL_CMS_INSTALL_MODE')) {
//    $lcSettings['enable_modules']['CDev'][] = 'DrupalConnector';
//}

if (defined('CMS_INSTALL_SETTINGS_CALLBACK') && function_exists(CMS_INSTALL_SETTINGS_CALLBACK)) {
    $lcSettings = call_user_func(CMS_INSTALL_SETTINGS_CALLBACK, $lcSettings);
}

