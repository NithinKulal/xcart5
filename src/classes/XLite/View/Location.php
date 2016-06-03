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
 * @ListChild (list="layout.main.breadcrumb", zone="customer", weight="100")
 */
class Location extends \XLite\View\AView
{
    use CacheableTrait;

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
        $cacheParams   = $this->getCacheParameters();
        $cacheParams[] = 'getNodeCount';

        return $this->executeCached(function () {
            return count($this->getNodes());
        }, $cacheParams);
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
            && 1 < $this->getNodeCount()
            && !$this->isCheckoutLayout()
            && \XLite::TARGET_404 != $this->getTarget();
    }

    protected function isCacheAvailable()
    {
        $controller = \XLite::getController();

        return $controller instanceof \XLite\Controller\Customer\Main
            || $controller instanceof \XLite\Controller\Customer\Category
            || $controller instanceof \XLite\Controller\Customer\Product;
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [\XLite\Core\URLManager::getCurrentURL()]
        );
    }
}
