<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Product\Details\Customer\Page;

/**
 * Abstract product page 
 * 
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

        $list['Reviews'] = array(
            'list' => 'product.details.page.tab.reviews',
        );

        return $list;
    }
}
