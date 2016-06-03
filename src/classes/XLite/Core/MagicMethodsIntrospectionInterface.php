<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Provides a way to check whether an implementer supports a particular "magic" method.
 * In this context magic method is an otherwise inaccessible method that is implemented via a __call magic.
 */
interface MagicMethodsIntrospectionInterface
{
    /**
     * Return true if method with $name can be invoked.
     *
     * @param $name string Method name
     *
     * @return boolean
     */
    public function hasMagicMethod($name);
}