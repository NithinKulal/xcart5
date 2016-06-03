<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Sitemap\View\Menu\Customer;

/**
 * Footer
 *
 * @Decorator\Depend ("!CDev\SimpleCMS")
 */
class Footer extends \XLite\View\Menu\Customer\Footer implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['map'] = array(
            'label'      => static::t('Sitemap'),
            'url'        => \XLite\Core\Converter::buildURL('map'),
            'controller' => '\XLite\Module\XC\Sitemap\Controller\Customer\Map',
        );

        return $items;
    }
}

