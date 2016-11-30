<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Form;

/**
 * Settings dialog form
 */
class Settings extends \XLite\View\Form\Settings
{
    /**
     * Get form parameters. Add hidden field for section
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        $params += array(
            'section' => \XLite\Core\Request::getInstance()->section,
        );

        return $params;
    }
}
