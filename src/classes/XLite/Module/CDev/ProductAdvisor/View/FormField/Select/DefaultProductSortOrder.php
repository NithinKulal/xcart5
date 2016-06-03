<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View\FormField\Select;

/**
 * Default products sort order selector
 */
class DefaultProductSortOrder extends \XLite\View\FormField\Select\DefaultProductSortOrder implements \XLite\Base\IDecorator
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = array();

        $options = parent::getDefaultOptions();

        $added = false;

        // Insert new option just after the default option
        foreach ($options as $key => $value) {
            $result[$key] = $value;
            if ('default' == $key) {
                $result['newest'] = static::t('Newest first');
                $added = true;
            }
        }

        if (!$added) {
            $result['newest'] = static::t('Newest first');
        }

        return $result;
    }
}
