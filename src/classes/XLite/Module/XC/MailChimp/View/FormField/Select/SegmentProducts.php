<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\FormField\Select;

/**
 * Select "Yes / No"
 */
class SegmentProducts extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = array();

        $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findBy(
            array(
                'useAsSegmentCondition' => true
            )
        );

        foreach ($products as $product) {
            $return[$product->getProductId()] = $product->getName();
        }

        return $return;
    }
}