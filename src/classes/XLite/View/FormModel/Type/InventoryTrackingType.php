<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\AView;
use XLite\View\FormModel\Type\Base\AType;

/**
 * Class used for Categories type on product modify page.
 *
 * It lazy load there choices, so list populated only with selected values
 */
class InventoryTrackingType extends AType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\Base\CompositeType';
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'fields'   => [
                    'inventory_tracking' => [
                        'type' => 'XLite\View\FormModel\Type\SwitcherType'
                    ],
                    'quantity' => [
                        'label' => ''
                    ]
                ]
            ]
        );
    }
}
