<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormField\Input\Checkbox;

/**
 * Highlighted
 *
 */
class Highlighted extends \XLite\View\FormField\Input\Checkbox\Simple
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/PINCodes/highlighted_checkbox.css';

        return $list;
    }

    /**
     * getWrapperClass
     *
     * @return array
     */
    public function getWrapperClass()
    {
        $class = parent::getWrapperClass();
        $class .= $this->getValue() ? ' checked' : '';
        
        return $class;
    }   
}
