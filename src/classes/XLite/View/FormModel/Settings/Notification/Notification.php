<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Settings\Notification;

class Notification extends \XLite\View\FormModel\AFormModel
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'notification/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        $list[] = 'notification/help.css';

        return $list;
    }

    /**
     * @return string|null
     */
    protected function getTarget()
    {
        return 'notification';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        return [
            'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
            'page'               => $this->getDataObject()->default->page,
        ];
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        return [
            'settings'        => [
                'label'    => static::t('Settings'),
                'position' => 100,
            ],
            'scheme'          => [
                'label'    => static::t('Scheme'),
                'position' => 200,
            ],
            'system_settings' => [
                'label'    => static::t('System settings'),
                'collapse' => true,
                'expanded' => false,
                'position' => 300,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $available = $this->getDataObject()->settings->available;

        $help = $this->getWidget([
            'template' => 'notification/help.twig',
        ])->getContent();

        return [
            'settings'        => [
                'status'  => [
                    'label'       => static::t('Notification state'),
                    'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                    'on_caption'  => 'checkbox.onoff.on',
                    'off_caption' => 'checkbox.onoff.off',
                    'disabled'    => !$available,
                    'position'    => 100,
                ],
                'subject' => [
                    'label'    => static::t('Subject'),
                    'help'     => $help,
                    'position' => 200,
                ],
            ],
            'scheme'          => [
                'header'    => [
                    'label'    => static::t('Header'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 100,
                ],
                'greeting'  => [
                    'label'    => static::t('Greeting'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 200,
                ],
                'text'      => [
                    'label'    => static::t('Text'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 300,
                ],
                'body'      => [
                    'label'    => static::t('Body'),
                    'position' => 400,
                    'help'     => 'The body template may include other templates to generate the email notification. To customize it, please, copy appropriate templates to "skins/theme_tweaker/mail/"',
                ],
                'signature' => [
                    'label'    => static::t('Signature'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 500,
                ],
            ],
            'system_settings' => [
                'name'        => [
                    'label'       => static::t('Name'),
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                    ],
                    'position'    => 100,
                ],
                'description' => [
                    'label'       => static::t('Description'),
                    'type'        => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                    ],
                    'position'    => 200,
                ],
            ],
        ];
    }


    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $list = parent::getFormButtons();
        $list['notifications'] = new \XLite\View\Button\SimpleLink(
            [
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('notifications'),
                \XLite\View\Button\AButton::PARAM_LABEL => 'Back to notifications list',
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
            ]
        );

        return $list;
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'form_model/settings/notification/notification.twig';

        return $list;
    }

    /**
     * Return all variables
     *
     * @return array
     */
    protected function getVariables()
    {
        return \XLite\Core\Mailer::getInstance()->getAllVariables();
    }
}
