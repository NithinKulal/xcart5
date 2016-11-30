<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Model;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail lists
 */
class MailChimpLists extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        if (Core\MailChimp::isSelectBoxElement()) {
            $return[] = 'modules/XC/MailChimp/mailchimp_lists/radio.js';
        }

        return $return;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $return = array(
            'name'          => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_LINK     => 'mailchimp_list_segments',
                static::COLUMN_ORDERBY  => 200,
            ),
            'groups'          => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Groups'),
                static::COLUMN_LINK     => 'mailchimp_list_groups',
                static::COLUMN_ORDERBY  => 250,
            ),
            'date_created'  => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_ORDERBY  => 300,
            ),
            'list_rating'   => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('List rating'),
                static::COLUMN_ORDERBY  => 400,
            ),
            'member_count'  => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Members count'),
                static::COLUMN_ORDERBY  => 500,
            ),
            'open_rate'     => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Open rate'),
                static::COLUMN_TEMPLATE => 'modules/XC/MailChimp/mailchimp_lists/cell.percent_rate.twig',
                static::COLUMN_ORDERBY  => 600,
            ),
            'click_rate'    => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Click rate'),
                static::COLUMN_TEMPLATE => 'modules/XC/MailChimp/mailchimp_lists/cell.percent_rate.twig',
                static::COLUMN_ORDERBY  => 700,
            )
        );

        if (Core\MailChimp::isSelectBoxElement()) {
            $return['subscribe_by_default'] = array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Subscribe by default'),
                static::COLUMN_TEMPLATE => 'modules/XC/MailChimp/mailchimp_lists/cell.radio.twig',
                static::COLUMN_ORDERBY  => 800,
            );
        } else {
            $return['subscribe_by_default'] = array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Subscribe by default'),
                static::COLUMN_CLASS    => '\XLite\View\FormField\Inline\Input\Checkbox\Simple',
                static::COLUMN_ORDERBY  => 800,
            );
        }

        return $return;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Module\XC\MailChimp\Model\MailChimpList';
    }

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
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $entity
     *
     * @return mixed
     */
    protected function getGroupsColumnValue(\XLite\Module\XC\MailChimp\Model\MailChimpList $entity)
    {
        return static::t('Groups');
    }
    
    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return Core\MailChimp::getInstance()->hasRemovedMailChimpLists();
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $list)
    {
        return $list->getIsRemoved();
    }

    /**
     * Format percentage rate
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list   MailChimp list
     * @param array                                          $column Column
     *
     * @return string
     */
    protected function formatPercentRate(\XLite\Module\XC\MailChimp\Model\MailChimpList $list, array $column)
    {
        $return = round((float) $list->{$column[self::COLUMN_CODE]});

        return $return . '%';
    }

    /**
     * Get radio button name
     *
     * @return string
     */
    protected function getRadioName()
    {
        return 'default_list';
    }

    /**
     * Get radio button ID
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getRadioId(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return 'radio-default-' . $list->getId();
    }

    /**
     * Get checkbox button name
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getCheckBoxName(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return 'data[' . $list->getId() . '][subscribe_by_default]';
    }

    /**
     * Get checkbox button ID
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getCheckBoxId(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return 'checkbox-default-' . $list->getId();
    }

    /**
     * Get MailChimp list ID
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getListId(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return $list->getId();
    }

    /**
     * Get is default
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return boolean
     */
    protected function isDefaultList(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return $list->getSubscribeByDefault();
    }
}
