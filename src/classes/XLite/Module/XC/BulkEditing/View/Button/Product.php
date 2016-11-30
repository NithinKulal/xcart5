<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Button;

/**
 * ItemsExport button
 */
class Product extends ABulkEdit
{
    /**
     * @return array
     */
    protected function getScenarios()
    {
        return [
            'product_categories'           => [
                'position' => 100,
            ],
            'product_inventory'            => [
                'position' => 200,
            ],
            'product_price_and_membership' => [
                'position' => 300,
            ],
            'product_shipping_info'        => [
                'position' => 400,
            ],
            // 'product_global_attributes'    => [
            //     'position' => 500,
            // ],
        ];
    }

    protected function defineAdditionalButtons()
    {
        $result = [
            'delete'         => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action link list-action hide-on-disable',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 0,
            ],
            'delete_divider' => [
                'class'    => 'XLite\View\Button\Dropdown\Divider',
                'params'   => [
                    'style'      => 'more-action hide-on-disable',
                ],
                'position' => 1,
            ],
        ];

        $scenarios = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarios();
        $availableScenarios = $this->getScenarios();

        foreach ($availableScenarios as $name => $options) {
            $data = isset($scenarios[$name]) ? $scenarios[$name] : [];
            if (!$data) {
                continue;
            }

            if (isset($data['url'])) {
                $result[$name] = [
                    'class'    => 'XLite\Module\XC\BulkEditing\View\Button\ComingSoon',
                    'params'   => [
                        'label'    => $data['title'],
                        'location' => $data['url'],
                        'blank'    => true,
                        'style'    => 'always-enabled action link list-action',
                    ],
                    'position' => $options['position'],
                ];

            } else {
                $result[$name] = [
                    'params'   => [
                        'label'      => $data['title'],
                        'action'     => 'start',
                        'formParams' => ['target' => 'bulk_edit', 'scenario' => $name],
                        'style'      => 'always-enabled action link list-action',
                    ],
                    'position' => $options['position'],
                ];
            }
        }

        return $result;
    }
}
