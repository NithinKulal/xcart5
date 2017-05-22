<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Logic\BulkEdit\Field\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Tag extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;

        return [
            $name                => [
                'label'    => static::t('Tags'),
                'type'     => 'XLite\Module\XC\ProductTags\View\FormModel\Type\TagsType',
                'multiple' => true,
                'position' => $position,
            ],
            $name . '_edit_mode' => [
                'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                'choices'           => [
                    static::t('Add')       => 'add',
                    static::t('Remove')    => 'remove',
                    static::t('Replace with') => 'replace_with',
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
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag');
        $tags = $repo->getListByIdOrName($data->tags);

        $tagsEditMode = $data->tags_edit_mode;
        if ($tagsEditMode === 'remove') {
            $object->removeTagsByTags($tags);

        } elseif ($tagsEditMode === 'replace_with') {
            $object->replaceTagsByTags($tags);

        } else {
            $object->addTagsByTags($tags);
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
                'name'    => static::t('Tags'),
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
        $result = [];
        /** @var \XLite\Module\XC\ProductTags\Model\Tag $tag */
        foreach ($object->getTags() as $tag) {
            $result[] = $tag->getName();
        }

        return $result ? implode(', ', $result) : static::t('Not set');
    }
}
