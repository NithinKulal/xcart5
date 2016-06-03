<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

abstract class AbstractClassAutoLoader
{
    public function register()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    public function unregister()
    {
        spl_autoload_unregister([$this, 'autoload']);
    }

    private function autoload($class)
    {
        if ($this->canLoad($class)) {
            $this->load($class);
        }
    }

    protected function canLoad($class)
    {
        $parts = explode('\\', $class);

        return $parts[0] == 'XLite' || $class == 'XLiteAbstract';
    }

    abstract protected function load($class);
}