<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer\Page;

/**
 * @Decorator\Depend({"XC\CrispWhiteSkin", "CDev\ProductAdvisor"})
 */
abstract class APageProductAdvisor extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        $targets = array(
            \XLite\Module\CDev\ProductAdvisor\View\ANewArrivals::WIDGET_TARGET_NEW_ARRIVALS,
            \XLite\Module\CDev\ProductAdvisor\View\AComingSoon::WIDGET_TARGET_COMING_SOON,
        );

        $labels = array_reverse($labels);
        $labels += \XLite\Module\CDev\ProductAdvisor\Main::getLabels($this->getProduct());
        $labels = array_reverse($labels);

        return $labels;
    }
}
