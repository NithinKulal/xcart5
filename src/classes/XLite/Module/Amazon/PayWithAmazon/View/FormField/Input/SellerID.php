<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\FormField\Input;

/**
 * Seller ID field with registration link
 */
class SellerID extends \XLite\View\FormField\Input\Text
{
    protected function getFieldTemplate()
    {
        return '../modules/Amazon/PayWithAmazon/sellerid.twig';
    }
}
