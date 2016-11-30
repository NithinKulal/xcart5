<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\View\FormField\Select;

/**
 * Default products sort order selector
 */
abstract class DefaultProductSortOrder extends \XLite\View\FormField\Select\DefaultProductSortOrder implements \XLite\Base\IDecorator
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return parent::getDefaultOptions()
        + [
            'bought' => static::t('Sales'),
        ];
    }

}
