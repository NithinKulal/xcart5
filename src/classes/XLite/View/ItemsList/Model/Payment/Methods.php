<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Payment;

/**
 * Methods items list
 */
class Methods extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get payment methods list
     * N.B. Not sure if needed
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findAllMethods();

        foreach ($list as $i => $method) {
            if (!$method->getProcessor()) {
                unset($list[$i]);
            }
        }

        return $list;
    }

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
        return 'payment_appearance';
    }


    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'payment/appearance/style.css';

        return $list;
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'title' => array(
                static::COLUMN_NAME      => static::t('Title'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_TEMPLATE  => 'items_list/model/table/payment/cell.name.twig',
                static::COLUMN_PARAMS    => array('required' => true),
                static::COLUMN_ORDERBY   => 200,
                static::COLUMN_EDIT_ONLY => true,
            ),
            'description' => array(
                static::COLUMN_NAME    => static::t('Description'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS  => array(
                    'required' => false,
                    \XLite\View\FormField\Inline\AInline::PARAM_VIEW_FULL_WIDTH => true,
                ),
                static::COLUMN_ORDERBY  => 300,
                static::COLUMN_EDIT_ONLY => true,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Payment\Method';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
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
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return $column[static::COLUMN_CODE] === 'title' || parent::isClassColumnVisible($column, $entity);
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' payment-methods';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $result->{\XLite\Model\Repo\Payment\Method::P_ADDED} = true;
        $result->{\XLite\Model\Repo\Payment\Method::P_POSITION} = array(
            \XLite\Model\Repo\Payment\Method::FIELD_DEFAULT_POSITION,
            static::SORT_ORDER_ASC,
        );

        return $result;
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return parent::getEmptyListDir() . '/payment/appearance';
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $result = parent::defineLineClass($index, $entity);

        if (!$entity->getEnabled()) {
            $result[] = 'disabled-method';
        }

        return $result;
    }

    /**
     * Get hint text for entity status
     *
     * @param \XLite\Model\AEntity $entity Line model
     *
     * @return string
     */
    protected function getMethodStatusTitle(\XLite\Model\AEntity $entity)
    {
        return $entity->getEnabled()
            ? static::t('Payment method is enabled')
            : static::t('Payment method is disabled');
    }
}
