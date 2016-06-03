<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\ItemsExport;

/**
 * Order ItemsExport button
 */
class Reviews extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = array();
        $list['CSV'] = $this->getWidget(
            array(
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Module\XC\Reviews\Logic\Export\Step\Reviews',
                'session'    => \XLite\Module\XC\Reviews\View\ItemsList\Model\Review::getConditionCellName(),
            ),
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
