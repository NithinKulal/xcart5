<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * Controller widget extension
 */
class Controller extends \XLite\View\Controller implements \XLite\Base\IDecorator
{
    /**
     * Return common data to send to JS
     *
     * @return array
     */
    protected function getCommonJSData()
    {
        $data = parent::getCommonJSData();

        $data['a2cp_enable_for_dropping'] = \XLite\Core\Config::getInstance()->XC->Add2CartPopup->a2cp_enable_for_dropping;

        return $data;
    }
}
