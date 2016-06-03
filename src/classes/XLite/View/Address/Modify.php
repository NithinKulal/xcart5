<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Address;

/**
 * \XLite\View\Address\Modify
 */
class Modify extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'address_book';

        return $result;
    }

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'address/modify_address.twig';
    }
}
