<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Category extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;

        return [
            $name . '_edit_mode' => [
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => [
                    static::t('Add')          => 'add',
                    static::t('Remove')       => 'remove',
                    static::t('Replace with') => 'replace',
                ],
                'choices_as_values' => true,
                'placeholder'       => false,
                'multiple'          => false,
                'expanded'          => true,
                'is_data_field'     => false,
                // 'input_grid'        => 'col-sm-8',
                'position'          => $position,
            ],
            $name                => [
                'label'    => static::t('Category'),
                'type'     => 'XLite\View\FormModel\Type\ProductCategoryType',
                'multiple' => true,
                // 'input_grid' => 'col-sm-8',
                'position' => $position + 1,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name . '_edit_mode' => 'add',
            $name                => [],
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')->findByIds($data->{$name});

        $categoryEditMode = $data->{$name . '_edit_mode'};
        if ($categoryEditMode === 'remove') {
            $object->removeCategoryProductsLinksByCategories($categories);

        } elseif ($categoryEditMode === 'replace') {
            $object->replaceCategoryProductsLinksByCategories($categories);

        } else {
            $object->addCategoryProductsLinksByCategories($categories);
        }
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

        $categories = [];
        /** @var \XLite\Model\Category $category */
        foreach ($object->getCategories() as $category) {
            $categories[] = $category->getStringPath();
        }

        return [
            $name => [
                'label'    => static::t('Category'),
                'value'    => $categories ? implode(', ', $categories) : static::t('Not defined'),
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }
}
