<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * \XLite\View\TestEmail
 */
class TestEmail extends \XLite\View\Dialog
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'test_email';
    }

}
