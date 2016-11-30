<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\StickyPanel\Country\Admin;

/**
 * Panel form items list-based form
 */
class Main extends \XLite\View\StickyPanel\Country\Admin\Main
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $return = parent::defineButtons();
        $return['backToList'] = $this->getWidget(
            [
                'style'    => 'action link',
                'label'    => static::t('Back to currencies list'),
                'disabled' => false,
                'location' => $this->buildURL('currencies'),
            ],
            'XLite\View\Button\SimpleLink'
        );

        return $return;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
        ];
    }
}
