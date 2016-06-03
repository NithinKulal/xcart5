<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Submit button
 */
class Submit extends \XLite\View\Button\AButton
{
    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();
        $list['type'] = 'submit';

        return $list;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Submit';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . $this->getSubmitClass();
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getSubmitClass()
    {
        return ' submit';
    }
}
