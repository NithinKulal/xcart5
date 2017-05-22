<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller;

use XLite\Core\DependencyInjection\ContainerAwareTrait;
use XLite\View\FormField\Select\ObjectNameInPageTitleOrder;

/**
 * Abstract controller
 */
abstract class AController extends \XLite\Core\Handler
{
    use ContainerAwareTrait;

    /**
     * Controller main params
     */
    const PARAM_TARGET = 'target';
    const PARAM_ACTION = 'action';

    const PARAM_REDIRECT_CODE = 'redirectCode';

    const TRIAL_NOTICE_DISPLAYED = 'trialNoticeDisplayed';

    /**
     * Request param to pass URLs to return
     */
    const RETURN_URL = 'returnURL';

    /**
     * Root category identificator (it is available in all controllers and views)
     *
     * @var integer|null
     */
    protected static $rootCategoryId = null;

    /**
     * "Is logged" flag
     *
     * @var mixed
     */
    protected static $isLogged = null;

    /**
     * Object to keep action status
     *
     * @var \XLite\Model\ActionStatus
     */
    protected $actionStatus;

    /**
     * returnURL
     *
     * @var string
     */
    protected $returnURL;

    /**
     * params
     *
     * @var string
     */
    protected $params = array('target');

    /**
     * Validity flag
     * TODO - check where it's really needed
     *
     * @var boolean
     */
    protected $valid = true;

    /**
     * Hard (main page redirect) redirect in AJAX request
     *
     * @var boolean
     */
    protected $hardRedirect = false;

    /**
     * Internal (into popup ) redirect in AJAX request
     *
     * @var boolean
     */
    protected $internalRedirect = false;

    /**
     * Popup silence close in AJAX request
     *
     * @var boolean
     */
    protected $silenceClose = false;

    /**
     * Pure action flag in AJAX request
     * Set to true if the client does not require any action
     *
     * @var boolean
     */
    protected $pureAction = false;

    /**
     * Suppress output flag
     *
     * @var boolean
     */
    protected $suppressOutput = false;

    /**
     * Current viewer
     *
     * @var \XLite\View\AView
     */
    protected $viewer = null;

    /**
     * Get target by controller class name
     *
     * @return string
     */
    protected static function getTargetByClassName()
    {
        $parts = explode('\\', get_called_class());

        return \Includes\Utils\Converter::convertFromCamelCase(lcfirst(array_pop($parts)));
    }

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        return $classes;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getMainTitle()
    {
        return $this->isForceChangePassword()
            ? static::t('Change password')
            : $this->getTitle();
    }

    /**
     * Checks if the force change password needed
     *
     * @return boolean
     */
    public function isForceChangePassword()
    {
        return $this->isLogged() && \XLite\Core\Session::getInstance()->forceChangePassword;
    }

    /**
     * Check if current user is logged in
     *
     * @return boolean
     */
    public function isLogged()
    {
        if (is_null(static::$isLogged)) {
            static::$isLogged = \XLite\Core\Auth::getInstance()->isLogged();
        }

        return static::$isLogged;
    }

    /**
     * Check if the used is logged admin
     *
     * @return boolean
     */
    public function isLoggedAdmin()
    {
        return $this->isLogged() && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Display JSON
     *
     * @param mixed $data Data
     *
     * @return void
     */
    protected function displayJSON($data)
    {
        $data = json_encode($data);

        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($data));

        print $data;
    }

    // {{{ Pages

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        return array(
            'dragDropCart' => $this->getDragDropCartFlag(),
            'tabletDevice' => $this->isTableDevice(),
        );
    }

    /**
     * Currently the drag and drop cart feature is disabled for the mobile devices
     *
     * @return boolean
     */
    protected function getDragDropCartFlag()
    {
        return \XLite\Core\Request::isDragDropCartFlag();
    }

    /**
     * Defines if the device is a tablet
     *
     * @return boolean
     */
    protected function isTableDevice()
    {
        return \XLite\Core\Request::isTablet();
    }

    /**
     * Get current page
     * FIXME: to revise
     *
     * @return string
     */
    public function getPage()
    {
        $page  = $this->page;
        $pages = $this->getPages();

        return $page && isset($pages[$page]) ? $page : key($pages);
    }

    /**
     * getPages
     *
     * @return array
     */
    public function getPages()
    {
        return array();
    }

    /**
     * Return list of page templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        return array();
    }

    // }}}

    // {{{ Other

    /**
     * Admin area: Popup trial notice is displayed right after login
     * Customer area: Inline trial notice is displayed when trial period expired
     *
     * @return boolean
     */
    public function isTrialNoticeAutoDisplay()
    {
        $showTrialNotice = \XLite\Core\Session::getInstance()->get(\XLite::SHOW_TRIAL_NOTICE);

        \XLite\Core\Session::getInstance()->set(\XLite::SHOW_TRIAL_NOTICE, null);

        $result = \XLite::isAdminZone()
            ? ($showTrialNotice || \XLite\Core\Request::getInstance()->activate_key)
            : \XLite::isTrialPeriodExpired();

        if ($result && \XLite::isAdminZone()) {
            \XLite\Core\Session::getInstance()->set(static::TRIAL_NOTICE_DISPLAYED, true);
        }

        return $result;
    }

    /**
     * Get number of days left before trial period will expire
     *
     * @param boolean $returnDays Flag: return in days
     *
     * @return integer
     */
    public function getTrialPeriodLeft($returnDays = true)
    {
        return \XLite::getTrialPeriodLeft($returnDays);
    }

    /**
     * Return true if block popup should be displayed
     *
     * @return boolean
     */
    public function isKeysNoticeAutoDisplay()
    {
        $result = $this->isRootAdmin()
            && !\XLite\Core\Session::getInstance()->get(\XLite::SHOW_TRIAL_NOTICE)
            && !\XLite\Core\Session::getInstance()->get(static::TRIAL_NOTICE_DISPLAYED)
            && !$this->isDisplayBlockContent()
            && \XLite\Core\Marketplace::getInstance()->hasUnallowedModules()
            && \XLite::isTrialPeriodExpired()
            && !\XLite\Core\Request::getInstance()->activate_key;

        if (\XLite\Core\Session::getInstance()->get(static::TRIAL_NOTICE_DISPLAYED)) {
            \XLite\Core\Session::getInstance()->set(static::TRIAL_NOTICE_DISPLAYED, null);
        }

        return $result;
    }

    /**
     * Return true if block content should be displayed
     *
     * @return boolean
     */
    public function isDisplayBlockContent()
    {
        return $this->isRootAdmin()
            && \XLite::getXCNLicense()
            && !\XLite::isFreeLicense()
            && \XLite\Core\Marketplace::getInstance()->hasInactiveLicenseKey();
    }

    /**
     * Return true if current user - root admin
     *
     * @return boolean
     */
    protected function isRootAdmin()
    {
        return \XLite::isAdminZone()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Get controller parameters
     * TODO - check this method
     * FIXME - backward compatibility
     *
     * @param string $exceptions Parameter keys string OPTIONAL
     *
     * @return array
     */
    public function getAllParams($exceptions = null)
    {
        $result = array();
        $exceptions = isset($exceptions) ? explode(',', $exceptions) : false;
        foreach ($this->get('params') as $name) {
            $value = $this->get($name);
            if (isset($value) && (!$exceptions || in_array($name, $exceptions))) {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * Is redirect needed
     *
     * @return boolean
     */
    public function isRedirectNeeded()
    {
        $isRedirectNeeded = (\XLite\Core\Request::getInstance()->isPost() || $this->getReturnURL()) && !$this->silent;

        if (!$isRedirectNeeded) {
            $host = \XLite::getInstance()->getOptions(
                array(
                    'host_details',
                    $this->isHTTPS() ? 'https_host' : 'http_host'
                )
            );
            if ($host != $_SERVER['HTTP_HOST']) {
                $isRedirectNeeded = true;
                $this->setReturnURL($this->getShopURL($this->getURL(), $this->isHTTPS()));
            }
        }

        return $isRedirectNeeded;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return \XLite\Core\Request::getInstance()->action;
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return \XLite\Core\Converter::getPlainClassName($this);
    }

    /**
     * Get the secure full URL of the page
     * Example: getSecureShopURL('cart.php') = "https://domain/dir/cart.php
     *
     * @param string $url    Relative URL OPTIONAL
     * @param array  $params Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getSecureShopURL($url = '', array $params = array())
    {
        return \XLite\Core\URLManager::getShopURL($url, true, $params, null, false);
    }

    /**
     * Get the full URL of the page
     * Example: getShopURL('cart.php') = "http://domain/dir/cart.php
     *
     * @param string  $url    Relative URL OPTIONAL
     * @param boolean $secure Flag to use HTTPS OPTIONAL
     * @param array   $params Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getShopURL($url = '', $secure = null, array $params = array())
    {
        return \XLite::getInstance()->getShopURL($url, $secure, $params);
    }

    /**
     * Get the URL for storefront with assured accessibility
     *
     * @param boolean $shopStatus Shop status OPTIONAL
     *
     * @return string
     */
    public function getAccessibleShopURL($shopStatus = null)
    {
        if (!is_bool($shopStatus)) {
            $shopStatus = !\XLite\Core\Auth::getInstance()->isClosedStorefront();
        }

        $params = $shopStatus
            ? array()
            : array('shopKey' => \XLite\Core\Auth::getInstance()->getShopKey());

        return $this->getShopURL('', null, $params);
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        if (!isset($this->returnURL)) {
            $this->returnURL = \XLite\Core\Request::getInstance()->{static::RETURN_URL};
        }

        if ($this->returnURL && !\XLite\Core\URLManager::isValidDomain($this->returnURL, false)) {
            \XLite\Logger::getInstance()->log(
                'Untrusted returnURL parameter passed: ' . $this->returnURL
            );
            $this->returnURL = $this->getShopURL();
        }

        return $this->returnURL;
    }

    /**
     * Set return URL
     *
     * @param string $url URL to set
     *
     * @return void
     */
    public function setReturnURL($url)
    {
        $this->returnURL = str_replace('&amp;', '&', $url);
    }

    /**
     * Get current URL with additional params
     *
     * @param array $params Query params to use
     *
     * @return void
     */
    public function setReturnURLParams(array $params)
    {
        $this->setReturnURL($this->buildURL($this->getTarget(), '', $params));
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (!$this->checkLicense()) {
            $this->markAsLicenseRestriction();

        } elseif (!$this->checkAccess()) {
            $this->markAsAccessDenied();

        } elseif (!$this->isVisible()) {
            $this->display404();

        } elseif ($this->needSecure()) {
            $this->redirectToSecure();

        } elseif (!$this->checkLanguage()) {
            $this->redirectToCurrentLanguage();

        } else {
            $this->run();
        }

        if ($this->isRedirectNeeded()) {
            $this->doRedirect();

        } elseif ($this->isAJAX()) {
            \XLite\Core\Event::getInstance()->display();
            \XLite\Core\Event::getInstance()->clear();
        }
    }

    /**
     * isBlockContentAllowed
     *
     * @return boolean
     */
    public function isBlockContentAllowed()
    {
        return false;
    }

    /**
     * Alias: check for an AJAX request
     *
     * @return boolean
     */
    public function isAJAX()
    {
        return \XLite\Core\Request::getInstance()->isAJAX();
    }

    /**
     * Return Viewer object
     *
     * @return \XLite\View\Controller
     */
    public function getViewer()
    {
        if (!isset($this->viewer)) {
            $class = $this->getViewerClass();
            $this->viewer = new $class($this->getViewerParams(), $this->getViewerTemplate());
        }

        return $this->viewer;
    }

    /**
     * Send headers
     *
     * @param  array $additional Additional headers OPTIONAL
     *
     * @return void
     */
    public static function sendHeaders($additional = array())
    {
        // send no-cache headers
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: text/html; charset=utf-8');

        if (\XLite::isAdminZone()) {
            header('X-Robots-Tag: noindex, nofollow');
        }

        foreach (\XLite\Core\URLManager::getAllowedDomains() as $domain) {
            header('Access-Control-Allow-Origin: ' . $domain, false);
        }
        $option = \XLite::getInstance()->getOptions(array('other', 'x_frame_options'));
        if (isset($option) && 'disabled' !== $option) {
            header('X-Frame-Options:' . $option);
        }

        foreach ($additional as $header => $value) {
            header($header . ': ' . $value);
        }
    }

    /**
     * Returns additional HTTP headers to be sent with response
     *
     * @return array
     */
    protected function getAdditionalHeaders()
    {
        return array();
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        if (!$this->suppressOutput) {
            $viewer = $this->getViewer();
            if (!\XLite\Core\Request::getInstance()->isCLI()) {
                $additional = $this->getAdditionalHeaders();
                static::sendHeaders($additional);
            }
            $viewer->init();

            if (!$this->isAJAX()) {
                $viewer->display();

            } elseif ($this->checkAccess()
                && (
                    !$viewer->getAllowedTargets()
                    || in_array($this->getTarget(), $viewer->getAllowedTargets())
                )
            ) {
                $this->printAJAXOutput($viewer);
            }
        }
    }

    /**
     * This function called after template output
     * FIXME - may be there is a better way to handle this?
     *
     * @return void
     */
    public function postprocess()
    {
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite::TARGET_404 == \XLite\Core\Request::getInstance()->target
            ? static::t('Page not found')
            : null;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return true;
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getPageTitle()
    {
        $config = \XLite\Core\Config::getInstance();
        $title = [];

        if ($config->CleanURL->company_name) {
            $title[] = $this->getTitleCompanyNamePart();
        }

        if ($config->CleanURL->parent_category_path) {
            $title[] = $this->getTitleParentPart();
        }
        
        $title[] = $this->getTitleObjectPart();

        if ($config->CleanURL->object_name_in_page_title_order == true) {
            $title = array_reverse($title);
        }

        return implode(static::t('title-delimiter'), array_filter($title));
    }

    /**
     * Return the page title company name part (for the <title> tag)
     *
     * @return string
     */
    public function getTitleCompanyNamePart()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Return the page title parent category part (for the <title> tag)
     *
     * @return string
     */
    public function getTitleParentPart()
    {
        return '';
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        return $this->getMainTitle();
    }

    /**
     * Check if an error occurred
     *
     * @return boolean
     */
    public function isActionError()
    {
        return isset($this->actionStatus) && $this->actionStatus->isError();
    }

    /**
     * setActionStatus
     *
     * @param integer $status  Error/success
     * @param string  $message Status info OPTIONAL
     * @param integer $code    Status code OPTIONAL
     *
     * @return void
     */
    public function setActionStatus($status, $message = '', $code = 0)
    {
        $this->actionStatus = new \XLite\Model\ActionStatus($status, $message, $code);
    }

    /**
     * setActionError
     *
     * @param string  $message Status info  OPTIONAL
     * @param integer $code    Status code OPTIONAL
     *
     * @return void
     */
    public function setActionError($message = '', $code = 0)
    {
        $this->setActionStatus(\XLite\Model\ActionStatus::STATUS_ERROR, $message, $code);
    }

    /**
     * setActionSuccess
     *
     * @param string  $message Status info OPTIONAL
     * @param integer $code    Status code OPTIONAL
     *
     * @return void
     */
    public function setActionSuccess($message = '', $code = 0)
    {
        $this->setActionStatus(\XLite\Model\ActionStatus::STATUS_SUCCESS, $message, $code);
    }

    /**
     * Check if handler is valid
     * TODO - check where it's really needed
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Check - is secure connection or not
     *
     * @return boolean
     */
    public function isHTTPS()
    {
        return \XLite\Core\Request::getInstance()->isHTTPS();
    }

    /**
     * Get access level
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    /**
     * getProperties
     *
     * @return array
     */
    public function getProperties()
    {
        $result = array();

        foreach ($_REQUEST as $name => $value) {
            $result[$name] = $this->get($name);
        }

        return $result;
    }

    /**
     * getURL
     *
     * @param array $params URL parameters OPTIONAL
     *
     * @return string
     */
    public function getURL(array $params = array())
    {
        $params = array_merge($this->getAllParams(), $params);
        $target = isset($params['target']) ? $params['target'] : '';
        unset($params['target']);

        return $this->buildURL($target, '', $params);
    }

    /**
     * Get referer URL
     *
     * @return string
     */
    public function getReferrerURL()
    {
        if (\XLite\Core\Request::getInstance()->referer) {
            $url = \XLite\Core\Request::getInstance()->referer;
        } elseif (!empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            $url = $this->getURL();
        }

        return $url;
    }

    /**
     * getPageTemplate
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getPageTemplates(), $this->getPage());
    }

    /**
     * Return the array(pages) for tabber
     * FIXME - move to the Controller/Admin/Abstract.php:
     * tabber is not used in customer area
     *
     * @return array
     */
    public function getTabPages()
    {
        return $this->getPages();
    }

    /**
     * getUploadedFile
     *
     * @return string
     */
    public function getUploadedFile()
    {
        $file = null;

        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $file = $_FILES['userfile']['tmp_name'];

        } elseif (is_readable($_POST['localfile'])) {
            $file = $_POST['localfile'];

        } else {
            $this->doDie('FAILED: data file unspecified');
        }

        // security check
        $name = $_FILES['userfile']['name'];

        if (strstr($name, '../') || strstr($name, '..\\')) {
            $this->doDie('ACCESS DENIED');
        }

        return $file;
    }

    /**
     * checkUploadedFile
     *
     * @return boolean
     */
    public function checkUploadedFile()
    {
        $check = true;

        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $file = $_FILES['userfile']['tmp_name'];

        } elseif (is_readable($_POST['localfile'])) {
            $file = $_POST['localfile'];

        } else {
            $check = false;
        }

        if ($check) {
            // security check
            $name = $_FILES['userfile']['name'];

            if (strstr($name, '../') || strstr($name, '..\\')) {
                $check = false;
            }
        }

        return $check;
    }

    /**
     * Get controller charset
     *
     * @return string
     */
    public function getCharset()
    {
        return 'utf-8';
    }

    /**
     * isSecure
     *
     * @return boolean
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * Return the reserved ID of root category
     *
     * @return integer
     */
    public function getRootCategoryId()
    {
        if (!isset(static::$rootCategoryId)) {
            static::$rootCategoryId = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getRootCategoryId();
        }

        return static::$rootCategoryId;
    }

    /**
     * Return current category Id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->category_id;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return null;
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->getDefaultKeywords();
    }

    /**
     * Get default meta keywords
     *
     * @return string
     */
    public function getDefaultKeywords()
    {
        return static::t('default-meta-keywords') != 'default-meta-keywords' ? static::t('default-meta-keywords') : '';
    }

    /**
     * Return model form object
     *
     * @param array $params Form constructor params OPTIONAL
     *
     * @return \XLite\View\Model\AModel|void
     */
    public function getModelForm(array $params = array())
    {
        $result = null;
        $class  = $this->getModelFormClass();

        if (isset($class)) {
            $result = \XLite\Model\CachingFactory::getObject(
                __METHOD__ . $class . (empty($params) ? '' : md5(serialize($params))),
                $class,
                $params
            );
        }

        return $result;
    }

    /**
     * Check - current request is AJAX background request for page center or not
     *
     * @return boolean
     */
    public function isAJAXCenterRequest()
    {
        return $this->isAJAX() && \XLite\Core\Request::getInstance()->only_center;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return \XLite\Core\Auth::getInstance()->isAuthorized($this);
    }

    /**
     * Check all controller access controls
     *
     * @return bool
     */
    public function checkAccessControls()
    {
        $ace = $this->getAccessControlEntities();
        $acz = $this->getAccessControlZones();
        return (empty($ace) || $this->checkAccessByACE()) && (empty($acz) || $this->checkAccessByACZ()) && $this->checkAccessControlsNotEmpty();
    }

    /**
     * Check if at least one of access controls not empty
     *
     * @return bool
     */
    public function checkAccessControlsNotEmpty()
    {
        $ace = $this->getAccessControlEntities();
        $acz = $this->getAccessControlZones();
        return !empty($ace) || !empty($acz);
    }

    /**
     * Return Access control entities for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlEntities()
    {
        return [];
    }

    /**
     * Check access by Access Control Entities
     * 
     * @return boolean
     */
    protected function checkAccessByACE()
    {
        foreach ($this->getAccessControlEntities() as $accessControlEntity) {
            if (is_object($accessControlEntity) && \XLite\Core\Auth::getInstance()->checkACEAccess($accessControlEntity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return Access control zones for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlZones()
    {
        return [];
    }

    /**
     * Check access by Access Control Zones
     *
     * @return boolean
     */
    protected function checkAccessByACZ()
    {
        $zones = $this->getAccessControlZones();
        foreach ($zones as $accessControlZone) {
            if (\XLite\Core\Auth::getInstance()->checkACZAccess($accessControlZone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current page is accessible for current x-cart license
     *
     * @return boolean
     */
    protected function checkLicense()
    {
        return true;
    }

    /**
     * Check if form id is valid or not
     *
     * @return boolean
     */
    protected function isFormIdValid()
    {
        \XLite\Core\Database::getRepo('XLite\Model\FormId')->removeExpired();
        $request = \XLite\Core\Request::getInstance();
        $result = true;

        if ($this->isActionNeedFormId()) {
            if (!isset($request->{\XLite::FORM_ID}) || !$request->{\XLite::FORM_ID}) {
                $result = false;

            } elseif (\XLite::getInstance()->getFormIdStrategy() === 'per-session') {
                $formId = \XLite\Core\Session::getInstance()->getSessionFormId();

                $result = $formId && $formId->getFormId() === $request->{\XLite::FORM_ID};
                if (!$result && $formId) {
                    \XLite\Core\Session::getInstance()->resetFormId();
                }

            } else {
                $form = null;

                foreach (\XLite\Core\Session::getInstance()->getModel()->getFormIds() as $formId) {
                    if ($formId->getFormId() === $request->{\XLite::FORM_ID}) {
                        $form = $formId;
                    }
                }

                $result = isset($form);

                if ($form) {
                    \XLite\Core\Database::getRepo('XLite\Model\FormId')->delete($form);
                    $newFormId = \XLite\Core\Session::getInstance()->createFormId(true);

                    if ($this->isAJAX()) {
                        $value = json_encode([
                            'name'  => \XLite::FORM_ID,
                            'value' => $newFormId,
                        ]);
                        header('update-csrf: ' . $value);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Restore form id
     *
     * @return void
     */
    protected function restoreFormId()
    {
        if ($this->isActionNeedFormId()) {
            \XLite\Core\Session::getInstance()->restoreFormId();
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array();
    }

    /**
     * Check if the form ID validation is needed
     *
     * @return boolean
     */
    protected function isActionNeedFormId()
    {
        return $this->getAction() && !in_array($this->getAction(), static::defineFreeFormIdActions());
    }

    /**
     * Check form id
     *
     * @return boolean
     */
    public function checkFormId()
    {
        $result = !static::needFormId()
            || (
                static::needFormId()
                && (
                    ($this->getTarget() && $this->isIgnoredTarget())
                    || ($this->isFormIdValid())
                )
            );

        if (!$result) {
            \XLite\Core\TopMessage::addWarning('The form could not be identified as a form generated by X-Cart');

            \XLite\Logger::getInstance()->log(
                'Form ID checking failure (target: ' . $this->getTarget() . ', action: ' . $this->getAction() . ')',
                LOG_WARNING
            );
        }

        return $result;
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false;
    }

    /**
     * Return true if promo block with specified ID is visible
     * (used in promo.twig)
     *
     * @param string $blockId Promo block unique ID
     *
     * @return boolean
     */
    public function isPromoBlockVisible($blockId)
    {
        $cookie = \XLite\Core\Request::getInstance()->getCookieData();

        return empty($cookie[$blockId . 'PromoBlock']);
    }

    /**
     * Check - current target and action is ignored (form id validation is disabled) or not
     *
     * @return boolean
     */
    protected function isIgnoredTarget()
    {
        $result = false;

        if ($this->isRuleExists($this->defineIgnoredTargets())) {
            $result = true;

        } else {
            $request = \XLite\Core\Request::getInstance();

            if ($this->isRuleExists($this->defineSpecialIgnoredTargets())
                && isset($request->login)
                && isset($request->password)
                && \XLite\Core\Auth::getInstance()->isLogged()
                && \XLite\Core\Auth::getInstance()->getProfile()->getLogin() == $request->login
            ) {
                $postLogin = $request->login;
                $postPassword = $request->password;

                if (!empty($postLogin) && !empty($postPassword)) {
                    $postPassword = \XLite\Core\Auth::getInstance()->encryptPassword($postPassword);
                    $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                        ->findByLoginPassword($postLogin, $postPassword, 0);

                    if (isset($profile)) {
                        $profile->detach();
                        if ($profile->isEnabled() && \XLite\Core\Auth::getInstance()->isAdmin($profile)) {
                            $result = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Define common ignored targets
     *
     * @return array
     */
    protected function defineIgnoredTargets()
    {
        return array(
            'callback'       => '*',
            'payment_method' => 'callback',
        );
    }

    /**
     * Define special ignored targets
     *
     * @return array
     */
    protected function defineSpecialIgnoredTargets()
    {
        return array(
            'files'          => array('tar', 'tar_skins', 'untar_skins'),
        );
    }

    /**
     * Check - rule is exists with current target and action or not
     *
     * @param array $rules Rules
     *
     * @return boolean
     */
    protected function isRuleExists(array $rules)
    {
        $request = \XLite\Core\Request::getInstance();

        return isset($rules[$request->target])
            && (
                '*' == $rules[$request->target]
                || (
                    is_array($rules[$request->target])
                    && (isset($request->action) && in_array($request->action, $rules[$request->target]))
                )
            );
    }

    /**
     * Return default redirect code
     *
     * @return integer
     */
    protected function getDefaultRedirectCode()
    {
        return $this->isAJAX() ? 200 : 302;
    }

    /**
     * Default URL to redirect
     *
     * @return string
     */
    protected function getDefaultReturnURL()
    {
        return null;
    }

    /**
     * Perform redirect
     *
     * @param string $url Redirect URL OPTIONAL
     *
     * @return void
     */
    protected function redirect($url = null)
    {
        $location = $this->getReturnURL();

        if (!isset($location)) {
            $location = isset($url) ? $url : $this->getURL();
        }

        // filter FORM ID from redirect url
        // FIXME - check if it's really needed
        $action = $this->get('action');

        if (empty($action)) {
            $location = $this->filterXliteFormID($location);
        }

        if ($this->isAJAX()) {
            \XLite\Core\Event::getInstance()->display();
            \XLite\Core\Event::getInstance()->clear();
        }

        $location = $this->addCleanupCacheMark($location);

        if (LC_USE_CLEAN_URLS
            && \XLite\Core\Router::getInstance()->isUseLanguageUrls()
            && !\XLite::isAdminZone()
        ) {
            $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']);
            if ($webDir && strpos($location, $webDir) !== 0 && strpos($location, 'http') !== 0) {
                $location = $webDir . '/' . $location;
            }
        }

        \XLite\Core\Operator::redirect(
            $location,
            $this->getRedirectMode(),
            $this->getParam(static::PARAM_REDIRECT_CODE)
        );
    }

    /**
     * Get redirect mode - force redirect or not
     *
     * @return boolean
     */
    protected function getRedirectMode()
    {
        return false;
    }

    /**
     * Select template to use
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        return 'main.twig';
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
            static::PARAM_REDIRECT_CODE => new \XLite\Model\WidgetParam\TypeInt('Redirect code', $this->getDefaultRedirectCode()),
        );
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|null
     */
    protected function getModelFormClass()
    {
        return null;
    }

    /**
     * Perform some actions before redirect
     *
     * @param string $action Performed action
     *
     * @return void
     */
    protected function actionPostprocess($action)
    {
        $method = __FUNCTION__ . \Includes\Utils\Converter::convertToPascalCase($action);

        if (method_exists($this, $method)) {
            // Call action method
            $this->$method();
        }
    }

    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        $action = $this->getAction();
        $method = 'doAction' . \Includes\Utils\Converter::convertToPascalCase($action);

        if (method_exists($this, $method)) {
            // Call method doAction<action-name-in-camel-case>
            $this->$method();

        } else {
            \XLite\Logger::getInstance()->log(
                'Handler for the action "' . $action . '" is not defined for the "' . get_class($this) . '" class'
            );
        }

        $this->actionPostprocess($action);
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        if ($this->getAction() && $this->isValid()) {
            $this->callAction();

        } else {
            $this->doNoAction();
        }

        if (!$this->isValid()) {
            $this->restoreFormId();
        }
    }

    /**
     * Do redirect
     *
     * @return void
     */
    protected function doRedirect()
    {
        if ($this->isAJAX()) {
            $this->translateTopMessagesToHTTPHeaders();
            $this->assignAJAXResponseStatus();
        }

        $this->redirect();
    }

    /**
     * Translate top messages to HTTP headers (AJAX)
     *
     * @return void
     */
    protected function translateTopMessagesToHTTPHeaders()
    {
        foreach (\XLite\Core\TopMessage::getInstance()->getAJAXMessages() as $message) {
            $encodedMessage = json_encode(
                array(
                    'type'    => $message[\XLite\Core\TopMessage::FIELD_TYPE],
                    'message' => $message[\XLite\Core\TopMessage::FIELD_TEXT],
                )
            );
            header('event-message: ' . $encodedMessage);
        }

        \XLite\Core\TopMessage::getInstance()->clearAJAX();
    }

    /**
     * Assign AJAX response status to HTTP header(s)
     *
     * @return void
     */
    protected function assignAJAXResponseStatus()
    {
        if (!$this->isValid()) {
            // AXAX-based - cancel redirect
            header('ajax-response-status: 0');
            header('not-valid: 1');

        } elseif ($this->hardRedirect) {
            // Main page redirect
            header('ajax-response-status: 278');

        } elseif ($this->internalRedirect) {
            // Popup internal redirect
            header('ajax-response-status: 279');

        } elseif ($this->silenceClose) {
            // Popup silence close
            header('ajax-response-status: 277');

        } elseif ($this->pureAction) {
            // Pure action
            header('ajax-response-status: 276');

        } else {
            header('ajax-response-status: 270');
        }
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Display 404 page
     *
     * @return void
     */
    protected function display404()
    {
        \XLite\Core\Request::getInstance()->target = \XLite::TARGET_404;
        \XLite\Core\Request::getInstance()->action = '';
        $this->headerStatus(404);
    }

    /**
     * Set internal popup redirect
     *
     * @param boolean $flag Internal redirect status OPTIONAL
     *
     * @return void
     */
    protected function setInternalRedirect($flag = true)
    {
        if ($this->isAJAX()) {
            $this->internalRedirect = (bool) $flag;
        }
    }

    /**
     * Set hard (main page redirect) redirect
     *
     * @param boolean $flag Internal redirect status OPTIONAL
     *
     * @return void
     */
    protected function setHardRedirect($flag = true)
    {
        if ($this->isAJAX()) {
            $this->hardRedirect = (bool) $flag;
        }
    }

    /**
     * Set silence close popup
     *
     * @param boolean $flag Silence close status OPTIONAL
     *
     * @return void
     */
    protected function setSilenceClose($flag = true)
    {
        if ($this->isAJAX()) {
            $this->silenceClose = (bool) $flag;
        }
    }

    /**
     * Set pure action flag
     *
     * @param boolean $flag Flag OPTIONAL
     *
     * @return void
     */
    protected function setPureAction($flag = false)
    {
        if ($this->isAJAX()) {
            $this->pureAction = (bool) $flag;
        }
    }

    /**
     * Set suppress output flag
     *
     * @param boolean $suppressOutput Flag
     *
     * @return void
     */
    protected function setSuppressOutput($suppressOutput)
    {
        $this->suppressOutput = (bool)$suppressOutput;
    }

    /**
     * Check if current viewer is for an AJAX request
     *
     * @return boolean
     */
    protected function isAJAXViewer()
    {
        return $this->isAJAX() && \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Return class of current viewer
     *
     * @return string
     */
    protected function getViewerClass()
    {
        return $this->isAJAXViewer()
            ? \XLite\Core\Request::getInstance()->widget
            : '\XLite\View\Controller';
    }

    /**
     * Print AJAX request output
     *
     * @param mixed $viewer Viewer to display in AJAX
     *
     * @return void
     */
    protected function printAJAXOutput($viewer)
    {
        $content = $viewer->getContent();

        $class = 'ajax-container-loadable'
            . ' ctrl-' . implode('-', \XLite\Core\Operator::getInstance()->getClassNameAsKeys(get_called_class()))
            . ' widget-' . implode('-', \XLite\Core\Operator::getInstance()->getClassNameAsKeys($viewer));

        echo (
            '<div'
            . ' class="' . $class . '"'
            . ' title="' . func_htmlspecialchars(static::t($this->getTitle())) . '"'
            . ' ' . $this->printAJAXAttributes() . ' >' . PHP_EOL
            . $this->printAJAXResources() . PHP_EOL
            . $content
            . '</div>'
        );
    }

    /**
     * Returns AJAX output attributes of container box.
     * @return string
     */
    protected function printAJAXAttributes()
    {
        return '';
    }

    /**
     * Print AJAX request output
     *
     * @return void
     */
    protected function printAJAXResources()
    {
        $resources = \XLite\Core\Layout::getInstance()->getRegisteredPreparedResources();
        $widget = json_encode(
            array(
                'widget' => $this->getViewerClass(),
            )
        );

        $resContainer = array(
            'widget' => $this->getViewerClass(),
        );

        $js = $this->prepareResourcesList($resources[\XLite\View\AView::RESOURCE_JS]);
        $css = $this->prepareResourcesList($resources[\XLite\View\AView::RESOURCE_CSS]);

        if ($css || $js) {
            $resContainer = array_merge(
                $resContainer,
                array(
                    'css' => $css,
                    'js' => $js,
                )
            );
        }

        $resJson = json_encode($resContainer);

        $code = <<<RES
<script type='application/json' data-resource>
    $resJson
</script>
RES;

        return $code;
    }

    /**
     * Print AJAX request output
     *
     * @param array $list Resources list
     *
     * @return array
     */
    protected function prepareResourcesList($list)
    {
        return array_map(
            function ($item) {
                return $item['url'];
            },
            array_values($list)
        );
    }

    /**
     * Mark controller run thread as access denied
     *
     * @return void
     */
    protected function markAsAccessDenied()
    {
        $this->params = array('target');
        $this->set('target', 'access_denied');
        \XLite\Core\Request::getInstance()->target = 'access_denied';
        $this->headerStatus(403);
    }

    /**
     * Mark controller run thread as license restricted
     *
     * @return void
     */
    protected function markAsLicenseRestriction()
    {
        $this->params = array('target');
        $this->set('target', 'license_restriction');
        \XLite\Core\Request::getInstance()->target = 'license_restriction';
        $this->headerStatus(403);
    }

    /**
     * Header status
     *
     * @param integer $code Code
     *
     * @return void
     */
    protected function headerStatus($code)
    {
        switch ($code) {
            case 400:
                header('HTTP/1.0 400 Bad Request', true, 400);
                header('Status: 400 Bad Request');
                break;

            case 403:
                header('HTTP/1.0 403 Forbidden', true, 403);
                header('Status: 403 Forbidden');
                header('X-Robots-Tag: noindex, nofollow');
                break;

            case 404:
                header('HTTP/1.0 404 Not Found', true, 404);
                header('Status: 404 Not Found');
                header('X-Robots-Tag: noindex, nofollow');
                break;

            case 500:
                header('HTTP/1.0 500 Internal Server Error', true, 500);
                header('Status: 500 Internal Server Error');
                break;

            default:
        }
    }

    /**
     * startDownload
     *
     * @param string $filename    File name
     * @param string $contentType Content type OPTIONAL
     *
     * @return void
     */
    protected function startDownload($filename, $contentType = 'application/force-download')
    {
        @set_time_limit(0);
        header('Content-type: ' . $contentType);
        header('Content-disposition: attachment; filename=' . $filename);
    }

    /**
     * startImage
     *
     * @return void
     */
    protected function startImage()
    {
        header('Content-type: image/gif');
        $this->set('silent', true);
    }

    /**
     * startDump
     *
     * @return void
     */
    protected function startDump()
    {
        @set_time_limit(0);

        $this->set('silent', true);

        if (!isset(\XLite\Core\Request::getInstance()->mode) || 'cp' != \XLite\Core\Request::getInstance()->mode) {
            func_refresh_start();
            $this->dumpStarted = true;
        }
    }

    /**
     * Filter XLite form ID
     *
     * @param string $url URL
     *
     * @return string
     */
    protected function filterXliteFormID($url)
    {
        if (preg_match('/(\?|&)(' . \XLite::FORM_ID . '=[a-zA-Z0-9]+)(&.+)?$/', $url, $matches)) {
            if ($matches[1] == '&') {
                $param = $matches[1] . $matches[2];

            } elseif (empty($matches[3])) {
                $param = $matches[1] . $matches[2];

            } else {
                $param = $matches[2] . '&';
            }

            $url = str_replace($param, '', $url);
        }

        return $url;
    }

    /**
     * Add cleanup cache mark
     *
     * @param string $location URL
     *
     * @return string
     */
    protected function addCleanupCacheMark($location)
    {
        if (\XLite::getCleanUpCacheFlag()) {
            $location .= (strpos($location, '?') === false ? '?' : '&')
                . \Includes\Decorator\Utils\CacheManager::KEY_NAME . '='
                . \Includes\Decorator\Utils\CacheManager::getKey(true);
        }

        return $location;
    }

    /**
     * Get viewer parameters
     *
     * @return array
     */
    protected function getViewerParams()
    {
        $params = array();

        // FIXME: is it really needed?
        foreach (array(static::PARAM_SILENT, static::PARAM_DUMP_STARTED) as $name) {
            $params[$name] = $this->get($name);
        }

        if ($this->isAJAXViewer()) {
            $data = \XLite\Core\Request::getInstance()->getData();

            unset($data['target']);
            unset($data['action']);

            $params += $data;
        }

        return $params;
    }

    /**
     * Get current logged user profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Check - need use secure protocol or not
     *
     * @return boolean
     */
    public function needSecure()
    {
        return $this->isSecure()
            && !$this->isHTTPS()
            && !\XLite\Core\Request::getInstance()->isCLI()
            && \XLite\Core\Request::getInstance()->isGet();
    }

    /**
     * Redirect to secure protocol
     *
     * @return void
     */
    protected function redirectToSecure()
    {
        $this->setHardRedirect();
        $this->assignAJAXResponseStatus();

        $this->redirect($this->getShopURL($this->getURL(), true));
    }

    /**
     * Check - need to redirect
     *
     * @return boolean
     */
    public function checkLanguage()
    {
        if (
            !LC_USE_CLEAN_URLS
            || !\XLite\Core\Request::getInstance()->isGet()
            || \XLite::isAdminZone()
            || !\XLite\Core\Router::getInstance()->isUseLanguageUrls()
        ) {
            return true;
        }

        $language = \XLite\Core\Session::getInstance()->getLanguage();

        return !(!$language->getDefaultAuth() && \XLite\Core\Request::getInstance()->getLanguageCode() != $language->getCode());
    }

    /**
     * Redirect to current language protocol
     *
     * @return void
     */
    protected function redirectToCurrentLanguage()
    {
        $this->setHardRedirect();
        $this->assignAJAXResponseStatus();

        $this->redirect($this->getShopURL($this->getURL()));
    }

    // }}}

    // {{{ Language-related routines

    /**
     * Get current language code
     *
     * @return string
     */
    public function getCurrentLanguage()
    {
        return \XLite\Core\Session::getInstance()->getLanguage()->getCode();
    }

    /**
     * Change current language
     *
     * @return void
     */
        protected function doActionChangeLanguage()
    {
        $code = strval(\XLite\Core\Request::getInstance()->language);

        $referrerUrl = $this->getReferrerURL();

        if (!empty($code)) {
            $language = \XLite\Core\Database::getRepo('\XLite\Model\Language')->findOneByCode($code);

            if (isset($language) && $language->getEnabled()) {
                $pattern = '#^[/]*(' . \XLite\Core\Session::getInstance()->getCurrentLanguage() . ')(?:/|$)#i';

                \XLite\Core\Session::getInstance()->setLanguage($language->getCode());
                if (\XLite\Core\Auth::getInstance()->isLogged()) {
                    \XLite\Core\Auth::getInstance()->getProfile()->setLanguage($language->getCode());
                    \XLite\Core\Database::getEM()->flush();
                }

                if (\XLite\Core\Router::getInstance()->isUseLanguageUrls()) {
                    $subReferrerUrl = substr($referrerUrl, strlen(\Includes\Utils\URLManager::getCurrentShopURL()));

                    if (preg_match($pattern, $subReferrerUrl, $matches)) {
                        $referrerUrl = substr_replace(
                            $referrerUrl,
                            $language->getDefaultAuth() ? '' : $language->getCode(),
                            strlen(\Includes\Utils\URLManager::getCurrentShopURL()) + $matches[0],
                            min($language->getDefaultAuth() ? 3 : 2, strlen($subReferrerUrl))
                        );
                    }
                }
            }
        }

        $this->setReturnURL($referrerUrl);
    }

    // }}}
}
