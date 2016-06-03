<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\AverageRating;

/**
 * Average product rating widget
 *
 * @Decorator\Depend ("XC\ColorSchemes")
 */
class AverageRating extends \XLite\Module\XC\Reviews\View\AverageRating implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $dir = 'modules/XC/Reviews/ColorSchemes/' . \XLite\Core\Layout::getInstance()->getLayoutColor();

        $list[] = $dir . '/form_field/input/rating/rating.css';
        $list[] = $dir . '/average_rating/style.css';
        

        return $list;
    }
}
