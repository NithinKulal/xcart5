<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View;

/**
 * Abstract view
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if (\XLite::isAdminZone()) {
            $list[] = 'modules/XC/VendorMessages/style.css';
        }

        return $list;
    }
}
