<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Customer;

/**
 * Reviews items list in product tab
 *
 * @ListChild (list="product.reviews.tab", zone="customer", weight="300")
 */
class ReviewsTab extends \XLite\Module\XC\Reviews\View\ItemsList\Model\Customer\Review
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'product';

        return $result;
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
        $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_COUNT]->setValue(
            \XLite\Core\Config::getInstance()->XC->Reviews->reviewsCountPerTab
        );
        $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]->setValue(
            \XLite\Core\Config::getInstance()->XC->Reviews->reviewsCountPerTab
        );
        $this->widgetParams[\XLite\View\Pager\APager::PARAM_ONLY_PAGES]->setValue(true);
    }
}
