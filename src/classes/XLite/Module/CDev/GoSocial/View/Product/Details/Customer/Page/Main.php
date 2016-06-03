<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Product\Details\Customer\Page;

/**
 * Main product page
 */
abstract class Main extends \XLite\View\Product\Details\Customer\Page\Main implements \XLite\Base\IDecorator
{
    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        $list[] = $this->getProduct()->getOpenGraphMetaTags();

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->gplus_use
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->gplus_page_id
        ) {
            $list[] = sprintf(
                '<link href="https://plus.google.com/%s" rel="publisher" />',
                \XLite\Core\Config::getInstance()->CDev->GoSocial->gplus_page_id
            );
        }

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

