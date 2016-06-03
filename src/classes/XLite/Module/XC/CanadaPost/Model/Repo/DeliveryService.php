<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Repo;

/**
 * Class represents a Canada Post delivery service repository
 */
class DeliveryService extends \XLite\Model\Repo\ARepo
{
    // {{{ Get delivery service model methods

    /**
     * Get service model
     *
     * @param string $code        Delivery service code
     * @param string $countryCode Country code (OPTIONAL)
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    public function getServiceByCode($code, $countryCode = '')
    {
        $service = $this->findOneBy(array('code' => $code, 'countryCode' => $countryCode));

        if (
            isset($service)
            && $service->isExpired()
        ) {
            // Remove expired service
            // TODO: model should be updated not simply replaced
            \XLite\Core\Database::getEM()->remove($service);
            \XLite\Core\Database::getEM()->flush();

            $service = null;
        }

        if (!isset($service)) {
            // Send request to Canada Post server
            $service = $this->collectServiceData($code, $countryCode);
        }

        return $service;
    }

    /**
     * Retrieve and save delivery service data from the Canada Post server
     *
     * @param string $code        Delivery service code
     * @param string $countryCode Country code (OPTIONAL)
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    protected function collectServiceData($code, $countryCode = '')
    {
        // Get data from the Canada Post server
        $data = \XLite\Module\XC\CanadaPost\Core\Service\Rating::getInstance()
            ->callGetServiceByCode($code, $countryCode);

        $service = null;

        if (isset($data->service)) {

            // Save data into the DB
            $service = $this->saveServiceData($data, $countryCode);

        } else if ($data->errors) {

            // TODO: add logging here

        }

        return $service;
    }

    /**
     * Save delivery service data
     *
     * @param \XLite\Core\CommonCell $data        Service data
     * @param string                 $countryCode Country code which were used during the call to server (OPTIONAL)
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    protected function saveServiceData($data, $countryCode = '')
    {
        $service = new \XLite\Module\XC\CanadaPost\Model\DeliveryService();

        \XLite\Core\Database::getEM()->persist($service);

        // Set general delivery service data
        $service->setCode($data->service->serviceCode);
        $service->setCountryCode($countryCode);
        $service->setName($data->service->serviceName);

        // Set weight restrictions data
        $service->setMaxWeight($data->service->restrictions->weightRestriction->attrs->max);
        $service->setMinWeight($data->service->restrictions->weightRestriction->attrs->min);

        // Set dimensional restrictions (length, width, height)
        $dimRestrictions = $data->service->restrictions->dimensionalRestrictions;

        foreach (array('length', 'width', 'height') as $k => $v) {
            $service->{'setMax' . ucfirst($v)}(doubleval($dimRestrictions->{$v}->attrs->max));
            $service->{'setMin' . ucfirst($v)}(doubleval($dimRestrictions->{$v}->attrs->min));
        }

        // Set optional dimensional restrictions (length-plus-girth-max, length-height-width-sum-max, oversize-limit)
        foreach (array('lengthPlusGirthMax', 'lengthHeightWidthSumMax', 'oversizeLimit') as $k => $v) {
            if (isset($dimRestrictions->{$v})) {
                $service->{'set' . ucfirst($v)}($dimRestrictions->{$v});
            }
        }

        // Set other restrictions
        if (isset($data->service->restrictions->densityFactor)) {
            $service->setDensityFactor($data->service->restrictions->densityFactor);
        }

        $service->setCanShipInMailingTube($data->service->restrictions->canShipInMailingTube);
        $service->setCanShipUnpackaged($data->service->restrictions->canShipUnpackaged);
        $service->setAllowedAsReturnService($data->service->restrictions->allowedAsReturnService);

        // Set options which are available/applicable to this service
        foreach ($data->service->options as $_option) {

            $option = new \XLite\Module\XC\CanadaPost\Model\DeliveryService\Option();

            $service->addOption($option);

            \XLite\Core\Database::getEM()->persist($option);

            $option->setCode($_option->optionCode);
            $option->setName($_option->optionName);
            $option->setMandatory($_option->mandatory);
            $option->setQualifierRequired($_option->qualifierRequired);
            $option->setQualifierMax($_option->qualifierMax);
        }

        $this->flushChanges();

        return $service;
    }

    // }}}
}
