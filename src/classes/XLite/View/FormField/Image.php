<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Image loader
 */
class Image extends \XLite\View\FormField\AFormField
{
    const PARAM_BUTTON_LABEL   = 'buttonLabel';
    const PARAM_REMOVE_BUTTON  = 'removeButton';
    const PARAM_OBJECT         = \XLite\View\Button\FileSelector::PARAM_OBJECT;
    const PARAM_OBJECT_ID      = \XLite\View\Button\FileSelector::PARAM_OBJECT_ID;
    const PARAM_FILE_OBJECT    = \XLite\View\Button\FileSelector::PARAM_FILE_OBJECT;
    const PARAM_FILE_OBJECT_ID = \XLite\View\Button\FileSelector::PARAM_FILE_OBJECT_ID;

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
        return 'image.twig';
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

        $this->widgetParams += array(
            self::PARAM_BUTTON_LABEL    => new \XLite\Model\WidgetParam\TypeString('Button label', 'Add image'),
            self::PARAM_REMOVE_BUTTON   => new \XLite\Model\WidgetParam\TypeBool('Remove button', false),
            self::PARAM_OBJECT          => new \XLite\Model\WidgetParam\TypeString('Object', ''),
            self::PARAM_OBJECT_ID       => new \XLite\Model\WidgetParam\TypeInt('Object ID', 0),
            self::PARAM_FILE_OBJECT     => new \XLite\Model\WidgetParam\TypeString('File object', 'image'),
            self::PARAM_FILE_OBJECT_ID  => new \XLite\Model\WidgetParam\TypeInt('File object ID', 0),
        );
    }
    
    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */    
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (0 !== $this->getWidgetParams(static::PARAM_FILE_OBJECT_ID)->value) {
            $this->getWidgetParams(static::PARAM_BUTTON_LABEL)->setValue('Update image');
        }
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getObjectId();
    }

    /**
     * Check - remove button is visible or not
     * 
     * @return boolean
     */
    protected function isRemoveButtonVisible()
    {
        return $this->getParam(static::PARAM_REMOVE_BUTTON);
    }

    /**
     * Get remove button label 
     * 
     * @return string
     */
    protected function getRemoveButtonLabel()
    {
        return 'Remove image';
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return trim(parent::getDefaultWrapperClass() . ' image-selector');
    }

    /**
     * Get button label 
     * 
     * @return string
     */
    protected function getButtonLabel()
    {
        return $this->getParam(self::PARAM_BUTTON_LABEL);
    }

    /**
     * Get object
     *
     * @return string
     */
    protected function getObject()
    {
        return $this->getParam(self::PARAM_OBJECT);
    }

    /**
     * Get object id
     *
     * @return integer
     */
    protected function getObjectId()
    {
        return $this->getParam(self::PARAM_OBJECT_ID);
    }

    /**
     * Get file object
     *
     * @return string
     */
    protected function getFileObject()
    {
        return $this->getParam(self::PARAM_FILE_OBJECT);
    }

    /**
     * Get file object id
     *
     * @return integer
     */
    protected function getFileObjectId()
    {
        return $this->getParam(self::PARAM_FILE_OBJECT_ID);
    }

}

