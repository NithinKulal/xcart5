<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Customer\Product;

/**
 * Pager for the search products page
 */
class Search extends \XLite\View\Pager\Customer\Product\AProduct
{
    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = 'substring';
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
