<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\TinyMCE\View\FormModel\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class used for Categories type on product modify page.
 *
 * It lazy load there choices, so list populated only with selected values
 */
class TextareaAdvancedType extends \XLite\View\FormModel\Type\TextareaAdvancedType implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    public static function getJSFiles()
    {
        return ['modules/CDev/TinyMCE/form_model/type/tinymce.js'];
    }

    /**
     * @return string
     */
    public static function getCSSFiles()
    {
        return [
            [
                'file'  => 'modules/CDev/TinyMCE/form_model/type/tinymce.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\OldType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'oldType' => 'XLite\Module\CDev\TinyMCE\View\FormField\Textarea\Advanced',
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
            'fieldOptions' => array_replace(
                $view->vars['fieldOptions'],
                [
                    'attributes' => [
                        'v-tinymce' => $view->vars['v_model'],
                        'v-model'   => $view->vars['v_model'],
                        'id'        => $view->vars['id'],
                    ],
                    'value'      => $view->vars['value'],
                ]
            ),
        ]);
    }
}
