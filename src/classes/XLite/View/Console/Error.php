<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Console;

/**
 * Console error widget
 *
 * @ListChild (list="cli.center", zone="console")
 */
class Error extends \XLite\View\Console\AConsole
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'error';

        return $result;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'error.twig';
    }
}
