<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Select;

/**
 * QuoteType selector
 *
 */
class QuoteType extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_NON_CONTRACTED => static::t('Counter - will return the regular price paid by retail consumers'),
            \XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED     => static::t('Commercial - will return the contracted price between Canada Post and the contract holder'),
        );
    }
}
