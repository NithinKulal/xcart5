<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\FormField\Input\Text;

/**
 * Zero-dollar auth description
 *
 */
class ZeroAuthDescription extends \XLite\View\FormField\Input\Text
{
    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return \XLite\Core\Translation::lbl('Description of the card setup payment');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'description';
    }

}
