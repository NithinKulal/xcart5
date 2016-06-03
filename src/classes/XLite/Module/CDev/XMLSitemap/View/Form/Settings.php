<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\View\Form;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'model/form/content.twig';
    }
}
