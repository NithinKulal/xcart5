<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\SearchPanel\Order\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\Order\Admin\Main implements \XLite\Base\IDecorator
{
    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + array(
            'messages' => array(
                static::CONDITION_CLASS                       => 'XLite\Module\XC\VendorMessages\View\FormField\Select\OrderMessages',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Messages'),
            ),
        );
    }

    /**
     * Return true if search panel should use filters
     *
     * @return boolean
     */
    protected function isUseFilter()
    {
        return true;
    }

    /**
     * Get name of the 'Reset filter' option
     *
     * @return string
     */
    protected function getClearFilterName()
    {
        return static::t('All orders');
    }

    /**
     * Define search filters options
     * TODO: Review and correct before commit!
     *
     * @return array
     */
    protected function defineFilterOptions()
    {
        $result = parent::defineFilterOptions();

        // Calculate recent orders number
        $count = \XLite\Core\Database::getRepo('XLite\Model\Order')->searchRecentOrders(null, true);

        if ($count) {
            $recentOrdersFilter = new \XLite\Model\SearchFilter();
            $recentOrdersFilter->setId('recent');
            $recentOrdersFilter->setName(static::t('Orders awaiting processing'));
            $recentOrdersFilter->setSuffix(sprintf('(%d)', $count));
            $result = array('recent' => $recentOrdersFilter) + $result;
        }

        return $result;
    }
}
