<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;


interface IAction
{
    /**
     * Check if action should be executed.
     * Checked on executing
     *
     * @return bool
     */
    public function isApplicable();

    /**
     * Action data itself
     *
     * @return array
     */
    public function getActionData();
}