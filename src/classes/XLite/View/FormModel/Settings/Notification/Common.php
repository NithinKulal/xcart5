<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Settings\Notification;

class Common extends \XLite\View\FormModel\AFormModel
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'notifications/common.less',
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
        return 'notification_common';
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
    protected function defineSections()
    {
        return array_replace(parent::defineSections(), [
            'customer' => [
                'label'       => static::t('notification.section.customer'),
                'description' => static::t('The settings in this section determine the content of email notifications sent to customer.'),
                'position'    => 100,
            ],
            'admin'    => [
                'label'       => static::t('notification.section.administrator'),
                'description' => static::t('The settings in this section determine the content of email notifications sent to admin.'),
                'position'    => 200,
            ],
        ]);
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $help = $this->getWidget([
            'template' => 'notification/help.twig',
        ])->getContent();

        return [
            self::SECTION_DEFAULT => [
                'greeting' => [
                    'label'    => static::t('Greeting'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 100,
                ],
            ],
            'customer'            => [
                'header'    => [
                    'label'    => static::t('Header'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 100,
                ],
                'signature' => [
                    'label'    => static::t('Signature'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 200,
                ],
            ],
            'admin'               => [
                'header'    => [
                    'label'    => static::t('Header'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 100,
                ],
                'signature' => [
                    'label'    => static::t('Signature'),
                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'help'     => $help,
                    'position' => 200,
                ],
            ],
        ];
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
