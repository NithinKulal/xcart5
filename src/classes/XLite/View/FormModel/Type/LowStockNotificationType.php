<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LowStockNotificationType extends \XLite\View\FormModel\Type\Base\AType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setData(isset($options['data']) ? $options['data'] : false);
        $builder->addViewTransformer(new BooleanToStringTransformer($options['value']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'value'       => $options['value'],
            'checked'     => null !== $form->getViewData(),
            'on_caption'  => $options['on_caption'],
            'off_caption' => $options['off_caption'],
        ]);

        if (!$this->isNotificationsEnabled()) {
            $view->vars['value'] = false;
            $view->vars['checked'] = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form, $viewData) {
            return $viewData;
        };

        $resolver->setDefaults([
            'value'       => true,
            'empty_data'  => $emptyData,
            'compound'    => false,
            'on_caption'  => static::t('Switcher YES'),
            'off_caption' => static::t('Switcher NO'),
        ]);

        if (!$this->isNotificationsEnabled()) {
            $resolver->setDefaults([
                'disabled'       => true,
                'help'           => static::t('Notify administrator inactivity help', [
                    'link' => \XLite\Core\Converter::buildURL('notification', '', [
                        'templatesDirectory' => 'low_limit_warning',
                        'page'               => 'admin'
                    ])
                ]),
                'form_row_class' => 'form-group disabled'
            ]);
        }
    }

    /**
     * Check if low limit warning notification disabled
     *
     * @return bool
     */
    protected function isNotificationsEnabled()
    {
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find('low_limit_warning');

        return $notification && $notification->getEnabledForAdmin();
    }
}