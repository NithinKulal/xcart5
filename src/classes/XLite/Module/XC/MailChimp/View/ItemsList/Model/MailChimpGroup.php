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
class MailChimpGroup extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_list_groups';

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
        return 'mailchimp_list_groups';
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
                'id' => \XLite\Core\Request::getInstance()->id,
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
            'title'          => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Title'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_LINK     => 'mailchimp_list_interests',
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
        return '\XLite\Module\XC\MailChimp\Model\MailChimpGroup';
    }

    /**
     * @inheritdoc
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return isset($column[static::COLUMN_LINK]) && $column[static::COLUMN_LINK] === 'mailchimp_list_interests'
            ? \XLite\Core\Converter::buildURL(
                $column[static::COLUMN_LINK],
                '',
                [
                    'group_id'  => $entity->getUniqueIdentifier(),
                    'id'        => \XLite\Core\Request::getInstance()->id,
                ]
            )
            : parent::buildEntityURL($entity, $column);
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
        $cnd->parentList = TypeEquality::create(
            'list',
            \XLite\Core\Request::getInstance()->id
        );

        return parent::getData($cnd, $countOnly);
    }
}
