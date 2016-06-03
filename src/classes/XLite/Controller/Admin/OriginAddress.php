<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping methods management page controller
 */
class OriginAddress extends \XLite\Controller\Admin\Settings
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Origin address');
    }

    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findOriginOptions();
    }
}
