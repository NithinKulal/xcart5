<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Model;

/**
 * The Product model repository
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Flag to exporting entities (no need setters and getters)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xcPendingBulkEdit = false;
}
