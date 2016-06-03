<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Model;

/**
 * Review view model
 *
 */
class Review extends \XLite\View\Model\AModel
{

    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'rating' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Rating',
            self::SCHEMA_REQUIRED => false,
        ),
        'email' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Email',
            self::SCHEMA_REQUIRED => true,
        ),
        'reviewerName' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Customer name',
            self::SCHEMA_REQUIRED => true,
        ),
        'review' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Text of review',
            self::SCHEMA_REQUIRED => false,
        ),
        'status' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Status',
            self::SCHEMA_REQUIRED => false,
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
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->find($this->getModelId())
            : null;

        return $model
            ? $model
            : new \XLite\Module\XC\Reviews\Model\Review;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\Reviews\View\Form\Model\Review';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if ($this->getModelObject()->getId()) {
            if ($this->isApproved()) {
                $result['submit'] = new \XLite\View\Button\Submit(
                    array(
                        \XLite\View\Button\AButton::PARAM_LABEL    => 'Update',
                        \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                        \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                    )
                );
            } else {
                $result['approve'] = new \XLite\View\Button\Submit(
                    array(
                        \XLite\View\Button\AButton::PARAM_LABEL    => 'Approve',
                        \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                        \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                    )
                );
                $result['remove'] = new \XLite\View\Button\Submit(
                    array(
                        \XLite\View\Button\AButton::PARAM_LABEL => 'Remove',
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action',
                    )
                );
            }

        } else {
            $result['submit'] = new \XLite\View\Button\Submit(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL    => 'Create',
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                )
            );
        }

        return $result;
    }

    /**
     * Return whether review is approved
     *
     * @return boolean
     */
    protected function isApproved()
    {
        return \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED == $this->getModelObject()->getStatus();
    }
}
