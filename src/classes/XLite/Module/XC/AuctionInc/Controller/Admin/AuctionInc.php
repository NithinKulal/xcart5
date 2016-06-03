<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Controller\Admin;

/**
 * AuctionInc shipping module settings controller
 */
class AuctionInc extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * Check if SS available
     *
     * @return boolean
     */
    public function isSSAvailable()
    {
        return \XLite\Module\XC\AuctionInc\Main::isSSAvailable();
    }

    /**
     * Check if XS available
     *
     * @return boolean
     */
    public function isXSAvailable()
    {
        return !$this->isSSAvailable()
            && \XLite\Module\XC\AuctionInc\Main::isXSTrialPeriodValid();
    }

    /**
     * Check if XS expired
     *
     * @return boolean
     */
    public function isXSExpired()
    {
        return !$this->isSSAvailable() && !\XLite\Module\XC\AuctionInc\Main::isXSTrialPeriodValid();
    }

    /**
     * Return XS days
     *
     * @return boolean
     */
    public function getXSDays()
    {
        $result = 0;

        if (\XLite\Module\XC\AuctionInc\Main::isXSTrialPeriodValid()) {
            $firstUsageDate = \XLite\Core\Config::getInstance()->XC->AuctionInc->firstUsageDate;

            $result = \XLite\Module\XC\AuctionInc\Main::TRIAL_PERIOD_DURATION - (LC_START_TIME - $firstUsageDate);

            $result = round($result / (60 * 60 * 24));
        }

        return $result;
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \XLite\Module\XC\AuctionInc\Model\Shipping\Processor\AuctionInc();
    }

    /**
     * Returns options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\AuctionInc';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\AuctionInc\View\Model\Settings';
    }

    /**
     * Get schema of an array for test rates routine
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $schema = parent::getTestRatesSchema();
        unset($schema['srcAddress']['city'], $schema['dstAddress']['city'], $schema['cod_enabled']);

        $schema['dimensions'] = 'dimensions';

        if (\XLite\Module\XC\AuctionInc\Main::isSSAvailable()) {
            unset($schema['srcAddress']);
        }

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
        list($data['length'], $data['width'], $data['height']) = $data['dimensions'];
        unset($data['dimensions']);

        return array(
            'package' => $data
        );
    }
}
