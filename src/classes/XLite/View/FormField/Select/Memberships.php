<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Memberships selector
 */
class Memberships extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Get Memberships list
     *
     * @return array
     */
    protected function getMembershipsList()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Membership')->findActiveMemberships() as $m) {
            $list[$m->getMembershipId()] = $m->getName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getMembershipsList();
    }
}
