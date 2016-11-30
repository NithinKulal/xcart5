<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Customer\Product;

/**
 * Pager for the category products page
 */
class Category extends \XLite\View\Pager\Customer\Product\AProduct
{
    /**
     * Widget parameter names
     */
    const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Return current category model object
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        return $this->getWidgetParams(self::PARAM_CATEGORY_ID)->getObject();
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
            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Category ID', null
            ),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = self::PARAM_CATEGORY_ID;
    }

    /**
     * Should we use cache for pageId
     *
     * @return boolean
     */
    protected function isSavedPageId()
    {
        return false;
    }
}
