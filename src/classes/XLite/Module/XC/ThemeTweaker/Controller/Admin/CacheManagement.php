<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;


class CacheManagement extends \XLite\Controller\Admin\CacheManagement implements \XLite\Base\IDecorator
{
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionRebuildViewLists()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\ViewList');
        $overriddenData = $repo->findOverriddenData();

        parent::doActionRebuildViewLists();

        foreach ($overriddenData as $listData) {
            $conditions = [
                'list'   => $listData['list'],
                'child'  => $listData['child'],
                'tpl'    => $listData['tpl'],
                'zone'   => $listData['zone'],
                'weight' => $listData['weight'],
            ];

            if ($list = $repo->findEqualByData($conditions)) {
                $list->map([
                    'list_override'   => $listData['list_override'],
                    'weight_override' => $listData['weight_override'],
                    'override_mode'   => $listData['override_mode'],
                ]);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }
}