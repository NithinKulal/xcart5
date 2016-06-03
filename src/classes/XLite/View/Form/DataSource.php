<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Ecwid data source form
 */
class DataSource extends \XLite\View\Form\AForm
{
    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'test';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        $class = parent::getDefaultClassName();

        $class .= ($class ? ' ' : '') . 'validationEngine';

        return $class;
    }
}
