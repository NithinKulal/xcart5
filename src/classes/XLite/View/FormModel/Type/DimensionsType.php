<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

class DimensionsType extends AType
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
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('length', 'XLite\View\FormModel\Type\PatternType', [
            'label' => false,
            'pattern'    => [
                'alias'          => 'decimal',
                'rightAlign'     => false,
                'digits'         => 2,
            ],
        ]);
        $builder->add('width', 'XLite\View\FormModel\Type\PatternType', [
            'label' => false,
            'pattern'    => [
                'alias'          => 'decimal',
                'rightAlign'     => false,
                'digits'         => 2,
            ],
        ]);
        $builder->add('height', 'XLite\View\FormModel\Type\PatternType', [
            'label' => false,
            'pattern'    => [
                'alias'          => 'decimal',
                'rightAlign'     => false,
                'digits'         => 2,
            ],
        ]);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
    }
}
