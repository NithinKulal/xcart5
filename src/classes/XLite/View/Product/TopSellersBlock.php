<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

/**
 * Top sellers block widget
 */
class TopSellersBlock extends \XLite\View\Dialog
{
    const PARAM_PERIOD       = 'period';
    const PARAM_AVAILABILITY = 'availability';

    /**
     * Add widget specific CSS file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Add widget specific JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PERIOD       => new \XLite\Model\WidgetParam\TypeString('Period', $this->definePeriod()),
            static::PARAM_AVAILABILITY => new \XLite\Model\WidgetParam\TypeString(
                'Availability', $this->defineAvailability()
            ),
        );
    }

    /**
     * Return period
     *
     * @return string
     */
    protected function definePeriod()
    {
        $request = \XLite\Core\Request::getInstance();

        return $request->{static::PARAM_PERIOD}
            ?: \XLite\View\ItemsList\Model\Product\Admin\TopSellers::P_PERIOD_LIFETIME;
    }

    /**
     * Return period
     *
     * @return string
     */
    protected function defineAvailability()
    {
        $request = \XLite\Core\Request::getInstance();

        return $request->{static::PARAM_AVAILABILITY}
            ?: \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/top_sellers';
    }

    /**
     * Get options for selector (allowed periods)
     *
     * @return array
     */
    protected function getOptions()
    {
        return \XLite\View\ItemsList\Model\Product\Admin\TopSellers::getAllowedPeriods();
    }

    /**
     * Get options for selector (allowed availability)
     *
     * @return array
     */
    protected function getAvailabilityOptions()
    {
        return \XLite\View\ItemsList\Model\Product\Admin\TopSellers::getAllowedAvailability();
    }

    /**
     * Return true if current period is a default
     *
     * @param string $period Period name
     *
     * @return boolean
     */
    protected function isSelectedPeriod($period)
    {
        return $this->getSelectedPeriod() === $period;
    }

    /**
     * Return true if current period is a default
     *
     * @return string
     */
    protected function getSelectedPeriod()
    {
        return $this->getParam(static::PARAM_PERIOD);
    }

    /**
     * Return true if current availability is selected
     *
     * @param string $value Period name
     *
     * @return boolean
     */
    protected function isSelectedAvailability($value)
    {
        return $this->getSelectedAvailability() === $value;
    }

    /**
     * Return true if current availability is selected
     *
     * @return string
     */
    protected function getSelectedAvailability()
    {
        return $this->getParam(static::PARAM_AVAILABILITY);
    }

    /**
     * Return true if there are no statistics for lifetime period
     *
     * @return boolean
     */
    protected function isEmptyStats()
    {
        return !\XLite\Core\Database::getRepo('XLite\Model\Product')
            ->hasTopSellers();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
               && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }
}
