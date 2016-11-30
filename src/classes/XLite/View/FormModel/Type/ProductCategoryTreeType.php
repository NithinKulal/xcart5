<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

/**
 * Class used for Categories type on product modify page.
 *
 * It lazy load there choices, so list populated only with selected values
 */
class ProductCategoryTreeType extends AType
{
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
        $loader = new ProductCategoryTreeLoader();

        $resolver->setDefaults(
            [
                'choice_loader' => $loader,
                'choice_label'  => function ($value) use ($loader) {
                    /**
                     * When $loader::getValueLabel() called $loader::loadValuesForChoices() already invoked
                     */
                    return $loader->getValueLabel($value);
                },
            ]
        );
    }
}
