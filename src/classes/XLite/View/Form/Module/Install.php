<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Module;

/**
 * Install
 */
class Install extends \XLite\View\Form\Module\AModule
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'addons_list_marketplace';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return '';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['sessionCell'] = 'XLiteViewItemsListModuleInstall';

        return $list;
    }
}
