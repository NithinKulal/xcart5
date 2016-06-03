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

class FieldSetType extends AType
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
                'compound'       => true,
                'help'           => null,
                'description'    => null,
                'fields'         => [],
                'collapse'       => false,
                'expanded'       => true,
                'error_bubbling' => false,
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
            foreach ($options['fields'] as $name => $field) {
                $type = (isset($field[0]) && is_string($field[0])) ? $field[0] : null;

                if ($type) {
                    $options = (is_array($field) && isset($field[1])) ? $field[1] : [];
                    $builder->add($name, $type, $options);
                }
            }
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $title_attr = [];

        /**
         * Bootstrap collapse behavior used
         * @see http://getbootstrap.com/javascript/#collapse
         */
        if ($options['collapse']) {
            $view->vars['collapse'] = $options['collapse'];

            $title_attr['data-toggle'] = 'collapse';
            $title_attr['data-target'] = '#' . $view->vars['id'];
            $title_attr['aria-expanded'] = $options['expanded'] ? 'true' : 'false';
            $title_attr['aria-controls'] = $view->vars['id'];
            $title_attr['class'] = 'collapsible';
            if (!$options['expanded']) {
                $title_attr['class'] .= ' collapsed';
            }
            $view->vars['title_attr'] = $title_attr;

            $view->vars['attr']['class'] = isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] : '';
            $view->vars['attr']['class'] .= ' collapse';

            if ($options['expanded']) {
                $view->vars['attr']['class'] .= ' in';
            }
        }

        $label = $options['label'];

        /**
         * `help` and `description` available only for non-empty `label`
         */
        $view->vars = array_replace($view->vars, [
            'label'       => $label,
            'help'        => $label ? $options['help'] : null,
            'description' => $label ? $options['description'] : null,
            'title_attr'  => $title_attr,
        ]);
    }
}
