<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;


interface IBackendAction
{
    /**
     * Check if action should be executed.
     * Checked on executing
     *
     * @return bool
     */
    public function isBackendApplicable();

    /**
     * Action data in backend format
     *
     * @return array
     */
    public function getActionDataForBackend();
}