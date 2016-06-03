<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\ItemsList;

/**
 * Coupons items list
 */
class Coupons extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/coupons/list/style.css';

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
            'code' => array(
                static::COLUMN_NAME    => static::t('Coupon code'),
                static::COLUMN_LINK    => 'coupon',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY  => 100,
            ),
            'comment' => array(
                static::COLUMN_NAME    => static::t('Comment'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY  => 200,
            ),
            'value' => array(
                static::COLUMN_NAME => static::t('Discount'),
                static::COLUMN_ORDERBY  => 300,
            ),
            'uses' => array(
                static::COLUMN_NAME => static::t('Uses left'),
                static::COLUMN_ORDERBY  => 400,
            ),
            'uses_count' => array(
                static::COLUMN_NAME => static::t('Uses count'),
                static::COLUMN_TEMPLATE => 'modules/CDev/Coupons/coupons/list/uses_count.twig',
                static::COLUMN_ORDERBY  => 500,
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
        return 'XLite\Module\CDev\Coupons\Model\Coupon';
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return static::t('X coupon(s) has been created', array('count' => $count));
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return static::t('X coupon(s) has been removed', array('count' => $count));
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('coupon');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New discount coupon';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
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
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('coupons');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' coupons';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\Coupons\View\StickyPanel\Coupon\Admin\Coupons';
    }

    // {{{ Data

    /**
     * Return coupons list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->search($cnd, $countOnly);
    }

    // }}}

    // {{{ Content helpers

    /**
     * Define line class  as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);
        if ($entity) {
            $classes[] = $entity->getEnabled() ? 'enabled' : 'disabled';
            $classes[] = $entity->isActive() ? 'active' : 'inactive';

            if ($this->isInfinityUsesLeft($entity)) {
                $classes[] = 'uses-infinity';
            }
        }

        return $classes;
    }

    /**
     * Check - coupon's uses left is infinity or not
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return boolean
     */
    protected function isInfinityUsesLeft(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return 0 >= $coupon->getUsesLimit();
    }

    /**
     * Get uses left
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return integer
     */
    protected function getUsesLeft(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->getUsesLimit() - $coupon->getUses();
    }

    /**
     * Format percent discount
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return float
     */
    protected function formatPercentDiscount(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return round($coupon->getValue(), 2);
    }

    // }}}

    // {{{ Preprocessors

    /**
     * Preprocess value for Discount column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Entity
     *
     * @return string
     */
    protected function preprocessValue($value, array $column, \XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->isAbsolute()
            ? static::formatPrice($value)
            : round($value, 2) . '%';
    }

    /**
     * Preprocess value for Uses left column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Entity
     *
     * @return string
     */
    protected function preprocessUses($value, array $column, \XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return $this->isInfinityUsesLeft($coupon)
            ? (chr(226) . chr(136) . chr(158))
            : max(0, $coupon->getUsesLimit() - $value);
    }

    // }}}
}
