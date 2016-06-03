<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Modify\Base;

/**
 * Details (abstract)
 */
abstract class Single extends \XLite\View\Form\Product\Modify\AModify
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array('product_id' => $this->getProductId());
    }
}
