<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\FormField\Input;

/**
 * Image
 *
 */
abstract class AImage extends \XLite\View\FormField\Input\AInput
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return 'file';
    }

    /**
     * Return the image URL value
     *
     * @return string
     */
    abstract protected function getImage();

    /**
     * Return the image URL value
     *
     * @return string
     */
    abstract protected function getReturnToDefaultLabel();

    /**
     * Return the inner name for widget.
     * It is used in model widget identification of the "useDefaultImage" value
     *
     * @return string
     */
    abstract protected function getImageName();

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        // We encorage to upload the image files only. HTML5 support
        $list['accept'] = 'image/*';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '/form_field/image.twig';
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/SimpleCMS';
    }
}
