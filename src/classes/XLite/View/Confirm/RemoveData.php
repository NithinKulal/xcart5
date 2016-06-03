<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Confirm;

/**
 * Confirmation with password widget
 */
class RemoveData extends \XLite\View\Confirm\WithPassword
{
    /**
     * getDefaultTemplate
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'page/remove_data/confirm.twig';
    }
}
