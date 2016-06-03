<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer\Page;

/**
 * Abstract product page
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        $result = parent::isLoupeVisible();
        $product = $this->getProduct();

        if (!$result && $product->hasVariants()) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image');

            return $repo->countByProduct($product);
        }

        return $result;
    }
}
