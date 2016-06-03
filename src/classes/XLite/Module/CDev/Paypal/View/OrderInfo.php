<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Extend Order details page widget
 */
class OrderInfo extends \XLite\View\Order\Details\Admin\Info implements \XLite\Base\IDecorator
{
    /**
     * getCSSFiles 
     * 
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/order/style.css';

        return $list;
    }
}
