<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

/**
 * Left side menu widget
 *
 * @ListChild (list="admin.main.page.content.left", weight="100", zone="admin")
 */
class LeftMenu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Quick links (cache)
     *
     * @var array
     */
    protected $quickLinks;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/menu.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Get quick links
     *
     * @return array
     */
    public function getQuickLinks()
    {
        if (!isset($this->quickLinks)) {
            $this->quickLinks = $this->defineQuickLinks();
            $this->quickLinks = $this->prepareItems($this->quickLinks);
        }

        return $this->quickLinks;
    }

    /**
     * Initialize handler
     *
     * @return void
     */
    public function init()
    {
        \XLite\Core\Marketplace::getInstance()->getXC5Notifications();
    }

    /**
     * Define quick links
     *
     * @return array
     */
    protected function defineQuickLinks()
    {
        $result = [
            'info' => [
                static::ITEM_ICON_SVG => 'images/bell.svg',
                static::ITEM_WIDGET   => 'XLite\View\Menu\Admin\LeftMenu\Info',
                static::ITEM_WEIGHT   => 100,
            ],
            'marketplace' => [
                static::ITEM_ICON_SVG => 'images/marketplace.svg',
                static::ITEM_WIDGET   => 'XLite\View\Menu\Admin\LeftMenu\Marketplace',
                static::ITEM_WEIGHT   => 200,
                static::ITEM_LABEL_LINK  => $this->buildURL('addons_list_marketplace', null, ['landing' => 1]),
            ],
            'add_new' => [
                static::ITEM_TOOLTIP    => static::t('Add new'),
                static::ITEM_WEIGHT     => 300,
                static::ITEM_ICON_SVG   => 'images/add.svg',
                static::ITEM_CHILDREN   => [
                    'add_product' => [
                        static::ITEM_TITLE      => static::t('Product'),
                        static::ITEM_ICON_SVG   => 'images/add_product.svg',
                        static::ITEM_TARGET     => 'product',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'add_category' => [
                        static::ITEM_TITLE      => static::t('Category'),
                        static::ITEM_ICON_SVG   => 'images/add_category.svg',
                        static::ITEM_TARGET     => 'categories',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_EXTRA      => ['add_new' => 1],
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'add_user' => [
                        static::ITEM_TITLE      => static::t('User'),
                        static::ITEM_ICON_SVG   => 'images/add_user.svg',
                        static::ITEM_TARGET     => 'profile',
                        static::ITEM_EXTRA      => ['mode' => 'register'],
                        static::ITEM_PERMISSION => 'manage users',
                        static::ITEM_WEIGHT     => 300,
                    ],
                ],
            ],
        ];

        if (\XLite::isFreeLicense()) {
            $result['add_new'][static::ITEM_CHILDREN]['add_coupon'] = [
                static::ITEM_TITLE      => static::t('Coupon'),
                static::ITEM_ICON_SVG   => 'images/add_product.svg',
                static::ITEM_TARGET     => 'main',
                static::ITEM_EXTRA      => ['page' => 'license_restriction'],
                static::ITEM_WEIGHT     => 400,
            ];
        }

        return $result;
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = [
            'sales' => [
                static::ITEM_TITLE       => static::t('Orders'),
                static::ITEM_ICON_SVG    => 'images/orders.svg',
                static::ITEM_WEIGHT      => 100,
                static::ITEM_TARGET      => 'order_list',
                static::ITEM_LABEL_LINK  => $this->buildURL('order_list', 'search', ['filter_id' => 'recent']),
                static::ITEM_LABEL_TITLE => static::t('Orders awaiting processing'),
                static::ITEM_CHILDREN    => [
                    'order_list' => [
                        static::ITEM_TITLE      => static::t('Orders list'),
                        static::ITEM_TARGET     => 'order_list',
                        static::ITEM_PERMISSION => 'manage orders',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'orders_stats' => [
                        static::ITEM_TITLE      => static::t('Statistics'),
                        static::ITEM_TARGET     => 'orders_stats',
                        static::ITEM_PERMISSION => 'manage orders',
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'payment_transactions' => [
                        static::ITEM_TITLE      => static::t('Payment transactions'),
                        static::ITEM_TARGET     => 'payment_transactions',
                        static::ITEM_PERMISSION => 'manage orders',
                        static::ITEM_WEIGHT     => 300,
                    ],
                ],
            ],
            'catalog' => [
                static::ITEM_TITLE      => static::t('Catalog'),
                static::ITEM_ICON_SVG   => 'images/fa-tags.svg',
                static::ITEM_TARGET     => 'product_list',
                static::ITEM_WEIGHT     => 200,
                static::ITEM_CHILDREN   => [
                    'product_list' => [
                        static::ITEM_TITLE      => static::t('Products'),
                        static::ITEM_TARGET     => 'product_list',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'categories' => [
                        static::ITEM_TITLE      => static::t('Categories'),
                        static::ITEM_TARGET     => 'categories',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 300,
                    ],
                    'front_page' => [
                        static::ITEM_TITLE      => static::t('Front page'),
                        static::ITEM_TARGET     => 'front_page',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 350,
                    ],
                    'product_classes' => [
                        static::ITEM_TITLE      => static::t('Classes & attributes'),
                        static::ITEM_TARGET     => 'product_classes',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 400,
                    ],
                    'import' => [
                        static::ITEM_TITLE      => static::t('Import'),
                        static::ITEM_TARGET     => 'import',
                        static::ITEM_PERMISSION => 'manage import',
                        static::ITEM_ICON_SVG   => 'images/import.svg',
                        static::ITEM_WEIGHT     => 500,
                    ],
                    'export' => [
                        static::ITEM_TITLE      => static::t('Export'),
                        static::ITEM_TARGET     => 'export',
                        static::ITEM_PERMISSION => 'manage export',
                        static::ITEM_ICON_SVG   => 'images/export.svg',
                        static::ITEM_WEIGHT     => 600,
                    ],
                ],
            ],
            'promotions' => [
                static::ITEM_TITLE      => static::t('Promotions'),
                static::ITEM_ICON_SVG   => 'images/fa-gift.svg',
                static::ITEM_WEIGHT     => 300,
                static::ITEM_TARGET     => 'promotions',
                static::ITEM_CHILDREN   => [],
            ],
            'users' => [
                static::ITEM_TITLE      => static::t('Users'),
                static::ITEM_ICON_SVG   => 'images/fa-users.svg',
                static::ITEM_WEIGHT     => 400,
                static::ITEM_TARGET     => 'profile_list',
                static::ITEM_CHILDREN   => [
                    'profile_list' => [
                        static::ITEM_TITLE      => static::t('Users'),
                        static::ITEM_TARGET     => 'profile_list',
                        static::ITEM_PERMISSION => 'manage users',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'memberships' => [
                        static::ITEM_TITLE      => static::t('Membership levels'),
                        static::ITEM_TARGET     => 'memberships',
                        static::ITEM_PERMISSION => 'manage users',
                        static::ITEM_WEIGHT     => 200,
                    ],
                ],
            ],
            'extensions' => [
                static::ITEM_TITLE      => static::t('Modules'),
                static::ITEM_ICON_SVG   => 'images/fa-puzzle-piece.svg',
                static::ITEM_WEIGHT     => 600,
                static::ITEM_TARGET     => 'addons_list_installed',
            ],
            'store_setup' => [
                static::ITEM_TITLE      => static::t('Store setup'),
                static::ITEM_ICON_SVG   => 'images/fa-info-circle.svg',
                static::ITEM_WEIGHT     => 700,
                static::ITEM_TARGET     => 'settings',
                static::ITEM_EXTRA      => ['page' => 'Company'],
                static::ITEM_CHILDREN   => [
                    'store_info' => [
                        static::ITEM_TITLE      => static::t('Store info'),
                        static::ITEM_TARGET     => 'settings',
                        static::ITEM_EXTRA      => ['page' => 'Company'],
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'general' => [
                        static::ITEM_TITLE      => static::t('Cart & checkout'),
                        static::ITEM_TARGET     => 'general_settings',
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'payment_settings' => [
                        static::ITEM_TITLE      => static::t('Payments'),
                        static::ITEM_TARGET     => 'payment_settings',
                        static::ITEM_WEIGHT     => 300,
                    ],
                    'countries' => [
                        static::ITEM_TITLE      => static::t('Countries, states and zones'),
                        static::ITEM_TARGET     => 'countries',
                        static::ITEM_WEIGHT     => 400,
                    ],
                    'shipping_methods' => [
                        static::ITEM_TITLE      => static::t('Shipping'),
                        static::ITEM_TARGET     => 'shipping_methods',
                        static::ITEM_WEIGHT     => 500,
                    ],
                    'tax_classes' => [
                        static::ITEM_TITLE      => static::t('Taxes'),
                        static::ITEM_TARGET     => 'tax_classes',
                        static::ITEM_WEIGHT     => 600,
                    ],
                    'localization' => [
                        static::ITEM_TITLE      => static::t('Localization'),
                        static::ITEM_TARGET     => 'units_formats',
                        static::ITEM_WEIGHT     => 700,
                    ],
                    'translations' => [
                        static::ITEM_TITLE      => static::t('Translations'),
                        static::ITEM_TARGET     => 'languages',
                        static::ITEM_WEIGHT     => 800,
                    ],
                    'notifications' => [
                        static::ITEM_TITLE      => static::t('Email notifications'),
                        static::ITEM_TARGET     => 'notifications',
                        static::ITEM_WEIGHT     => 900,
                    ],
                    'accounting' => [
                        static::ITEM_TITLE      => static::t('Accounting'),
                        static::ITEM_TARGET     => 'accounting',
                        static::ITEM_WEIGHT     => 1100,
                    ],
                ],
            ],
            'css_js' => [
                static::ITEM_TITLE      => static::t('Look & Feel'),
                static::ITEM_ICON_SVG   => 'images/fa-picture-o.svg',
                static::ITEM_TARGET     => 'layout',
                static::ITEM_WEIGHT     => 800,
                static::ITEM_CHILDREN   => [
                    'layout' => [
                        static::ITEM_TITLE      => static::t('Layout'),
                        static::ITEM_TARGET     => 'layout',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'images' => [
                        static::ITEM_TITLE      => static::t('Images'),
                        static::ITEM_TARGET     => 'images',
                        static::ITEM_WEIGHT     => 600,
                    ],
                    'css_js_performance' => [
                        static::ITEM_TITLE      => static::t('Performance'),
                        static::ITEM_TARGET     => 'css_js_performance',
                        static::ITEM_WEIGHT     => 700,
                    ],
                ],
            ],
            'system_settings' => [
                static::ITEM_TITLE      => static::t('System settings'),
                static::ITEM_ICON_SVG   => 'images/fa-cog.svg',
                static::ITEM_WEIGHT     => 900,
                static::ITEM_TARGET     => 'db_backup',
                static::ITEM_CHILDREN   => [
                    'db_backup' => [
                        static::ITEM_TITLE      => static::t('Tools'),
                        static::ITEM_TARGET     => 'db_backup',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'rebuild_cache' => [
                        static::ITEM_TITLE      => static::t('Cache management'),
                        static::ITEM_TARGET     => 'cache_management',
                        static::ITEM_CLASS      => 'rebuild-cache',
                        static::ITEM_WEIGHT     => 300,
                    ],
                    'seo' => [
                        static::ITEM_TITLE      => static::t('Clean URLs'),
                        static::ITEM_TARGET     => 'settings',
                        static::ITEM_EXTRA      => ['page' => 'CleanURL'],
                        static::ITEM_WEIGHT     => 450,
                    ],
                    'environment' => [
                        static::ITEM_TITLE      => static::t('Environment'),
                        static::ITEM_TARGET     => 'settings',
                        static::ITEM_EXTRA      => ['page' => 'Environment'],
                        static::ITEM_WEIGHT     => 400,
                    ],
                    'view_log_file' => [
                        static::ITEM_TITLE      => static::t('View system logs'),
                        static::ITEM_TARGET     => 'upgrade',
                        static::ITEM_EXTRA      => ['action' => 'view_log_file'],
                        static::ITEM_WEIGHT     => 500,
                        static::ITEM_BLANK_PAGE => true,
                    ],
                    'email_settings' => [
                        static::ITEM_TITLE      => static::t('Email settings'),
                        static::ITEM_TARGET     => 'settings',
                        static::ITEM_EXTRA      => ['page' => 'Email'],
                        static::ITEM_WEIGHT     => 600,
                    ],
                    'safe_mode' => [
                        static::ITEM_TITLE      => static::t('Safe mode'),
                        static::ITEM_TARGET     => 'safe_mode',
                        static::ITEM_WEIGHT     => 700,
                    ],
                    'remove_daa' => [
                        static::ITEM_TITLE      => static::t('Remove data'),
                        static::ITEM_TARGET     => 'remove_data',
                        static::ITEM_WEIGHT     => 750,
                    ],
                    'security_settings' => [
                        static::ITEM_TITLE      => static::t('HTTPS settings'),
                        static::ITEM_TARGET     => 'https_settings',
                        static::ITEM_WEIGHT     => 800,
                    ],
                ],
            ],
        ];

        // Check if cloned products exists and add menu item
        // TODO: need to be reviewed - search should not be used on each load of admin interface pages
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_SUBSTRING} = '[ clone ]';
        $cnd->{\XLite\Model\Repo\Product::P_BY_TITLE} = 'Y';

        if (0 < \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($cnd, true)) {
            $items['catalog'][static::ITEM_CHILDREN]['clone_products'] = [
                static::ITEM_TITLE      => static::t('Cloned products'),
                static::ITEM_TARGET     => 'cloned_products',
                static::ITEM_PERMISSION => 'manage catalog',
                static::ITEM_WEIGHT     => 220,
            ];
        }

        $pagesStatic = \XLite\Controller\Admin\Promotions::getPagesStatic();
        if ($pagesStatic) {
            foreach ($pagesStatic as $k => $v) {
                $items['promotions'][static::ITEM_CHILDREN][$k] = [
                    static::ITEM_TITLE  => $v['name'],
                    static::ITEM_TARGET => 'promotions',
                    static::ITEM_EXTRA  => ['page' => $k],
                    static::ITEM_PERMISSION => !empty($v['permission']) ? $v['permission'] : null
                ];

                $items['promotions'][static::ITEM_EXTRA] = ['page' => $k];
            }
        }

        // Orders label
        $count = \XLite\Core\Database::getRepo('XLite\Model\Order')->searchRecentOrders(null, true);

        if ($count) {
            $items['sales'][static::ITEM_LABEL] = $count;
        }

        return $items;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'left_menu';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\LeftMenu\Node';
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $attributes = [
            'id' => 'leftMenu',
        ];

        if (!empty($_COOKIE['XCAdminLeftMenuCompressed'])) {
            $attributes['class'] = ['compressed'];
        }

        return $attributes;
    }
}
