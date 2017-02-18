<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin;

use \XLite\Core\Layout;

/**
 * Development module that simplifies the process of implementing design changes
 *
 */
abstract class Main extends \XLite\Module\AModuleSkin
{
    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'X-Cart team';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Crisp White skin';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '2';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '2';
    }

    /**
     * Get minor core version which is required for the module activation
     *
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '2';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Light and clean skin developed in minimalist style and combining flat and material design will make your store look clean and modern.';
    }

    /**
     * No settings for this module
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return false;
    }

    /**
     * Returns supported layout types
     *
     * @return array
     */
    public static function getLayoutTypes()
    {
        return [
            Layout::LAYOUT_GROUP_DEFAULT => Layout::getInstance()->getLayoutTypes(),
            Layout::LAYOUT_GROUP_HOME => Layout::getInstance()->getLayoutTypes()
        ];
    }

    /**
     * The following pathes are defined as substitutional skins:
     *
     * admin interface:     skins/custom_skin/admin/en/
     * customer interface:  skins/custom_skin/default/en/
     * mail interface:      skins/custom_skin/mail/en
     *
     * @return array
     */
    public static function getSkins()
    {
        return [
            \XLite::CUSTOMER_INTERFACE => ['crisp_white' . LC_DS . 'customer'],
        ];
    }

    protected static function moveTemplatesInLists()
    {
        $templates = [
            'authorization/parts/field.links.twig' => [
                static::TO_ADD => [
                    ['customer.signin.popup.fields', 500, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/content/main.location.twig' => [
                static::TO_DELETE => [
                    ['layout.main', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['center.top', 1000, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/content/product.location.twig' => [
                static::TO_ADD => [
                    ['product.details.page.info', 5, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/top_menu.twig' => [
                static::TO_DELETE => [
                    ['layout.main', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['layout.header', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/header.right.twig' => [
                static::TO_DELETE => [
                    ['layout.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['header.menu.after', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.search.twig' => [
                static::TO_DELETE => [
                    ['layout.header.bar', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['layout.header.bar', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/account_menu.twig' => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/language_menu.twig' => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/search_menu.twig' => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/slidebar_menu.twig' => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['layout.header.mobile.menu', 2000, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'mini_cart/horizontal/parts/mobile.icon.twig' => [
                static::TO_DELETE => [
                    ['minicart.horizontal.children', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.labels.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.small_thumbnails.customer.details', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.small_thumbnails.customer.info.photo', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.image', 17, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.remove.twig' => [
                static::TO_DELETE => [
                    ['cart.item', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['cart.item', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.info.weight.twig' => [
                static::TO_DELETE => [
                    ['cart.item.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['cart.item.info', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.drag-n-drop-handle.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.small_thumbnails.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/stock/label.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.info', 18, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', 32, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.sort-options.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.display-modes.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.product-name.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/center/list/parts/common.product-name.twig' => [
                static::TO_ADD => [
                    ['itemsList.product.list.customer.info', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-select-product.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.columns', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-product-qty.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.columns', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/table.captions.field-select-all.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.captions', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.added-mark.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.small_thumbnails.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.big_thumbnails.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.image-next.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.image.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.image-previous.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.image.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.loupe.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.image.photo', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.briefDescription.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.info', 18, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.stock.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-editable-attributes.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.info', 35, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.more-info-link.twig' => [
                static::TO_DELETE => [
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.quicklook.image', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/grid.button-add2cart-wrapper.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.tail', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.info.photo', 998, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-added.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.info.buttons-added', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info.buttons-added', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.image', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.image', 5, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.product-title.twig' => [
                static::TO_DELETE => [
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'checkout/steps/shipping/parts/address.billing.same.twig' => [
                static::TO_DELETE => [
                    ['checkout.payment.address.after', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['checkout.payment.address.before', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
        ];

        if (static::isModuleEnabled('CDev\GoSocial')) {
            $templates += [
                'modules/CDev/GoSocial/product/details/parts/common.share.twig' => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.details.page.image', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\FreeShipping')) {
            $templates += [
                'modules/XC/FreeShipping/free_ship.label.twig' => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.details.page.info', 17, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\ProductAdvisor')) {
            $templates += [
                'modules/CDev/ProductAdvisor/product/details/parts/common.coming_soon.twig' => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.details.page.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.details.quicklook.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\FastLaneCheckout')) {
            $templates += [
                'modules/XC/FastLaneCheckout/checkout_fastlane/header/back_button.twig' => [
                    static::TO_DELETE => [
                        ['checkout_fastlane.header.left', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/XC/FastLaneCheckout/checkout_fastlane/header/title.twig' => [
                    static::TO_DELETE => [
                        ['checkout_fastlane.header.left', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\Reviews')) {
            $templates += [
                'modules/XC/Reviews/product.items_list.rating.twig' => [
                    static::TO_DELETE => [
                        ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['itemsList.product.grid.customer.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 35, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/product_details.rating.twig' => [
                    static::TO_ADD => [
                        ['product.details.quicklook.image', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ]
            ];
        }

        if (static::isModuleEnabled('CDev\Sale')) {
            $templates += [
                'modules/CDev/Sale/label.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price.tail', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.plain_price', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/CDev/Sale/details/price.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/CDev/Sale/details/sale_price.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price.tail.sale_price.text', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.plain_price.sale_price.text', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/CDev/Sale/details/you_save.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price.tail.sale_price.text', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.plain_price.sale_price.text', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/CDev/Sale/details/label.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price.tail.sale_price.text', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.plain_price', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\VAT')) {
            $templates += [
                'modules/CDev/VAT/price.twig' => [
                    static::TO_DELETE => [
                        ['product.plain_price_only', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.plain_price', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD => [
                        ['product.plain_price_only', 13, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.plain_price', 13, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\SocialLogin')) {
            $templates['modules/CDev/SocialLogin/signin/signin.checkout.social.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin-anonymous-title', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\MultiVendor')) {
            $templates['modules/XC/MultiVendor/product/details/parts/vendor.twig'] = [
                static::TO_DELETE => [
                    ['product.details.page', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.info', 11, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info', 11, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('Amazon\PayWithAmazon')) {
            $templates['modules/Amazon/PayWithAmazon/login/signin/signin.checkout.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin-anonymous-title', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\Paypal')) {
            $templates['modules/CDev/Paypal/login/signin/signin.checkout.paypal.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin-anonymous-title', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        return $templates;
    }

    /**
     * @return array
     */
    protected static function moveClassesInLists()
    {
        $classes_list = [
            'XLite\View\MinicartAttributeValues' => [
                ['minicart.horizontal.item', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ['minicart.horizontal.item.name', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ],
            'XLite\View\Product\Details\Customer\PhotoBox' => [
                ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ['product.details.page.image', 5, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ],

            'XLite\View\LanguageSelector\Customer' => [
                static::TO_DELETE => [
                    ['layout.header.bar.links.newby', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['layout.header.bar.links.logged', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\TopContinueShopping' => [
                static::TO_DELETE => [
                    ['layout.main.breadcrumb', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\Product\Details\Customer\Gallery' => [
                static::TO_DELETE => [
                    ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\ShippingEstimator\ShippingEstimateBox' => [
                static::TO_DELETE => [
                    ['cart.panel.box', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\BannerRotation\BannerRotation' => [
                static::TO_DELETE => [
                    ['center', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['layout.main', 350, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
        ];

        if (static::isModuleEnabled('CDev\Coupons')) {
            $classes_list['XLite\Module\CDev\Coupons\View\CartCoupons'] = [
                static::TO_DELETE => [
                    ['checkout.review.selected', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['checkout.review.selected', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\Geolocation')) {
            $classes_list['XLite\Module\XC\Geolocation\View\Button\LocationSelectPopup'] = [
                ['layout.header.bar', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ];
        }

        if (static::isModuleEnabled('XC\MultiCurrency')) {
            $classes_list['XLite\Module\XC\MultiCurrency\View\LanguageSelector\CustomerMobile'] = [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['slidebar.settings', 0, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\NextPreviousProduct')) {
            $classes_list['XLite\Module\XC\NextPreviousProduct\View\Product\Details\Customer\NextPreviousProduct'] = [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['product.details.page.image', 0, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\NewsletterSubscriptions')) {
            $classes_list['XLite\Module\XC\NewsletterSubscriptions\View\SubscribeBlock'] = [
                static::TO_DELETE => [
                    ['layout.main.footer', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['layout.main.footer.before', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\ProductComparison')) {
            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\Product'] = [
                static::TO_ADD => [
                    ['product.details.quicklook.info.buttons.cart-buttons', 120, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info.buttons-added.cart-buttons', 129, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareIndicator'] = [
                static::TO_ADD => [
                    ['layout.header.right', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['layout.header.right.mobile', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareLink'] = [
                static::TO_ADD => [
                    ['slidebar.additional-menu.links', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\Products'] = [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.marks', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.marks', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.marks', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\ProductComparison'] = [
                static::TO_DELETE => [
                    ['sidebar.single', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['sidebar.second', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        return $classes_list;
    }

    /**
     * Returns image sizes
     *
     * @return array
     */
    public static function getImageSizes()
    {
        return [
            \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT => [
                'SBSmallThumbnail' => [100, 120],
                'XSThumbnail' => [58, 70],
                'MSThumbnail' => [75, 90],
            ],
        ];
    }

    /**
     * Determines if some module is enabled
     *
     * @return boolean
     */
    public static function isModuleEnabled($name)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled($name);
    }

    /**
     * @inheritDoc
     */
    public static function callInstallEvent()
    {
        parent::callInstallEvent();

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Layout',
            'name' => 'layout_type_' . \XLite\Core\Layout::LAYOUT_GROUP_HOME,
            'value' => \XLite\Core\Layout::LAYOUT_ONE_COLUMN,
        ]);
    }


}
