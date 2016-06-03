<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin\Modifier;

/**
 * Shipping modifier widget
 */
class Shipping extends \XLite\View\Order\Details\Admin\Modifier
{
    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/parts/totals.modifier.shipping.twig';
    }
}
