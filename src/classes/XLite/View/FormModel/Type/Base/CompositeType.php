<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use XLite\View\FormModel\FormGenerator;

class CompositeType extends AType
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
                'fields'   => [],
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['fields'])) {

            $fields = static::prepareFields($options['fields']);
            foreach (FormGenerator::generateFields($fields) as $name => $field) {
                $type = (isset($field[0]) && is_string($field[0])) ? $field[0] : null;

                if ($type) {
                    $builder->add($name, $type, isset($field[1]) ? $field[1] : []);
                }
            }
        }
    }

    protected static function prepareFields($fields)
    {
        $first = true;

        /** @todo: rewrite: use domain related classes and BOOTSTRAP mixins */
        return array_map(
            function ($item) use (&$first) {
                if ($first) {
                    $first = false;
                    $showLabelBlock =  array_key_exists('show_label_block', $item)
                        ? $item['show_label_block'] === true || false
                        : false;

                    return array_replace(
                        $item,
                        [
                            'show_label_block' => $showLabelBlock,
                            'form_row_class' => '',
                        ]
                    );

                } else {
                    return array_replace(
                        $item,
                        [
                            'form_row_class' => '',
                        ]
                    );
                }
            },
            $fields
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, [
            'input_grid' => '',
        ]);
    }
}
