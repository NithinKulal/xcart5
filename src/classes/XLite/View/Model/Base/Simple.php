<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Base;

/**
 * Simple CRUD 
 */
abstract class Simple extends \XLite\View\Model\AModel
{
    /**
     * Update message 
     * 
     * @var string
     */
    protected $updateMessage = null;

    /**
     * Create message 
     * 
     * @var string
     */
    protected $createMessage = null;

    /**
     * Entity class 
     * 
     * @var string
     */
    protected $entityClass = null;

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
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo($this->entityClass)->find($this->getModelId())
            : null;

        return $model ?: new $this->entityClass;
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
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
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
        if ('create' != $this->currentAction) {
            if ($this->updateMessage) {
                \XLite\Core\TopMessage::addInfo($this->updateMessage);
            }

        } else {
            if ($this->createMessage) {
                \XLite\Core\TopMessage::addInfo($this->createMessage);
            }
        }
    }

}

