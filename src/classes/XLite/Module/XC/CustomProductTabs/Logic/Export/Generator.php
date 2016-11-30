<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Export;

/**
 * Generator
 */
class Generator extends \XLite\Logic\Export\Generator implements \XLite\Base\IDecorator
{
    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array_merge(
            parent::defineSteps(),
            ['XLite\Module\XC\CustomProductTabs\Logic\Export\Step\CustomTabs']
        );
    }
}