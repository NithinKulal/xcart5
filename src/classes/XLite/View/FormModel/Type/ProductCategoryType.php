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
class ProductCategoryType extends AType
{
    /**
     * @return array
     */
    public static function getCommonFiles()
    {
        return [
            AView::RESOURCE_JS  => ['select2/dist/js/select2.min.js'],
            AView::RESOURCE_CSS => ['select2/dist/css/select2.min.css']
        ];
    }

    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/product_category_type.js'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $loader = new ProductCategoryLoader();

        $resolver->setDefaults(
            [
                'choice_loader' => $loader,
                'choice_label'  => function ($value) use ($loader) {
                    /**
                     * When $loader::getValueLabel() called $loader::loadValuesForChoices() already invoked
                     */
                    return $loader->getValueLabel($value);
                }
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
            'attr'  => array_replace(
                $view->vars['attr'],
                ['v-xlite-product-category' => $view->vars['v_model']]
            )
        ]);
    }
}
