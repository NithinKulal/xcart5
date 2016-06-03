<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Modules\XC\BulkEditing\View\StickyPanel\FormModel;

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
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_model/sticky_panel/body.twig';
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['product_list'] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to product list'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('product_list'),
            ),
            '\XLite\View\Button\SimpleLink'
        );

        return $list;
    }
}
