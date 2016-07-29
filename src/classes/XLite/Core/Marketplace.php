<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Marketplace
 */
class Marketplace extends \XLite\Base\Singleton
{
    /**
     * Marketplace request types
     */
    const ACTION_CHECK_FOR_UPDATES = 'check_for_updates';
    const ACTION_GET_CORES         = 'get_cores';
    const ACTION_GET_CORE_PACK     = 'get_core_pack';
    const ACTION_GET_CORE_HASH     = 'get_core_hash';
    const ACTION_GET_ADDONS_LIST   = 'get_addons';
    const ACTION_GET_ADDON_PACK    = 'get_addon_pack';
    const ACTION_GET_ADDON_INFO    = 'get_addon_info';
    const ACTION_GET_ADDON_HASH    = 'get_addon_hash';
    const ACTION_CHECK_ADDON_KEY   = 'check_addon_key';
    const ACTION_GET_HOSTING_SCORE = 'get_hosting_score';
    const ACTION_GET_ALL_TAGS      = 'get_all_tags';
    const ACTION_GET_ALL_BANNERS   = 'get_all_banners';
    const ACTION_GET_LANDING_AVAILABLE = 'is_landing_available';
    const ACTION_TEST_MARKETPLACE  = 'test_marketplace';
    const ACTION_RESEND_KEY        = 'resend_key';
    const ACTION_REQUEST_FOR_UPGRADE = 'request_for_upgrade';
    const ACTION_GET_XC5_NOTIFICATIONS = 'get_XC5_notifications';
    const ACTION_GET_WAVES         = 'get_waves';
    const ACTION_CHANGE_KEY_WAVE   = 'change_key_wave';
    const ACTION_UPDATE_PM         = 'update_pm';
    const ACTION_UPDATE_SHM        = 'update_shm';
    const ACTION_GET_DATASET       = 'get_dataset';

    /**
     * Request/response fields
     */
    const FIELD_VERSION_CORE_CURRENT  = 'currentCoreVersion';
    const FIELD_MODULE_VERSION        = 'currentModuleVersion';
    const FIELD_VERSION               = 'version';
    const FIELD_VERSION_MAJOR         = 'major';
    const FIELD_VERSION_MINOR         = 'minor';
    const FIELD_VERSION_BUILD         = 'build';
    const FIELD_MIN_CORE_VERSION      = 'minorRequiredCoreVersion';
    const FIELD_REVISION              = 'revision';
    const FIELD_REVISION_DATE         = 'revisionDate';
    const FIELD_LANDING_POSITION      = 'landingPosition';
    const FIELD_LENGTH                = 'length';
    const FIELD_GZIPPED               = 'gzipped';
    const FIELD_NAME                  = 'name';
    const FIELD_KEY_TYPE              = 'keyType';
    const FIELD_MODULE                = 'module';
    const FIELD_MODULES               = 'modules';
    const FIELD_AUTHOR                = 'author';
    const FIELD_KEY                   = 'key';
    const FIELD_KEYS                  = 'keys';
    const FIELD_WAVE                  = 'wave';
    const FIELD_EMAIL                 = 'email';
    const FIELD_INSTALLATION_LNG      = 'installation_lng';
    const FIELD_DO_REGISTER           = 'doRegister';
    const FIELD_IS_UPGRADE_AVAILABLE  = 'isUpgradeAvailable';
    const FIELD_ARE_UPDATES_AVAILABLE = 'areUpdatesAvailable';
    const FIELD_IS_CONFIRMED          = 'isConfirmed';
    const FIELD_READABLE_NAME         = 'readableName';
    const FIELD_READABLE_AUTHOR       = 'readableAuthor';
    const FIELD_MODULE_ID             = 'moduleId';
    const FIELD_DESCRIPTION           = 'description';
    const FIELD_PRICE                 = 'price';
    const FIELD_CURRENCY              = 'currency';
    const FIELD_ICON_URL              = 'iconURL';
    const FIELD_PAGE_URL              = 'pageURL';
    const FIELD_AUTHOR_PAGE_URL       = 'authorPageURL';
    const FIELD_DEPENDENCIES          = 'dependencies';
    const FIELD_RATING                = 'rating';
    const FIELD_RATING_RATE           = 'rate';
    const FIELD_RATING_VOTES_COUNT    = 'votesCount';
    const FIELD_DOWNLOADS_COUNT       = 'downloadCount';
    const FIELD_HAS_LICENSE           = 'has_license';
    const FIELD_LICENSE               = 'license';
    const FIELD_SHOP_ID               = 'shopID';
    const FIELD_SHOP_DOMAIN           = 'shopDomain';
    const FIELD_SHOP_URL              = 'shopURL';
    const FIELD_ERROR_CODE            = 'error';
    const FIELD_ERROR_MESSAGE         = 'message';
    const FIELD_IS_SYSTEM             = 'isSystem';
    const FIELD_XCN_PLAN              = 'xcn_plan';
    const FIELD_XCN_LICENSE_KEY       = 'xcn_license_key';
    const FIELD_TAGS                  = 'tags';
    const FIELD_AUTHOR_EMAIL          = 'authorEmail';
    const FIELD_IS_LANDING            = 'isLanding';
    const FIELD_BANNER_MODULE         = 'banner_module';
    const FIELD_BANNER_IMG            = 'banner_img';
    const FIELD_BANNER_URL            = 'banner_url';
    const FIELD_EDITION_STATE         = 'edition_state';
    const FIELD_EDITIONS              = 'editions';
    const FIELD_KEY_DATA              = 'keyData';
    const FIELD_VERSION_API           = 'versionAPI';
    const FIELD_LANDING               = 'landing';
    const FIELD_XB_PRODUCT_ID         = 'xbProductId';
    const FIELD_IS_REQUEST_FOR_UPGRADE_SENT = 'isRequestForUpgradeSent';
    const FIELD_AFFILIATE_ID          = 'affiliateId';
    const FIELD_TRIAL                 = 'trial';
    const FIELD_MODULE_ENABLED        = 'enabled';
    const FIELD_QUERIES               = 'querySets';

    const FIELD_TAG_NAME                    = 'tag_name';
    const FIELD_TAG_BANNER_EXPIRATION_DATE  = 'tag_banner_expiration_date';
    const FIELD_TAG_BANNER_IMG              = 'tag_banner_img';
    const FIELD_TAG_MODULE_BANNER           = 'tag_module_banner';
    const FIELD_TAG_BANNER_URL              = 'tag_banner_url';

    const FIELD_NOTIFICATION_TYPE        = 'type';
    const FIELD_NOTIFICATION_MODULE      = 'module';
    const FIELD_NOTIFICATION_IMAGE       = 'image';
    const FIELD_NOTIFICATION_TITLE       = 'title';
    const FIELD_NOTIFICATION_DESCRIPTION = 'description';
    const FIELD_NOTIFICATION_LINK        = 'link';
    const FIELD_NOTIFICATION_DATE        = 'date';

    const INACTIVE_KEYS = 'inactiveMPKeys';

    /**
     * Marketplace API version
     */
    const MP_API_VERSION      = '2.2';
    const XC_FREE_LICENSE_KEY = 'XC5-FREE-LICENSE';

    /**
     * Some predefined TTLs
     */
    const TTL_LONG  = 86400;
    const TTL_SHORT = 3600;

    /**
     * Some regexps
     */
    const REGEXP_VERSION  = '/\d+\.?[\w-\.]*/';
    const REGEXP_WORD     = '/[\w\"\']+/';
    const REGEXP_NUMBER   = '/\d+/';
    const REGEXP_HASH     = '/\w{32}/';
    const REGEXP_CURRENCY = '/[A-Z]{1,3}/';
    const REGEXP_CLASS    = '/[\w\\\\]+/';

    /**
     * Error codes
     */
    const ERROR_CODE_REFUND = 1030;
    const ERROR_CODE_FREE_LICENSE_REGISTERED = 3090;

    /**
     * Dedicated return code for the "performActionWithTTL" method
     */
    const TTL_NOT_EXPIRED = '____TTL_NOT_EXPIRED____';

    /**
     * HTTP request TTL
     */
    const REQUEST_TTL = 30;

    /**
     * HTTP request TTL for long actions
     */
    const REQUEST_LONG_TTL = 60;

    /**
     * Interval between attempts to access marketplace after error of connection
     */
    const ERROR_REQUEST_TTL = 3600;

    /**
     * HTTP request TTL for 'test_marketplace' action
     */
    const TTL_TEST_MP = 300; // 5 minutes

    /**
     * Last error code
     *
     * @var string
     */
    protected static $lastErrorCode = null;

    /**
     * Error message
     *
     * @var mixed
     */
    protected $error = null;


    /**
     * URL of the page where license can be purchased
     *
     * @param integer $id     Product ID                OPTIONAL
     * @param array   $params Additional URL parameters OPTIONAL
     *
     * @return string
     */
    public static function getPurchaseURL($id = 0, $params = array(), $ignoreId = false)
    {
        if (!$ignoreId && 0 == intval($id)) {
            $id = 391;
        }

        $commonParams = array(
            'area'      => 'purchase_services',
            'target'    => 'generate_invoice',
            'action'    => 'buy',
            'store_url' => \XLite\Core\URLManager::getShopURL(\XLite\Core\Converter::buildURL()),
            'email'     => \XLite\Core\Auth::getInstance()->getProfile()->getLogin(),
        );

        if (!$ignoreId) {
            $commonParams['add_' . $id] = $id;
        }

        $params = array_merge($commonParams, $params);

        $urlParams = array();
        foreach ($params as $k => $v) {
            $urlParams[] = $k . '=' . urlencode($v);
        }

        return \XLite::getXCartURL('https://www.x-cart.com/order?' . implode('&', $urlParams));
    }

    /**
     * This function defines original link to X-Cart.com site's Contact Us page
     *
     * @return string
     */
    public static function getContactUsURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/contact-us.html');
    }

    /**
     * This function defines original link to X-Cart.com site's License Agreement page
     *
     * @return string
     */
    public static function getLicenseAgreementURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/license-agreement.html');
    }

    /**
     * Get long actions
     *
     * @return array
     */
    public static function getLongActions()
    {
        return array(
            static::ACTION_GET_CORE_PACK,
            static::ACTION_GET_CORE_HASH,
            static::ACTION_GET_ADDON_PACK,
            static::ACTION_GET_ADDON_HASH,
            static::ACTION_GET_ADDON_INFO,
            static::ACTION_GET_ADDONS_LIST,
        );
    }

    /**
     * Get last error code
     *
     * @return mixed
     */
    public function getLastErrorCode()
    {
        return static::$lastErrorCode;
    }

    /**
     * Get last error message from bouncer
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the error message
     *
     * @param mixed $error
     *
     * @return void
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    // {{{ Update payment methods list from marketplace

    /**
     * Update payment methods
     *
     * @param integer|null $ttl TTL
     */
    public function updatePaymentMethods($ttl = null)
    {
        list($cellTTL, $cellData) = $this->getActionCacheVars(static::ACTION_UPDATE_PM);

        $ttl = !is_null($ttl) ? $ttl : static::TTL_LONG;

        // Check if expired
        if (!$this->checkTTL($cellTTL, $ttl)) {

            $url = $this->getMarketplaceURL();

            if ($url) {
                $suffix = sprintf('/sites/default/files/pm-%s.json', \XLite::getInstance()->getMajorVersion());
                $url = preg_replace('/\/[^\/]+$/USs', $suffix, str_replace('https://', 'http://', $url));
                $request = new \XLite\Core\HTTP\Request($url);
                $response = $request->sendRequest();

                if (!empty($response->body)) {
                    $data = json_decode($response->body, true);

                    if (!empty($data) && is_array($data)) {
                        \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->updatePaymentMethods($data);
                        $this->setTTLStart($cellTTL);

                    } else {
                        $this->logError(static::ACTION_UPDATE_PM, 'Empty response from marketplace');
                    }

                } else {
                    $this->logError(static::ACTION_UPDATE_PM, 'Wrong response from marketplace: ' . ($request->getErrorMessage() ?: 'empty'));
                }
            }
        }
    }

    // }}}

    // {{{ Update shipping methods list from marketplace

    /**
     * Update shipping methods
     *
     * @param integer|null $ttl TTL
     */
    public function updateShippingMethods($ttl = null)
    {
        list($cellTTL,) = $this->getActionCacheVars(static::ACTION_UPDATE_SHM);

        $ttl = !is_null($ttl) ? $ttl : static::TTL_LONG;

        // Check if expired
        if (!$this->checkTTL($cellTTL, $ttl)) {
            $url = $this->getMarketplaceURL();

            if ($url) {
                $suffix = sprintf('/sites/default/files/shm-%s.json', \XLite::getInstance()->getMajorVersion());
                $url = preg_replace('/\/[^\/]+$/USs', $suffix, str_replace('https://', 'http://', $url));
                $request = new \XLite\Core\HTTP\Request($url);
                $response = $request->sendRequest();

                if (!empty($response->body)) {
                    $data = json_decode($response->body, true);

                    if (!empty($data) && is_array($data)) {
                        \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->updateShippingMethods($data);
                        $this->setTTLStart($cellTTL);

                    } else {
                        $this->logError(static::ACTION_UPDATE_SHM, 'Empty response from marketplace');
                    }

                } else {
                    $this->logError(static::ACTION_UPDATE_SHM, 'Wrong response from marketplace: ' . ($request->getErrorMessage() ?: 'empty'));
                }
            }
        }
    }

    // }}}

    // {{{ "Get dataset" request

    /**
     * Request 'get_dataset'
     *
     * @param array $actions List of actions
     *
     * @return array
     */
    public function getDataset($actions = array())
    {
        $result = null;

        $actions = $this->getActionsForGetDataset();

        if (!empty($actions)) {

            $data = $this->getDatasetData($actions);

            if (!empty($data)) {
                $response = $this->sendRequestToMarketplace(static::ACTION_GET_DATASET, $data);

                if (isset($response)) {
                    $result = $this->processGetDatasetResult($response);
                }
            }
        }

        return $result;
    }

    /**
     * Get actions list for 'get_dataset' request
     *
     * @return array
     */
    public function getActionsForGetDataset()
    {
        if (empty($actions)) {
            $actions = $this->getExpiredActions();
        }

        $actions = array_fill_keys($actions, array());

        $scheduled = $this->getScheduledActions();

        if ($scheduled) {
            $actions = array_merge($actions, $scheduled);
        }

        return $actions;
    }

    /**
     * Get list of expired actions
     *
     * @return array
     */
    protected function getExpiredActions()
    {
        return array_filter(array_keys($this->getMarketplaceActions()), array($this, 'isActionActive'));
    }

    /**
     * Return true if action is active (non-empty and not expired)
     *
     * @param string $action Action type
     *
     * @return boolean
     */
    public function isActionActive($action)
    {
        list($cellTTL, $cellData) = $this->getActionCacheVars($action);

        return !$this->checkTTL($cellTTL, $this->getActionTTL($action));
    }

    /**
     * Get data for 'get_dataset' request
     *
     * @param array $data Input data
     *
     * @return array
     */
    protected function getDatasetData($actions)
    {
        $result = array();

        $excludedActions = $this->getDatasetExcludedActions();

        foreach ($actions as $action => $data) {

            if (in_array($action, $excludedActions)) {
                // Exclude action
                unset($actions[$action]);
                continue;

            } elseif (empty($data)) {

                $method = 'getRequestDataFor' . \Includes\Utils\Converter::convertToPascalCase($action) . 'Action';
                if (method_exists($this, $method)) {
                    $data = $this->$method($data);
                }

                if (empty($data)) {

                    if (static::ACTION_CHECK_ADDON_KEY == $action) {
                        // Exclude check_addon_key action if there are no keys to check
                        unset($actions[$action]);
                        continue;

                    } else {
                        // $actions must be an array where keys are action types and values are data.
                        // We need to fill values with non-empty array otherwise they will be lost on marketplace side
                        $data = array(0);
                    }
                }

                $actions[$action] = $data;
            }
        }

        if ($actions) {
            $result[static::FIELD_QUERIES] = $actions;
        }

        return $result;
    }

    /**
     * Get list of actions which cannot be issued in the 'get_dataset' request
     *
     * @return array
     */
    protected function getDatasetExcludedActions()
    {
        return array(
            static::ACTION_GET_DATASET,
            static::ACTION_TEST_MARKETPLACE,
            static::ACTION_GET_CORE_HASH,
            static::ACTION_GET_CORE_PACK,
            static::ACTION_GET_ADDON_INFO,
            static::ACTION_GET_ADDON_HASH,
            static::ACTION_GET_ADDON_PACK,
            static::ACTION_RESEND_KEY,
            static::ACTION_REQUEST_FOR_UPGRADE,
            static::ACTION_CHANGE_KEY_WAVE,
            static::ACTION_UPDATE_PM,
            static::ACTION_UPDATE_SHM,
        );
    }

    /**
     * Parse response
     *
     * @param \PEAR2\HTTP\Request\Response $response Response
     *
     * @return array
     */
    protected function parseResponseForGetDatasetAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response data
     *
     * @param array $data Data to validate
     *
     * @return boolean
     */
    protected function validateResponseForGetDatasetAction($data)
    {
        return true;
    }

    /**
     * Process result of 'get_dataset' request
     *
     * @param array $data Result data of 'get_dataset' request
     *
     * @return boolean
     */
    protected function processGetDatasetResult($responseData)
    {
        if (is_array($responseData)) {

            foreach($responseData as $action => $data) {

               $actionResult = $this->prepareResponseData($action, $data);

                // $result is NULL when nothing is received from the marketplace
                if (isset($actionResult)) {

                    $saveInTmpVars = true;

                    if (static::ACTION_GET_ADDONS_LIST == $action) {
                        if (is_array($actionResult)) {
                            $this->saveAddonsList($actionResult);
                        }
                        $saveInTmpVars = false;

                    } elseif (static::ACTION_CHECK_ADDON_KEY == $action) {
                        if (is_array($actionResult)) {
                            $this->updateLicenseKeys($actionResult);
                        }
                        $saveInTmpVars = false;
                    }

                    $this->saveResultInCache($action, $actionResult, $saveInTmpVars);
                }
            }
        }

        return $responseData;
    }

    // }}}

    // {{{ "Check for updates" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function checkForUpdates($ttl = null)
    {
        return $this->performActionWithTTL($ttl, static::ACTION_CHECK_FOR_UPDATES);
    }

    /**
     * Return specific data array for "Check for updates" request
     *
     * @return array
     */
    protected function getRequestDataForCheckForUpdatesAction()
    {
        $data = array();

        $modules = $this->getInstalledModulesList();

        if ($modules) {
            $data[static::FIELD_MODULES] = serialize($modules);
        }

        $keys = $this->getModuleLicenseKeys();

        if ($keys) {
            $data[static::FIELD_KEYS] = $keys;
        }

        $email = \XLite\Core\Config::getInstance()->Company->site_administrator;

        if (!$email) {
            // Search for first active root administrator
            $cnd = new \XLite\Core\CommonCell;
            $cnd->{\XLite\Model\Repo\Profile::SEARCH_PERMISSIONS} = \XLite\Model\Role\Permission::ROOT_ACCESS;
            $cnd->{\XLite\Model\Repo\Profile::P_ORDER_BY} = array('p.profile_id');
            $rootAdmins = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd);

            if ($rootAdmins) {
                foreach ($rootAdmins as $admin) {
                    if ($admin->isAdmin() && $admin->isEnabled()) {
                        $email = $admin->getLogin();
                        break;
                    }
                }
            }
        }

        $data[static::FIELD_EMAIL] = $email;

        if (!\XLite::getXCNLicense() && !\XLite::isTrialPeriodExpired()) {
            $data[static::FIELD_TRIAL] = 1;
        }

        return $data;
    }

    /**
     * Get list of installed modules for API request
     *
     * @return array
     */
    protected function getInstalledModulesList()
    {
        $result = array();

        $modules = \XLite\Core\Database::getCacheDriver()
            ? \XLite\Core\Database::getCacheDriver()->fetch('InstalledModules')
            : null;

        if (!$modules) {
             $modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->search($this->getCheckForUpdatesDataCnd());
             \XLite\Core\Database::getCacheDriver()->save('InstalledModules', $modules);
        }

        if ($modules) {
            foreach ($modules as $module) {
                $result[] = array(
                    static::FIELD_NAME   => $module->getName(),
                    static::FIELD_AUTHOR => $module->getAuthor(),
                    static::FIELD_VERSION_MAJOR  => $module->getMajorVersion(),
                    static::FIELD_VERSION_MINOR  => $module->getFullMinorVersion(),
                    static::FIELD_MODULE_ENABLED => $module->getEnabled() ? 1 : 0,
                );
            }
        }

        return $result;
    }

    /**
     * Return data about installed module version for get_addon_pack and get_addon_hash requests
     *
     * @param array $installedModule Installed module identity data
     *
     * @return array
     */
    protected function getInstalledModuleVersion($installedModule)
    {
        return array(
            static::FIELD_VERSION_MAJOR => $installedModule['majorVersion'],
            static::FIELD_VERSION_MINOR => $installedModule['minorVersion'],
            static::FIELD_VERSION_BUILD => !empty($installedModule['build']) ? $installedModule['build'] : 0,
        );
    }

    /**
     * Get module licence keys
     *
     * @return array
     */
    protected function getModuleLicenseKeys()
    {
        $result = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll() as $key) {
            if ('Core' == $key->getName() && 'CDev' == $key->getAuthor()) {
                // Core key - ignore this

            } else {
                // Module key - include this
                $result[] = $key->getKeyValue();
            }
        }

        return $result;
    }

    /**
     * Return conditions for search modules for "Check for updates" request
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getCheckForUpdatesDataCnd()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;

        return $cnd;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForCheckForUpdatesAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForCheckForUpdatesAction(array $data)
    {
        return $this->validateAgainstSchema($data, $this->getSchemaResponseForCheckForUpdatesAction());
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForCheckForUpdatesAction()
    {
        return array(
            static::FIELD_IS_UPGRADE_AVAILABLE  => FILTER_VALIDATE_BOOLEAN,
            static::FIELD_ARE_UPDATES_AVAILABLE => FILTER_VALIDATE_BOOLEAN,
        );
    }

    // }}}

    // {{{ "Get cores" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getCores($ttl = null)
    {
        $result = $this->performActionWithTTL($ttl, static::ACTION_GET_CORES);

        if (static::TTL_NOT_EXPIRED !== $result) {
            $this->clearUpgradeCell();
        }

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetCoresAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetCoresAction(array $data)
    {
        $result = true;

        foreach ($data as $core) {
            $result = $result && $this->validateAgainstSchema($core, $this->getSchemaResponseForGetCoresAction());
        }

        return $result;
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForGetCoresAction()
    {
        return array(
            static::FIELD_VERSION => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => static::REGEXP_VERSION),
            ),
            static::FIELD_REVISION_DATE => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array(),
            ),
            static::FIELD_LENGTH => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
        );
    }

    /**
     * Prepare response schema for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function prepareResponseForGetCoresAction(array $data)
    {
        $result = array();

        $hotfixBranch = \XLite::getInstance()->getHotfixBranchVersion();

        foreach ($data as $core) {
            $version = $core[static::FIELD_VERSION];
            $major = $version[static::FIELD_VERSION_MAJOR];
            $minor = $version[static::FIELD_VERSION_MINOR];
            $build = !empty($version[static::FIELD_VERSION_BUILD])
                ? $version[static::FIELD_VERSION_BUILD]
                : 0;

            $key = ($hotfixBranch === ($major . '.' . $minor) ? $hotfixBranch : $major);

            $fullCoreVersion = $major . '.' . $minor . ($build ? '.' . $build : '');

            $result[$key] = array(
                $fullCoreVersion,
                $core[static::FIELD_REVISION_DATE],
                $core[static::FIELD_LENGTH]
            );
        }

        return $result;
    }

    // }}}

    // {{{ "Get all modules tags" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getAllTags($ttl = null)
    {
        $data = $this->getAllTagsInfo($ttl);

        array_walk(
            $data,
            function (&$a, $k) {
                $a = $k;
            }
        );

        return $data;
    }

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getAllTagsInfo($ttl = null)
    {
        $data = $this->performActionWithTTL($ttl, static::ACTION_GET_ALL_TAGS);

        if (static::TTL_NOT_EXPIRED !== $data) {
            $this->clearUpgradeCell();
        }

        $data = is_array($data) ? $data : array();

        $result = array();
        foreach ($data as $tag) {
            $result[$tag[static::FIELD_TAG_NAME]] = $tag;
        }

        ksort($result);

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetAllTagsAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetAllTagsAction(array $data)
    {
        $result = true;
        foreach ($data as $tag) {
            $result = $result
                && $this->validateAgainstSchema($tag, $this->getSchemaResponseForGetAllTagsAction());
        }

        return $result;
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForGetAllTagsAction()
    {
        return array(
            static::FIELD_TAG_NAME => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_TAG_BANNER_EXPIRATION_DATE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_TAG_BANNER_IMG => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_TAG_MODULE_BANNER => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_TAG_BANNER_URL => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
        );
    }

    /**
     * Prepare response schema for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function prepareResponseForGetAllTagsAction(array $data)
    {
        return $data;
    }

    // }}}

    // {{{ "Get all banners info" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getAllBanners($ttl = null)
    {
        $result = $this->performActionWithTTL($ttl, static::ACTION_GET_ALL_BANNERS);

        if (static::TTL_NOT_EXPIRED !== $result) {
            $this->clearUpgradeCell();
        }

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetAllBannersAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetAllBannersAction(array $data)
    {
        $result = true;
        foreach ($data as $banner) {
            $result = $result
                && $this->validateAgainstSchema($banner, $this->getSchemaResponseForGetAllBannersAction());
        }

        return $result;
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForGetAllBannersAction()
    {
        return array(
            static::FIELD_BANNER_IMG => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_BANNER_MODULE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_BANNER_URL => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
        );
    }

    /**
     * Prepare response schema for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function prepareResponseForGetAllBannersAction(array $data)
    {
        return $data;
    }

    // }}}

    // {{{ "Activate free license" request

    /**
     * Defines the free license key edition name
     *
     * @return string
     */
    public function getFreeLicenseEdition()
    {
        $keys = $this->sendRequestToMarketplace(
            static::ACTION_CHECK_ADDON_KEY,
            array(
                static::FIELD_KEY => trim($this->processKey(static::XC_FREE_LICENSE_KEY)),
            )
        );
        $keyData = $keys[\XLite\Core\Marketplace::XC_FREE_LICENSE_KEY][0]['keyData'];

        return $keyData['editionName'];
    }

    /**
     *
     * @param string $email
     *
     * @return type
     */
    public function activateFreeLicense($email)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_CHECK_ADDON_KEY,
            array(
                static::FIELD_KEY               => trim($this->processKey(static::XC_FREE_LICENSE_KEY)),
                static::FIELD_EMAIL             => $email,
                static::FIELD_DO_REGISTER       => 1,
            )
        );
    }

    // }}}

    // {{{ "The landing page is available" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function isAvailableLanding($ttl = null)
    {
        $result = $this->performActionWithTTL($ttl, static::ACTION_GET_LANDING_AVAILABLE);

        if (static::TTL_NOT_EXPIRED !== $result) {
            $this->clearUpgradeCell();
        }

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForIsLandingAvailableAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForIsLandingAvailableAction(array $data)
    {
        return $this->validateAgainstSchema($data, $this->getSchemaResponseForIsLandingAvailableAction());
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForIsLandingAvailableAction()
    {
        return array(
            static::FIELD_LANDING => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
        );
    }

    /**
     * Prepare response schema for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function prepareResponseForIsLandingAvailableAction(array $data)
    {
        return $data;
    }

    // }}}

    // {{{ "Get core pack" request

    /**
     * The certain request handler
     *
     * @param string $versionMajor Major version of core to get
     * @param string $versionMinor Minor version of core to get
     *
     * @return string
     */
    public function getCorePack($versionMajor, $versionMinor)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_GET_CORE_PACK,
            array(
                static::FIELD_VERSION => $this->getVersionField($versionMajor, $versionMinor),
                static::FIELD_GZIPPED => $this->canCompress(),
            )
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return string
     */
    protected function parseResponseForGetCorePackAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->writeDataToFile($response);
    }

    // }}}

    // {{{ "Get core hash" request

    /**
     * The certain request handler
     *
     * @param string $versionMajor Major version of core to get
     * @param string $versionMinor Minor version of core to get
     *
     * @return string
     */
    public function getCoreHash($versionMajor, $versionMinor)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_GET_CORE_HASH,
            array(
                static::FIELD_VERSION => $this->getVersionField($versionMajor, $versionMinor),
            )
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return string
     */
    protected function parseResponseForGetCoreHashAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetCoreHashAction(array $data)
    {
        return !empty($data) && empty($data['error']);
    }

    // }}}

    // {{{ "Get addons list" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return boolean
     */
    public function getAddonsList($ttl = null)
    {
        $data = array();

        $modules = $this->getInstalledModulesList();

        if ($modules) {
            $data[static::FIELD_MODULES] = serialize($modules);
        }

        $result = $this->performActionWithTTL($ttl, static::ACTION_GET_ADDONS_LIST, $data, false);

        if (static::TTL_NOT_EXPIRED !== $result) {
            $this->saveAddonsList($result);
        }

        return (bool) $result;
    }

    /**
     * The certain request handler
     *
     * @param array $result Modules list received from marketplace
     *
     * @return void
     */
    protected function saveAddonsList($result)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Module')->updateMarketplaceModules((array) $result);

        $this->clearUpgradeCell();
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return string
     */
    protected function parseResponseForGetAddonsAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetAddonsAction(array $data)
    {
        $result = true;

        foreach ($data as $module) {
            $result = $result
                && is_array($module)
                && $this->validateAgainstSchema($module, $this->getSchemaResponseForGetAddonInfoAction());
        }

        return $result;
    }

    /**
     * Prepare data for certain response
     *
     * @param array $data Data received from marketplace
     *
     * @return array
     */
    protected function prepareResponseForGetAddonsAction(array $data)
    {
        $result = array();

        foreach ($data as $module) {
            // Module key fields
            $author = $this->getField($module, static::FIELD_AUTHOR);
            $name   = $this->getField($module, static::FIELD_NAME);

            // Arrays passed in response
            $version = $this->getField($module, static::FIELD_VERSION) ?: array();
            $rating  = $this->getField($module, static::FIELD_RATING)  ?: array();

            // Module versions
            $majorVersion = $this->getField($version, static::FIELD_VERSION_MAJOR);
            $minorVersion = $this->getField($version, static::FIELD_VERSION_MINOR);
            $build = $this->getField($version, static::FIELD_VERSION_BUILD) ?: 0;

            // Short names
            $key = $author . '_' . $name . '_' . $majorVersion . '.' . $minorVersion;

            // To make modules list unique
            if (
                !isset($result[$key])
                || version_compare($result[$key]['minorVersion'], $minorVersion, '<')
                || (
                    version_compare($result[$key]['minorVersion'], $minorVersion, '=')
                    && version_compare($result[$key]['build'], $build, '<')
                )
            ) {
                // It's the structure of \XLite\Model\Module class data
                $result[$key] = array(
                    'name'            => $name,
                    'author'          => $author,
                    'fromMarketplace' => true,
                    'rating'          => $this->getField($rating, static::FIELD_RATING_RATE),
                    'votes'           => $this->getField($rating, static::FIELD_RATING_VOTES_COUNT),
                    'downloads'       => $this->getField($module, static::FIELD_DOWNLOADS_COUNT),
                    'price'           => $this->getField($module, static::FIELD_PRICE),
                    'currency'        => $this->getField($module, static::FIELD_CURRENCY),
                    'majorVersion'    => $majorVersion,
                    'minorVersion'    => $minorVersion,
                    'build'           => $build,
                    'minorRequiredCoreVersion' => $this->getField($module, static::FIELD_MIN_CORE_VERSION),
                    'revisionDate'    => $this->getField($module, static::FIELD_REVISION_DATE),
                    'landingPosition' => $this->getField($module, static::FIELD_LANDING_POSITION),
                    'moduleName'      => $this->getField($module, static::FIELD_READABLE_NAME),
                    'authorName'      => $this->getField($module, static::FIELD_READABLE_AUTHOR),
                    'description'     => $this->getField($module, static::FIELD_DESCRIPTION),
                    'iconURL'         => $this->getField($module, static::FIELD_ICON_URL),
                    'pageURL'         => $this->getField($module, static::FIELD_PAGE_URL),
                    'authorPageURL'   => $this->getField($module, static::FIELD_AUTHOR_PAGE_URL),
                    'dependencies'    => (array) $this->getField($module, static::FIELD_DEPENDENCIES),
                    'packSize'        => $this->getField($module, static::FIELD_LENGTH),
                    'isSystem'        => (bool) $this->getField($module, static::FIELD_IS_SYSTEM),
                    'xcnPlan'         => $this->getField($module, static::FIELD_XCN_PLAN),
                    'hasLicense'      => $this->getField($module, static::FIELD_HAS_LICENSE),
                    'tags'            => $this->getField($module, static::FIELD_TAGS),
                    'authorEmail'     => $this->getField($module, static::FIELD_AUTHOR_EMAIL),
                    'isLanding'       => (bool) $this->getField($module, static::FIELD_IS_LANDING),
                    'editionState'    => $this->getField($module, static::FIELD_EDITION_STATE),
                    'editions'        => (array) $this->getField($module, static::FIELD_EDITIONS),
                    'xbProductId'     => $this->getField($module, static::FIELD_XB_PRODUCT_ID),
                );

                $result[$key] = array_merge($result[$key], $this->adjustResponseItemForGetAddonsAction($module));

            } else {
                // :TODO: add logging here
            }
        }

        return $result;
    }

    /**
     * Adjust result array item for get_addons action
     *
     * @param array $module
     *
     * @return array
     */
    protected function adjustResponseItemForGetAddonsAction($module)
    {
        return array();
    }

    // }}}

    // {{{ "Get addon pack" request

    /**
     * The certain request handler
     *
     * @param string $moduleID        External module identifier
     * @param string $key             Module license key OPTIONAL
     * @param array  $installedModule Installed module identity data
     *
     * @return string
     */
    public function getAddonPack($moduleID, $key = null, $installedModule = null)
    {
        $data = array(
            static::FIELD_MODULE_ID => $moduleID,
            static::FIELD_KEY       => $key,
            static::FIELD_GZIPPED   => $this->canCompress(),
        );

        if (is_array($installedModule)) {
            $data[static::FIELD_MODULE_VERSION] = $this->getInstalledModuleVersion($installedModule);
        }

        return $this->sendRequestToMarketplace(
            static::ACTION_GET_ADDON_PACK,
            $data
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return string
     */
    protected function parseResponseForGetAddonPackAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->writeDataToFile($response);
    }

    // }}}

    // "Get addon info" request

    /**
     * The certain request handler
     *
     * @param string $moduleID External module identifier
     * @param string $key      Module license key OPTIONAL
     *
     * @return array
     */
    public function getAddonInfo($moduleID, $key = null)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_GET_ADDON_INFO,
            array(
                static::FIELD_MODULE_ID => $moduleID,
                static::FIELD_KEY       => $key,
            )
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetAddonInfoAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetAddonInfoAction(array $data)
    {
        return $this->validateAgainstSchema($data, $this->getSchemaResponseForGetAddonInfoAction());
    }

    /**
     * Return validation schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForGetAddonInfoAction()
    {
        return array(
            static::FIELD_VERSION => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => static::REGEXP_VERSION),
            ),
            static::FIELD_REVISION_DATE => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array(),
            ),
            static::FIELD_AUTHOR => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NAME => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_READABLE_AUTHOR => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_READABLE_NAME   => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_MODULE_ID => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_HASH),
            ),
            static::FIELD_DESCRIPTION => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_PRICE => FILTER_VALIDATE_FLOAT,
            static::FIELD_CURRENCY => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_CURRENCY),
            ),
            static::FIELD_ICON_URL => FILTER_SANITIZE_URL,
            static::FIELD_PAGE_URL => FILTER_SANITIZE_URL,
            static::FIELD_AUTHOR_PAGE_URL => FILTER_SANITIZE_URL,
            static::FIELD_RATING => array(
                'filter'  => FILTER_SANITIZE_NUMBER_FLOAT,
                'flags'   => FILTER_REQUIRE_ARRAY | FILTER_FLAG_ALLOW_FRACTION,
            ),
            static::FIELD_DEPENDENCIES => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => static::REGEXP_CLASS),
            ),
            static::FIELD_DOWNLOADS_COUNT => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
            static::FIELD_LENGTH => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
            static::FIELD_XCN_PLAN => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => -1),
            ),
            static::FIELD_TAGS => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => static::REGEXP_CLASS),
            ),
            static::FIELD_AUTHOR_EMAIL => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_IS_LANDING => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
            static::FIELD_LANDING_POSITION => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => -1),
            ),
            static::FIELD_MIN_CORE_VERSION => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
            static::FIELD_EDITION_STATE => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
            static::FIELD_EDITIONS => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => static::REGEXP_CLASS),
            ),
            static::FIELD_XB_PRODUCT_ID => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
        );
    }

    // }}}

    // {{{ "Get addon hash" action

    /**
     * The certain request handler
     *
     * @param string $moduleID        External module identifier
     * @param string $key             Module license key OPTIONAL
     * @param array  $installedModule Installed module identity data
     *
     * @return array
     */
    public function getAddonHash($moduleID, $key = null, $installedModule = null)
    {
        $data = array(
            static::FIELD_MODULE_ID => $moduleID,
            static::FIELD_KEY       => $key,
        );

        if (is_array($installedModule)) {
            $data[static::FIELD_MODULE_VERSION] = $this->getInstalledModuleVersion($installedModule);
        }

        return $this->sendRequestToMarketplace(
            static::ACTION_GET_ADDON_HASH,
            $data
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return string
     */
    protected function parseResponseForGetAddonHashAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetAddonHashAction(array $data)
    {
        return !empty($data) && empty($data['error']);
    }

    // }}}

    // {{{ "Check addon key" request

    /**
     * The certain request handler
     *
     * @param string $key Module license to check
     *
     * @return array
     */
    public function checkAddonKey($key)
    {
        $params = array(
            static::FIELD_KEY         => trim($this->processKey($key)),
            static::FIELD_DO_REGISTER => 1,
        );

        $wave = \XLite\Core\Config::getInstance()->Environment->upgrade_wave;
        $waves = $this->getWaves();

        if (isset($waves[$wave])) {
            $waveKeys = array_keys($waves);
            $lastKey = array_pop($waveKeys);
            if ($lastKey != $wave) {
                $params[static::FIELD_WAVE] = $wave;
            }
        }

        return $this->sendRequestToMarketplace(
            static::ACTION_CHECK_ADDON_KEY,
            $params
        );
    }

    /**
     * Preprocess key value
     *
     * @param string $key Key value
     *
     * @return string
     */
    protected function processKey($key)
    {
        $hostDetails = \XLite::getInstance()->getOptions('host_details');
        $host = \XLite\Core\Request::getInstance()->isHTTPS() ? $hostDetails['https_host'] : $hostDetails['http_host'];

        return $this->decryptKey($key, $host) ?: $key;
    }

    /**
     * Decrypt key value
     *
     * @param string $crypted Encrypted key string
     * @param string $sk      Service key
     *
     * @return string
     */
    protected function decryptKey($crypted, $sk)
    {
        $result = '';
        $s1 = $s2 = array();

        for ($i = 0; $i < (strlen($crypted) - 1); $i += 2) {
            $s1[] = $crypted[$i];
            $s2[] = $crypted[$i + 1];
        }

        $s1 = implode('', array_reverse($s1));
        $s2 = substr(implode($s2), 0, 32);

        if (substr(md5($sk), 0, min(32, strlen($s1))) == $s2) {
            $result = base64_decode($s1);
        }

        return $result;
    }

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getWaves($ttl = null)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')->findAll()
            ? $this->performActionWithTTL($ttl, static::ACTION_GET_WAVES)
            : array();
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetWavesAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetWavesAction(array $data)
    {
        $result = true;

        foreach ($data as $key => $value) {
            $result = $result
                && is_integer($key)
                && is_string($value);
        }

        return $result;
    }

    /**
     * The certain request handler
     *
     * @return boolean
     */
    public function changeKeysWave($wave)
    {
        $result = null;

        $keys = array_unique(
            \Includes\Utils\ArrayManager::getObjectsArrayFieldValues(
                \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')->findAll(),
                'getKeyValue',
                true
            )
        );

        if (!empty($keys)) {
            $result = $this->sendRequestToMarketplace(
                static::ACTION_CHANGE_KEY_WAVE,
                array(
                    static::FIELD_KEYS => $keys,
                    static::FIELD_WAVE => $wave,
                )
            );
        }

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForChangeKeyWaveAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for error message
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForChangeKeyWaveAction(array $data)
    {
        return is_array($data) && !empty($data[0]) && 0 === strcasecmp($data[0], 'ok');
    }

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return boolean
     */
    public function checkAddonsKeys($ttl = null)
    {
        // Make request to the marketplace
        $result = $this->performActionWithTTL($ttl, static::ACTION_CHECK_ADDON_KEY, array(), false);

        if (!is_null($result) && static::TTL_NOT_EXPIRED !== $result) {
            $this->updateLicenseKeys($result);

        } else {
            // Wrong response or TTL is not expired
            $result = false;
        }

        return $result;
    }

    /**
     * Update info about license keys
     *
     * @param array $result Result received from marketplace
     *
     * @return void
     */
    protected function updateLicenseKeys($result)
    {
        $repoModuleKey = \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey');
        $repoModule = \XLite\Core\Database::getRepo('\XLite\Model\Module');

        // Get current keys
        $keys = \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')->findAll();

        foreach ($keys as $existingKey) {

            $keyValue = $existingKey->getKeyValue();

            if (isset($result[$keyValue]) && is_array($result[$keyValue])) {

                // MP response contains info about the existing key

                foreach ($result[$keyValue] as $info) {

                    if ('CDev' == $info['author'] && 'Core' == $info['name']) {
                        // Entity is core
                        $isValid = true;

                    } else {
                        // Entity is module. Search for existing module
                        $isValid = (bool) $repoModule->findOneBy(
                            array(
                                'author' => $info['author'],
                                'name'   => $info['name'],
                            )
                        );
                    }

                    if ($isValid) {

                        $info['active'] = 1;
                        unset($info['key']);

                        // Update existing key model object
                        $repoModuleKey->update($existingKey, $info);

                        // Clear cache for proper installation
                        $this->clearActionCache(\XLite\Core\Marketplace::ACTION_GET_ADDONS_LIST);

                    } else {
                        // No module has been found - delete key
                        $repoModuleKey->delete($existingKey);
                    }

                } // foreach

            } else {
                // MP response doesn't contain existing key
                if (\XLite\Core\Config::getInstance()->Version->deleteEmptyKeys) {
                    // Add key to the remove list
                    $toDelete[] = $existingKey;

                } else {
                    // Set key to inactive state
                    $existingKey->setActive(0);
                }
            }

        } // foreach ($keys...

        if (!empty($toDelete)) {
            // Delete keys
            $repoModuleKey->deleteInBatch($toDelete);
        }

        if (\XLite\Core\Config::getInstance()->Version->deleteEmptyKeys) {
            $option = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                array(
                    'name'     => 'deleteEmptyKeys',
                    'category' => 'Version'
                )
            );

            if ($option) {
                \XLite\Core\Database::getEM()->delete($option);
                \XLite\Core\Config::updateInstance();
            }
        }
    }

    /**
     * Return specific data array for "check_addon_key" request
     *
     * @return array
     */
    protected function getRequestDataForCheckAddonKeyAction()
    {
        $result = array();

        $keys = \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')->findAll();

        if (!empty($keys)) {

            // Prepare list of keys for the request
            $result[static::FIELD_KEY] = array_unique(
                \Includes\Utils\ArrayManager::getObjectsArrayFieldValues(
                    $keys,
                    'getKeyValue',
                    true
                )
            );
        }

        return $result;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForCheckAddonKeyAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForCheckAddonKeyAction(array $data)
    {
        $result = true;

        $schema = $this->getSchemaResponseForCheckAddonKeyAction();

        foreach ($data as $key => $addons) {
            foreach ($addons as $addon) {
                $result = $result
                    && is_array($addon)
                    && $this->validateAgainstSchema($addon, $schema);
            }
        }

        return $result;
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForCheckAddonKeyAction()
    {
        return array(
            static::FIELD_AUTHOR => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NAME => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_KEY_TYPE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_NUMBER),
            ),
            static::FIELD_KEY_DATA => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => array('regexp' => '/.*/'),
            ),
            static::FIELD_KEY => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
        );
    }

    /**
     * Validate response for error message
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForErrorAction(array $data)
    {
        return $this->validateAgainstSchema($data, $this->getSchemaResponseForError());
    }

    /**
     * Return response schema for errors
     *
     * @return array
     */
    protected function getSchemaResponseForError()
    {
        return array(
            static::FIELD_ERROR_CODE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_ERROR_MESSAGE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
        );
    }

    // }}}

    // {{{ "Re-send key" request

    /**
     * Resend license key
     *
     * @param string $email Email
     *
     * @return array
     */
    public function doResendLicenseKey($email)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_RESEND_KEY,
            array(
                static::FIELD_EMAIL => $email,
            )
        );
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForResendKeyAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForResendKeyAction(array $data)
    {
        return is_array($data) && !empty($data[0]) && 0 === strcasecmp($data[0], 'ok');
    }

    // }}}

    // {{{ "Get hosting score" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getHostingScore($ttl = null)
    {
        return $this->performActionWithTTL($ttl, static::ACTION_GET_HOSTING_SCORE);
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetHostingScoreAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * FIXME: use a schema
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetHostingScoreAction(array $data)
    {
        $result = true;

        foreach ($data as $row) {
            if (!is_array($row)
                || !empty($row['name'])
                || !isset($row['score'])
                || !ctype_digit($row['score'])
                || (isset($row['link']) && !is_string($row['link']))
            ) {
                $result = false;

                break;
            }
        }

        return $result;
    }

    // }}}

    // {{{ "Request for upgrade" request

    /**
     * The certain request handler
     *
     * @param array $modules Modules
     *
     * @return array
     */
    public function requestForUpgrade($modules)
    {
        return $this->sendRequestToMarketplace(
            static::ACTION_REQUEST_FOR_UPGRADE,
            $this->getRequestForUpgradeData($modules)
        );
    }

    /**
     * Return specific data array for "Check for updates" request
     *
     * @param array $modules Modules
     *
     * @return array
     */
    protected function getRequestForUpgradeData($modules)
    {
        $data = array();

        if ($modules) {
            $data[static::FIELD_EMAIL] = \XLite\Core\Auth::getInstance()->getProfile()->getLogin();
            $data[static::FIELD_MODULES] = array();
            /** @var \XLite\Model\Module $module */
            foreach ($modules as $module) {
                if (!$module->isCustom()) {
                    $data[static::FIELD_MODULES][] = array(
                        static::FIELD_NAME => $module->getName(),
                        static::FIELD_AUTHOR => $module->getAuthor(),
                        static::FIELD_VERSION_MAJOR => $module->getMajorVersion(),
                        static::FIELD_VERSION_MINOR => $module->getFullMinorVersion(),
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForRequestForUpgradeAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForRequestForUpgradeAction(array $data)
    {
        return $this->validateAgainstSchema($data, $this->getSchemaResponseForRequestForUpgradeAction());
    }

    /**
     * Return response schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForRequestForUpgradeAction()
    {
        return array(
            static::FIELD_IS_REQUEST_FOR_UPGRADE_SENT => FILTER_VALIDATE_BOOLEAN,
        );
    }

    // }}}

    // {{{ "Get xc5 notifications" request

    /**
     * The certain request handler
     *
     * @param integer $ttl Data TTL OPTIONAL
     *
     * @return array
     */
    public function getXC5Notifications($ttl = null)
    {
        $result = $this->performActionWithTTL($ttl ?: static::TTL_SHORT, static::ACTION_GET_XC5_NOTIFICATIONS);

        return is_array($result) ? $result : array();
    }

    /**
     * Unseen updates available hash
     *
     * @return string Hash of modules updates messages
     */
    public function unseenUpdatesHash()
    {
        $result = array();
        $messages       = $this->getXC5Notifications();
        $coreVersion    = \XLite\Upgrade\Cell::getInstance()->getCoreVersion();
        if ($messages) {
            foreach ($messages as $message) {
                if ($message['type'] == 'module') {
                    $result[] = $message;
                }
            }
        }
        return md5(serialize($result) . serialize($coreVersion));
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForGetXC5NotificationsAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for certain action
     *
     * FIXME: use a schema
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForGetXC5NotificationsAction(array $data)
    {
        $result = true;

        foreach ($data as $notification) {
            $result = $result
                && is_array($notification)
                && $this->validateAgainstSchema($notification, $this->getSchemaResponseForGetXC5NotificationAction());
        }

        return $result;
    }

    /**
     * Return validation schema for certain action
     *
     * @return array
     */
    protected function getSchemaResponseForGetXC5NotificationAction()
    {
        return array(
            static::FIELD_NOTIFICATION_TYPE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NOTIFICATION_MODULE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NOTIFICATION_IMAGE => FILTER_SANITIZE_URL,
            static::FIELD_NOTIFICATION_TITLE => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NOTIFICATION_DESCRIPTION => array(
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => static::REGEXP_WORD),
            ),
            static::FIELD_NOTIFICATION_LINK => FILTER_SANITIZE_URL,
            static::FIELD_NOTIFICATION_DATE => array(
                'filter'  => FILTER_VALIDATE_INT,
                'options' => array('min_range' => 0),
            ),
        );
    }

    // }}}

    // {{{ Test marketplace request

    /**
     * The certain request handler
     *
     * @return array
     */
    public function doTestMarketplace()
    {
        // Perform request in run-time (not schedule this)
        $data = $this->performActionWithTTL(static::TTL_TEST_MP, static::ACTION_TEST_MARKETPLACE, array(), true, false);

        return is_array($data) && !empty($data[0]) && 0 === strcasecmp($data[0], 'ok');
    }

    /**
     * Parse response for certain action
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     *
     * @return array
     */
    protected function parseResponseForTestMarketplaceAction(\PEAR2\HTTP\Request\Response $response)
    {
        return $this->parseJSON($response);
    }

    /**
     * Validate response for error message
     *
     * @param array $data Response data
     *
     * @return array
     */
    protected function validateResponseForTestMarketplaceAction(array $data)
    {
        return is_array($data) && !empty($data[0]) && 0 === strcasecmp($data[0], 'ok');
    }

    // }}}

    // {{{ Common methods to send request to marketplace

    /**
     * Send request to marketplace endpoint and return the response
     *
     * @param string $action Name of the action
     * @param array  $data   Custom data to send in request OPTIONAL
     *
     * @return string NULL-result means that nothing was received from Marketplace
     *                (the most often reason is the connection timeout)
     */
    protected function sendRequestToMarketplace($action, array $data = array())
    {
        $result = null;

        $skipRequest = (!in_array($action, array(static::ACTION_TEST_MARKETPLACE, static::ACTION_RESEND_KEY)))
            && \XLite\Core\Session::getInstance()->mpServerError
            && \XLite\Core\Converter::time() < (\XLite\Core\Session::getInstance()->mpServerError + static::ERROR_REQUEST_TTL);

        if (!$skipRequest) {
            \XLite\Core\Session::getInstance()->mpServerError = null;

            // Start timer
            $startTime = microtime(true);

            // Run bouncer
            $request = $this->getRequest($action, $data);
            $response = $request->sendRequest();

            // Stop timer
            $responseTime = (microtime(true) - $startTime);

            $log = array(
                'action' => $action,
            );

            if ($response) {
                $error = $this->checkForErrors($response, $data);

                if ($error) {
                    $this->logError($action, $error);
                    $log['error'] = $error;

                } else {
                    $result = $this->prepareResponse($response, $action);
                    $log['success'] = true;
                }

            } else {
                \XLite\Core\Session::getInstance()->mpServerError = time();
                $message = static::t('Can\'t connect to the marketplace server') . ': ' . $request->getErrorMessage();
                $this->logError($action, $message);
                if ($this->isDisplayErrorTopMessage($action)) {
                    \XLite\Core\TopMessage::addError($message);
                }
                $log['errormsg'] = $message;
            }

            $log['time'] = $responseTime;

            // Uncomment line below to log requests to marketplace
            // \XLite\Logger::logCustom('mp-access', array('sendRequestToMarketplace' => $log));
        }

        return $result;
    }

    /**
     * Return true if error top message should be displayed.
     * Do not display error top messages if query was called in asyncronous mode (get_dataset)
     *
     * @param string $action Action
     *
     * @return boolean
     */
    protected function isDisplayErrorTopMessage($action)
    {
        return static::ACTION_GET_DATASET != $action;
    }

    /**
     * Return prepared request object
     *
     * @param string $action Action name
     * @param array  $data   Request data OPTIONAL
     *
     * @return \XLite\Core\HTTP\Request
     */
    protected function getRequest($action, array $data = array())
    {
        $url   = $this->getMarketplaceActionURL($action);
        $data += $this->getRequestCommonData();

        $request = new \XLite\Core\HTTP\Request($url);
        $request->body = $data;

        if (function_exists('curl_version')) {
            $request->setAdditionalOption(\CURLOPT_SSLVERSION, 1);
            $curlVersion = curl_version();

            if ($curlVersion
                && $curlVersion['ssl_version']
                && 0 !== strpos($curlVersion['ssl_version'], 'NSS')
            ) {
                $request->setAdditionalOption(\CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            }
        }

        if (in_array($action, static::getLongActions())) {
            $request->requestTimeout = static::REQUEST_LONG_TTL;

        } else {
            $request->requestTimeout = static::REQUEST_TTL;
        }

        $this->logInfo($action, 'The "{{url}}" URL requested', array('url' => $url), $data);

        return $request;
    }

    /**
     * Common data for all request types
     *
     * @return array
     */
    protected function getRequestCommonData()
    {
        $data = array(
            static::FIELD_VERSION_API => static::MP_API_VERSION,
            static::FIELD_SHOP_ID     => $this->generateShopID(),
            static::FIELD_SHOP_DOMAIN => \Includes\Utils\ConfigParser::getOptions(array('host_details', 'http_host')),
            static::FIELD_SHOP_URL    => \XLite\Core\URLManager::getShopURL(),
            static::FIELD_VERSION_CORE_CURRENT => $this->getVersionField(
                \XLite::getInstance()->getMajorVersion(),
                \XLite::getInstance()->getMinorVersion()
            ),
            static::FIELD_XCN_LICENSE_KEY => \XLite::getXCNLicenseKey(),
            static::FIELD_INSTALLATION_LNG  => \XLite::getInstallationLng(),
        );

        if (\XLite::getAffiliateId()) {
            $data[static::FIELD_AFFILIATE_ID] = \XLite::getAffiliateId();
        }

        return $data;
    }

    /**
     * Generate shop ID value
     *
     * @return string
     */
    protected function generateShopID()
    {
        $authCode = \Includes\Utils\ConfigParser::getOptions(array('installer_details', 'auth_code'));
        $secretKey = \Includes\Utils\ConfigParser::getOptions(array('installer_details', 'shared_secret_key'));

        return $authCode
            ? md5($authCode . $secretKey)
            : '';
    }

    /**
     * Check for response errors
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     * @param array                        $data     Request data
     *
     * @return string  Error message
     * @return boolean False if there are no errors
     */
    protected function checkForErrors(\PEAR2\HTTP\Request\Response $response, array $data)
    {
        $result = false;
        $errorBlock = $this->parseJSON($response);

        if (is_array($errorBlock)
            && $this->validateResponseForErrorAction($errorBlock)
        ) {
            $this->doErrorAction($errorBlock, $data);

            $result = 'Error code ('
                . $errorBlock[static::FIELD_ERROR_CODE] . '): '
                . $errorBlock[static::FIELD_ERROR_MESSAGE];
        }

        return $result;
    }

    /**
     * Do some actions concerning errors
     *
     * @param array $error Error block
     * @param array $data  Request data
     *
     * @return void
     */
    protected function doErrorAction(array $error, array $data)
    {
        static::$lastErrorCode = $error[static::FIELD_ERROR_CODE];

        if (static::ERROR_CODE_REFUND === $error[static::FIELD_ERROR_CODE]) {
            // Refunded Module license key must be removed from shop
            $key = \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')
                ->findOneBy(array('keyValue' => $data[static::FIELD_KEY]));

            if ($key) {
                \XLite\Core\Database::getEM()->remove($key);
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Prepare the marketplace response
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to prepare
     * @param string                       $action   Current action
     *
     * @return mixed
     */
    protected function prepareResponse(\PEAR2\HTTP\Request\Response $response, $action)
    {
        $result = null;
        $method = 'ResponseFor' . \Includes\Utils\Converter::convertToPascalCase($action) . 'Action';

        if (200 == $response->code) {
            if (isset($response->body)) {
                $result = $this->{'parse' . $method}($response);

            } else {
                $this->logError($action, 'An empty response received');
            }
        } else {
            $this->logError($action, 'Returned the "{{code}}" code', array('code' => $response->code));
        }

        return $this->prepareResponseData($action, $result);
    }

    /**
     * Prepare the marketplace response data
     *
     * @param string $action Current action
     * @param array  $result Parsed response
     *
     * @return mixed
     */
    protected function prepareResponseData($action, $result)
    {
        $method = 'ResponseFor' . \Includes\Utils\Converter::convertToPascalCase($action) . 'Action';

        if (is_array($result)) {
            if ($this->{'validate' . $method}($result)) {
                if (method_exists($this, 'prepare' . $method)) {
                    $result = $this->{'prepare' . $method}($result);
                }

                $suffix = empty($result) ? ' (empty)' : '';

                $this->logInfo($action, 'Valid response received' . $suffix, array(), $result);

            } else {
                $this->logError($action, 'Response has an invalid format', array(), $result);

                $result = null;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Cache-related routines

    /**
     * Clearing the temporary cache for a given marketplace action
     *
     * @param string|array $action Marketplace action OPTIONAL
     *
     * @return mixed
     */
    public function clearActionCache($action = null)
    {
        $list = isset($action)
            ? (
                !empty($action) && is_array($action)
                ? $action
                : array($action)
            )
            : $this->getCachedRequestTypes();

        foreach ($list as $requestType) {
            list($cellTTL, $cellData) = $this->getActionCacheVars($requestType);

            \XLite\Core\TmpVars::getInstance()->$cellData = null;
            \XLite\Core\TmpVars::getInstance()->$cellTTL  = null;
        }
        \XLite\Core\Session::getInstance()->mpServerError = null;

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Return list of marketplace request types which are cached in tmp_vars
     *
     * @return array
     */
    protected function getCachedRequestTypes()
    {
        return array(
            static::ACTION_CHECK_FOR_UPDATES,
            static::ACTION_CHECK_ADDON_KEY,
            static::ACTION_GET_CORES,
            static::ACTION_GET_ADDONS_LIST,
            static::ACTION_GET_ALL_BANNERS,
            static::ACTION_GET_ALL_TAGS,
        //    static::ACTION_GET_HOSTING_SCORE,
            static::ACTION_GET_LANDING_AVAILABLE,
            static::ACTION_GET_WAVES,
            static::ACTION_UPDATE_PM,
            static::INACTIVE_KEYS,
        );
    }

    /**
     * Get all marketplace actions list
     *
     * @return integer
     */
    protected function getMarketplaceActions()
    {
        return array(
            static::ACTION_CHECK_FOR_UPDATES     => static::TTL_LONG,
            static::ACTION_CHECK_ADDON_KEY       => static::TTL_LONG,
            static::ACTION_GET_CORES             => static::TTL_LONG,
            static::ACTION_GET_ADDONS_LIST       => static::TTL_LONG,
            static::ACTION_GET_ALL_BANNERS       => static::TTL_LONG,
            static::ACTION_GET_ALL_TAGS          => static::TTL_LONG,
          //  static::ACTION_GET_HOSTING_SCORE     => static::TTL_LONG,
            static::ACTION_GET_LANDING_AVAILABLE => static::TTL_LONG,
            static::ACTION_GET_WAVES             => static::TTL_LONG,
            static::ACTION_GET_XC5_NOTIFICATIONS => static::TTL_SHORT,
            static::ACTION_UPDATE_PM             => static::TTL_LONG,
            static::ACTION_UPDATE_SHM            => static::TTL_LONG,
        );
    }

    /**
     * Get action TTL
     *
     * @param string $action Action type
     *
     * @return integer
     */
    protected function getActionTTL($action)
    {
        $ttls = $this->getMarketplaceActions();

        return isset($ttls[$action]) ? $ttls[$action] : null;
    }

    /**
     * Return action cache variables
     *
     * @param string $action Marketplace action
     *
     * @return array
     */
    protected function getActionCacheVars($action)
    {
        return array(
            $action . 'TTL',
            $action . 'Data'
        );
    }

    /**
     * Perform some action if a TTL is expired
     *
     * @param integer $ttl           Time to live
     * @param string  $action        Marketplace action
     * @param array   $data          Data to send to marketplace OPTIONAL
     * @param boolean $saveInTmpVars Flag OPTIONAL
     *
     * @return mixed
     */
    protected function performActionWithTTL($ttl, $action, array $data = array(), $saveInTmpVars = true, $schedule = true)
    {
        \XLite\Core\Lock\MarketplaceLocker::getInstance()->waitForUnlocked($action);

        \XLite\Core\Lock\MarketplaceLocker::getInstance()->lock($action);

        $result = static::TTL_NOT_EXPIRED;

        // Do not schedule action if $ttl = 0
        $schedule = $schedule && 0 !== $ttl;

        $ttl = !is_null($ttl) ? $ttl : static::TTL_LONG;

        list($cellTTL, $cellData) = $this->getActionCacheVars($action);

        // Check if expired
        if (!$this->checkTTL($cellTTL, $ttl)) {

            if (empty($data)) {
                $method = 'getRequestDataFor' . \Includes\Utils\Converter::convertToPascalCase($action) . 'Action';
                if (method_exists($this, $method)) {
                    $data = $this->$method($data);
                }
            }

            if (static::ACTION_CHECK_ADDON_KEY == $action && empty($data)) {
                // Skip check_addon_key with empty data
                $saveInTmpVars = false;

            } else {

                if ($schedule) {
                    $this->scheduleAction($action, $data);
                    $saveInTmpVars = false;

                } else {
                    // Call method
                    $result = $this->sendRequestToMarketplace($action, $data);

                    // $result is NULL when nothing is received from the marketplace
                    if (isset($result)) {
                        $this->saveResultInCache($action, $result, $saveInTmpVars);
                    }
                }
            }
        }

        \XLite\Core\Lock\MarketplaceLocker::getInstance()->unlock($action);

        return $saveInTmpVars ? \XLite\Core\TmpVars::getInstance()->$cellData : $result;
    }

    /**
     * Save result in the cache
     *
     * @param string  $action        Action type
     * @param mixed   $result        Result
     * @param boolean $saveInTmpVars Flag: true - save result in cache, false - save only timestamp or request
     */
    protected function saveResultInCache($action, $result, $saveInTmpVars)
    {
        list($cellTTL, $cellData) = $this->getActionCacheVars($action);

        if ($saveInTmpVars) {
            // Save in DB (if needed)
            \XLite\Core\TmpVars::getInstance()->$cellData = $result;
        }

        $this->removeScheduledAction($action);
        $this->setTTLStart($cellTTL);
    }

    /**
     * Schedule action
     *
     * @param string $action Action type
     * @param array  $data   Action data
     *
     * @return void
     */
    protected function scheduleAction($action, $data)
    {
        $current = \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;

        if (!$current) {
            $current = array();
        }

        $current[$action] = $data;

        \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = $current;
    }

    /**
     * Remove action from the scheduled actions list
     *
     * @param string $action Action type
     *
     * @return void
     */
    protected function removeScheduledAction($action)
    {
        $current = \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;

        if (isset($current[$action])) {
            unset($current[$action]);
            \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = $current ?: null;
        }

    }

    /**
     * Get list of scheduled actions
     *
     * @return array
     */
    protected function getScheduledActions()
    {
        return \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;
    }

    /**
     * Clear list of scheduled actions
     *
     * @return void
     */
    protected function clearScheduledActions()
    {
        \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = null;
    }

    /**
     * Check and update cache TTL
     *
     * @param string  $cell Name of the cache cell
     * @param integer $ttl  TTL value (in seconds)
     *
     * @return boolean
     */
    protected function checkTTL($cell, $ttl)
    {
        // Fetch a certain cell value
        $start = \XLite\Core\TmpVars::getInstance()->$cell;

        return isset($start) && \XLite\Core\Converter::time() < ($start + $ttl);
    }

    /**
     * Renew TTL cell value
     *
     * @param string $cell Name of the cache cell
     *
     * @return void
     */
    protected function setTTLStart($cell)
    {
        \XLite\Core\TmpVars::getInstance()->$cell = \XLite\Core\Converter::time();
    }

    // }}}

    // {{{ Parsers and validators

    /**
     * Compose versions into one field
     *
     * @param string $versionMajor Major version of core to get
     * @param string $versionMinor Minor version of core to get
     *
     * @return string
     */
    protected function getVersionField($versionMajor, $versionMinor)
    {
        list($versionMinor, $build) = \Includes\Utils\Converter::parseMinorVersion($versionMinor);

        return array(
            static::FIELD_VERSION_MAJOR => $versionMajor,
            static::FIELD_VERSION_MINOR => $versionMinor,
            static::FIELD_VERSION_BUILD => $build,
        );
    }

    /**
     * Alias
     *
     * @param array  $data  Data to get field value from
     * @param string $field Name of field to get
     *
     * @return mixed
     */
    protected function getField(array $data, $field)
    {
        return \Includes\Utils\ArrayManager::getIndex($data, $field, true);
    }

    /**
     * Parse JSON string
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to get data
     *
     * @return mixed
     */
    protected function parseJSON(\PEAR2\HTTP\Request\Response $response)
    {
        return json_decode($response->body, true);
    }

    /**
     * Write data from request into a file
     *
     * @param \PEAR2\HTTP\Request\Response $response Response to get data
     *
     * @return string
     */
    protected function writeDataToFile(\PEAR2\HTTP\Request\Response $response)
    {
        if (!\Includes\Utils\FileManager::isDir(LC_DIR_TMP)) {
            \Includes\Utils\FileManager::mkdir(LC_DIR_TMP);
        }

        if (!\Includes\Utils\FileManager::isDirWriteable(LC_DIR_TMP)) {
            \Includes\ErrorHandler::fireError('Directory "' . LC_DIR_TMP . '" is not writeable');
        }

        $path = \Includes\Utils\FileManager::getUniquePath(
            LC_DIR_TMP,
            uniqid() . '.' . \Includes\Utils\PHARManager::getExtension() ?: 'tar'
        );

        return (isset($response->body) && \Includes\Utils\FileManager::write($path, $response->body)) ? $path : null;
    }

    /**
     * Common method to validate response
     *
     * FIXME: must ignore unknown fields in data from marketplace
     *
     * @param array $data   Data to validate
     * @param array $schema Validation schema
     *
     * @return boolean
     */
    protected function validateAgainstSchema(array $data, array $schema)
    {
        // :NOTE: do not change operator to the "===":
        // "Filter" extension changes type for some variables
        return array_intersect_key($data, $filtered = filter_var_array($data, $schema)) == $filtered;
    }

    // }}}

    // {{{ Misc methods

    /**
     * Return markeplace URL
     *
     * @return string
     */
    public function getMarketplaceURL()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('marketplace', 'url'));
    }

    /**
     * Get enpoint URL for certain action
     *
     * @param string $action Action name
     *
     * @return string
     */
    protected function getMarketplaceActionURL($action)
    {
        return \Includes\Utils\Converter::trimTrailingChars($this->getMarketplaceURL(), '/') . '/' . $action;
    }

    /**
     * To determine what type of archives to download
     *
     * @return boolean
     */
    protected function canCompress()
    {
        return \Includes\Utils\PHARManager::canCompress();
    }

    /**
     * Clear saved data
     *
     * @return void
     */
    protected function clearUpgradeCell()
    {
        \XLite\Core\TmpVars::getInstance()->{\XLite\Upgrade\Cell::CELL_NAME} = null;
    }

    // }}}

    // {{{ License check

    /**
     * Return true if system detected unallowed modules
     *
     * @return boolean
     */
    public function hasUnallowedModules()
    {
        return (bool) $this->getInactiveContentData(false);
    }

    /**
     * Return true if system detected inactive license key
     *
     * @return boolean
     */
    public function hasInactiveLicenseKey()
    {
        return (bool) $this->getInactiveContentData(true);
    }

    /**
     * Get inactive content by type
     *
     * @param boolean $isCore Flag of result type (true - inactive core license key; false - unalloed modules)
     *
     * @return array
     */
    public function getInactiveContentData($isCore = true)
    {
        $result = array();

        foreach ($this->getInactiveLicenseKeys() as $k => $v) {
            if (
                ($isCore && $v['isCore'])
                || (!$isCore && !$v['isCore'])
            ) {
                $result[] = $v;
            }
        }

        return $result;
    }

    /**
     * Check and return list of inactive license keys and unallowed modules
     *
     * @return array
     */
    public function getInactiveLicenseKeys($ttl = null)
    {
        list($cellTTL, $cellData) = $this->getActionCacheVars(static::INACTIVE_KEYS);

        // Get cached result
        $result = \XLite\Core\TmpVars::getInstance()->$cellData;

        if (empty($result) || !is_array($result)) {
            $result = array();
        }

        $ttl = !is_null($ttl) ? $ttl : static::TTL_LONG;

        // Check if TTL has expired
        if (!$this->checkTTL($cellTTL, $ttl)) {

            $this->getAddonsList();
            $this->checkAddonsKeys(0);

            $entities = array();

            $coreLicense = \XLite::getXCNLicense();

            if ($coreLicense && !\XLite::isFreeLicense() && !$coreLicense->getActive()) {
                $entities[] = array(
                    'isCore' => true,
                    'key'    => $coreLicense->getKeyValue(),
                );
            }

            $entities = array_merge($entities, $this->getUnallowedModules());

            \XLite\Core\TmpVars::getInstance()->$cellData = $entities;

            $this->setTTLStart($cellTTL);

            $result = $entities;
        }

        return $result;
    }

    /**
     * Get list of unallowed modules
     *
     * @return array
     */
    protected function getUnallowedModules()
    {
        $result = array();

        $modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->findUnallowedModules();

        foreach ($modules as $module) {
            $result[] = array(
                'isCore'       => false,
                'name'         => $module[0]->getName(),
                'author'       => $module[0]->getAuthor(),
                'key'          => $module['key'],
            );
        }

        return $result;
    }

    // }}}

    // {{{ Error handling

    /**
     * Log error
     *
     * @param string $action  Current request action
     * @param string $message Message to log
     * @param array  $args    Message args OPTIONAL
     * @param array  $data    Data sent/received OPTIONAL
     *
     * @return void
     */
    protected function logError($action, $message, array $args = array(), array $data = array())
    {
        $this->setError($message);

        $this->logCommon('Error', $action, $message, $args, $data);
    }

    /**
     * Log warning
     *
     * @param string $action  Current request action
     * @param string $message Message to log
     * @param array  $args    Message args OPTIONAL
     * @param array  $data    Data sent/received OPTIONAL
     *
     * @return void
     */
    protected function logWarning($action, $message, array $args = array(), array $data = array())
    {
        $this->logCommon('Warning', $action, $message, $args, $data);
    }

    /**
     * Log info
     *
     * @param string $action  Current request action
     * @param string $message Message to log
     * @param array  $args    Message args OPTIONAL
     * @param array  $data    Data sent/received OPTIONAL
     *
     * @return void
     */
    protected function logInfo($action, $message, array $args = array(), array $data = array())
    {
        $this->logCommon('Info', $action, $message, $args, $data);
    }

    /**
     * Common logging procedure
     *
     * @param string $method  Method to call
     * @param string $action  Current request action
     * @param string $message Message to log
     * @param array  $args    Message args OPTIONAL
     * @param array  $data    Data sent/received OPTIONAL
     *
     * @return void
     */
    protected function logCommon($method, $action, $message, array $args = array(), array $data = array())
    {
        $message = 'Marketplace [' . $action . ']: ' . lcfirst($message);

        if (!empty($data) && \Includes\Utils\ConfigParser::getOptions(array('marketplace', 'log_data'))) {
            $message .= '; data: ' . PHP_EOL . '{{data}}';
            $args += array('data' => print_r($data, true));
        }

        \XLite\Upgrade\Logger::getInstance()->{'log' . $method}($message, $args, false);
    }

    // }}}
}
