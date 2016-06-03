<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Console;

/**
 * Console base widget
 *
 * @ListChild (list="cli.center", zone="console")
 */
class Main extends \XLite\View\Console\AConsole
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'main';

        return $result;
    }

    /**
     * Get allowed commands
     *
     * @return array
     */
    public function getAllowedCommands()
    {
        $dsQuoted = preg_quote(LC_DS, '/');
        $path = \Includes\Decorator\ADecorator::getCacheClassesDir() . 'XLite' . LC_DS . 'Controller' . LC_DS . 'Console' . LC_DS . '*.php';
        $commands = array();
        $list = glob($path);
        if ($list) {
            foreach ($list as $f) {
                if (!preg_match('/Abstract.php$/Ss', $f) && !preg_match('/' . $dsQuoted . 'A[A-Z]/Ss', $f)) {
                    $commands[] = strtolower(substr(basename($f), 0, -4));
                }
            }
        }

        return $commands;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'base.twig';
    }
}
