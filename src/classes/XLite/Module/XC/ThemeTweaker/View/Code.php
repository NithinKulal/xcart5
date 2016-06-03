<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Code widget
 */
class Code extends \XLite\Module\XC\ThemeTweaker\View\Custom
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir(). '/code';
    }

    /**
     * Code is used or not
     *
     * @return boolean
     */
    protected function isUsed()
    {
        return \XLite\Core\Config::getInstance()->XC->ThemeTweaker->{'use_' . \XLite\Core\Request::getInstance()->target};
    }

    /**
     * Return custom text
     *
     * @return boolean
     */
    protected function getUseCustomText()
    {
        return 'custom_css' == \XLite\Core\Request::getInstance()->target
            ? static::t('Use custom css')
            : static::t('Use custom js');
    }
}
