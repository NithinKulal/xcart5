<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DependencyInjection;

use Interop\Container\ContainerInterface;

/**
 * Provides DI container instantiation logic.
 * To be used in class that provides global (and single) access point to container.
 */
trait ContainerHolderTrait
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Gets the container.
     *
     * @return ContainerInterface  A ContainerInterface instance
     */
    public function getContainer()
    {
        if (!isset($this->container)) {
            $this->container = LC_DEVELOPER_MODE
                ? (new ContainerFactory())->createDevContainer()
                : (new ContainerFactory())->createContainer();
        }

        return $this->container;
    }
}