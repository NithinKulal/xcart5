<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Model;

use XLite\Core\Layout;
use XLite\Core\Templating\CacheManagerInterface;

/**
 * Theme tweaker template view model
 */
class Template extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'body' => [
            self::SCHEMA_CLASS                                                                => 'XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror',
            self::SCHEMA_LABEL                                                                => 'Template',
            self::SCHEMA_REQUIRED                                                             => false,
            self::SCHEMA_FIELD_ONLY                                                           => true,
            self::SCHEMA_TRUSTED                                                              => true,
            \XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror::PARAM_CODE_MODE => 'twig',
            \XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror::PARAM_COLS      => 130,
        ],
    ];

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Retrieve property from the request or from  model object
     *
     * @param string $name Field/property name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        if ('template' === $name
            && \XLite\Core\Request::getInstance()->template
        ) {
            $result = \XLite\Core\Request::getInstance()->template;

        } else {
            $result = parent::getDefaultFieldValue($name);
        }

        return $result;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\ThemeTweaker\Model\Template
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->find($this->getModelId())
            : null;

        if (!$model && \XLite\Core\Request::getInstance()->template) {
            $localPath = \XLite\Core\Request::getInstance()->template;
            $model = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')
                ->findOneByTemplate($localPath);
        }

        return $model ?: new \XLite\Module\XC\ThemeTweaker\Model\Template();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\Form\Model\Template';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['save'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save changes',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        if (!\XLite\Core\Request::getInstance()->template) {
            $result['templates'] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Back to templates list',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('theme_tweaker_templates'),
                ]
            );
        }

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' !== $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The template has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The template has been added');
        }
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        switch ($name) {
            case 'body':
                $value = '';
                $localPath = '';

                if (\XLite\Core\Request::getInstance()->template) {
                    $localPath = \XLite\Core\Request::getInstance()->template;

                } elseif ($this->getModelObject()->getId()) {
                    $localPath = parent::getModelObjectValue('template');
                }

                if ($localPath) {
                    $value = \Includes\Utils\FileManager::read(\LC_DIR_SKINS . $localPath);
                }

                break;

            default:
                $value = parent::getModelObjectValue($name);
                break;
        }

        return $value;
    }

    /**
     * Returns a (cached) templating engine instance
     *
     * @return CacheManagerInterface
     */
    protected function getTemplateCacheManager()
    {
        return $this->getContainer()->get('template_cache_manager');
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $body = $data['body'];
        unset($data['body']);

        $data['date'] = LC_START_TIME;

        $localPath = '';
        $layout = \XLite\Core\Layout::getInstance();
        if (\XLite\Core\Request::getInstance()->template) {
            $localPath = \XLite\Core\Request::getInstance()->template;

        } elseif ($this->getModelObject()->getId()) {
            $localPath = $this->getModelObjectValue('template');
        }

        if ($localPath) {
            $interface = \XLite\Core\Request::getInstance()->interface;
            $innerInterface = \XLite\Core\Request::getInstance()->innerInterface;

            if ($interface === \XLite::MAIL_INTERFACE) {
                $layout->setMailSkin($innerInterface);
            }

            $fullPath = $layout->getFullPathByLocalPath($localPath, $interface);

            if (\Includes\Utils\FileManager::write($fullPath, $body)) {
                $data['template'] = substr($fullPath, strlen(\LC_DIR_SKINS));

                $this->getTemplateCacheManager()->invalidate($fullPath);
            } else {
                $this->addErrorMessage(
                    'file permissions',
                    static::t('The file {{file}} does not exist or is not writable.', ['file' => $localPath]),
                    ['file' => $fullPath]
                );
            }

        }

        parent::setModelProperties($data);
    }
}
