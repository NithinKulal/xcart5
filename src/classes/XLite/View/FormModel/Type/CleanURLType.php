<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use XLite\View\AView;
use XLite\View\FormModel\Type\Base\AType;

class CleanURLType extends AType
{
    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/clean_url_type.js'];
    }

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
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound'        => true,
                'extension'       => '',
                'objectClassName' => '',
                'objectId'        => '',
                'objectIdName'    => '',
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('autogenerate', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label'          => static::t('Autogenerate Clean URL'),
            'form_row_class' => '',
        ]);
        $builder->add('clean_url', 'XLite\View\FormModel\Type\SymbolType', [
            'show_label_block' => false,
            'help'             => static::t('Human readable and SEO friendly web address for the page.'),
            'right_symbol'     => $options['extension'],
            'pattern'          => [
                'alias'     => 'CleanUrl',
                'regex'     => '^[\w\-]*$',
                'extension' => $options['extension'],
            ],
            'enable_when'        => [
                '..' => [
                    'autogenerate' => false,
                ],
            ],
            'form_row_class'   => '',
        ]);
        $builder->add('force', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label'          => static::t('Assign entered Clean URL to this page anyway'),
            'show_when'      => [
                '..' => [
                    'autogenerate' => false,
                ],
            ],
            'form_row_class' => '',
        ]);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $disabled = !LC_USE_CLEAN_URLS;

        $entity = \Xlite\Core\Database::getRepo($options['objectClassName'])->find($options['objectId']);
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        $hasForcibleError = false;
        $hasUnForcibleError = false;
        $errorMessage = '';
        $resolveHints = [];

        $value = $view->vars['value']->clean_url . $options['extension'];

        if (!$view->vars['valid']
            && !$repo->isURLUnique($value, $options['objectClassName'], $options['objectIdName'])
        ) {
            $conflict = $repo->getConflict($value, $options['objectClassName'], $options['objectIdName']);

            $resolveHints[] = static::t('Enter a different Clean URL value for this page');

            if ($conflict->getCleanURL() === $value) {
                $hasUnForcibleError = true;
                $errorMessage = static::t('The Clean URL entered is already in use.', ['entityURL' => $repo->buildEditURL($conflict)]);
            } else {
                $hasForcibleError = true;
                $errorMessage = static::t('The Clean URL entered is a redirect to object.', ['entityURL' => $repo->buildEditURL($conflict)]);
                $resolveHints[] = static::t('Enable the option "Assign entered Clean URL to this page anyway" to dissociate the entered Clean URL from the page it is currently used for and assign it to the page of the object being edited.');
            }
        }

        $view->vars = array_replace($view->vars, [
            'disabled'              => $disabled,
            'cleanUrlTemplate'      => \XLite::getInstance()->getShopURL($repo->buildFakeURL($entity ?: $options['objectClassName'])),
            'cleanUrl'              => \XLite::getInstance()->getShopURL($repo->buildURL($options['objectClassName'], [$options['objectIdName'] => $options['objectId']])),
            'savedValue'            => $entity ? $entity->getCleanURL() : '',
            'extension'             => $options['extension'],
            'hasForcibleConflict'   => $hasForcibleError ? '1' : '0',
            'hasUnForcibleConflict' => $hasUnForcibleError ? '1' : '0',
            'errorMessage'          => $errorMessage,
            'resolveHint'           => sprintf('<ul><li>' . implode('</li><li>', $resolveHints) . '</li></ul>'),
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\Base\CompositeType';
    }
}
