<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping rates page controller
 */
class ShippingRates extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $method = $this->getModelForm()->getModelObject();

        return $method
            ? $method->getName()
            : static::t('Shipping rates');
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Shipping\Offline';
    }

    /**
     * Do action update
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        $itemsList = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $itemsList->processQuick();

        $this->setReturnURL(
            $this->buildURL(
                'shipping_rates',
                '',
                array(
                    'widget'       => 'XLite\View\Shipping\EditMethod',
                    'methodId'     => $this->getModelForm()->getModelObject()->getMethodId(),
                    'shippingZone' => \XLite\Core\Request::getInstance()->shippingZone,
                )
            )
        );

        $this->setInternalRedirect();
        \XLite\Core\Event::updateShippingMethods();
    }
}
