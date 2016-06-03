<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View;

/**
 * Main page page
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
abstract class Page extends \XLite\Module\CDev\SimpleCMS\View\CustomerPage implements \XLite\Base\IDecorator
{
    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        $list[] = $this->getPage()->getOpenGraphMetaTags();

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/GoSocial/product.css';

        return $list;
    }
}
