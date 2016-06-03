<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Search;

/**
 * Languages and language labels modification
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Search extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'order_list';

        return $result;
    }


    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/search.twig';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'order';
    }

    /**
     * Check - search block visible or not
     * 
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return true;
    }
}
