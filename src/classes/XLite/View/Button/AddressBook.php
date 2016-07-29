<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Address book popup button
 */
class AddressBook extends \XLite\View\Button\PopupButton
{
    const PARAM_ADDRESS_TYPE = 'addressType';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array(
            'button/js/address_book.js'
        );
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ADDRESS_TYPE => new \XLite\Model\WidgetParam\TypeString('Address type', 's'),
        );
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'       => 'select_address',
            'atype'        => $this->getParam(self::PARAM_ADDRESS_TYPE),
            'widget'       => 'XLite\View\SelectAddress',
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-button address-book-button';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }
}
