<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Layout manager
 */
class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Get skin paths (file system and web)
     *
     * @param string  $interface        Interface code OPTIONAL
     * @param boolean $isModuleResource Is module resource OPTIONAL
     * @param boolean $reset            Local cache reset flag OPTIONAL
     * @param boolean $baseSkins          Use base skins only flag OPTIONAL
     *
     * @return array
     */
    public function getSkinPaths($interface = null, $isModuleResource = false, $reset = false, $baseSkins = false)
    {
        return 'custom' == $interface
            ? array (
                array(
                    'name' => 'custom',
                    'fs'   => rtrim(LC_DIR_VAR, LC_DS),
                    'web'  => 'var',
                )
            )
            : parent::getSkinPaths($interface, $isModuleResource, $reset, $baseSkins);
    }
}
