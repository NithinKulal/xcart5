<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

class Location extends \XLite\View\Location implements \XLite\Base\IDecorator
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (parent::isVisible() && 2 < $this->getNodeCount())
            || (
                \XLite::TARGET_404 !== $this->getTarget()
                && 'main' !== $this->getTarget()
                && 1 === $this->getNodeCount()
                && !$this->isCheckoutLayout()
            );
    }
}
