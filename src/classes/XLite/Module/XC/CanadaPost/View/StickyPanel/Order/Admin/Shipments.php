<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\StickyPanel\Order\Admin;

/**
 * Panel for shipments form
 */
class Shipments extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $buttons = array();

        $buttons['save'] = $this->getWidget(
            array(
                'style'    => 'action submit',
                'label'    => static::t('Save changes'),
                'disabled' => true,
            ),
            'XLite\View\Button\Submit'
        );

        return $buttons;
    }
}
