<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\FormModel;

class Panel extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = [];
        $list[] = 'form_model/sticky_panel/controller.js';

        return $list;
    }

    /**
     * Set buttons
     *
     * @param array $buttons Buttons
     *
     * @return void
     */
    public function setButtons($buttons)
    {
        $this->buttonsList = $buttons;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_model/sticky_panel/body.twig';
    }
}
