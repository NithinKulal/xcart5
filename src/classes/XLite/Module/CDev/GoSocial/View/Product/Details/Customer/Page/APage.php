<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Product\Details\Customer\Page;

/**
 * Abstract product page 
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (
            \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_comments_use
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id
        ) {
            $list['Comments'] = array(
                'list' => 'product.details.page.tab.comments',
            );
        }

        return $list;
    }
}
