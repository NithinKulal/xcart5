<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * AddonInstall
 */
class AddonInstall extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Delimiter for licenses in the popup window textarea
     */
    const LICENSE_DELIMITER = '==============';

    protected $licenseCache = null;

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('view_license','select_installation_type', 'warnings'));
    }

    /**
     * Controller constructor
     *
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        if ('view_license' === $this->getAction()) {
            $this->getLicense();
        }
    }

    // {{{ Public methods for viewers

    /**
     * Defines the modules identificators for installation
     *
     * @return array
     */
    public function getModuleIds()
    {
        return \XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\SelectInstallationType::PARAM_MODULEIDS}
            ? explode(',', \XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\SelectInstallationType::PARAM_MODULEIDS})
            : array(\XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\Install::PARAM_MODULEID});
    }

    /**
     * Return title
     *
     * @return string
     */
    public function getTitle()
    {
        $title = '';
        switch ($this->getAction()) {
            case 'view_license':
                $title = static::t('Modules license agreements');
                break;
            case 'warnings':
                $title = static::t('Installation warnings');
                break;

            default:
                $title = static::t('Updates are available');
                break;
        }
        return $title;
    }

    /**
     * Return LICENSE text for the modules
     *
     * @return string
     */
    public function getLicense()
    {
        if (is_null($this->licenseCache)) {

            $this->setPaymentMethodToInstall();

            \XLite\Upgrade\Cell::getInstance()->clear();

            $result = array();
            $error = false;
            foreach ($this->getModuleIds() as $id) {
                $module = $this->getModule($id);
                $info = \XLite\Core\Marketplace::getInstance()->getAddonInfo($module->getMarketplaceID());
                if ($info) {
                    // Do not display the empty license
                    // If the info is correct the empty license means that the admin is already agree with the license
                    if (!empty($info[\XLite\Core\Marketplace::FIELD_LICENSE])) {
                        $result[] .= static::t('{{module}} license agreement', array('module' => $module->getModuleName())) . ":\r\n"
                            . $info[\XLite\Core\Marketplace::FIELD_LICENSE] . "\r\n\r\n";
                    }

                } else {
                    $error = true;
                    $this->showError(__FUNCTION__, static::t('License is not received'));
                }
            }

            // Since this action is performed in popup
            // If the result is empty but there is no error then we redirect the admin to the installation of the modules
            $errorMessages = $this->getErrorMessages();
            if (!$this->isFreeSpaceCheckAvailable() || !empty($errorMessages) ) {
                $this->setReturnURL(
                    $this->buildURL(
                        'addon_install',
                        'warnings',
                        array(
                            'widget' => '\XLite\View\ModulesManager\ModuleWarnings',
                            'moduleIds' => implode(',', $this->getModuleIds()),
                        )
                    )
                );

                $this->doRedirect();

            } elseif (empty($result) && !$error && $this->isFreeSpaceCheckAvailable()) {
                $this->setReturnURL(
                    ($this->hasCoreUpdate() && !$this->ignoreCoreUpdate())
                        ? $this->buildURL(
                            'addon_install',
                            'select_installation_type',
                            array(
                                'widget' => '\XLite\View\ModulesManager\InstallationType',
                                'moduleIds' => implode(',', $this->getModuleIds()),
                            )
                        )
                        : $this->buildURL(
                            'upgrade',
                            'install_addon_force',
                            array(
                                'agree'     => 'Y',
                                'moduleIds' => $this->getModuleIds(),
                            )
                        )
                );

                $this->doRedirect();
            }

            $this->licenseCache = implode(static::LICENSE_DELIMITER . "\r\n", $result);
        }

        return $this->licenseCache;
    }

    /**
     * Set session value 'paymentMethodToInstall'.
     * Used to redirect to payment settings page after module installation process is complete
     *
     * @return void
     */
    protected function setPaymentMethodToInstall()
    {
        \XLite\Core\Session::getInstance()->paymentMethodToInstall
            = \XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\Install::PARAM_PAYMENT_METHOD_ID};
    }

    // }}}

    /**
     * Check if the core has update (but not upgrade) available
     *
     * @return boolean
     */
    protected function hasCoreUpdate()
    {
        $update = \XLite\Core\Marketplace::getInstance()->checkForUpdates();

        return !empty($update)
            && (
                $update[\XLite\Core\Marketplace::FIELD_ARE_UPDATES_AVAILABLE]
                && !$update[\XLite\Core\Marketplace::FIELD_IS_UPGRADE_AVAILABLE]
            );
    }

    /**
     * Ignore core update
     *
     * @return boolean
     */
    protected function ignoreCoreUpdate()
    {
        return null !== \XLite\Core\Request::getInstance()->paymentMethodId;
    }

    // {{{ Short-name methods

    /**
     * Search for module
     *
     * @param integer $id
     *
     * @return \XLite\Model\Module|void
     */
    protected function getModule($id)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->find($id);
    }

    /**
     * Is disk_free_space function available
     *
     * @return boolean
     */
    public function isFreeSpaceCheckAvailable()
    {
        return \XLite\Upgrade\Cell::getInstance()->isFreeSpaceCheckAvailable();
    }

    /**
     * Get error messages
     *
     * @return boolean
     */
    public function getErrorMessages()
    {
        return \XLite\Upgrade\Cell::getInstance()->getErrorMessages();
    }

    // }}}

    // {{{ Action handlers

    /**
     * doActionViewLicense
     *
     * @return void
     */
    protected function doActionViewLicense()
    {
        if (\XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\Install::PARAM_PAYMENT_METHOD_ID}) {
            \XLite\Core\Session::getInstance()->paymentMethodToInstall
               = \XLite\Core\Request::getInstance()->{\XLite\View\Button\Addon\Install::PARAM_PAYMENT_METHOD_ID};
        }
    }

    /**
     * doActionSelectInstallationType
     *
     * @return void
     */
    protected function doActionSelectInstallationType()
    {
    }
    // }}}
}
