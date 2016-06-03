<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Core;

/**
 * Layout manager
 *
 * @Decorator\Depend ("XC\Mobile")
 */
abstract class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Defines the Mobile skin specific JS files
     *
     * @param array $files
     *
     * @return array
     */
    protected function defineMobileJSResources($files)
    {
        $files = parent::defineMobileJSResources($files);

        if ('checkout' == \XLite\Core\Request::getInstance()->target) {
            $files[] = 'modules/XC/Stripe/payment.mobile.js';
            $files[] = array(
                'url' => 'https://checkout.stripe.com/checkout.js',
            );
        }

        return $files;
    }
}
