<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class BulkEditTypeExtension extends AbstractTypeExtension
{
    /**
     * Adds a CSRF field to the root form view.
     *
     * @param FormView      $view    The form view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();
            $bulkEditForm = $factory->createNamed(
                'bulk_edit',
                'XLite\View\FormModel\Type\Base\SystemHiddenType',
                json_encode([]),
                [
                    'mapped' => false,
                ]
            );

            $view->children['bulk_edit'] = $bulkEditForm->createView($view);
        }
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
