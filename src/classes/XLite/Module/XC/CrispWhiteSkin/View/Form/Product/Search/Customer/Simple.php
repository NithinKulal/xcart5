<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Form\Product\Search\Customer;

/**
 * Simple form for searching products widget
 */
abstract class Simple extends \XLite\View\Form\Product\Search\Customer\Simple implements \XLite\Base\IDecorator
{
    /**
     * Returns id attribute value for substring input field
     *
     * @return string
     */
    protected function getSearchSubstringInputId()
    {
        return $this->getUniqueId(parent::getSearchSubstringInputId());
    }
}
