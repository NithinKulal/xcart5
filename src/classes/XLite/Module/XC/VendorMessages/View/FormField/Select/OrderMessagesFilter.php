<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\FormField\Select;

/**
 * Order messages filter selector
 */
class OrderMessagesFilter extends \XLite\View\FormField\Select\Regular
{

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        $list = array(
            ''  => static::t('All communication threads'),
            'U' => static::t('Сommunication threads with unread messages'),
        );

        if (\XLite\Module\XC\VendorMessages\Main::isAllowDisputes()) {
            $list['D'] = static::t('Сommunication threads with open disputes');
        }

        return $list;
    }
}
