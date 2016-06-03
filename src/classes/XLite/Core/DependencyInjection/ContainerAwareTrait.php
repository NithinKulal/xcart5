<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DependencyInjection;

use Interop\Container\ContainerInterface;

/**
 * Provides access to container for host classes.
 */
trait ContainerAwareTrait
{
    /**
     * Gets the container.
     *
     * @return ContainerInterface  A ContainerInterface instance
     */
    protected function getContainer()
    {
        return \XLite::getInstance()->getContainer();
    }
}