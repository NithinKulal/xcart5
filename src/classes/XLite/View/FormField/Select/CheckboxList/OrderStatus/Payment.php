<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList\OrderStatus;

/**
 * Payment order status selector
 */
class Payment extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Order\Status\Payment')->findBy(array(), array('position' => 'asc')) as $status) {
            $list[$status->getId()] = $status->getName();
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All payment statuses');

        return $list;
    }

}
