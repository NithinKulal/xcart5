<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\Model;

/**
 * Tabs related to shipping settings (USPS related pages)
 */
class Methods extends \XLite\View\Tabs\ShippingSettings implements \XLite\Base\IDecorator
{
    /**
     * Get list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ('usps' == \XLite\Core\Request::getInstance()->processor) {
            $list[] = 'modules/CDev/USPS/style.css';
        }

        return $list;
    }
}
