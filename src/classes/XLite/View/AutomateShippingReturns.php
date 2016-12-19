<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Automate shipping routine page view
 */
class AutomateShippingReturns extends \XLite\View\AView
{
    /**
     * Runtime cache for module object
     * @var \XLite\Model\Module | null
     */
    protected $module;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array(
                'automate_shipping_refunds'
            )
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'automate_shipping_returns/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

         $list[] = 'automate_shipping_returns/style.css';

        return $list;
    }

    /**
     * Get module object (fromMarketplace=true one)
     *
     * @return \XLite\Model\Module | null
     */
    protected function getModule()
    {
        if(!$this->module) {
            $this->module = \XLite\Core\Database::getRepo('XLite\Model\Module')
                ->findOneByModuleName('AutomatedShippingRefunds71LBS\\SeventyOnePounds', true);
        }

        return $this->module;
    }

    /**
     * Is 71lbs installed
     *
     * @return boolean
     */
    public function isModuleInstalled()
    {
        return $this->getModule()
            ? $this->getModule()->isInstalled()
            : false;
    }

    /**
     * Get "Configure" link
     *
     * @return string
     */
    public function getConfigureLink()
    {
        $moduleInstalled = $this->getModule()
            ?  $this->getModule()->getModuleInstalled()
            : null;

        return $moduleInstalled
            ? $moduleInstalled->getSettingsForm()
            : $this->buildURL('addons_list_marketplace', '', [ 'substring' => 'SeventyOnePounds' ]);
    }

    /**
     * Get "enable module" link
     *
     * @return string
     */
    public function getEnableLink()
    {
        return $this->getModule()
            ? $this->getModule()->getMarketplaceURL()
            : $this->buildURL('addons_list_marketplace', '', [ 'substring' => 'SeventyOnePounds' ]);
    }
}
