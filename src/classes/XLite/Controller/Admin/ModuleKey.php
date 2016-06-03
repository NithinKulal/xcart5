<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * ModuleKey
 */
class ModuleKey extends \XLite\Controller\Admin\AAdmin
{
    // {{{ Public methods for viewers

    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Enter license key');
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

    // }}}

    /**
     * Return true if unallowed modules should be ignored on current page
     *
     * @return boolean
     */
    protected function isIgnoreUnallowedModules()
    {
        return true;
    }

    // {{{ "Register key" action handler

    /**
     * Action of view license view
     *
     * @return void
     */
    protected function doActionView()
    {
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('view'));
    }

    /**
     * Action of license key registration
     *
     * @return void
     */
    protected function doActionRegisterKey()
    {
        $key = trim(\XLite\Core\Request::getInstance()->key);

        if ($key) {
            $keysInfo = \XLite\Core\Marketplace::getInstance()->checkAddonKey($key);

        } else {
            $keysInfo = null;
            $emptyKey = true;
        }

        $this->setReturnURL($this->buildURL('addons_list_marketplace'));

        if ($keysInfo && $keysInfo[$key]) {
            $keysInfo = $keysInfo[$key];
            $repo = \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey');

            foreach ($keysInfo as $info) {
                if (\XLite\Model\ModuleKey::KEY_TYPE_XCN == $info['keyType']) {
                    $xcnPlan = $info['xcnPlan'];
                    $keyData = $info['keyData'];

                    // Unset some fields which is not in database
                    unset($info['xcnPlan']);
                    unset($info['keyData']);
                    unset($info['key']);

                    $entity = $repo->findOneBy($info);

                    if (!$entity) {
                        $entity = new \XLite\Model\ModuleKey();
                        $entity->map($info);
                    }

                    $entity->setKeyValue($key);
                    $entity->setXcnPlan($xcnPlan);
                    $entity->setKeyData($keyData);

                    if (!empty($keyData['wave'])) {
                        $this->updateUpgradeWaveOption($keyData['wave']);
                    }

                    $isValid = true;

                    $isFreeLicense = \XLite::isFreeLicense($entity);

                    if ($isFreeLicense) {
                        if (0 == \XLite\Core\Database::getRepo('XLite\Model\Module')->hasMarketplaceModules(true)) {
                            $isValid = false;
                            $this->showError(
                                __FUNCTION__,
                                static::t('Cannot gather modules from the marketplace. Please try later.')
                            );
                        }
                    }

                    if ($isValid) {
                        // Save entity (key) in the database
                        \XLite\Core\Database::getEM()->persist($entity);
                        \XLite\Core\Database::getEM()->flush();

                        if ($isFreeLicense) {
                            // Search for modules from non-free edition
                            $nonFreeModules = \XLite\Core\Database::getRepo('XLite\Model\Module')
                                ->getNonFreeEditionModulesList(false);

                            if ($nonFreeModules) {
                                // Try to uninstall these modules...
                                foreach ($nonFreeModules as $module) {
                                    $messages = array();

                                    $res = \XLite\Core\Database::getRepo('XLite\Model\Module')
                                        ->uninstallModule($module, $messages);

                                    if ($messages) {
                                        foreach ($messages as $message) {
                                            $method = ($res ? 'Info' : 'Error');
                                            \XLite\Upgrade\Logger::getInstance()
                                                ->{'log' . $method}($message, array(), false);
                                        }
                                    }
                                }
                            }

                            \XLite::setCleanUpCacheFlag(true);
                        }

                        if (empty($keyData['message'])) {
                            $this->showInfo(
                                __FUNCTION__,
                                static::t('X-Cart license key has been successfully verified')
                            );

                        } else {
                            $this->showWarning(
                                __FUNCTION__,
                                static::t('X-Cart license key has been successfully verified and activated. But this key has expired and do not allow upgrade store.')
                            );
                        }

                        // Renew marketplace cache
                        $this->renewMarketplaceCache();

                        $this->setHardRedirect();
                    }

                } else {

                    $keyData = $info['keyData'];

                    $module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy(
                        array(
                            'author' => $info['author'],
                            'name'   => $info['name'],
                        )
                    );

                    if ($module) {
                        $entity = $repo->findKey($info['author'], $info['name']);

                        if ($entity) {
                            $entity->setKeyValue($key);
                            $repo->update($entity);

                        } else {
                            $entity = $repo->insert($info + array('keyValue' => $key));
                        }

                        if (!empty($keyData['wave'])) {
                            $this->updateUpgradeWaveOption($keyData['wave']);
                        }

                        // Clear cache for proper installation
                        \XLite\Core\Marketplace::getInstance()->clearActionCache();

                        if (empty($keyData['message'])) {
                            $this->showInfo(
                                __FUNCTION__,
                                static::t(
                                    'License key has been successfully verified for "{{name}}" module by "{{author}}" author',
                                    array(
                                        'name'   => $module->getModuleName(),
                                        'author' => $module->getAuthorName(),
                                    )
                                )
                            );

                        } else {
                            $this->showWarning(
                                __FUNCTION__,
                                static::t(
                                    'License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author. But this key has expired and do not allow upgrade store.',
                                    array(
                                        'name'   => $module->getModuleName(),
                                        'author' => $module->getAuthorName(),
                                    )
                                )
                            );
                        }

                        if (!$module->isInstalled()) {
                            // We install the addon after the successfull key verification
                            $this->setReturnURL(
                                $this->buildURL(
                                    'upgrade',
                                    'install_addon_force',
                                    array(
                                        'moduleIds[]' => $module->getModuleID(),
                                        'agree'       => 'Y',
                                    )
                                )
                            );
                        }

                    } else {
                        $this->showError(
                            __FUNCTION__,
                            static::t('Key is validated, but the module X was not found', array('module' => implode(',', $info)))
                        );
                    }
                }
            }

        } elseif (!isset($emptyKey)) {
            $error = \XLite\Core\Marketplace::getInstance()->getError();
            $message = $error
                ? static::t('Response from marketplace: X', array('response' => $error))
                : static::t('Response from marketplace is not received');
            $this->showError(__FUNCTION__, $message);

        } else {
            $this->showError(__FUNCTION__, static::t('Please specify non-empty key'));
        }
    }

    /**
     * Update value of 'upgrade_wave' option
     *
     * @param integer $wave Wave number
     *
     * @return void
     */
    protected function updateUpgradeWaveOption($wave)
    {
        $data = array(
            'category' => 'Environment',
            'name'     => 'upgrade_wave',
            'value'    => $wave,
        );

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption($data);
    }

    // }}}

    /**
     * Do action 'unset_core_license'
     *
     * @return void
     */
    protected function doActionUnsetCoreLicense()
    {
        $keys = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findBy(
            array(
                'name'   => 'Core',
                'author' => 'CDev',
            )
        );

        $allKeys = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll();

        $toDelete = array();

        foreach ($keys as $key) {
            foreach ($allKeys as $key2) {
                // Search and delete all keys with the same keyBody
                // (used in case of license key assigned to several entities)
                if ($key->getKeyValue() == $key2->getKeyValue()) {
                    $toDelete[] = $key2;
                }
            }
        }

        if ($toDelete) {
            // Delete core license
            \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->deleteInBatch($toDelete);

            // Renew marketplace cache
            $this->renewMarketplaceCache();
        }

        $this->setReturnURL($this->buildURL('main'));
    }

    /**
     * Renew marketplace cache
     *
     * @return void
     */
    protected function renewMarketplaceCache()
    {
        // Force to re-read core license
        \XLite::getXCNLicense(true);

        // Clear marketplace actions cache
        \XLite\Core\Marketplace::getInstance()->clearActionCache();

        // Update addons list
        \XLite\Core\Marketplace::getInstance()->getAddonsList(0);

        // Update inactive licenses information
        \XLite\Core\Marketplace::getInstance()->getInactiveLicenseKeys(0);
    }
}
