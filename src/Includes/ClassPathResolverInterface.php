<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

interface ClassPathResolverInterface
{
    public function getPathname($class);

    public function getClass($pathname);

    public function getFullPath($subPath);

    public function getRelativePath($fullPath);
}