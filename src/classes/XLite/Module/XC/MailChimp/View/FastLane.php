<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View;

/**
 * Class FastLane
 * @Decorator\Depend ("XC\FastLaneCheckout")
 */
class FastLane extends \XLite\Module\XC\FastLaneCheckout\View\Sections\Payment implements \XLite\Base\IDecorator
{
    /**
     * Get js files list
     *
     * @return string[]
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                'modules/XC/MailChimp/place-order.js',
            )
        );
    }
}