<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Banner rotation configuration form model
 */
class BannerRotation extends \XLite\View\Model\Settings
{
    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\BannerRotation';
    }
}
