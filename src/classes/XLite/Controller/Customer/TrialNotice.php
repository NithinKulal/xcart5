<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Trial notice page controller
 *
 */
class TrialNotice extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Print widget used for iframe content
     */
    protected function doNoAction()
    {
        $widget = new \XLite\View\ModulesManager\TrialNotice();

        print $widget->getContent();

        $this->silent = true;
        $this->setSuppressOutput(true);
    }
}
