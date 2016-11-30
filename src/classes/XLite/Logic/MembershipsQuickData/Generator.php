<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\MembershipsQuickData;

/**
 * Quick data generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Return memberships
     *
     * @return \XLite\Model\Membership[]
     */
    public function getMemberships()
    {
        $ids = $this->getOptions()->memberships;

        return \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($ids);
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array(
            'XLite\Logic\MembershipsQuickData\Step\Products',
        );
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return 'membershipsQuickDataTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return 'membershipsQuickDataCancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'membershipsQuickData';
    }

    // }}}
}
