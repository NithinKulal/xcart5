<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Model;

/**
 * MailChimp mail lists
 */
class MailChimpSegments extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_list_segments';

        return $return;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'id'            => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('ID'),
                static::COLUMN_ORDERBY  => 100,
            ),
            'name'          => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_LINK     => 'mailchimp_segment',
                static::COLUMN_ORDERBY  => 200,
            ),
            'static'        => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Type'),
                static::COLUMN_ORDERBY  => 300,
            ),
            'created_date'  => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Date'),
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
        return '\XLite\Module\XC\MailChimp\Model\MailChimpSegment';
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
        $cnd->{\XLite\Module\XC\MailChimp\Model\Repo\MailChimpSegment::S_LIST}
            = \XLite\Core\Request::getInstance()->id;

        return parent::getData($cnd, $countOnly);
    }

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        $return = parent::isLink($column, $entity);

        return $return && $this->isStaticSegment($entity);
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, \XLite\Model\AEntity $entity)
    {
        if ('static' != $column[static::COLUMN_CODE]) {
            $return = parent::getColumnValue($column, $entity);
        } else {
            $return = $this->getSegmentType($entity);
        }

        return $return;
    }

    /**
     * Check if segment is static
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment Segment
     *
     * @return boolean
     */
    protected function isStaticSegment(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment)
    {
        return $segment->getStatic();
    }

    /**
     * Get segment type
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment Segment
     *
     * @return string
     */
    protected function getSegmentType(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment)
    {
        $return = \XLite\Core\Translation::lbl('Static');

        if (!$this->isStaticSegment($segment)) {
            $return = \XLite\Core\Translation::lbl('Auto-Updated');
        }

        return $return;
    }
}
