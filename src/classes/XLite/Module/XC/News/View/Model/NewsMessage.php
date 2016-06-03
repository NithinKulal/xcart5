<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\Model;

/**
 * News message view model
 */
class NewsMessage extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'date' => array(
            self::SCHEMA_CLASS    => 'XLite\View\DatePicker',
            self::SCHEMA_LABEL    => 'Date',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
            self::SCHEMA_REQUIRED => false,
        ),
        'name' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'News title',
            self::SCHEMA_REQUIRED => true,
        ),
        'body' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Content',
            self::SCHEMA_REQUIRED => true,
        ),
        'brief_description' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Brief description',
            self::SCHEMA_REQUIRED => true,
            \XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES => array(
                'maxlength' => '300'
            )
        ),
        'enabled' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Enabled',
            self::SCHEMA_REQUIRED => false,
        ),
        'cleanURL' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL    => 'Clean URL',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\AFormField::PARAM_LABEL_HELP => 'Human readable and SEO friendly web address for the page.',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_CLASS_NAME  => 'XLite\Module\XC\News\Model\NewsMessage',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_ID_NAME     => 'id',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_ID                 => 'cleanurl',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_EXTENSION          => \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION,
        ),
        'meta_title' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'News page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use news title as Page Title.',
        ),
        'meta_tags' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
        ),
        'meta_desc_type' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\MetaDescriptionType',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
        ),
        'meta_desc' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => '',
            \XLite\View\FormField\AFormField::PARAM_USE_COLON => false,
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array (
                    'meta_desc_type' => array('C'),
                )
            ),
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
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        if ('meta_title' === $name) {
            $data[static::SCHEMA_PLACEHOLDER] = static::t('Default');
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\News\Model\NewsMessage
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\News\Model\NewsMessage')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\XC\News\Model\NewsMessage;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\News\View\Form\Model\NewsMessage';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update' : 'Create';

        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => $label,
                \XLite\View\Button\AButton::PARAM_STYLE => 'action regular-main-button',
            )
        );

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
            \XLite\Core\TopMessage::addInfo('The news message has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The news message has been added');
        }
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
        if (isset($data['date']) && !is_numeric($data['date'])) {
            $data['date'] = \XLite\Core\Converter::parseFromJsFormat($data['date']);
        }

        parent::setModelProperties($data);
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        parent::rollbackModel();

        $urls = $this->getModelObject()->getCleanURLs();
        foreach ($urls as $url) {
            \XLite\Core\Database::getEM()->detach($url);
        }
    }
}
