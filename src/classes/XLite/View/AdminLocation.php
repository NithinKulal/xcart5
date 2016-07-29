<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Bread crumbs widget
 *
 * @ListChild (list="layout.main.breadcrumb", zone="admin", weight="100")
 */
class AdminLocation extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_NODES = 'nodes';

    /**
     * Return breadcrumbs
     *
     * @return array
     */
    public function getNodes()
    {
        $list = array_values($this->getLocationPath());

        if ($list) {
            $list[count($list) - 1]->setWidgetParams(
                [
                    \XLite\View\Location\Node::PARAM_IS_LAST => true,
                ]
            );
        }

        return $list;
    }

    /**
     * Get (cached) node count
     *
     * @return int
     */
    protected function getNodeCount()
    {
        return count($this->getNodes());
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'location/location.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'location/location.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite::TARGET_404 != $this->getTarget();
    }
}
