<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View;


class CacheManagementActions extends \XLite\View\ItemsList\Model\CacheManagementActions implements \XLite\Base\IDecorator
{
    
    /**
     * @inheritDoc
     */
    protected function getData()
    {
        $data = parent::getData();

        if (\XLite\Core\Config::getInstance()->XC->ProductFilter->attributes_filter_by_category
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->attributes_filter_cache_mode
        ) {
            $data[] = [
                'name'        => static::t('Remove product filter cache'),
                'description' => static::t('Remove product filter cache tooltip'),
                'view'        => '\XLite\View\Button\SimpleLink',
                'viewParams'  => [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Start'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'btn always-enabled regular-button',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cache_management', 'remove_product_filter_cache'),
                ],
            ];
        }

        return $data;
    }
}
