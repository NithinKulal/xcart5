<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Moneybookers\View\Payment;

/**
 * Payment methods widget
 */
class Methods extends \XLite\View\ItemsList\Model\Payment implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Moneybookers/methods.css';

        return $list;
    }
}

