<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment\TransactionDataValue;

/**
 * Order items summary
 */
class CartItems extends \XLite\View\Payment\TransactionDataValue
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'payment/transaction_data_value/cart_items.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/transaction_data_value/cart_items.twig';
    }

    /**
     * Returns cell value
     *
     * @return mixed
     */
    protected function getValue()
    {
        $result = unserialize($this->getCell()->getValue());

        return !empty($result) && is_array($result) ? $result : array();
    }

    public function isVisible()
    {
        return parent::isVisible() && $this->getValue();
    }
}
