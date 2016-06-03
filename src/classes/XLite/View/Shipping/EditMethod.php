<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Shipping;

/**
 * Edit shipping method dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class EditMethod extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'shipping_rates';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'shipping/add_method/edit.twig';
    }

    /**
     * Offline help template
     *
     * @return string
     */
    protected function getOfflineHelpTemplate()
    {
        return 'shipping/add_method/parts/offline_help.twig';
    }

    /**
     * Online help template
     *
     * @return string
     */
    protected function getOnlineHelpTemplate()
    {
        return 'shipping/add_method/parts/online_help.twig';
    }
}
