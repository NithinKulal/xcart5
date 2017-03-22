<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\FileUploader;

/**
 * Abstract file uploader
 */
abstract class AFileUploader extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_MULTIPLE           = 'multiple';
    const PARAM_MAX_WIDTH          = 'maxWidth';
    const PARAM_MAX_HEIGHT         = 'maxHeight';
    const PARAM_IS_VIA_URL_ALLOWED = 'isViaUrlAllowed';
    const PARAM_IS_REMOVABLE       = 'removable';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_FILE;
    }

    /**
     * Return 'isImage' flag
     *
     * @return boolean
     */
    abstract protected function isImage();

    /**
     * Return max width
     *
     * @return integer
     */
    protected function getMaxWidth()
    {
        return $this->getParam(static::PARAM_MAX_WIDTH);
    }

    /**
     * Return max height
     *
     * @return integer
     */
    protected function getMaxHeight()
    {
        return $this->getParam(static::PARAM_MAX_HEIGHT);
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
            static::PARAM_MULTIPLE           => new \XLite\Model\WidgetParam\TypeBool('Multiple', false),
            static::PARAM_MAX_WIDTH          => new \XLite\Model\WidgetParam\TypeInt('Max. width', 122),
            static::PARAM_MAX_HEIGHT         => new \XLite\Model\WidgetParam\TypeInt('Max. height', 122),
            static::PARAM_IS_VIA_URL_ALLOWED => new \XLite\Model\WidgetParam\TypeInt('Is ViaUrl allowed', true),
            static::PARAM_IS_REMOVABLE       => new \XLite\Model\WidgetParam\TypeBool('Is removable', true),
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return $this->getParam(static::PARAM_MULTIPLE)
            ? 'file_uploader/multiple.twig'
            : 'file_uploader/single.twig';
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if ($this->getParam(static::PARAM_MULTIPLE)) {
            $list['multiple'] = $this->getParam(static::PARAM_MULTIPLE);
        }
        $list['max_width'] = $this->getParam(static::PARAM_MAX_WIDTH);
        $list['max_height'] = $this->getParam(static::PARAM_MAX_HEIGHT);

        return $list;
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getDataCode()
    {
        $result = '';

        foreach ($this->getAttributes() as $name => $value) {
            if ('class' != $name) {
                $result .= ' data-' . $name . '="' . func_htmlspecialchars($value) . '"';
            }
        }

        return $result;
    }

    /**
     * Return files list
     *
     * @return mixed
     */
    protected function getFiles()
    {
        $result = $this->getValue();

        if (is_array($result)) {
            foreach ($result as $k => $v) {
                $temporaryFile = isset($v['temp_id'])
                    ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($v['temp_id'])
                    : null;

                if ($temporaryFile) {
                    $result[$k] = $temporaryFile;

                } else {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    /**
     * Is ia url allowed
     *
     * @return boolean
     */
    protected function isViaUrlAllowed()
    {
        return $this->getParam(static::PARAM_IS_VIA_URL_ALLOWED);
    }

    /**
     * Check object is removable or not
     *
     * @return boolean
     */
    protected function isRemovable()
    {
        return $this->getParam(static::PARAM_IS_REMOVABLE) || $this->getParam(static::PARAM_MULTIPLE);
    }

    /**
     * Check field value validity
     *
     * @return boolean
     */
    protected function checkFieldValue()
    {
        $value = $this->getValue();
        return !empty($value);
    }
}
