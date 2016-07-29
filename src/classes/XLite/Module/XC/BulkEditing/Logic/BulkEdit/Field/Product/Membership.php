<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Membership extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $position = isset($options['position']) ? $options['position'] : 0;

        $memberships = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findActiveMemberships() as $membership) {
            $memberships[$membership->getMembershipId()] = $membership->getName();
        }

        return [
            $name                => [
                'label'             => static::t('Memberships'),
                'type'              => 'XLite\View\FormModel\Type\Select2Type',
                'multiple'          => true,
                'choices'           => array_flip($memberships),
                'choices_as_values' => true,
                'position'          => $position,
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
        $memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($data->{$name});

        $membershipEditMode = $data->{$name . '_edit_mode'};
        if ($membershipEditMode === 'remove') {
            $object->removeMembershipsByMemberships($memberships);

        } elseif ($membershipEditMode === 'replace_with') {
            $object->replaceMembershipsByMemberships($memberships);

        } else {
            $object->addMembershipsByMemberships($memberships);
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
                'name'    => static::t('Memberships'),
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
        /** @var \XLite\Model\Membership $membership */
        foreach ($object->getMemberships() as $membership) {
            $result[] = $membership->getName();
        }

        return implode(', ', $result);
    }
}
