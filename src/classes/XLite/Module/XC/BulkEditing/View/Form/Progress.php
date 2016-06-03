<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Form;

/**
 * Image resize form
 */
class Progress extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'bulk_edit';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'cancel';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        return array_replace(
            parent::getCommonFormParams(),
            [
                'scenario' => \XLite::getController()->getCurrentScenario(),
            ]
        );
    }
}
