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
class DeleteAddress extends \XLite\View\Button\Regular
{
    /*
     * Address identificator parameter
     */
    const PARAM_ADDRESS_ID = 'addressId';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/delete_address.js';

        return $list;
    }

    /**
     * Register CSS files for delete address button
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/delete_address.css';

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
        );
    }

    /**
     * Return JS parameters
     *
     * @return array
     */
    protected function getJSParams()
    {
        return array(
            'address_id'    => $this->getParam(self::PARAM_ADDRESS_ID),
            'warning_text'  => static::t('Delete this address?'),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/delete_address.twig';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' delete-address';
    }

}
