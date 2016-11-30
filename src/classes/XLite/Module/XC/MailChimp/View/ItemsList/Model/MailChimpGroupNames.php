<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Model;
use XLite\Model\SearchCondition\Expression\TypeEquality;

/**
 * MailChimp mail group lists
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class MailChimpGroupNames extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_list_interests';

        return $return;
    }

    /**
     * @inheritDoc
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
        return 'mailchimp_list_interests';
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
                'group_id' => \XLite\Core\Request::getInstance()->group_id,
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
            'name'          => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 200,
            ),
            'subscriber_count'    => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Members count'),
                static::COLUMN_ORDERBY  => 300,
            ),
            'subscribe_by_default'    => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Subscribe by default'),
                static::COLUMN_CLASS    => '\XLite\View\FormField\Inline\Input\Checkbox\Simple',
                static::COLUMN_ORDERBY  => 400,
            )
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Module\XC\MailChimp\Model\MailChimpGroupName';
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
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->parentGroup = TypeEquality::create(
            'group',
            \XLite\Core\Request::getInstance()->group_id
        );

        return parent::getData($cnd, $countOnly);
    }
}
