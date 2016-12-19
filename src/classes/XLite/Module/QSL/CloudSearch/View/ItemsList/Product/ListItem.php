<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Product;

use XLite\Model\WidgetParam\TypeCollection;


/**
 * Search products item list
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    const PARAM_CLOUD_FILTERS_FILTER_VARIANTS = 'cloudFiltersFilterVariants';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_CLOUD_FILTERS_FILTER_VARIANTS => new TypeCollection('CloudFilters filter variants'),
        );
    }

    /**
     * Get PARAM_CLOUD_FILTERS_FILTER_VARIANTS value
     *
     * @return mixed
     */
    protected function getCloudFiltersFilterVariants()
    {
        return $this->getParam(self::PARAM_CLOUD_FILTERS_FILTER_VARIANTS);
    }

    /**
     * Check visibility, initialize and display widget or fetch it from cache.
     *
     * @param string $template Override default template OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        if ($this->getCloudFiltersFilterVariants() !== null) {
            $this->constrainCloudSearchProductVariants($this->getCloudFiltersFilterVariants());
        }

        parent::display($template);

        if ($this->getCloudFiltersFilterVariants() !== null) {
            $this->unconstrainCloudSearchProductVariants();
        }
    }

    /**
     * Constrain product variants so that only filtered could be shown on a product list
     *
     * @param $filterVariants
     */
    protected function constrainCloudSearchProductVariants($filterVariants)
    {
        $this->getProduct()->constrainCloudSearchProductVariants($filterVariants);
    }

    /**
     * Remove filter constraint set with the above method
     */
    protected function unconstrainCloudSearchProductVariants()
    {
        $this->getProduct()->unconstrainCloudSearchProductVariants();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = serialize($this->getCloudFiltersFilterVariants());

        return $params;
    }
}