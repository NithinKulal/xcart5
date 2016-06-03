<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;


interface StaticReflectorFactoryInterface
{
    /**
     * @param $class
     * @return StaticReflectorInterface
     */
    public function reflectClass($class);

    /**
     * @param $pathname
     * @return StaticReflectorInterface
     */
    public function reflectSource($pathname);

//        public function finalizeReflector(StaticReflectorInterface $reflector);
}