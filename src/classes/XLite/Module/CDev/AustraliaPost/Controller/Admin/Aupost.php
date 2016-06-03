<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AustraliaPost\Controller\Admin;

/**
 * AustraliaPost shipping module settings controller
 */
class Aupost extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * getOptionsCategory
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'CDev\AustraliaPost';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|null
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\AustraliaPost\View\Model\Settings';
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \XLite\Module\CDev\AustraliaPost\Model\Shipping\Processor\AustraliaPost();
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
     * Renew settings allowed values
     *
     * @return void
     */
    protected function doActionRenewSettings()
    {
        $aupost = $this->getProcessor();

        $errors = $aupost->updateServiceData(true);

        if (!empty($errors)) {
            foreach ($errors as $key => $msg) {
                \XLite\Core\TopMessage::getInstance()->addWarning(
                    sprintf('Request "%s" has been failed: %s. Please retry later.', $key, $msg)
                );
            }

        } else {
            \XLite\Core\TopMessage::getInstance()->addInfo(
                static::t('Option values has been successfully updated.')
            );
        }

        $this->setReturnURL(\XLite\Core\Converter::buildURL($this->getTarget()));
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
            unset(
                $schema[$k]['city'],
                $schema[$k]['state']
            );
        }

        unset(
            $schema['dstAddress']['type'],
            $schema['cod_enabled']
        );

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
        $package = parent::getTestRatesData($schema, $errors);
        $package['from_postcode'] = $package['srcAddress']['zipcode'];
        $package['to_postcode']   = $package['dstAddress']['zipcode'];
        $package['country_code']  = $package['dstAddress']['country'];

        unset(
            $package['srcAddress'],
            $package['dstAddress']
        );

        return array(
            'packages' => array($package)
        );
    }
}
