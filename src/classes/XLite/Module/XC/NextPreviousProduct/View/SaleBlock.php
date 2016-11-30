<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\View;

/**
 * Decorate SaleBlock
 *
 * @Decorator\Depend("CDev\Sale")
 */
class SaleBlock extends \XLite\Module\CDev\Sale\View\SaleBlock implements \XLite\Base\IDecorator
{
    /**
     * Returns search products conditions
     *
     * @param \XLite\Core\CommonCell $cnd Initial search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions(\XLite\Core\CommonCell $cnd)
    {
        $cnd = parent::getSearchConditions($cnd);

        if ($this->getCategoryId() && 'product' === $this->getTarget()) {
            $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();
        }

        return $cnd;
    }
}
