<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Menu\Admin;

/**
 * Abstract admin menu
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of related targets
     *
     * @param string $target Target name
     *
     * @return array
     */
    public function getRelatedTargets($target)
    {
        $result = parent::getRelatedTargets($target);

        if ('payment_settings' === $target) {
            $result[] = 'paypal_settings';
            $result[] = 'paypal_credit';
        }

        return $result;
    }
}
