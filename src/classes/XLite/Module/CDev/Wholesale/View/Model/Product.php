<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Model;

/**
 * Product view model
 */
class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Preparing data for price param
     *
     * @param array $data Field description
     *
     * @return array 
     */
    protected function prepareFieldParamsPrice($data) {
        if ('product' == \XLite\Core\Request::getInstance()->target) {
            $data[self::SCHEMA_LINK_HREF] = $this->buildURL(
                'product',
                '',
                array(
                    'product_id'    => $this->getProductId(),
                    'page'          => 'wholesale_pricing',
                )
            );
            $data[self::SCHEMA_LINK_TEXT]   = 'Wholesale pricing';
            $data[self::SCHEMA_LINK_IMG]    = \XLite\Core\Layout::getInstance()->getResourceWebPath('modules/CDev/Wholesale/wp.svg');
        }
        return $data;
    }
}
