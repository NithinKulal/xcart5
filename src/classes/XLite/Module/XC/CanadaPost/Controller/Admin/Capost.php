<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Admin;

/**
 * CanadaPost settings page controller
 */
class Capost extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * getOptionsCategory
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\CanadaPost';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|null
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\CanadaPost\View\Model\Settings';
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \XLite\Module\XC\CanadaPost\Model\Shipping\Processor\CanadaPost();
    }

    /**
     * Do action "Enable merchant registration wizard"
     *
     * @return void
     */
    protected function doActionEnableWizard()
    {
        $options = array(
            'customer_number' => '',
            'contract_id'     => '',
            'user'            => '',
            'password'        => '',
            'quote_type'      => \XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED,
            'wizard_hash'     => '',
            'wizard_enabled'  => true,
            'developer_mode'  => false,
        );

        /** @var \XLite\Model\Repo\Config $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

        foreach ($options as $name => $value) {
            $repo->createOption(
                array(
                    'category' => $this->getOptionsCategory(),
                    'name'     => $name,
                    'value'    => $value,
                )
            );
        }

        $this->setReturnURL($this->buildURL('capost'));
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
        $package = parent::getTestRatesData($schema, $errors);
        $package['srcAddress']['country'] = 'CA';

        $data = array(
            'packages' => array($package),
        );

        return $data;
    }
}
