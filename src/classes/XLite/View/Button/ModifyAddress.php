<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;


/**
 * Delete address button widget
 */
class ModifyAddress extends \XLite\View\Button\APopupButton
{
    /*
     * Address identificator parameter
     */
    const PARAM_ADDRESS_ID = 'addressId';
    const PARAM_WIDGET_TITLE = 'widgetTitle';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/modify_address.js';

        return $list;
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
            self::PARAM_ADDRESS_ID => new \XLite\Model\WidgetParam\TypeInt('Address ID', 0),
            self::PARAM_WIDGET_TITLE => new \XLite\Model\WidgetParam\TypeString('Widget title', static::t('Edit address')),
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
            'target'       => 'address_book',
            'address_id'   => $this->getParam(self::PARAM_ADDRESS_ID),
            'widget'       => '\XLite\View\Address\Modify',
            'widget_title' => $this->getParam(self::PARAM_WIDGET_TITLE)
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' modify-address';
    }

}
