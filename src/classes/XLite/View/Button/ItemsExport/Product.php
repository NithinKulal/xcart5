<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\ItemsExport;

/**
 * Product ItemsExport button
 */
class Product extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = array();
        $list['CSV'] = $this->getWidget(
            array(
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Products',
                'session'    => \XLite\View\ItemsList\Model\Product\Admin\Search::getConditionCellName(),
            ),
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
