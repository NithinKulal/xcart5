<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field;

abstract class AField
{
    /**
     * @param $name
     * @param $options
     *
     * @return array
     */
    public static function getSchema($name, $options)
    {
        return [];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getData($name, $object)
    {
        return [];
    }

    /**
     * @param $name
     * @param $object
     * @param $data
     */
    public static function populateData($name, $object, $data)
    {
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return string
     */
    public static function getViewValue($name, $object)
    {
        return '';
    }

    /**
     * Language label translation short method
     *
     * @param string $name      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function t($name, array $arguments = [], $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $arguments, $code);
    }
}
