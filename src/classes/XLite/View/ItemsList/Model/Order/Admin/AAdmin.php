<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Order\Admin;

/**
 * Abstract admin order-based list
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Order\AOrder
{
    /**
     * Action information cell names
     */
    const ACTION_NAME     = 'name';
    const ACTION_URL      = 'url';
    const ACTION_ACTION   = 'action';
    const ACTION_CLASS    = 'class';
    const ACTION_TEMPLATE = 'template';
    const ACTION_PARAMS   = 'parameters';

    /**
     * Get order actions
     *
     * @param \XLite\Model\Order $entity Order
     *
     * @return array
     */
    protected function getOrderActions(\XLite\Model\Order $entity)
    {
        $list = array();

        foreach ($this->defineOrderActions($entity) as $action) {
            $parameters = empty($action[static::ACTION_PARAMS]) || !is_array($action[static::ACTION_PARAMS])
                ? array()
                : $action[static::ACTION_PARAMS];
            $parameters['entity'] = $entity;

            // Build URL
            if (!empty($action[static::ACTION_ACTION]) && empty($action[static::ACTION_URL])) {
                $action[static::ACTION_URL] = \XLite\Core\Converter::buildURL(
                    'order',
                    $action[static::ACTION_ACTION],
                    array('order_number' => $entity->getOrderNumber())
                );
            }

            if (!isset($action[static::ACTION_CLASS]) && !isset($action[static::ACTION_TEMPLATE])) {

                // Define widget as link-button
                $action[static::ACTION_CLASS] = 'XLite\View\Button\Link';
                $parameters['label'] = $action[static::ACTION_NAME];
                $parameters['location'] = $action[static::ACTION_URL];

            } elseif (!empty($action[static::ACTION_CLASS])) {

                // Prepare widget parameters
                if (!empty($action[static::ACTION_URL])) {
                    $parameters['url'] = $action[static::ACTION_URL];
                }

                if (!empty($action[static::ACTION_ACTION])) {
                    $parameters['action'] = $action[static::ACTION_ACTION];
                }
            }

            if (!empty($action[static::ACTION_TEMPLATE])) {
                $parameters['template'] = $action[static::ACTION_TEMPLATE];
            }

            $list[] = empty($action[static::ACTION_CLASS])
                ? $this->getWidget($parameters)
                : $this->getWidget($parameters, $action[static::ACTION_CLASS]);
        }

        return $list;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.order.blank');
    }

    /**
     * Returns true if order has allowed backend payment transactions
     *
     * @param \XLite\Model\Order $entity Order
     *
     * @return boolean
     */
    protected function hasPaymentActions(\XLite\Model\Order $entity)
    {
        $result = \Includes\Utils\ArrayManager::filterByKeys(
            $entity->getAllowedPaymentActions(),
            $this->getTransactionsFilter()
        );
        return !empty($result);
    }

    /**
     * Get list of transaction types to filter allowed backend transactions list
     *
     * @return void
     */
    protected function getTransactionsFilter()
    {
        return array('capture');
    }

    /**
     * Define order actions
     *
     * @param \XLite\Model\Order $entity Order
     *
     * @return array
     */
    protected function defineOrderActions(\XLite\Model\Order $entity)
    {
        $list = array();

        foreach ($entity->getAllowedActions() as $action) {
            $list[] = array(
                static::ACTION_ACTION => $action,
            );
        }

        return $list;
    }
    
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }
}
