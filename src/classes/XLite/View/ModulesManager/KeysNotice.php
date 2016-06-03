<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * License keys notice page
 *
 * @ListChild (list="admin.center", zone="admin", weight=0)
 */
class KeysNotice extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Cached value of isCoreWarning() method
     *
     * @var boolean
     */
    protected $isCoreWarning = null;

    /**
     * Cahced list of unallowed modules
     *
     * @var array
     */
    protected $unallowedModules = null;

    /**
     * 'Purchase all' link URL
     *
     * @var string
     */
    protected $purchaseAllURL = null;

    /**
     * Get list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'keys_notice';

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
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'keys_notice';
    }

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }

    /**
     * URL of the X-Cart company's Contact Us page
     *
     * @return string
     */
    protected function getContactUsURL()
    {
        return \XLite\Core\Marketplace::getContactUsURL();
    }

    /**
     * Get list of unallowed modules
     *
     * @return array
     */
    protected function getUnallowedModules()
    {
        if (!isset($this->unallowedModules)) {

            $result = array();

            $this->xbProductIds = array(
                'editions'        => array(),
                'addons'          => array(),
                'common_editions' => array(),
            );

            $list = \XLite\Core\Marketplace::getInstance()->getInactiveContentData(false);

            if ($list) {
                foreach ($list as $k => $data) {
                    $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
                        array(
                            'name' => $data['name'],
                            'author' => $data['author'],
                            'fromMarketplace' => 1,
                        )
                    );

                    if ($module) {
                        $item = $this->preprocessUnallowedModule($data, $module);
                        if ($item) {
                            $result[] = $item;
                        }
                    }
                }

                usort($result, array($this, 'sortUnallowedModules'));

                if (!empty($this->xbProductIds['editions'])) {
                    $this->xbProductIds['editions'] = array_unique($this->xbProductIds['editions']);
                    if ($this->xbProductIds['common_editions']) {
                        $this->xbProductIds['editions'] = $this->xbProductIds['common_editions'];
                    }
                    $first = array_shift($this->xbProductIds['editions']);
                    $this->xbProductIds['editions'] = array($first);
                }
            }

            $this->unallowedModules = $result;
        }

        return $this->unallowedModules;
    }

    /**
     * Method to sort list of modules in alphabet order
     *
     * @param array First module data
     * @param array Second module data
     *
     * @return integer
     */
    public function sortUnallowedModules($m1, $m2)
    {
        return strcmp($m1['title'], $m2['title']);
    }

    /**
     * Get module name
     */
    protected function preprocessUnallowedModule($moduleData, $module)
    {
        $skipItem = false;

        $result = $moduleData;
        $result['title'] = $module->getModuleName();

        $message = '';
        $url = '';

        if (!empty($moduleData['key'])) {
            $message = static::t('Inactive license key ({{key}})', array('key' => $moduleData['key']));

            if ($module->getXbProductId()) {
                $url = \XLite\Core\Marketplace::getPurchaseURL($module->getXbProductId());
            }

        } else {

            $license = \XLite::getXCNLicense();
            $edition = null;

            $moduleEditions = $module->getEditionNames();

            if ($moduleEditions) {

                // Get hash of edition IDs
                $editionIds = $module->getEditionIds();

                $keyData = $license ? $license->getKeyData() : null;
                $edition = !empty($keyData['edition']) ? $keyData['edition'] : null;

                if (!in_array($edition, $moduleEditions)) {

                    $commonEditionIds = array();

                    foreach ($moduleEditions as $k => $v) {

                        if ('Free' == $v) {
                            // Skip module available for Free license
                            $skipItem = true;
                        }

                        if ($editionIds[$v]) {
                            $moduleEditions[$k] = sprintf(
                                '<a href="%s" target="_blank">%s</a>',
                                \XLite\Core\Marketplace::getPurchaseURL($editionIds[$v]),
                                $v
                            );
                            $this->xbProductIds['editions'][] = $editionIds[$v];
                            $commonEditionIds[] = $editionIds[$v];
                        }
                    }

                    if ($commonEditionIds) {
                        $this->xbProductIds['common_editions'] = $this->xbProductIds['common_editions']
                            ? array_intersect($this->xbProductIds['common_editions'], $commonEditionIds)
                            : $commonEditionIds;
                    }

                    $list = '';

                    if (1 == count($moduleEditions)) {
                        $list = array_pop($moduleEditions);

                    } elseif (1 < count($moduleEditions)) {
                        $last = array_shift($moduleEditions);
                        $list = implode(', ', $moduleEditions) . ' ' . static::t('or') . ' ' . $last;
                    }

                    if ($edition) {
                        $message = static::t('Does not match license type (requires {{list}} edition)', array('list' => $list));

                    } else {
                        $message = static::t('Requires {{list}} edition', array('list' => $list));
                    }
                }

            } elseif (0 < $module->getPrice()) {
                $message = static::t('License key is missing');
                if ($module->getXbProductId()) {
                    $url = \XLite\Core\Marketplace::getPurchaseURL($module->getXbProductId());
                    $this->xbProductIds['addons'][] = $module->getXbProductId();
                }
            }
        }

        $result['message'] = $message;
        $result['url'] = $url;

        return !$skipItem ? $result : null;
    }

    /**
     * Get URL for 'Remove unallowed modules' action
     *
     * @return string
     */
    protected function getRemoveModulesURL()
    {
        return $this->buildURL('addons_list_installed', 'uninstall_unallowed');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getBackToTrialURL()
    {
        return $this->buildURL('module_key', 'unset_core_license');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getRecheckURL()
    {
        return $this->buildURL(
            'keys_notice',
            'recheck',
            array(
                'returnUrl' => \XLite\Core\Request::getInstance()->returnUrl
            )
        );
    }

    /**
     * Get true if widget in core-license mode
     *
     * @return boolean
     */
    protected function isCoreWarning()
    {
        if (!isset($this->isCoreWarning)) {
            $this->isCoreWarning = $this->isDisplayBlockContent();
        }

        return $this->isCoreWarning;
    }

    /**
     * Return true if fraud status has been confirmed
     *
     * @return boolean
     */
    protected function isFraudStatusConfirmed()
    {
        $result = false;

        $flags = \XLite\Core\Marketplace::getInstance()->checkForUpdates();

        if (!empty($flags[\XLite\Core\Marketplace::FIELD_IS_CONFIRMED])) {
            $result = true;
            \XLite\Core\Session::getInstance()->fraudWarningDisplayed = true;
        }

        return $result;
    }

    /**
     * Get 'Purchase all' button URL
     *
     * @return string
     */
    protected function getPurchaseAllURL()
    {
        if (!isset($this->purchaseAllURL)) {

            $urlParamsAggregated = array();
            $i = 1;

            if ($this->xbProductIds) {
                foreach (array('editions', 'addons') as $licType) {
                    if (!empty($this->xbProductIds[$licType])) {
                        foreach ($this->xbProductIds[$licType] as $id) {
                            $urlParamsAggregated['add_' . $i] = $id;
                            $i ++;
                        }
                    }
                }
            }

            if ($this->isCoreWarning()) {
                $license = $this->getCoreLicense();
                if ($license && !empty($license['xbProductId'])) {
                    $urlParamsAggregated['add_' . $i] = $license['xbProductId'];
                }
            }

            $this->purchaseAllURL = $urlParamsAggregated
                ? \XLite\Core\Marketplace::getPurchaseURL(null, $urlParamsAggregated, true)
                : '';
        }

        return $this->purchaseAllURL;
    }

    /**
     * Return true if 'Purchase all' button should be displayed
     *
     * @return boolean
     */
    protected function isDisplayPurchaseAllButton()
    {
        return (bool)$this->getPurchaseAllURL();
    }

    /**
     * Get currently activated core license data
     *
     * @return array
     */
    protected function getCoreLicense()
    {
        if (!isset($this->coreLicense)) {

            $result = array();

            $license = \XLite::getXCNLicense();

            if ($license) {
                $keyData = $license->getKeyData();
                $xbProductId = !empty($keyData['xbProductId'])
                    ? $keyData['xbProductId']
                    : \XLite\Core\Database::getRepo('XLite\Model\Module')->getEditionIdByName($keyData['editionName']);
                $xbProductId = intval($xbProductId);

                $result['title'] = 'X-Cart ' . $keyData['editionName'];
                $result['message'] = static::t('Inactive license key ({{key}})', array('key' => $license->getKeyValue()));
                if (0 < $xbProductId) {
                    $result['url'] = \XLite\Core\Marketplace::getPurchaseURL($xbProductId);
                    $result['xbProductId'] = $xbProductId;
                }
            }

            $this->coreLicense = $result;
        }

        return $this->coreLicense;
    }
}
