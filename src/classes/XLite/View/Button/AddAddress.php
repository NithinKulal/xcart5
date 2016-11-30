<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;


/**
 * Add address button widget
 */
class AddAddress extends \XLite\View\Button\APopupButton
{
    /*
     * Profile identificator parameter
     */
    const PARAM_PROFILE_ID = 'profileId';
    const PARAM_WIDGET_TITLE = 'widgetTitle';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/add_address.js';

        $model = new \XLite\View\Model\Address\Address();
        $list = array_merge($list, $model->getJSFiles());

        foreach ($model->getFormFieldsForSectionDefault() as $field) {
            $list = array_merge($list, $field->getJSFiles());
        }

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
            self::PARAM_PROFILE_ID => new \XLite\Model\WidgetParam\TypeInt('Profile ID', 0),
            self::PARAM_WIDGET_TITLE => new \XLite\Model\WidgetParam\TypeString('Widget title', static::t('Add address')),
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
            'profile_id'   => $this->getParam(self::PARAM_PROFILE_ID),
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
        return 'btn regular-button popup-button add-address';
    }
}
