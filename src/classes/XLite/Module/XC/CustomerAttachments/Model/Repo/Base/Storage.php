<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Model\Repo\Base;

/**
 * Abstract storage repository
 */
abstract class Storage extends \XLite\Model\Repo\Base\Storage implements \XLite\Base\IDecorator
{
    /**
     * Define all storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $list = parent::defineStorageRepositories();

        $list[] = 'XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment';

        return $list;
    }
}