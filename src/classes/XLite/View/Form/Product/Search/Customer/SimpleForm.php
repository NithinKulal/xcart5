<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Search\Customer;

/**
 * Simple form
 */
class SimpleForm extends \XLite\View\Form\Product\Search\Customer\Main
{
    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'true';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array(
            \XLite\View\ItemsList\Product\Customer\Search::PARAM_INCLUDING => \XLite\Model\Repo\Product::INCLUDING_ANY,
        );
    }
}
