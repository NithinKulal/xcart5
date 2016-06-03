<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Product;

/**
 * File attachments list for customer interface
 */
abstract class Customer extends \XLite\Module\CDev\FileAttachments\View\Product\Customer implements \XLite\Base\IDecorator
{
    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        $list = parent::getAttachments();

        foreach ($list as $i => $attachment) {
            if ($attachment->getPrivate()) {
                unset($list[$i]);
            }
        }

        return array_values($list);
    }

}

