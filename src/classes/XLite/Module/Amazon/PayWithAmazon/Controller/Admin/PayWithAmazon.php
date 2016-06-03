<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Admin;

/**
 * PayWithAmazon settings page controller
 */
class PayWithAmazon extends \XLite\Controller\Admin\Module
{
    /**
     * Gets the PayWithAmazon module id
     *
     * @return integer
     */
    public function getModuleId()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(array(
            'author'            => 'Amazon',
            'name'              => 'PayWithAmazon',
            'fromMarketplace'   => false,
        ))->getModuleID();
    }

}
