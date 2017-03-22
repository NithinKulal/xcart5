<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\FormField\Input;

/**
 * Rating field (rate product via stars)
 *
 * @Decorator\Depend ("XC\ColorSchemes")
 */
class ColorSchemesRating extends \XLite\Module\XC\Reviews\View\FormField\Input\Rating implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/ColorSchemes/'
            . \XLite\Core\Layout::getInstance()->getLayoutColor()
            . '/form_field/input/rating/rating.css';

        return $list;
    }
}
