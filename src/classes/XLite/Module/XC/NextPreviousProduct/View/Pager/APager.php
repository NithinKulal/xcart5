<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\View\Pager;

/**
 * Decorated APager
 */
abstract class APager extends \XLite\View\Pager\APager implements \XLite\Base\IDecorator
{
    /**
     * Public wrapper for getPageId()
     *
     * @return integer
     */
    public function getPageIdWrapper()
    {
        return $this->getPageId();
    }


} 