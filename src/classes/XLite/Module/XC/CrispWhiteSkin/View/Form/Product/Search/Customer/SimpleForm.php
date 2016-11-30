<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Form\Product\Search\Customer;

/**
 * Simple form
 */
class SimpleForm extends \XLite\View\Form\Product\Search\Customer\SimpleForm implements \XLite\Base\IDecorator
{
    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array_merge(parent::getDefaultParams(), [
            \XLite\View\ItemsList\Product\Customer\Search::PARAM_INCLUDING => \XLite\Model\Repo\Product::INCLUDING_ALL,
        ]);
    }
}