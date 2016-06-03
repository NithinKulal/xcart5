<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer\Page;

abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Get product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return [];
    }
}
