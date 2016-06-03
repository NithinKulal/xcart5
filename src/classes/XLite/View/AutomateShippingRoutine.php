<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Automate shipping routine page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AutomateShippingRoutine extends \XLite\View\AView
{
    const PROPERTY_VALUE_YES    = 'Y';
    const PROPERTY_VALUE_NO     = 'N';
    const PROPERTY_VALUE_APP_TYPE_CLOUD         = 'C';
    const PROPERTY_VALUE_APP_TYPE_WINDOWS       = 'W';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('automate_shipping_routine'));
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'automate_shipping_routine/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'automate_shipping_routine/style.css';

        return $list;
    }

    /**
     * Get shipping modules
     * 
     * @return string
     */
    protected function getShippingModulesLink()
    {
        return $this->buildURL(
            'addons_list_marketplace',
            '',
            array(
                'tag'       => 'Shipping',
                'vendor'    => '',
                'price'     => '',
                'substring' => '',
            )
        );
    }

    // {{{ Template methods

    protected function getMarketplaceURL($module)
    {
        return $module->getMarketplaceURL();
    }

    /**
     * Get shipping modules list
     * 
     * @return array
     */
    protected function getShippingModules()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');

        $modules = array(
            $repo->findOneByModuleName('Qualiteam\\ShippingEasy', true),
            $repo->findOneByModuleName('ShipStation\\Api', true),
            $repo->findOneByModuleName('Webgility\\Shiplark', true),
            $repo->findOneByModuleName('AutomatedShippingRefunds71LBS\\SeventyOnePounds', true),
        );

        return array_filter($modules);
    }

    /**
     * Get shipping module properties list
     * 
     * @return array
     */
    protected function getShippingModuleProperties()
    {
        return array(
            'common' => array(
                'labels'    => static::t('Print Shipping labels'),
                'trial'     => static::t('FREE trial'),
                'refunds'   => static::t('Refunds'),
            ),
            'integrations' => array(
                'ebay'      => static::t('eBay'),
                'amazon'    => static::t('Amazon'),
                'etsy'      => static::t('ETSY'),
                'stamps'    => static::t('Stamps.com'),
                'fedex'     => static::t('FedEx'),
                'ups'       => static::t('UPS'),
                'usps'      => static::t('USPS'),
                'dhl'       => static::t('DHL'),
            ),
            'app' => array(
                'type' => static::t('App type')
            ),
        );
    }

    /**
     * Get shipping module property value
     *
     * @return array
     */
    protected function getShippingModulePropertyDictionary()
    {
        return array(
            'ShippingEasy'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_NO,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_YES,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_CLOUD,
                ),
            ),
            'Api'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_YES,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_YES,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_CLOUD,
                ),
            ),
            'Shiplark'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_YES,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_NO,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_WINDOWS,
                ),
            ),
            'SeventyOnePounds'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_NO,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_YES,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_NO,
                    'amazon'    => static::PROPERTY_VALUE_NO,
                    'etsy'      => static::PROPERTY_VALUE_NO,
                    'stamps'    => static::PROPERTY_VALUE_NO,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_NO,
                    'dhl'       => static::PROPERTY_VALUE_NO,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_CLOUD,
                ),
            ),
        );
    }

    /**
     * Get properties group label
     * 
     * @return string
     */
    protected function getGroupLabel($groupKey)
    {
        return 'integrations' === $groupKey
            ? strtoupper(static::t('Integration with'))
            : '';
    }

    /**
     * Check if module has settings form
     * 
     * @param \XLite\Model\Module $module Module
     * 
     * @return boolean
     */
    protected function hasSetting($module)
    {
        return $module->callModuleMethod('showSettingsForm');
    }

    /**
     * Get module logo
     * 
     * @param \XLite\Model\Module $module Module
     * 
     * @return boolean
     */
    protected function getImageURL($module)
    {
        $name = $module->getName();
        $path = sprintf('automate_shipping_routine/images/%s_logo.png', strtolower($name));

        return \XLite\Core\Layout::getInstance()->getResourceWebPath($path) ?: $module->getPublicIconURL();
    }

    /**
     * Check if module has settings form
     * 
     * @param \XLite\Model\Module $module Module
     * 
     * @return boolean
     */
    protected function getSettingsForm($module)
    {
        return $module->getModuleInstalled()->getSettingsForm();
    }

    /**
     * Get shipping module property template by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getPropertyTemplate($value)
    {
        $template = '';

        switch ($value) {
            case static::PROPERTY_VALUE_YES:
                $template = 'automate_shipping_routine/parts/property_yes.twig';
                break;
            case static::PROPERTY_VALUE_NO:
                $template = 'automate_shipping_routine/parts/property_no.twig';
                break;
            case static::PROPERTY_VALUE_APP_TYPE_CLOUD:
            case static::PROPERTY_VALUE_APP_TYPE_WINDOWS:
                $template = 'automate_shipping_routine/parts/property_app_type.twig';
                break;
        }

        return $template;
    }

    /**
     * Get shipping module property icon by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getAppTypeIcon($value)
    {
        return $value == static::PROPERTY_VALUE_APP_TYPE_CLOUD
            ? 'fa-cloud'
            : 'fa-windows';
    }

    /**
     * Get shipping module property icon by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getAppTypeText($value)
    {
        return $value == static::PROPERTY_VALUE_APP_TYPE_CLOUD
            ? static::t('Cloud Service')
            : static::t('Win app');
    }

    /**
     * Get shipping module property value
     * 
     * @param \XLite\Model\Module $module Module
     * @param string $property Property key
     * 
     * @return string
     */
    protected function getShippingModulePropertyValue($module, $type, $property)
    {
        $dict = $this->getShippingModulePropertyDictionary();
        $moduleTypeDict = $dict[$module->getName()][$type];

        return $moduleTypeDict[$property];
    }

    // }}}
}
