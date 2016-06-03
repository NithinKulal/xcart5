<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Module;

/**
 * Abstract form for modules
 */
abstract class AModule extends \XLite\View\Form\AForm
{
    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array_merge(
            parent::getDefaultParams(),
            array(
                \XLite\View\Pager\Admin\Module\AModule::PARAM_CLEAR_PAGER => 1,
            )
        );
    }
}
