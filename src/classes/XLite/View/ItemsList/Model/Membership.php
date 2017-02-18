<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Memberships items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Membership extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('memberships'));
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getController()->isCalculationNotFinished();
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.membership.blank');
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
        return 'memberships';
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
                static::COLUMN_CLASS  => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => array('required' => true),
                static::COLUMN_MAIN   => true,
                static::COLUMN_NAME   => static::t('Membership name'),
                static::COLUMN_ORDERBY  => 100,
            ),
            'users' => array(
                static::COLUMN_NAME   => static::t('Users'),
                static::COLUMN_LINK   => 'profile_list',
                static::COLUMN_ORDERBY  => 200,
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
        return 'XLite\Model\Membership';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('membership');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New membership';
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
     * Preprocess users column
     *
     * @param mixed                   $value  Column value
     * @param array                   $column Column data
     * @param \XLite\Model\Membership $entity Order
     *
     * @return string
     */
    protected function preprocessUsers($value, array $column, \XLite\Model\Membership $entity)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP} = $entity;

        return \XLite\Core\Database::getRepo('\XLite\Model\Profile')->search($cnd, true);
    }


    // {{{ Behaviors

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

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
        return parent::getContainerClass() . ' memberships';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\Membership';
    }


    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    // }}}

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        switch ($column[static::COLUMN_LINK]) {

            case 'profile_list':
                $result = \XLite\Core\Converter::buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    array('membership' => $entity->getMembershipId())
                );
                break;

            default:
                $result = parent::buildEntityURL($entity, $column);
                break;

        }

        return $result;
    }
}
