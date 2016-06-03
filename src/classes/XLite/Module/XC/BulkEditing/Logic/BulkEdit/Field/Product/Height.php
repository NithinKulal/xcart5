<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Height extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'    => static::t('Height'),
                'type'     => 'XLite\View\FormModel\Type\SymbolType',
                'symbol'   => \XLite\Core\Config::getInstance()->Units->dim_symbol,
                'pattern'  => [
                    'alias'      => 'decimal',
                    'rightAlign' => false,
                ],
                // 'input_grid' => 'col-sm-2',
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => 0,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $object->setBoxHeight($data->{$name});
    }

    /**
     * @param string               $name
     * @param \XLite\Model\Product $object
     * @param array                $options
     *
     * @return array
     */
    public static function getViewData($name, $object, $options)
    {
        $requiresShipping = $object->getShippable();
        $separateBox = $object->getUseSeparateBox();

        return $requiresShipping && $separateBox
            ? [
                $name => [
                    'label'    => static::t('Height'),
                    'value'    => $object->getBoxHeight(),
                    'position' => isset($options['position']) ? $options['position'] : 0,
                ],
            ]
            : [];
    }
}
