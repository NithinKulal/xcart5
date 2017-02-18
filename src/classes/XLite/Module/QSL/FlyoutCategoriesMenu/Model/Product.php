<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\Model;

/**
 * Category repository
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * @param integer $delta
     */
    public function changeAmount($delta)
    {
        if ($this->getInventoryEnabled()
            && $this->getAmount() > 0
            && 'directLink' === \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
            && \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num
        ) {
            parent::changeAmount($delta);

            if ($this->getAmount() === 0) {
                \XLite\Core\Database::getRepo('XLite\Model\Category')->bumpVersion();
            }
        } else {
            parent::changeAmount($delta);
        }
    }
}
