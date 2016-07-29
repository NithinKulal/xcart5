<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Controller\Admin;

/**
 * USPS module settings page controller
 */
class Usps extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        $list = array();

        $CODRelatedOptions = array('first_class_mail_type', 'use_cod_price', 'cod_price');
        foreach (parent::getOptions() as $option) {
            if (!in_array($option->getName(), $CODRelatedOptions, true)
                || $this->isUSPSCODPaymentEnabled()
            ) {
                $list[] = $option;
            }

            if ('cacheOnDeliverySeparator' === $option->getName()) {
                $list[] = new \XLite\Model\Config(array(
                    'name'        => 'cod_status',
                    'type'        => 'XLite\View\FormField\Input\Checkbox\OnOff',
                    'value'       => $this->isUSPSCODPaymentEnabled() ? true : false,
                    'orderby'     => $option->getOrderby() + 1,
                    'option_name' => static::t('"Cash on delivery" status'),
                ));
            }
        }

        return $list;
    }

    /**
     * getOptionsCategory
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'CDev\USPS';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\USPS\View\Model\Settings';
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS();
    }

    /**
     * Returns request data
     *
     * @return array
     */
    protected function getRequestData()
    {
        $list = parent::getRequestData();
        $list['dimensions'] = serialize($list['dimensions']);

        return $list;
    }

    /**
     * Check if 'Cash on delivery (FedEx)' payment method enabled
     *
     * @return boolean
     */
    protected function isUSPSCODPaymentEnabled()
    {
        return \XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS::isCODPaymentEnabled();
    }

    /**
     * Get schema of an array for test rates routine
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $schema = parent::getTestRatesSchema();

        foreach (array('srcAddress', 'dstAddress') as $k) {
            unset($schema[$k]['city'], $schema[$k]['state']);
        }

        unset($schema['dstAddress']['type']);

        return $schema;
    }

    /**
     * Get input data to calculate test rates
     *
     * @param array $schema  Input data schema
     * @param array &$errors Array of fields which are not set
     *
     * @return array
     */
    protected function getTestRatesData(array $schema, &$errors)
    {
        $data = parent::getTestRatesData($schema, $errors);

        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        $package = array(
            'weight'    => $data['weight'],
            'subtotal'  => $data['subtotal'],
            'length'    => $config->length,
            'width'     => $config->width,
            'height'    => $config->height,
        );

        $data['packages'] = array($package);

        unset($data['weight'], $data['subtotal']);

        return $data;
    }

    /**
     * Refresh list of available USPS shipping methods
     *
     * @return void
     */
    protected function doActionRefresh()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        // Prepare default input data
        $data = array();
        $data['packages'] = array();
        $data['packages'][] = array(
            'weight'    => 5,
            'subtotal'  => 50,
            'length'    => $config->length,
            'width'     => $config->width,
            'height'    => $config->height,
        );
        $data['srcAddress'] = array(
            'country' => 'US',
            'zipcode' => '10001',
        );

        // Prepare several destination addresses
        $dstAddresses = array();
        $dstAddresses[] = array(
            'country' => 'US',
            'zipcode' => '90001',
        );
        $dstAddresses[] = array(
            'country' => 'CA',
            'zipcode' => 'V7P 1S0',
        );
        $dstAddresses[] = array(
            'country' => 'GB',
            'zipcode' => 'EC1A 1BB',
        );
        $dstAddresses[] = array(
            'country' => 'CN',
            'zipcode' => '100001',
        );

        foreach ($dstAddresses as $addr) {

            $data['dstAddress'] = $addr;

            // Get rates for each destination address.
            // All non-existing methods will be created after this
            $rates = $this->getProcessor()->getRates($data, true);
        }

        $this->setReturnURL(
            $this->buildURL('shipping_methods', null, array('processor' => 'usps'))
        );
    }
}
