<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Notifications items list
 */
class Notification extends \XLite\View\ItemsList\Model\Table
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
        return 'notifications';
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Returns CSS Files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'notifications/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME    => static::t('Name'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_LINK    => 'notification',
                static::COLUMN_ORDERBY => 100,
            ),
            'enabledForAdmin' => array(
                static::COLUMN_NAME    => static::t('Administrator'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\NotificationStatus',
                static::COLUMN_ORDERBY => 200,
            ),
            'enabledForCustomer' => array(
                static::COLUMN_NAME    => static::t('Customer'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\NotificationStatus',
                static::COLUMN_ORDERBY => 300,
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
        return 'XLite\Model\Notification';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' notifications';
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isTemplateColumnVisible($column, $entity);

        switch ($column[static::COLUMN_CODE]) {
            case 'enabledForAdmin':
                $result = $result && ($entity->getAvailableForAdmin() || $entity->getEnabledForAdmin());
                break;

            case 'enabledForCustomer':
                $result = $result && ($entity->getAvailableForCustomer() || $entity->getEnabledForCustomer());
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);

        switch ($column[static::COLUMN_CODE]) {
            case 'enabledForAdmin':
                $result = $result && ($entity->getAvailableForAdmin() || $entity->getEnabledForAdmin());
                break;

            case 'enabledForCustomer':
                $result = $result && ($entity->getAvailableForCustomer() || $entity->getEnabledForCustomer());
                break;

            default:
                break;
        }

        return $result;
    }
}