<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Module linked 
 */
interface IModuleLinked
{
    /**
     * Switch module link
     *
     * @param boolean             $enabled Module enabled status
     * @param \XLite\Model\Module $module  Model module
     *
     * @return mixed
     */
    public function switchModuleLink($enabled, \XLite\Model\Module $module);
}
