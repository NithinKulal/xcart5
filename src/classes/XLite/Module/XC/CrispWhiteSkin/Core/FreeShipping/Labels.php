<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Core\FreeShipping;

/**
 * Class to collect labels for displaying in items list
 *
 * @Decorator\Depend("XC\FreeShipping")
 */
class Labels extends \XLite\Module\XC\FreeShipping\Core\Labels implements \XLite\Base\IDecorator
{
    /**
     * Get content of Free shipping label
     *
     * @return array
     */
    protected static function getLabelContent()
    {
        return array(
            'blue free-shipping' => \XLite\Core\Translation::getInstance()->translate('Free shipping'),
        );
    }
}
