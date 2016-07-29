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
            $name                => [
                'label'    => static::t('Categories'),
                'type'     => 'XLite\View\FormModel\Type\ProductCategoryType',
                'multiple' => true,
                'position' => $position,
            ],
            $name . '_edit_mode' => [
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => [
                    static::t('Add')     => 'add',
                    static::t('Remove')  => 'remove',
                    static::t('Move to') => 'move-to',
                ],
                'choices_as_values' => true,
                'placeholder'       => false,
                'multiple'          => false,
                'expanded'          => true,
                'is_data_field'     => false,
                'position'          => $position + 1,
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

        } elseif ($categoryEditMode === 'move-to') {
            $object->replaceCategoryProductsLinksByCategories($categories);

        } else {
            $object->addCategoryProductsLinksByCategories($categories);
        }
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('Categories'),
                'orderBy' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        $categories = [];
        /** @var \XLite\Model\Category $category */
        foreach ($object->getCategories() as $category) {
            $categories[] = $category->getStringPath();
        }

        return $categories ? implode(', ', $categories) : static::t('Not set');
    }
}
