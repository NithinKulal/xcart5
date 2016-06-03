<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Shipping;

/**
 * Online shipping carriers list
 */
class OnlineList extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'shipping/online_list/body.twig';
    }

    /**
     * Returns online shipping methods (carriers)
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function getMethods()
    {
        /** @var \XLite\Model\Repo\Shipping\Method $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');

        return $repo->findOnlineCarriers();
    }

    /**
     * Returns shipping carrier settings url
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getSettingsURL(\XLite\Model\Shipping\Method $method)
    {
        $url = null;

        $module = $method->getProcessorModule();

        if ($module) {

            if ($module->isInstalled() && $module->getEnabled()) {
                $url = $method->getProcessorObject()
                    ? $method->getProcessorObject()->getSettingsURL()
                    : '';

            } elseif ($module->isInstalled()) {
                $url = $module->getInstalledURL();

            } else {
                $url = $module->getMarketplaceURL();
            }
        }

        return $url;
    }
}
