<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View\FormField\Select;

/**
 * Mark products selector
 */
class MarkProducts extends \XLite\View\FormField\Select\Regular
{
    const PARAM_MARK_NOWHERE = 'N';
    const PARAM_MARK_IN_CATALOG_ONLY = 'C';
    const PARAM_MARK_IN_CATALOG_AND_PRODUCT_PAGES = 'CP';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::PARAM_MARK_NOWHERE                      => static::t('Don\'t label'),
            static::PARAM_MARK_IN_CATALOG_ONLY              => static::t('In catalog only'),
            static::PARAM_MARK_IN_CATALOG_AND_PRODUCT_PAGES => static::t('On catalog and product pages'),
        );
    }

    /**
     * @param $value string option value
     *
     * @return boolean
     */
    public static function isCatalogEnabled($value)
    {
        return in_array($value, [
            static::PARAM_MARK_IN_CATALOG_ONLY,
            static::PARAM_MARK_IN_CATALOG_AND_PRODUCT_PAGES,
        ]);
    }
    
    /**
     * @param $value string option value
     *
     * @return boolean
     */
    public static function isProductPageEnabled($value)
    {
        return $value === static::PARAM_MARK_IN_CATALOG_AND_PRODUCT_PAGES;
    }
}