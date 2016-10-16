<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Module\XC\Add2CartPopup\Core;

/**
 * Add2CartPopup product sources class
 * @Decorator\Depend ("XC\Add2CartPopup")
 */
abstract class Add2CartPopup extends \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup implements \XLite\Base\IDecorator
{
    /**
     * Do not display add to cart popup on 'amazon_checkout' target
     *
     * @return array
     */
    protected static function getAdd2CartPopupExcludedTargets()
    {
        $targets = parent::getAdd2CartPopupExcludedTargets();
        $targets[] = 'amazon_checkout';

        return $targets;
    }
}
