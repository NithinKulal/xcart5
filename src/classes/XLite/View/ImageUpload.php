<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Image upload component.
 */
class ImageUpload extends \XLite\View\AView
{
    /*
     * Widget parameters names
     */
    const PARAM_FIELD = 'field';
    const PARAM_ACTION_NAME = 'actionName';
    const PARAM_FORM_NAME = 'formName';
    const PARAM_OBJECT = 'object';

    /**
     * Show delete control
     *
     * @var boolean
     */
    public $showDelete = true;

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/image_upload.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FIELD       => new \XLite\Model\WidgetParam\TypeString('Field', ''),
            self::PARAM_ACTION_NAME => new \XLite\Model\WidgetParam\TypeString('Action name', ''),
            self::PARAM_FORM_NAME   => new \XLite\Model\WidgetParam\TypeString('Form name', ''),
            self::PARAM_OBJECT      => new \XLite\Model\WidgetParam\TypeObject('Object', null),
        );
    }

    /**
     * Check if object has image
     *
     * @return boolean
     */
    protected function hasImage()
    {
        $field = $this->getParam(self::PARAM_FIELD);
        $method = 'has' . $field;
        $object = $this->getParam(self::PARAM_OBJECT);

        $result = false;

        if (is_object($object) && method_exists($object, $method)) {

            // $method asembled 'has' + $field
            $result = $object->$method();
        }

        return $result;
    }

    /**
     * Check if image is on file system
     *
     * @return void
     */
    protected function isFS()
    {
        return 'F' == $this->getParam(self::PARAM_OBJECT)->get($this->getParam(self::PARAM_FIELD))->getDefaultSource();
    }
}
