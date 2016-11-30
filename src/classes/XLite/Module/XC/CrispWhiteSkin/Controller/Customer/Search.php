<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

/**
 * Products search
 */
class Search extends \XLite\Controller\Customer\Search implements \XLite\Base\IDecorator
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getMainTitle()
    {
        return null;
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        return $this->getTitle();
    }
}