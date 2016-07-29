<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Form;

/**
 * Settings 
 */
class Settings extends \XLite\View\Form\Settings
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/XPaymentsConnector/settings/style.css';

        return $list;
    }

    /**
     * Get form parameters. Add hidden field for page 
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        $params += array(
            'page' => \XLite\Core\Request::getInstance()->page,
        );

        return $params;
    }
}
