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

class SymbolType extends AType
{
    /**
     * @return array
     */
    public static function getCommonFiles()
    {
        return [
            AView::RESOURCE_JS => ['jquery.inputmask/dist/jquery.inputmask.bundle.js'],
        ];
    }

    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/symbol_type.js'];
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
                'left_symbol'  => '',
                'right_symbol' => '',
                'symbol'       => '',
                'pattern'      => '',
                'compound'     => false
            ]
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['pattern']) {
            $pattern = !is_array($options['pattern']) ? ['mask' => $options['pattern']] : $options['pattern'];

            $view->vars = array_replace(
                $view->vars,
                [
                    'attr' => array_replace(
                        $view->vars['attr'],
                        [
                            'v-xlite-pattern' => '',
                            'pattern'         => json_encode($pattern),
                        ]
                    ),
                ]
            );
        }

        $view->vars = array_replace(
            $view->vars,
            [
                'left_symbol'  => $options['left_symbol'] ?: $options['symbol'],
                'right_symbol' => $options['right_symbol'],
            ]
        );
    }
}
