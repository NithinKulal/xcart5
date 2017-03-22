<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Settings
 * todo: FULL REFACTOR!!!
 */
class Settings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Clean URL article url
     */
    const CLEAN_URL_ARTICLE_URL = 'http://kb.x-cart.com/en/general_setup/setting_up_seo-friendly_urls.html';

    /**
     * Installation directory article url
     */
    const INSTALLATION_DIRECTORY_ARTICLE_URL = 'http://kb.x-cart.com/en/general_setup/moving_x-cart_to_another_location.html';

    /**
     * Values to use for $page identification
     */
    const GENERAL_PAGE      = 'General';
    const COMPANY_PAGE      = 'Company';
    const EMAIL_PAGE        = 'Email';
    const ENVIRONMENT_PAGE  = 'Environment';
    const PERFORMANCE_PAGE  = 'Performance';
    const UNITS_PAGE        = 'Units';
    const LAYOUT_PAGE       = 'Layout';
    const CLEAN_URL         = 'CleanURL';

    /**
     * Params
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Page
     *
     * @var string
     */
    public $page = self::GENERAL_PAGE;

    /**
     * _waiting_list
     * @todo: rename
     *
     * @var mixed
     */
    public $_waiting_list;

    /**
     * Curl response temp variable
     *
     * @var mixed
     */
    private $curlResponse;

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $list = $this->getPages();
        if (isset($list[$this->page])) {
            $classes[] = 'settings-'
                . str_replace('_', '-', \XLite\Core\Converter::convertFromCamelCase(preg_replace('/\W/', '', $list[$this->page])));
        }

        return $classes;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $list = $this->getPages();

        return isset($list[$this->page])
            ? $list[$this->page]
            : '';
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        $list = $this->getPages();

        /**
         * Settings controller is available directly if the $page request variable is provided
         * if the $page is omitted, the controller must be the subclass of Settings main one.
         *
         * The inner $page variable must be in the getPages() array
         */
        return parent::checkAccess()
            && isset($list[$this->page])
            && (
                ($this instanceof \XLite\Controller\Admin\Settings && isset(\XLite\Core\Request::getInstance()->page))
                || is_subclass_of($this, '\XLite\Controller\Admin\Settings')
            );
    }

    // {{{ Pages

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::GENERAL_PAGE]     = static::t('Cart & checkout');
        $list[static::COMPANY_PAGE]     = static::t('Store info');
        $list[static::EMAIL_PAGE]       = static::t('Email settings');
        $list[static::ENVIRONMENT_PAGE] = static::t('Environment');
        $list[static::CLEAN_URL]        = static::t('SEO settings');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        foreach ($this->getPages() as $name => $title) {
            $list[$name] = 'settings/base.twig';
        }

        $list[static::ENVIRONMENT_PAGE] = 'settings/summary/body.twig';
        $list[static::CLEAN_URL] = 'settings/clean_url/tabs.twig';

        return $list;
    }

    /**
     * @return array
     */
    public function getCleanUrlCommentedData()
    {
        $result = [];

        if (\XLite\Core\Request::getInstance()->page == 'CleanURL') {
            $result = [
                'companyName'               => \XLite\Core\Config::getInstance()->Company->company_name,
                'companyNameLabel'          => static::t('Company name'),
                'delimiter'                 => " > ",
                'productTitle'              => static::t('Product'),
                'categoryTitle'             => static::t('Category'),
                'staticTitle'               => static::t('Page'),
                'categoryNameLabel'         => static::t('Category name'),
                'parentCategoryNameLabel'   => static::t('Parent category name'),
                'productNameLabel'          => static::t('Product name'),
                'staticPageNameLabel'       => static::t('Page name'),
            ];
        }

        return $result;
    }

    // }}}

    // {{{ Other

    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible($this->page);
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    // }}}

    // {{{ Additional methods

    /**
     * Defines if the clean URL is enabled in the store
     *
     * @return boolean
     */
    public function isCleanURLEnabled()
    {
        return LC_USE_CLEAN_URLS;
    }

    /**
     * Defines if the clean urls can be enabled on the current server environment
     *
     * @return boolean
     */
    public function canEnableCleanURL()
    {
        $urlToCheck = \XLite::getInstance()->getShopURL() . \XLite::CLEAN_URL_CHECK_QUERY;
        $request = new \XLite\Core\HTTP\Request($urlToCheck);
        $request->setAdditionalOption(\CURLOPT_SSL_VERIFYPEER, false);
        $request->setAdditionalOption(\CURLOPT_SSL_VERIFYHOST, false);
        $this->curlResponse = $request->sendRequest();

        return !$this->isCleanURLEnabled()
            && $this->curlResponse
            && in_array($this->curlResponse->code, array(200, 301, 302));
    }

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getCleanURLArticleURL()
    {
        return static::CLEAN_URL_ARTICLE_URL;
    }

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getInstallationDirectoryHelpLink()
    {
        return static::INSTALLATION_DIRECTORY_ARTICLE_URL;
    }
    /**
     * Check for the GDLib extension
     *
     * @return boolean
     */
    public function isGDLibLoaded()
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }

    /**
     * isOpenBasedirRestriction
     *
     * @return boolean
     */
    public function isOpenBasedirRestriction()
    {
        $res = (string) @ini_get('open_basedir');

        return ('' !== $res);
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getTranslationDriver()
    {
        return \XLite\Core\Translation::getInstance()->getDriver()->getName();
    }

    /**
     * Returns value by request
     *
     * @param string $name Type of value
     *
     * @return string
     */
    public function get($name)
    {
        switch($name) {

            case 'phpversion':
                $return = PHP_VERSION;
                break;

            case 'os_type':
                list($osType) = explode(' ', PHP_OS);
                $return = $osType;
                break;

            case 'mysql_server':
                $return = \Includes\Utils\Database::getDbVersion();
                break;

            case 'innodb_support':
                $return = \Includes\Utils\Database::isInnoDBSupported();
                break;

            case 'root_folder':
                $return = getcwd();
                break;

            case 'web_server':
                $return = isset($_SERVER['SERVER_SOFTWARE'])
                    ? $_SERVER['SERVER_SOFTWARE']
                    : '';
                break;

            case 'xml_parser':
                $return = $this->getXMLParserValue();
                break;

            case 'gdlib':
                $return = $this->getGdlibValue();
                break;

            case 'core_version':
                $return = \XLite::getInstance()->getVersion();
                break;

            case 'libcurl':
                $return = $this->getLibcurlValue();
                break;

            case 'license_keys':
                $return = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll();
                break;

            case 'check_files':
                $return = $this->getCheckFilesValue();
                break;

            case 'check_dirs':
                $return = $this->getCheckDirsValue();
                break;

            default:
                $return = parent::get($name);
        }

        return $return;
    }

    /**
     * Get XML parser value
     *
     * @return string
     */
    public function getXMLParserValue()
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpInfo = ob_get_contents();
        ob_end_clean();

        $return = null;
        if (preg_match('/EXPAT.+>([\.\d]+)/mi', $phpInfo, $m)) {
            $return = $m[1];
        } else {
            $return = function_exists('xml_parser_create') ? 'found' : '';
        }

        return $return;
    }

    /**
     * Get Gdlib value
     *
     * @return string
     */
    public function getGdlibValue()
    {
        $return = null;

        if (!$this->is('GDLibLoaded')) {
            $return = '';

        } else {
            ob_start();

            phpinfo(INFO_MODULES);

            $phpInfo = ob_get_contents();

            ob_end_clean();

            $gdVersion = @gd_info();
            $gdVersion = (is_array($gdVersion) && isset($gdVersion['GD Version']))
                ? $gdVersion['GD Version']
                : null;

            if (!$gdVersion) {
                $isMatched = preg_match('/GD.+>([\.\d]+)/mi', $phpInfo, $m);

                $gdVersion = $isMatched
                    ? $m[1]
                    : 'unknown';

            }

            $return = 'found (' . $gdVersion . ')';
        }

        return $return;
    }

    /**
     * Get Libcurl value
     *
     * @return string
     */
    public function getLibcurlValue()
    {
        $return = null;

        if (function_exists('curl_version')) {
            $libcurlVersion = curl_version();

            if (is_array($libcurlVersion)) {
                $libcurlVersion = $libcurlVersion['version'];
            }

            $return = $libcurlVersion;

        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * Get CheckFiles value
     *
     * @return string
     */
    public function getCheckFilesValue()
    {
        $result = array();
        $files = array();

        foreach ($files as $file) {
            $mode = $this->getExpectedFilePermission($file);
            $modeStr = $this->getExpectedFilePermissionStr($file);
            $res = array('file' => $file, 'error' => '');

            if (!is_file($file)) {
                $res['error'] = 'does_not_exist';
                $result[] = $res;
                continue;
            }

            $perm = substr(sprintf('%o', @fileperms($file)), -4);

            if ($perm !== $modeStr) {
                if (!@chmod($file, $mode)) {
                    $res['error'] = 'cannot_chmod';
                    $result[] = $res;
                    continue;
                }

            } else {
                if ($this->getComplex('xlite.suMode') != 0 && !@chmod($file, $mode)) {
                    $res['error'] = 'wrong_owner';
                    $result[] = $res;
                    continue;
                }
            }

            $result[] = $res;
        }

        return $result;
    }

    /**
     * Get CheckDirs value
     *
     * @return string
     */
    public function getCheckDirsValue()
    {
        $result = array();

        $dirs = array(
            'var/run',
            'var/log',
            'var/backup',
            'var/tmp',
            'images',
            'skins/customer/modules',
            'skins/admin/modules',
            'skins/mail/modules',
            'skins/mail/images'
        );

        foreach ($dirs as $dir) {
            $mode = $this->getDirPermission($dir);

            $res = array(
                'dir'     => $dir,
                'error'   => '',
                'subdirs' => array(),
            );

            if (!$this->tryCreateDir($dir, $mode)) {
                $res['error'] = 'cannot_create';
                $result[] = $res;
                continue;
            }

            $modeStr = $this->getDirPermissionStr($dir);
            if (!\Includes\Utils\FileManager::isDirWriteable($dir)) {
                $res['error'] = 'wrong_permissions';
                $res['permission_command'] = \Includes\Utils\FileManager::getPermissionsCommand($dir, $modeStr);
                $result[] = $res;

                continue;
            }

            $subdirs = array();

            if ('images' !== $dir) {
                $this->checkSubdirs($dir, $subdirs);
            }

            if (!empty($subdirs)) {
                $res['error'] = 'wrong_permissions_subdirs';
                $res['permission_command'] = \Includes\Utils\FileManager::getPermissionsCommand($dir, $modeStr);
                $res['subdirs'] = $subdirs;
                $result[] = $res;
                continue;
            }
            $result[] = $res;
        }

        return $result;
    }

    /**
     * Try create if not exists
     *
     * @param  string   $dir    Dir to create
     * @param  integer  $mode   Permissions
     *
     * @return boolean
     */
    public function tryCreateDir($dir, $mode)
    {
        if (!is_dir($dir)) {
            @\Includes\Utils\FileManager::mkdirRecursive($dir, $mode);
        }

        return is_dir($dir);
    }

    /**
     * Try permissions
     *
     * @param  string   $dir      Dir to create
     * @param  string   $modeStr  Permissions string
     *
     * @return boolean
     */
    public function tryPermissions($dir, $modeStr = null)
    {
        $perm = substr(
            sprintf('%o', @fileperms($dir)),
            -4
        );

        return $modeStr === $perm;
    }

    /**
     * Get directory permission
     *
     * @param string $dir Directory path
     *
     * @return integer
     */
    public function getDirPermission($dir)
    {
        global $options;

        if ($this->getComplex('xlite.suMode') == 0) {
            if ($this->isSubdir($dir, 'var' . LC_DS, true)) {
                $mode = 0777;

            } else {
                $mode = isset($options['filesystem_permissions']['nonprivileged_permission_dir'])
                    ? base_convert($options['filesystem_permissions']['nonprivileged_permission_dir'], 8, 10)
                    : 0755;
            }

        } else {
            $mode = isset($options['filesystem_permissions']['privileged_permission_dir'])
                ? base_convert($options['filesystem_permissions']['privileged_permission_dir'], 8, 10)
                : 0711;
        }

        return $mode;
    }

    protected function isSubdir($dir, $subdir, $start = false)
    {
        return $start
            ? $subdir === "" || strrpos($dir, $subdir, -strlen($dir)) !== false
            : strpos($dir, $subdir) !== false;
    }

    /**
     * getDirPermissionStr
     *
     * @param string $dir Directory path OPTIONAL
     *
     * @return string
     */
    public function getDirPermissionStr($dir = '')
    {
        $mode = (int) $this->getDirPermission($dir);

        return (string) ('0' . base_convert($mode, 10, 8));
    }

    /**
     * Get expected file permission
     *
     * @param mixed $file File path OPTIONAL
     *
     * @return integer
     */
    public function getExpectedFilePermission($file = '')
    {
        global $options;

        switch ($file) {
            default:
                if ($this->getComplex('xlite.suMode') == 0) {
                    $mode = isset($options['filesystem_permissions']['nonprivileged_permission_file'])
                        ? base_convert($options['filesystem_permissions']['nonprivileged_permission_file'], 8, 10)
                        : 0644;

                } else {
                    $mode = isset($options['filesystem_permissions']['privileged_permission_file'])
                        ? base_convert($options['filesystem_permissions']['privileged_permission_file'], 8, 10)
                        : 0600;
                }
                break;
        }

        return $mode;
    }

    /**
     * Get expected file permission (string)
     *
     * @param string $file File path OPTIONAL
     *
     * @return string
     */
    public function getExpectedFilePermissionStr($file = '')
    {
        switch ($file) {
            default:
                $mode = (int) $this->getExpectedFilePermission($file);
                break;
        }

        return (string) '0' . base_convert($mode, 10, 8);
    }

    /**
     * checkSubdirs
     *
     * @param mixed $path          ____param_comment____
     * @param mixed &$subdirErrors ____param_comment____
     *
     * @return void
     */
    public function checkSubdirs($path, &$subdirErrors)
    {
        if (is_dir($path)) {
            $mode = $this->getDirPermission($path);
            $modeStr = $this->getDirPermissionStr($path);

            $dh = @opendir($path);

            while (($file = @readdir($dh)) !== false) {
                if ('.' !== $file && '..' !== $file) {
                    $fullpath = $path . DIRECTORY_SEPARATOR . $file;

                    if (@is_dir($fullpath)) {
                        if (!\Includes\Utils\FileManager::isDirWriteable($fullpath)) {
                            $subdirErrors[] = $fullpath;
                            continue;

                        }

                        $this->checkSubdirs($fullpath, $subdirErrors);
                    }
                }
            }
        }
    }

    /**
     * Get wrong permissions
     *
     * @return array
     */
    public function getWrongPermissions()
    {
        $permissions = array_map(
            function($dir) {
                return isset($dir['permission_command'])
                    ? $dir['permission_command']
                    : '';
            },
            $this->getCheckDirsValue()
        );

        return array_filter(
            array_merge($permissions)
        );
    }

    /**
     * Get wrong permissions
     *
     * @return array
     */
    public function getWrongPermissionsAsString()
    {
        $list = $this->getWrongPermissions();

        return $list ? implode('\\' . PHP_EOL, $list) : '';
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('phpinfo', 'switch_clean_url'));
    }

    /**
     * doActionPhpinfo
     *
     * @return void
     */
    public function doActionPhpinfo()
    {
        phpinfo();
        $this->setSuppressOutput(true);
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * Get error message header
     *
     * @return string
     */
    protected function getErrorMessageHeader()
    {
        $message = 'Clean_urls_error_message';

        return static::t($message, array('url' => $this->curlResponse->uri));
    }

    /**
     * Get error message by code
     *
     * @param integer $code Code
     *
     * @return string
     */
    protected function getErrorMessageCodeExplanation($code)
    {
        // TODO Add some explanation
        $explanation = '';
        switch ($code) {
            case 500:
                $explanation .= ': Internal server error';
                break;
        }

        return static::t('Error code explanation:'). $code . ' '. $explanation;
    }

    /**
     * Actions to enable the clean URL functionality
     *
     * @return void
     */
    public function doActionSwitchCleanUrl()
    {
        $oldValue = (bool) \XLite\Core\Config::getInstance()->CleanURL->clean_url_flag;
        $ajaxResponse = array(
            'Success'       => true,
            'Error'         => '',
            'NewState'      => !(bool) $oldValue
        );

        if ($oldValue === false && !$this->canEnableCleanURL()) {
            $ajaxResponse['Success'] = false;
            $ajaxResponse['Error'] = array(
                'msg'   => $this->getErrorMessageHeader(),
                'body'  => $this->getErrorMessageCodeExplanation($this->curlResponse->code)
            );

        } else {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'CleanURL',
                    'name'     => 'clean_url_flag',
                    'value'    => !(bool) $oldValue
                )
            );
        }

        $this->printAJAX($ajaxResponse);
        $this->silent = true;
        $this->setSuppressOutput(true);
    }

    /**
     * Send specific headers and print AJAX data as JSON string
     *
     * @param array $data
     *
     * @return void
     */
    protected function printAJAX($data)
    {
        // Move top messages into headers since we print data and die()
        $this->translateTopMessagesToHTTPHeaders();

        $content = json_encode($data);

        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);
    }

    /**
     * Actions to enable the clean URL functionality
     *
     * @return void
     */
    public function doActionEnableCleanUrl()
    {
        if ($this->canEnableCleanURL()) {
            /** @var \XLite\Model\Config $cleanURLFlag */
            $cleanURLFlag = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                array(
                    'name'      => 'clean_url_flag',
                    'category'  => 'CleanURL'
                )
            );

            \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                $cleanURLFlag,
                array(
                    'value' => true,
                )
            );

            \XLite\Core\TopMessage::addInfo(static::t('Clean URLs are enabled'));
        }

        $this->doRedirect();
    }

    /**
     * isWin
     *
     * @return boolean
     */
    public function isWin()
    {
        return (LC_OS_CODE === 'win');
    }

    /**
     * getStateById
     *
     * @param mixed $stateId State Id
     *
     * @return \XLite\Model\State
     */
    public function getStateById($stateId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->find($stateId);
    }

    /**
     * Flag to has email error
     *
     * @return string
     */
    public function hasTestEmailError()
    {
        return '' !== (string)\XLite\Core\Session::getInstance()->test_email_error;
    }

    /**
     * Return error test email sending
     *
     * @return string
     */
    public function getTestEmailError()
    {
        $error = (string)\XLite\Core\Session::getInstance()->test_email_error;

        \XLite\Core\Session::getInstance()->test_email_error = '';

        return $error;
    }

    // }}}

    // {{{ Service actions

    /**
     * Action to send test email notification
     *
     * @return void
     */
    protected function doActionTestEmail()
    {
        $request = \XLite\Core\Request::getInstance();

        $error = \XLite\Core\Mailer::sendTestEmail(
            $request->test_from_email_address,
            $request->test_to_email_address,
            $request->test_email_body
        );

        if ($error) {
            \XLite\Core\Session::getInstance()->test_email_error = $error;
            \XLite\Core\TopMessage::getInstance()->addError('Error of test e-mail sending: ' . $error);

        } else {
            \XLite\Core\TopMessage::getInstance()->add('Test e-mail have been successfully sent');
        }

        $this->setReturnURL(
            $this->buildURL('settings', '', array('page' => static::EMAIL_PAGE))
        );
    }

    // }}}
}
