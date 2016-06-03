<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View;

/**
 * Order pin codes
 */
class OrderItemPinCodes extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/PINCodes/order/style.css';

        return $list;
    }


    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/PINCodes/order/order_item_pin_codes.twig';
    }
}
