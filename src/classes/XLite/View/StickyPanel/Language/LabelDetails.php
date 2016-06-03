<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Language;

/**
 * Panel for Language label details form.
 */
class LabelDetails extends \XLite\View\Base\FormStickyPanel
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
                'disabled' => false,
            ),
            'XLite\View\Button\Submit'
        );

        return $buttons;
    }
}

