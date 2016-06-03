<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping test page controller
 */
class ShippingTest extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Current shipping method
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $method;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $result = static::t('Test shipping rates');
        if ($this->getMethod()) {
            $result = $this->getMethod()->getName();
        }

        return $result;
    }

    /**
     * Returns shipping method
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    public function getMethod()
    {
        if (null === $this->method) {
            /** @var \XLite\Model\Repo\Shipping\Method $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
            $this->method = $repo->findOnlineCarrier($this->getProcessorId());
        }

        return $this->method;
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
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getMethod();
    }
}
