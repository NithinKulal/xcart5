<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Shipping;

/**
 * Add shipping method dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AddMethod extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'shipping_method_selection';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'shipping/add_method/body.twig';
    }
}
