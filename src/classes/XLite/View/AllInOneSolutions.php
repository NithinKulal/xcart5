<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * AllInOne solutions block
 *
 * @ListChild (list="checkout.main", zone="customer", weight="1")
 */
class AllInOneSolutions extends \XLite\View\AView
{
    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getSolutions();
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'checkout/all_in_one_solutions/all_in_one_solutions.css';

        return $list;
    }

    /**
     * Get solutions
     *
     * @return array
     */
    public function getSolutions()
    {
        return array_filter(
            \XLite\Logic\AllInOneSolutionService::getInstance()->getSolutions(),
            function(\XLite\View\AView $solution) {
                return $solution->isVisible();
            }
        );
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/all_in_one_solutions/all_in_one_solutions.twig';
    }
}
