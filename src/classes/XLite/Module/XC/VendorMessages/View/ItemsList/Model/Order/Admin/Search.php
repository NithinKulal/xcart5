<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Model\Order\Admin;

/**
 * Search order
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Widget param names
     */
    const PARAM_MESSAGES = \XLite\Model\Repo\Order::SEARCH_MESSAGES;

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        $list = parent::getSearchParams();
        $list[\XLite\Model\Repo\Order::SEARCH_MESSAGES] = static::PARAM_MESSAGES;

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_MESSAGES => new \XLite\Model\WidgetParam\TypeString('Messages condition', ''),
        );
    }


}
