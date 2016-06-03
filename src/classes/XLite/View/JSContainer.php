<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * JS container. Must be the last element on the page
 * TODO: refactor admin area JS code.
 *
 * @ListChild (list="body", zone="customer", weight="999999")
 * @ListChild (list="body", zone="admin", weight="10")
 */
class JSContainer extends \XLite\View\AResourcesContainer
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'jscontainer';
    }

    protected function getResourceRegistry()
    {
        $records = $this->getCSSResources() + $this->getJSResources();

        $resources = array_map(
            function ($item) {
                return $item['url'];
            },
            $records
        );

        return json_encode(array_values($resources));
    }
}
