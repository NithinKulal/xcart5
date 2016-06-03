<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View\FormField;

/**
 * Profile image
 */
class ProfileImage extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_URL = 'url';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_URL => new \XLite\Model\WidgetParam\TypeString('URL', ''),
        );
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_TEXT;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/SocialLogin';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'pictureImage/body.twig';
    }
}
