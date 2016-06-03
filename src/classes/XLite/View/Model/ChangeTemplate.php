<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Change template
 */
class ChangeTemplate extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'template' => array(
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\Template',
            self::SCHEMA_LABEL      => 'Change template',
            self::SCHEMA_FIELD_ONLY => true,
        ),
    );

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Notification
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Model\ChangeTemplate';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Submit',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
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
            case 'template':
                $result = \XLite\View\FormField\Select\Template::SKIN_STANDARD;

                $currentModule = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();
                if ($currentModule) {
                    $currentColor = \XLite\Core\Layout::getInstance()->getLayoutColor();

                    $result = $currentModule->getModuleId()
                        . ($currentColor ? ('_' . $currentColor) : '');
                }

                break;

            default:
                $result = parent::getModelObjectValue($name);
        }

        return $result;
    }
}
