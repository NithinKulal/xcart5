<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping methods management page controller
 */
class ShippingMethods extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getMethod()
            ? static::t($this->getMethod()->getProcessorObject()->getProcessorName())
            : static::t('Shipping methods');
    }

    /**
     * Returns shipping method
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    public function getMethod()
    {
        /** @var \XLite\Model\Repo\Shipping\Method $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');

        return $repo->findOnlineCarrier($this->getProcessorId());
    }

    /**
     * Returns current processor id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return \XLite\Core\Request::getInstance()->processor;
    }

    /**
     * Returns current carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        $processorId = $this->getProcessorId();

        return $processorId && $processorId !== 'offline'
            ? $processorId
            : '';
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        \XLite\Core\Marketplace::getInstance()->updateShippingMethods();

        parent::run();
    }
}
