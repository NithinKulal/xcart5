<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Weight extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $weightFormat = \XLite\Core\Config::getInstance()->Units->weight_format;
        $weightFormatDelimiters = \XLite\View\FormField\Select\FloatFormat::getDelimiters($weightFormat);

        return [
            $name => [
                'label'    => static::t('Weight'),
                'type'     => 'XLite\View\FormModel\Type\SymbolType',
                'symbol'   => \XLite\Core\Config::getInstance()->Units->weight_symbol,
                'pattern'  => [
                    'alias'          => 'decimal',
                    'digitsOptional' => false,
                    'rightAlign'     => false,
                    'groupSeparator' => $weightFormatDelimiters[0],
                    'radixPoint'     => $weightFormatDelimiters[1],
                    'digits'         => 4,
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
        $object->setWeight($data->{$name});
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
        return [
            $name => [
                'label'    => static::t('Weight'),
                'value'    => \XLite\View\AView::formatWeight($object->getWeight()),
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }
}
