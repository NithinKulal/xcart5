<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\FormField\Select;

/**
 * Warehouse selector
 */
class Warehouse extends \XLite\View\FormField\Select\Regular
{
    const CACHE_KEY = 'pilibaba-warehouse';
    const CACHE_TTL = 3600;

    const PARAM_SHOW_HUMAN = 'showHuman';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SHOW_HUMAN => new \XLite\Model\WidgetParam\TypeBool('Show human', false),
        );
    }

    /**
     * Convert address to Entity
     *
     * @param  \PilipayWarehouseAddress $address Address
     *
     * @return \XLite\Model\AEntity
     */
    protected static function convertAddress(\PilipayWarehouseAddress $address)
    {
        return \XLite\Module\XC\Pilibaba\Logic\PilipayWarehouseAddressConverter::convertAddress($address);
    }

    /**
     * Load addresses list from Pilibaba API
     *
     * @return array
     */
    protected function loadAddressesList()
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        \PilipayConfig::setUseHttps(false);
        \PilipayConfig::setUseProductionEnv(true);
        \PilipayLogger::instance()->setHandler(
            function($level, $msg) {
                \XLite\Module\XC\Pilibaba\Model\Payment\Processor\Pilibaba::log(
                    sprintf('%s %s: %s' . PHP_EOL, date('Y-m-d H:i:s'), $level, $msg)
                );
            }
        );

        $addresses = @\PilipayWarehouseAddress::queryAll();

        $processedAddresses = array();

        foreach ($addresses as $key => $address) {
            $identifier = base64_encode(serialize($address));

            $name = array(
                $address->country,
                $address->city,
                $address->firstName,
                $address->lastName,
            );
            $processedAddresses[$identifier] = join(' ', $name);
        }


        return $processedAddresses;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getOldFieldTemplate()
    {
        return parent::getFieldTemplate();
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '../modules/XC/Pilibaba/warehouse/select.twig';
    }

    /**
     * Warehouse address
     *
     * @return array
     */
    public function getWarehouseAddress()
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        $addressObject = static::convertAddress(
            unserialize(
                base64_decode($this->getValue())
            )
        );

        return $addressObject;
    }

    /**
     * Show human form
     *
     * @return boolean
     */
    public function showHumanForm()
    {
        return $this->getParam(static::PARAM_SHOW_HUMAN) && $this->getValue() && $this->getValue() !== 'others';
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Core\Database::getCacheDriver()->fetch(static::CACHE_KEY);

        if (!$list) {
            $list = $this->loadAddressesList();
            \XLite\Core\Database::getCacheDriver()->save(static::CACHE_KEY, $list, static::CACHE_TTL);
        }

        $list = array_reverse($list, true);
        $list[''] = static::t('Select warehouse');
        $list = array_reverse($list, true);

        $list['others'] = 'Others';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Pilibaba/warehouse/select.css';

        return $list;
    }
}
