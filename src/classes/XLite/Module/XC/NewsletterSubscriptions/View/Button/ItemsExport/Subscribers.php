<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View\Button\ItemsExport;

/**
 * Subscribers ItemsExport button
 */
class Subscribers extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = array();
        $list['CSV'] = $this->getWidget(
            array(
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Module\XC\NewsletterSubscriptions\Logic\Export\Step\NewsletterSubscribers',
                'session'    => \XLite\Module\XC\NewsletterSubscriptions\View\ItemsList\Subscribers::getConditionCellName(),
            ),
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
