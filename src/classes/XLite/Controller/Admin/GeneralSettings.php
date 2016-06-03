<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * General settings
 */
class GeneralSettings extends \XLite\Controller\Admin\Settings
{
    /**
     * Page
     *
     * @var string
     */
    public $page = self::GENERAL_PAGE;
    
    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\GeneralSettings';
    }    
}
