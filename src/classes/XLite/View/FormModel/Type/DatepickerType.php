<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

class DatepickerType extends AType
{
    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/datepicker_type.js'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($originalValue) {
                    return \XLite\Core\Converter::formatDate($originalValue);
                },
                function ($submittedValue) {
                    return \XLite\Core\Converter::parseFromJsFormat($submittedValue);
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $currentFormats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();

        $resolver->setDefaults(
            [
                'date_format' => $currentFormats['jsFormat']
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
        $view->vars = array_replace($view->vars, [
            'v_model' => '',
            'attr'    => array_replace(
                $view->vars['attr'],
                [
                    'v-datepicker'  => $view->vars['v_model'],
                    'detect-change' => 'off',
                    'detect-blur'   => 'off',
                    'format'        => $options['date_format']
                ]
            )
        ]);
    }

}
