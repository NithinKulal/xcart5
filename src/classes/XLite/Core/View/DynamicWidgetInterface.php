<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;


/**
 * DynamicWidget marker interface marks widget as non-cacheable with its cacheable parent (by default all child widgets are cached with their parents).
 * Instead, dynamic widget is first rendered as a placeholder in the cached html. This placeholder is replaced by actual rendered content every time this cached content is displayed.
 */
interface DynamicWidgetInterface
{
}