<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Image
 */
class SimpleImage extends \XLite\View\FormField\AFormField
{
    const PARAM_IMAGE_OPTIONS = 'imageOptions';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return 'image';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'simple_image.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_VALUE] = new \XLite\Model\WidgetParam\TypeObject('Image', null, false, 'XLite\Model\Base\Image');
        $this->widgetParams[self::PARAM_IMAGE_OPTIONS] = new \XLite\Model\WidgetParam\TypeCollection('Image', []);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getValue();
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return trim(parent::getDefaultWrapperClass() . ' simple-image');
    }

    public function getImageOptions()
    {
        return array_merge(
            [
                'image'         => $this->getValue(),
                'maxWidth'      => 70,
                'maxHeight'     => 70,
            ],
            $this->getParam(static::PARAM_IMAGE_OPTIONS)
        );
    }
}

