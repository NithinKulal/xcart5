<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Attribute view model
 */
class Attribute extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Attribute',
            self::SCHEMA_REQUIRED => true,
        ],
        'attribute_group' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\AttributeGroups',
            self::SCHEMA_LABEL    => 'Attribute group',
            self::SCHEMA_REQUIRED => false,
        ],
        'type'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\AttributeTypes',
            self::SCHEMA_LABEL    => 'Type',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Return true if param value may contain anything
     *
     * @param string $name Param name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        $result = parent::isParamTrusted($name);

        if (!$result && $name === 'name') {
            $result = true;
        }

        return $result;
    }

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
     * Defines the CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'attribute/style.css';

        return $list;

    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $this->preprocessFormFieldsForSectionDefault();

        return $this->getFieldsBySchema($this->schemaDefault);
    }

    /**
     * Preprocess schemaDefault
     *
     * @return void
     */
    protected function preprocessFormFieldsForSectionDefault()
    {
        if ($this->getModelObject()->getId()) {
            $this->schemaDefault['type'][self::SCHEMA_COMMENT]
                = 'Before editing attributes specific for the chosen type you should save the changes';

            if (
                $this->getModelObject()->getAttributeValuesCount()
                || (
                    $this->getModelObject()->getProductClass()
                    && $this->getModelObject()->getProductClass()->getProductsCount()
                )
            ) {
                $this->schemaDefault['type'][self::SCHEMA_COMMENT] = 'Attribute data will be lost. warning text';
            }

            if (\XLite\Model\Attribute::TYPE_SELECT == $this->getModelObject()->getType()) {
                $this->schemaDefault['values'] = [
                    self::SCHEMA_CLASS                                    => 'XLite\View\FormField\ItemsList',
                    self::SCHEMA_LABEL                                    => 'Attribute values',
                    \XLite\View\FormField\ItemsList::PARAM_LIST_CLASS     => 'XLite\View\ItemsList\Model\AttributeOption',
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'custom-field type-' . $this->getModelObject()->getType(),
                ];

                if ($this->getModelObject()->getAttributeOptions()->count()) {
                    $this->schemaDefault['applySortingGlobally'] = [
                        self::SCHEMA_CLASS                                    => 'XLite\View\FormField\Input\Checkbox',
                        self::SCHEMA_FIELD_ONLY => true,
                        \XLite\View\FormField\Input\Checkbox::PARAM_CAPTION => static::t('Apply sorting globally'),
                    ];
                }
            }

            if (
                \XLite\Model\Attribute::TYPE_CHECKBOX == $this->getModelObject()->getType()
            ) {
                $this->schemaDefault['addToNew'] = [
                    self::SCHEMA_CLASS                                    => '\XLite\View\FormField\Select\CheckboxList\YesNo',
                    self::SCHEMA_LABEL                                    => 'Default value',
                    \XLite\View\FormField\AFormField::PARAM_HELP          => 'This value will be added to new products or class’s assigns automatically',
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'custom-field type-' . $this->getModelObject()->getType(),
                ];
            }
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
        $data['attribute_group'] = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')
            ->find($data['attribute_group']);

        if (!isset($data['addToNew'])) {
            $data['addToNew'] = [];
        }

        parent::setModelProperties($data);

        $this->getModelObject()->setProductClass($this->getProductClass());
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Attribute
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Model\Attribute;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Model\Attribute';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass()
        . (
        $this->getModelObject()->getId()
            ? ' attribute-type-' . $this->getModelObject()->getType()
            : ''
        );
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return null;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Save changes' : 'Next wizard';
        $style = 'action ' . ($this->getModelObject()->getId() ? 'save' : 'next');

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => $style,
            ]
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
            \XLite\Core\TopMessage::addInfo('The attribute has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The attribute has been added');
        }
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionUpdate()
    {
        parent::postprocessSuccessActionUpdate();

        if (\XLite\Model\Attribute::TYPE_SELECT == $this->getModelObject()->getType()
            && $this->getRequestData('applySortingGlobally')) {

            foreach ($this->getModelObject()->getAttributeOptions() as $option) {
                \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')
                    ->updatePositionByOption($option);
            }
        }
    }
}
