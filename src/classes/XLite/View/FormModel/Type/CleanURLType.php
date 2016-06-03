<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

class CleanURLType extends AType
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound' => true,
                'fields'   => [
                    'autogenerate' => [
                        'type'     => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                        'label'    => static::t('Autogenerate'),
                        'position' => 100,
                    ],
                    'clean_url'    => [
                        'show_label_block' => false,
                        'type'             => 'XLite\View\FormModel\Type\SymbolType',
                        'help'             => static::t('Human readable and SEO friendly web address for the page.'),
                        'right_symbol'     => '.html',
                        'show_when'        => [
                            '..' => [
                                'autogenerate' => false,
                            ],
                        ],
                        'position'         => 200,
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\Base\CompositeType';
    }
}
