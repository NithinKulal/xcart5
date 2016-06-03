<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View;

/**
 * Category page
 */
abstract class Category extends \XLite\View\Category implements \XLite\Base\IDecorator
{
    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        $list[] = $this->getCategory()->getOpenGraphMetaTags();

        return $list;
    }
}
