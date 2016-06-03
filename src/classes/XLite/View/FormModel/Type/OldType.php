<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use XLite\View\FormModel\Type\Base\AType;

/**
 * @param OptionsResolver $resolver
 * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
 */
class OldType extends AType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound'     => false,
                'oldType'      => '',
                'fieldOptions' => [],
            ]
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $fieldOptions = [
            'fieldName' => $view->vars['full_name'],
            // 'id'        => $view->vars['id'],
            'id' => 'twtwer',
            'fieldOnly' => true,
        ];

        $fieldOptions = array_replace($fieldOptions, $options['fieldOptions']);

        $view->vars = array_replace($view->vars, [
            'fieldClass'   => $options['oldType'],
            'fieldOptions' => $fieldOptions
        ]);
    }

}
