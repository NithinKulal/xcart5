<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Settings dialog model widget
 */
abstract class AShippingSettings extends \XLite\View\Model\Settings
{
    const FIELD_CARRIER_SERVICE = 'carrierService';

    /**
     * Single service
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $singleService;

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'model/shipping_settings.js';

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
        $list[] = 'model/shipping_settings.css';

        return $list;
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFields()
    {
        $list = array();

        if ($this->hasSingleService()) {
            $list[static::FIELD_CARRIER_SERVICE] = array(
                static::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                static::SCHEMA_LABEL    => static::t('Carrier service name'),
            );
        }

        return $list + parent::getSchemaFields();
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        return static::FIELD_CARRIER_SERVICE === $name && $this->hasSingleService()
            ? $this->getSingleService()->getName()
            : parent::getModelObjectValue($name);
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        parent::setModelProperties($data);

        if (isset($data[static::FIELD_CARRIER_SERVICE]) && $this->hasSingleService()) {
            $carrierService = $this->getSingleService();
            $carrierService->setName($data[static::FIELD_CARRIER_SERVICE]);

            $carrierService->update();
        }
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            )
        );

        return $result;
    }

    /**
     * Returns processor id
     *
     * @return string
     */
    protected function getProcessorId()
    {
        return \XLite::getController()->getProcessorId();
    }

    /**
     * Returns shipping method
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return \XLite::getController()->getMethod();
    }

    /**
     * Returns single service
     *
     * @return boolean|\XLite\Model\Shipping\Method
     */
    protected function getSingleService()
    {
        if (null === $this->singleService) {
            $this->singleService = false;

            $method = $this->getMethod();
            $carrierServices = $method->getChildrenMethods();
            if (count($carrierServices) === 1) {
                $this->singleService = $carrierServices[0];
            }
        }

        return $this->singleService;
    }

    /**
     * Check if carrier has single service
     *
     * @return boolean
     */
    protected function hasSingleService()
    {
        return (bool) $this->getSingleService();
    }

    /**
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsible($section)
    {
        return $this->isShowSectionHeader($section);
    }

    /**
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsed($section)
    {
        return $this->isShowSectionHeader($section);
    }
}
