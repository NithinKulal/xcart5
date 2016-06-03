<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Form;

/**
 * Search
 */
class Search extends \XLite\View\Form\Product\Search\ASearch
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_list';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search_featured_products';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = parent::getDefaultParams();
        $params[\XLite\Controller\AController::RETURN_URL] 
            = \XLite\Core\URLManager::getSelfURI() . '&mode=search_featured_products';

        return $params;
    }

    /**
     * JavaScript: this value will be returned on form submit
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'true';
    }

}
