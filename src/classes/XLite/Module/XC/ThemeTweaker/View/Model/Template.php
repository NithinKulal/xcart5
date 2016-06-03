<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Model;
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
    protected $schemaDefault = array(
        'body' => array(
            self::SCHEMA_CLASS      => 'XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror',
            self::SCHEMA_LABEL      => 'Template',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_FIELD_ONLY => true,
            self::SCHEMA_TRUSTED    => true,
            \XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror::PARAM_CODE_MODE => 'twig',
            \XLite\Module\XC\ThemeTweaker\View\FormField\Textarea\CodeMirror::PARAM_COLS      => 130,
        ),
    );

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

        return $model ?: new \XLite\Module\XC\ThemeTweaker\Model\Template;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\ThemeTweaker\View\Form\Model\Template';
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
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save changes',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        if (!\XLite\Core\Request::getInstance()->template) {
            $result['templates'] = new \XLite\View\Button\SimpleLink(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Back to templates list',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('theme_tweaker_templates'),
                )
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
                    $localPath = $this->getModelObjectValue('template');
                }

                if ($localPath) {
                    $model = $this->getModelObject();
                    $fullPath = $this->getFullPathByLocalPath(
                        $localPath,
                        $model->getId() ? 'theme_tweaker/default' : null
                    );
                    $value = \Includes\Utils\FileManager::read($fullPath);
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
        if (\XLite\Core\Request::getInstance()->template) {
            $localPath = \XLite\Core\Request::getInstance()->template;

        } elseif ($this->getModelObject()->getId()) {
            $localPath = $this->getModelObjectValue('template');
        }

        if ($localPath) {
            $fullPath = $this->getFullPathByLocalPath($localPath, 'theme_tweaker/default');
            \Includes\Utils\FileManager::write($fullPath, $body);

            $data['template'] = substr($fullPath, strlen(LC_DIR_SKINS));

            $this->getTemplateCacheManager()->invalidate($fullPath);
        }

        parent::setModelProperties($data);
    }

    /**
     * Returns full path
     *
     * @param string $localPath Local path
     * @param string $skin      Skin OPTIONAL
     *
     * @return string
     */
    protected function getFullPathByLocalPath($localPath, $skin = null)
    {
        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        $pathSkin = '';
        $locale = '';
        $shortPath = '';

        foreach ($layout->getSkinPaths(\XLite::CUSTOMER_INTERFACE) as $path) {
            if (strpos($localPath, $path['name']) === 0) {
                $pathSkin = $path['name'];

                $locale = substr(
                    $localPath,
                    strpos($localPath, LC_DS, strlen($pathSkin)) + strlen(LC_DS),
                    strpos($localPath, LC_DS, strlen($pathSkin . LC_DS)) - strlen($pathSkin . LC_DS)
                );

                $shortPath = substr($localPath, strpos($localPath, LC_DS, strlen($pathSkin . LC_DS)) + strlen(LC_DS));

                break;
            }
        }

        return ($shortPath && $pathSkin && $locale)
            ? $this->getFullPathByShortPath($shortPath, ($skin) ?: $pathSkin, $locale)
            : '';
    }

    /**
     * Returns full path
     *
     * @param string $shortPath Short path
     * @param string $skin      Skin OPTIONAL
     * @param string $locale    Locale OPTIONAL
     *
     * @return string
     */
    protected function getFullPathByShortPath($shortPath, $skin = 'theme_tweaker/default', $locale = null)
    {
        $result = '';

        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        foreach ($layout->getSkinPaths(\XLite::CUSTOMER_INTERFACE) as $path) {
            if ($path['name'] === $skin
                && (null === $locale || $path['locale'] === $locale)
            ) {
                $result = $path['fs'] . LC_DS . $shortPath;

                break;
            }
        }

        return $result;
    }
}
