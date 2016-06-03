<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Pager\Customer;

/**
 * Pager for the product reviews page
 *
 */
class Review extends \XLite\Module\XC\Reviews\View\Pager\Customer\ACustomer
{
    /**
     * Widget parameter names
     */
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_PRODUCT_ID = 'product_id';


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
     * Return current product model object
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getWidgetParams(self::PARAM_PRODUCT_ID)->getObject();
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
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\ObjectId\Product(
                'Product ID',
                null
            ),

            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Category ID',
                null
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

        $this->requestParams[] = self::PARAM_PRODUCT_ID;
        $this->requestParams[] = self::PARAM_CATEGORY_ID;
    }
}
