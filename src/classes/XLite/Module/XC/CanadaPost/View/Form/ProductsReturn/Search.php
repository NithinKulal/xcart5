<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Form\ProductsReturn;

/**
 * Search returns form
 */
class Search extends \XLite\Module\XC\CanadaPost\View\Form\ProductsReturn\AProductsReturn
{
    /**
     * Return default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'capost_returns';
    }

    /**
     * Return default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search';
    }

    /**
     * Return default params
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array('mode' => 'search');
    }
}
