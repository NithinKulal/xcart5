<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\ComparisonTable;

/**
 * Comparison table (absrtact)
 *
 */
abstract class AComparisonTable extends \XLite\View\AView
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison/comparison_table';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
