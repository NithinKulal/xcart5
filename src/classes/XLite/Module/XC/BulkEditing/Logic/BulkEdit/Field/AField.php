<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field;

abstract class AField
{
    public static function getSchema($name, $options)
    {
        return [];
    }

    public static function getData($name, $object)
    {
        return [];
    }

    public static function populateData($name, $object, $data)
    {
    }

    public static function getViewData($name, $object, $options)
    {
        return [];
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
    protected static function t($name, array $arguments = array(), $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $arguments, $code);
    }
}
