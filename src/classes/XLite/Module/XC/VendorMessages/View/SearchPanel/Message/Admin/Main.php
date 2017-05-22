<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\SearchPanel\Message\Admin;

/**
 * Main admin records search panel
 */
class Main extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\VendorMessages\View\Form\ItemsList\Messages\Admin\Search';
    }

    /**
     * @inheritdoc
     */
    protected function getLinkedItemsList()
    {
        return '.all-messages .widget.items-list';
    }

    /**
     * @inheritdoc
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + array(
            'messageSubstring' => array(
                static::CONDITION_CLASS                             => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search keywords'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ),
            'messages' => array(
                static::CONDITION_CLASS                            => 'XLite\Module\XC\VendorMessages\View\FormField\Select\OrderMessagesFilter',
                \XLite\View\FormField\AFormField::PARAM_LABEL      => static::t('Messages'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
        );
    }

}