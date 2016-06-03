<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RadioTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'v_model' => preg_replace('/\.[^.]*$/', '', $view->vars['v_model']),
        ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\RadioType';
    }
}
