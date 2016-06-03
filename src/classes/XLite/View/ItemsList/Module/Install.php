<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Module;

/**
 * Addons search and installation widget
 */
class Install extends \XLite\View\ItemsList\Module\AModule
{
    /**
     * Sort option name definitions
     */
    const SORT_OPT_POPULAR          = 'm.downloads';
    const SORT_OPT_LANDING_POSITION = 'm.landingPosition';

    /**
     * Price filter options
     */
    const PRICE_OPT_ALL  = 'all';
    const PRICE_OPT_FREE = \XLite\Model\Repo\Module::PRICE_FREE;
    const PRICE_OPT_PAID = \XLite\Model\Repo\Module::PRICE_PAID;
    const TAG_OPT_ALL    = 'All';
    const VENDOR_OPT_ALL = 'All';

    /**
     * Widget param names
     */
    const PARAM_TAG    = 'tag';
    const PARAM_PRICE  = 'price';
    const PARAM_VENDOR = 'vendor';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'addons_list_marketplace';

        return $result;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list['js'][] = array(
            'file'      => 'js/ui.selectmenu.min.js',
            'no_minify' => true,
        );
        // popup button is using several specific popup JS
        $list['js'][] = 'js/core.popup.js';
        $list['js'][] = 'js/core.popup_button.js';

        $list['css'][] = 'css/ui.selectmenu.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules_manager/css/common.css';

        // TODO must be taken from LICENSE module widget
        $list[] = 'modules_manager/license/css/style.css';
        $list[] = 'modules_manager/installation_type/css/style.css';
        $list[] = 'modules_manager/warnings/css/style.css';

        // TODO must be taken from SwitchButton widget
        $list[] = \XLite\View\Button\SwitchButton::SWITCH_CSS_FILE;

        return $list;
    }

    /**
     * Register JS files. TODO REWORK with Popup button widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        // TODO must be taken from Button/InstallAddon widget
        $list[] = 'button/js/install_addon.js';
        $list[] = 'button/js/select_installation_type.js';
        // TODO must be taken from SwitchButton widget
        $list[] = \XLite\View\Button\SwitchButton::JS_SCRIPT;
        // TODO must be taken from LICENSE module widget
        $list[] = 'modules_manager/license/js/switch-button.js';
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/js/controller.js';

        $list[] = 'items_list/module/install/parts/columns/info/actions/js/install.js';

        if (!$this->isMarketplaceAccessible()) {
            $list[] = 'modules_manager/install/js/connection.js';
        }

        return $list;
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $request = \XLite\Core\Request::getInstance();

        if ($request->clearSearch) {
            $params[static::PARAM_SUBSTRING] = '';
        }

        if ($request->clearCnd) {
            $params[static::PARAM_TAG] = '';
            $params[static::PARAM_VENDOR] = '';
            $params[static::PARAM_PRICE] = '';
        }
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/'
            . ($this->isMarketplaceAccessible() ? 'items_list' : 'marketplace_not_accessible') . '.twig';
    }

    /**
     * Check if curl extension is loaded
     *
     * @return boolean
     */
    protected function isCurlAvailable()
    {
        return function_exists('curl_init');
    }

    /**
     * Check if OpenSSL extension is loaded
     *
     * @return boolean
     */
    protected function isOpenSSLAvailable()
    {
        return extension_loaded('openssl');
    }

    /**
     * Check if phar extension is loaded
     *
     * @return boolean
     */
    protected function isPHARAvailable()
    {
        return extension_loaded('phar');
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return $this->isLandingPage()
            ? '\XLite\View\Pager\Admin\Module\InstallLandingPage'
            : '\XLite\View\Pager\Admin\Module\Install';
    }

    protected function getMarketplaceNotAccessibleData()
    {
        $result = array();

        if (\XLite\Core\Request::getInstance()->landing) {
            $result['landing'] = 1;
        }

        return $result;
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.install';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'install';
    }

    /**
     * isHeaderVisible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\TypeString(
                'Substring', ''
            ),
            static::PARAM_TAG => new \XLite\Model\WidgetParam\TypeString(
                'Tag', ''
            ),
            static::PARAM_VENDOR => new \XLite\Model\WidgetParam\TypeString(
                'Vendor', ''
            ),
            static::PARAM_PRICE => new \XLite\Model\WidgetParam\TypeSet(
                'Price', static::PRICE_OPT_ALL, false, $this->getPriceOptions()
            ),
        );
    }

    /**
     * Define so called "request" parameters
     * We do not store the tag, substring parameters if clearing condition is provided
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $request = \XLite\Core\Request::getInstance();

        if (!$request->clearSearch) {
            $this->requestParams[] = static::PARAM_SUBSTRING;
        }

        if (!$request->clearCnd) {
            $this->requestParams[] = static::PARAM_TAG;
            $this->requestParams[] = static::PARAM_VENDOR;
            $this->requestParams[] = static::PARAM_PRICE;
        }
    }

    /**
     * Return list of sort options
     *
     * @return array
     */
    protected function getSortOptions()
    {
        return array_merge(
            array(
                static::SORT_OPT_POPULAR => static::t('module-sort-Most popular'),
                static::SORT_OPT_NEWEST  => static::t('module-sort-Newest'),
            ),
            parent::getSortOptions()
        );
    }

    /**
     * Return list of sort options for selector
     *
     * @return array
     */
    protected function getSortOptionsForSelector()
    {
        $result = array();
        $substring = $this->getSubstring();

        foreach ($this->getSortOptions() as $value => $name) {
            $url = $this->buildURL(
                'addons_list_marketplace',
                '',
                array(
                    \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                    static::PARAM_SORT_BY   => $value,
                    static::PARAM_SUBSTRING => $substring,
                )
            );
            $result[$url] = $name;
        }

        return $result;
    }

    /**
     * Get current sort option value for selector
     *
     * @return string
     */
    protected function getSortOptionsValueForSelector()
    {
        return $this->buildURL(
            'addons_list_marketplace',
            '',
            array(
                \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                static::PARAM_SORT_BY => $this->getSortBy(),
                static::PARAM_SUBSTRING => $this->getSubstring(),
            )
        );
    }

    // {{{ Price

    /**
     * Return list of price filter options
     *
     * @return array
     */
    protected function getPriceOptions()
    {
        return array(
            static::PRICE_OPT_ALL  => 'price-All',
            static::PRICE_OPT_PAID => 'price-Paid',
            static::PRICE_OPT_FREE => 'price-Free',
        );
    }

    /**
     * Return list of price filter options for selector
     *
     * @return array
     */
    protected function getPriceOptionsForSelector()
    {
        $result = array();
        foreach ($this->getPriceOptions() as $value => $name) {
            $actionUrl = $this->getActionURL(
                array(
                    static::PARAM_PRICE => $value,
                    \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                )
            );

            $result[$actionUrl] = static::t($name);
        }

        return $result;
    }

    /**
     * Get price filter option value for selector
     *
     * @return string
     */
    protected function getPriceOptionsValueForSelector()
    {
        return $this->getActionURL(
            array(
                static::PARAM_PRICE                                       => $this->getParam(self::PARAM_PRICE),
                \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
            )
        );
    }

    // }}}

    // {{{ Tags

    /**
     * Return list of tag options
     *
     * @return array
     */
    protected function getTagOptions()
    {
        return array_merge(
            array(static::TAG_OPT_ALL => ''),
            $this->getTags()
        );
    }

    /**
     * Return list of tag options for selector
     *
     * @return array
     */
    protected function getTagOptionsForSelector()
    {
        $result = array();

        foreach ($this->getTagOptions() as $name => $value) {
            $actionUrl = $this->getActionURL(
                array(
                    static::PARAM_TAG                                         => $value,
                    \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                    'clearSearch'                                             => 1,
                )
            );

            $result[$actionUrl] = $this->getTagName($name);
        }

        return $result;
    }

    /**
     * Get tag option value for selector
     *
     * @return string
     */
    protected function getTagOptionsValueForSelector()
    {
        return $this->getActionURL(
            array(
                static::PARAM_TAG                                         => $this->getTagValue(),
                \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                'clearSearch'                                             => 1,
            )
        );
    }

    /**
     * Defines the tag value according the request
     *
     * @return string
     */
    protected function getTagValue()
    {
        return \XLite\Core\Request::getInstance()->clearCnd ? '' : $this->getFilteredTagValue();
    }

    /**
     * Filter the tag from tags list
     *
     * @return string
     */
    protected function getFilteredTagValue()
    {
        return in_array($this->getParam(static::PARAM_TAG), $this->getTags()) ? $this->getParam(static::PARAM_TAG) : '';
    }

    // }}}

    // {{{ Vendors

    /**
     * Return list of tag options
     *
     * @return array
     */
    protected function getVendorOptions()
    {
        $vendors = $this->getVendors();
        $authorNames = array_keys($vendors);

        return array_merge(
            array(static::VENDOR_OPT_ALL => ''),
            array_combine($authorNames, $authorNames)
        );
    }

    /**
     * Return list of tag options for selector
     *
     * @return array
     */
    protected function getVendorOptionsForSelector()
    {
        $result = array();

        foreach ($this->getVendorOptions() as $name => $value) {
            $actionUrl = $this->getActionURL(
                array(
                    static::PARAM_VENDOR                                      => $value,
                    \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                    'clearSearch'                                             => 1,
                )
            );

            $result[$actionUrl] = $this->getTagName($name);
        }

        return $result;
    }

    /**
     * Get tag option value for selector
     *
     * @return string
     */
    protected function getVendorOptionsValueForSelector()
    {
        return $this->getActionURL(
            array(
                static::PARAM_VENDOR                                      => $this->getVendorValue(),
                \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
                'clearSearch'                                             => 1,
            )
        );
    }

    /**
     * Defines the tag value according the request
     *
     * @return string
     */
    protected function getVendorValue()
    {
        return \XLite\Core\Request::getInstance()->clearCnd ? '' : $this->getFilteredVendorValue();
    }

    /**
     * Filter the tag from tags list
     *
     * @return string
     */
    protected function getFilteredVendorValue()
    {
        $vendorDecoded = html_entity_decode(
            $this->getParam(static::PARAM_VENDOR)
        );

        return in_array($vendorDecoded, $this->getVendorOptions())
            ? $vendorDecoded
            : '';
    }

    // }}}

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        if ($this->isLandingPage()) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Module::P_FROM_MARKETPLACE} = true;
            $cnd->{\XLite\Model\Repo\Module::P_ISSYSTEM}         = false;
            $cnd->{\XLite\Model\Repo\Module::P_IS_LANDING}       = true;
            $cnd->{\XLite\Model\Repo\Module::P_ORDER_BY}         = array(static::SORT_OPT_LANDING_POSITION, static::SORT_ORDER_ASC);
        } else {
            $cnd = parent::getSearchCondition();
            $cnd->{\XLite\Model\Repo\Module::P_FROM_MARKETPLACE} = true;
            $cnd->{\XLite\Model\Repo\Module::P_ISSYSTEM}         = false;

            if (isset(\XLite\Core\Request::getInstance()->clearCnd)) {
                $cnd->{\XLite\Model\Repo\Module::P_ORDER_BY} = array(static::SORT_OPT_ALPHA, static::SORT_ORDER_ASC);
            } else {
                if ($this->getModuleId()) {
                    $cnd->{\XLite\Model\Repo\Module::P_MODULEIDS}    = array($this->getModuleId());
                } else {
                    $cnd->{\XLite\Model\Repo\Module::P_PRICE_FILTER} = $this->getParam(static::PARAM_PRICE);
                    $cnd->{\XLite\Model\Repo\Module::P_SUBSTRING}    = $this->getSubstring();

                    $tag = $this->getTagValue();
                    if ($tag) {
                        $cnd->{\XLite\Model\Repo\Module::P_TAG}      = $tag;
                    }

                    $vendor = $this->getVendorValue();
                    if ($vendor) {
                        $cnd->{\XLite\Model\Repo\Module::P_VENDOR}   = $vendor;
                    }
                }
            }
        }

        return $cnd;
    }

    /**
     * Flag if the addon filters box is visible
     *
     * @return boolean
     */
    protected function isVisibleAddonFilters()
    {
        return !$this->isLandingPage() && !$this->getModuleId();
    }

    /**
     * Return warning message. Description of Marketplace unavailability
     *
     * @return string
     */
    protected function getWarningMessage()
    {
        $message = 'No Phar extension for PHP error';
        $params = array();

        if ($this->isPHARAvailable()) {
            $message = 'No Curl extension for PHP error';
            if ($this->isCurlAvailable()) {
                $message = 'No OpenSSL extension for PHP error';
                if ($this->isOpenSSLAvailable()) {

                    // Some cURL constant are undefined in PHP
                    if (!defined('CURLE_SSL_ENGINE_INITFAILED')) {
                        define('CURLE_SSL_ENGINE_INITFAILED', 66);
                    }

                    if (!defined('CURLE_SSL_CACERT_BADFILE')) {
                        define('CURLE_SSL_CACERT_BADFILE', 77);
                    }

                    if (!defined('CURLE_SSL_SHUTDOWN_FAILED')) {
                        define('CURLE_SSL_SHUTDOWN_FAILED', 80);
                    }

                    if (!defined('CURLE_SSL_ISSUER_ERROR')) {
                        define('CURLE_SSL_ISSUER_ERROR', 83);
                    }

                    switch (\XLite\Core\Session::getInstance()->getCURLError()) {
                        case CURLE_SSL_CONNECT_ERROR:
                        case CURLE_SSL_ENGINE_NOTFOUND:
                        case CURLE_SSL_ENGINE_SETFAILED:
                        case CURLE_SSL_CERTPROBLEM:
                        case CURLE_SSL_CIPHER:
                        case CURLE_SSL_CACERT:
                        case CURLE_SSL_ENGINE_INITFAILED:
                        case CURLE_SSL_CACERT_BADFILE:
                        case CURLE_SSL_SHUTDOWN_FAILED:
                        case CURLE_SSL_ISSUER_ERROR:
                            $message = 'SSL Error';
                            break;

                        case '':
                            $message = 'Timeout error';
                            break;

                        default:
                            $message = 'cURL error';
                            $params['error code']    = \XLite\Core\Session::getInstance()->getCURLError();
                            $params['error message'] = \XLite\Core\Session::getInstance()->getCURLErrorMessage();
                    }
                }
            }
        }

        return static::t($message, $params);
    }

    // {{{ Helpers to use in templates

    /**
     * Check if the module is installed
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isInstalled(\XLite\Model\Module $module)
    {
        return $module->isInstalled();
    }

    /**
     * Substring search getter
     *
     * @return string
     */
    protected function getSubstring()
    {
        return \XLite\Core\Request::getInstance()->clearSearch ? '' : $this->getParam(static::PARAM_SUBSTRING);
    }

    /**
     * Check if the module is free
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isFree(\XLite\Model\Module $module)
    {
        return !$this->isInstalled($module) && $module->isFree();
    }

    /**
     * Check if the module is purchased
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isPurchased(\XLite\Model\Module $module)
    {
        return !$this->isInstalled($module) && !$this->isFree($module) && $module->isPurchased();
    }

    /**
     * Check if there are some errors for the current module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function hasErrors(\XLite\Model\Module $module)
    {
        return !$this->isInstalled($module) && parent::hasErrors($module);
    }

    /**
     * Check if the XC module notice must be displayed.
     * The notice is displayed when the module is a part of X-Cart 5 license
     * and current X-Cart 5 license type of core differs from X-Cart 5 license type of module.
     *
     * @param \XLite\Model\Module $module Module entity
     *
     * @return boolean
     */
    protected function showXCNModuleNotice(\XLite\Model\Module $module)
    {
        return $this->isXCN($module) && !$this->isInstalled($module) && 1 < $module->getEditionState();
    }

    /**
     * Check if the price should be visible for module.
     * No price for X-Cart 5 module or already installed from marketplace
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function showPrice(\XLite\Model\Module $module)
    {
        return !($this->isInstalled($module) || $this->isXCN($module));
    }

    /**
     * Check if notice 'Module is available for X-Cart hosted stores only' should be displayed
     *
     * @param \XLite\Model\Module $module Module entity
     *
     * @return boolean
     */
    protected function showNotAvailModuleNotice(\XLite\Model\Module $module)
    {
        return \XLite\Model\Module::NOT_AVAILABLE_MODULE == intval($module->getXcnPlan());
    }

    /**
     * Check if the module can be enabled
     *
     * @param \XLite\Model\Module $module    Module
     * @param boolean             $safeCheck True - check if dependent modules can be enabled, false - dependent modules must be active
     *
     * @return boolean
     */
    protected function canEnable(\XLite\Model\Module $module, $safeCheck = false)
    {
        return parent::canEnable($module) && !$this->isInstalled($module);
    }

    /**
     * Check if the module can be installed
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function canInstall(\XLite\Model\Module $module)
    {
        return $this->canEnable($module)
            && $this->canAccess($module)
            && $module->getFromMarketplace()
            && $this->isLicenseAllowed($module);
    }

    /**
     * Check if module license is available and allowed
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isLicenseAllowed(\XLite\Model\Module $module)
    {
        return \XLite\Model\Module::NOT_XCN_MODULE == $module->getXcnPlan()
            || (\XLite\Model\Module::NOT_XCN_MODULE < $module->getXcnPlan() && 1 == $module->getEditionState());
    }

    /**
     * Check if the module can be purchased.
     * X-Cart 5 modules could not be purchased. Just X-Cart 5 license.
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function canPurchase(\XLite\Model\Module $module)
    {
        return !$this->isInstalled($module)
            && !$this->canAccess($module)
            && !$this->isXCN($module)
            && !$this->isModuleUpgradeNeeded($module);
    }

    /**
     * Check if module is accessible for installation
     * It must be already purchased or be free
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function canAccess(\XLite\Model\Module $module)
    {
        return $this->isPurchased($module) || $this->isFree($module);
    }

    /**
     * Get CSS classes for module cell
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return string
     */
    protected function getModuleClassesCSS(\XLite\Model\Module $module)
    {
        $classes = sprintf('module-item module-%d module-%s', $module->getModuleId(), $module->getName());

        if ($this->showNotAvailModuleNotice($module)) {
            $classes .= ' not-available';
        }

        if ($this->isInstalled($module) && !$this->isEnabled($module)) {
            $classes .= ' disabled';
        }

        return $classes;
    }

    /**
     * Check if module is accessible for purchase and installation
     *
     * @return string
     */
    protected function getMoreInfoURL()
    {
        return 'https://my.x-cart.com';
    }

    /**
     * Check module license and return true if it's non-empty
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function hasNonEmptyLicense(\XLite\Model\Module $module)
    {
        return $module->getHasLicense();
    }

    // }}}

    // {{{ Methods to search modules of certain types

    /**
     * Check if core requires new (but the same as core major) version of module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleUpdateAvailable(\XLite\Model\Module $module)
    {
        $installed = $this->getModuleInstalled($module);

        return $installed
            && (
                version_compare($installed->getMajorVersion(), $module->getMajorVersion(), '=')
                && version_compare($installed->getFullMinorVersion(), $module->getFullMinorVersion(), '<')
                || version_compare($installed->getMajorVersion(), $module->getMajorVersion(), '<')
            );
    }

    // }}}

    /**
     * Defines the tag promo banner
     *
     * @return boolean|array
     */
    protected function getPromoBanner()
    {
        if (is_null($this->bannerInfo)) {
            $this->bannerInfo = false;
            $tag = $this->getTagValue();

            if ($tag && !$this->isLandingPage()) {
                $tags = \XLite\Core\Marketplace::getInstance()->getAllTagsInfo();
                if (isset($tags[$tag])) {
                    $tagInfo = $tags[$tag];

                    // We show promo banner if
                    // banner image is defined
                    // and
                    // module for banner is defined
                    // and
                    // banner is not expired (or expiration date is 0, nonexpiry-type banner)
                    if (
                        $tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_IMG]
                        && (
                            !empty($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_MODULE_BANNER])
                            || !empty($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_URL])
                        )
                        && $this->isPromoBannerActive($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_EXPIRATION_DATE])
                    ) {
                        $this->bannerInfo = array(
                            'banner_url'        => $tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_IMG],
                            'module_banner_url' =>
                                (!empty($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_URL])
                                    ? $tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_URL]
                                    : $this->getBannerURL($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_MODULE_BANNER])
                                ),
                            'is_external_link'  => !empty($tagInfo[\XLite\Core\Marketplace::FIELD_TAG_BANNER_URL]),
                        );
                    }
                }
            }
        }

        return $this->bannerInfo;
    }

    /**
     * Defines if the promo banner is active or expired
     * Banner is active if no expiration date is defined ('' value)
     * or the expiration date is greater than current time
     *
     * @param string $date Date
     *
     * @return boolean
     */
    protected function isPromoBannerActive($date)
    {
        $timestamp = strtotime($date);

        return '' === $date || \XLite\Core\Converter::time() < $timestamp;
    }

    /**
     * Retrive banner specific URL
     *
     * @param string $banner Banner
     *
     * @return string
     */
    protected function getBannerURL($banner)
    {
        list($author, $module) = explode('-', $banner);

        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $module);
    }
}
