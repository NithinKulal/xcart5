<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Controller\Admin;

/**
 * Pilibaba settings page controller
 */
class PilibabaSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Pilibaba settings');
    }
}
