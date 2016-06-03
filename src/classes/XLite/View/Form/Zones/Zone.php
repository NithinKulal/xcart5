<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Zones;

/**
 * Zone form
 */
class Zone extends \XLite\View\Form\AForm
{
    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'zones';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['zone_id'] = $this->getZoneId();

        return $list;
    }

    /**
     * Get current zone Id
     *
     * @return integer
     */
    protected function getZoneId()
    {
        return \XLite\Core\Request::getInstance()->zone_id;
    }
}
