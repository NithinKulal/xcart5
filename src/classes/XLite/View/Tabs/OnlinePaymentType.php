<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to shipping settings
 *
 * @ListChild (list="add_payment", zone="admin", weight="10")
 */
class OnlinePaymentType extends \XLite\View\Tabs\AJsTabs
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return array(
            'payment_gateways' => array(
                'weight'    => 100,
                'title'     => 'Payment gateways',
                'widget'    => '\XLite\View\ItemsList\Model\Payment\OnlineMethods',
            ),
            'all_in_one_solutions' => array(
                'weight'   => 200,
                'title'    => 'PayPal all-in-one solutions',
                'template' => 'payment/add_method/parts/all_in_one_solutions.twig',
            ),
        );
    }
}
