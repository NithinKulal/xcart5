<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

/**
 * Abstracts instantiation logic if rendering context
 */
class RenderingContextFactory
{
    /**
     * Create new RenderingContextInterface implementation
     *
     * @return RenderingContextInterface
     */
    public static function createContext()
    {
        return new RenderingContext(new AssetRegistrar(), new MetaTagRegistrar());
    }
}
