<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin;

/**
 * All admin messages
 */
class All extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\All
{

    /**
     * Widget param names
     */
    const PARAM_SEARCH_MESSAGES          = 'messages';
    const PARAM_SEARCH_MESSAGE_SUBSTRING = 'messageSubstring';

    /**
     * @inheritdoc
     */
    static public function getSearchParams()
    {
        return parent::getSearchParams() + array(
            \XLite\Model\Repo\Order::SEARCH_MESSAGES          => static::PARAM_SEARCH_MESSAGES,
            \XLite\Model\Repo\Order::SEARCH_MESSAGE_SUBSTRING => static::PARAM_SEARCH_MESSAGE_SUBSTRING,
        );
    }

    /**
     * @inheritdoc
     */
    protected function isHeadVisible()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SEARCH_MESSAGES          => new \XLite\Model\WidgetParam\TypeString('Messages type', ''),
            static::PARAM_SEARCH_MESSAGE_SUBSTRING => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
        );
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\XC\VendorMessages\View\Pager\Message\Admin\All';
    }

    /**
     * Check - marks conversation marks visible or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isThreadsMultiple(\XLite\Model\Order $order)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_SEARCH_MESSAGES;
        $this->requestParams[] = static::PARAM_SEARCH_MESSAGE_SUBSTRING;
    }

}
