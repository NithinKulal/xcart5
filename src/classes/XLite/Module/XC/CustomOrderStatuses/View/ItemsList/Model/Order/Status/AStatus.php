<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

/**
 * Order status items list
 */
abstract class AStatus extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'order_statuses';
    }


    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'page' => $this->getPage(),
            )
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array (
                static::COLUMN_NAME => static::t('Name'),
                static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => array('required' => true),
                static::COLUMN_ORDERBY  => 100,
            ),
            'orders_count' => array(
                static::COLUMN_NAME => static::t('Orders'),
                static::COLUMN_TEMPLATE => 'modules/XC/CustomOrderStatuses/statuses/orders_count.twig',
                static::COLUMN_ORDERBY  => 200,
            ),
        );
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity)
            && !$entity->getCode()
            && !$this->getOrdersCount($entity);
    }

    /**
     * Return orders count
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return integer
     */
    protected function getOrdersCount(\XLite\Model\AEntity $entity)
    {
        if (null === $this->ordersCount) {
            $this->ordersCount = \XLite\Core\Database::getRepo('\XLite\Model\Order')->countByStatus($this->getPage());
        }

        return isset($this->ordersCount[$entity->getId()])
            ? $this->ordersCount[$entity->getId()]
            : 0;
    }

    /**
     * Return orders link
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getOrdersLink(\XLite\Model\AEntity $entity)
    {
        return $this->buildURL(
            'order_list',
            'search',
            array(
                $this->getPage() . 'Status' => array($entity->getId()),
                \XLite::FORM_ID             => \XLite::getFormId()
            )
        );
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add status';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CustomOrderStatuses/statuses/style.css';

        return $list;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' order_statuses';
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array_merge(
            parent::getRightActions(),
            array('modules/XC/CustomOrderStatuses/statuses/tooltip.twig')
        );
    }
}