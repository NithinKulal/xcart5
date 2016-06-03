<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\BulkEdit\Field\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class MinimumPurchaseQuantity extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => array_replace(
                $options,
                [
                    'label'    => isset($options['label']) ? $options['label'] : 0,
                    'type'     => 'XLite\View\FormModel\Type\PatternType',
                    'pattern'  => [
                        'alias'      => 'integer',
                        'rightAlign' => false,
                    ],
                    'position' => isset($options['position']) ? $options['position'] : 0,
                ]
            ),
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => 1,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $membershipRepo = \XLite\Core\Database::getRepo('XLite\Model\Membership');
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity');

        $membershipId = str_replace('membership_', '', $name);
        $membership = $membershipRepo->find($membershipId);

        $data = max(1, (int) $data->{$name});

        $minQuantity = $repo->getMinQuantity($object, $membership);
        if ($minQuantity) {
            $minQuantity->setQuantity($data);

        } else {
            $minQuantity = [
                'quantity' => $data,
                'product'  => $object,
            ];

            if ($membership) {
                $minQuantity['membership'] = $membership;
            }

            $repo->insertInBatch([$minQuantity]);
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
        $membershipRepo = \XLite\Core\Database::getRepo('XLite\Model\Membership');

        $membershipId = str_replace('membership_', '', $name);
        $membership = $membershipRepo->find($membershipId);

        return [
            $name => [
                'label'    => $options['label'],
                'value'    => $object->getMinQuantity($membership),
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }
}
