<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Order\Status;

/**
 * Abstract order status repository 
 */
class AStatus extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code'),
    );
}
