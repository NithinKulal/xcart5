<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Button;

/**
 * ItemsExport button
 */
class Product extends ABulkEdit
{
    protected function defineAdditionalButtons()
    {
        $result = [];
        $scenarios = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarios();
        $position = 100;

        foreach ($scenarios as $name => $data) {
            $result[$name] = [
                'label'      => $data['title'],
                'action'     => 'start',
                'formParams' => ['target' => 'bulk_edit', 'scenario' => $name],
                'style'      => 'always-enabled action link list-action',
                'position'   => $position,
            ];

            $position += 100;
        }
        
        return $result;
    }
}
