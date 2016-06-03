<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline;

use XLite\Core\MagicMethodsIntrospectionInterface;

/**
 * Abstract inline form-field
 */
abstract class AInline extends \XLite\View\AView
{
    const PARAM_ENTITY          = 'entity';
    const PARAM_ITEMS_LIST      = 'itemsList';
    const PARAM_FIELD_NAME      = 'fieldName';
    const PARAM_FIELD_PARAMS    = 'fieldParams';
    const PARAM_EDIT_ONLY       = 'editOnly';
    const PARAM_VIEW_ONLY       = 'viewOnly';
    const PARAM_FIELD_NAMESPACE = 'fieldNamespace';
    const PARAM_VIEW_TIP        = 'viewTip';
    const PARAM_VIEW_FULL_WIDTH = 'fullWidth';

    const FIELD_NAME   = 'name';
    const FIELD_PARAMS = 'parameters';
    const FIELD_CLASS  = 'class';
    const FIELD_LABEL  = 'label';

    /**
     * Form fields
     *
     * @var array
     */
    protected $fields;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/inline/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/inline/controller.js';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ENTITY          => new \XLite\Model\WidgetParam\TypeObject('Entity', null, false, 'XLite\Model\AEntity'),
            static::PARAM_ITEMS_LIST      => new \XLite\Model\WidgetParam\TypeObject('Items list', null, false, 'XLite\View\ItemsList\Model\AModel'),
            static::PARAM_FIELD_NAME      => new \XLite\Model\WidgetParam\TypeString('Field name', ''),
            static::PARAM_FIELD_PARAMS    => new \XLite\Model\WidgetParam\TypeCollection('Field parameters list', array()),
            static::PARAM_EDIT_ONLY       => new \XLite\Model\WidgetParam\TypeBool('Edit only flag', false),
            static::PARAM_VIEW_ONLY       => new \XLite\Model\WidgetParam\TypeBool('View only flag', false),
            static::PARAM_FIELD_NAMESPACE => new \XLite\Model\WidgetParam\TypeString('Field namespace', ''),
            static::PARAM_VIEW_TIP        => new \XLite\Model\WidgetParam\TypeString('View tip', $this->getDefaultViewTip()),
            static::PARAM_VIEW_FULL_WIDTH => new \XLite\Model\WidgetParam\TypeBool('Is full width flag', false),
        );
    }

    /**
     * Get entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function getEntity()
    {
        return $this->getParam(static::PARAM_ENTITY);
    }

    /**
     * Get edit only flag
     *
     * @return boolean
     */
    protected function getEditOnly()
    {
        return $this->getParam(static::PARAM_EDIT_ONLY);
    }

    /**
     * Get view only flag
     *
     * @return boolean
     */
    protected function getViewOnly()
    {
        return $this->getParam(static::PARAM_VIEW_ONLY);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/inline.twig';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return !$this->getViewOnly()
            && ($this->getEditOnly() || ($this->getEntity() && $this->getEntity()->isPersistent()));
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return !$this->getEditOnly();
    }

    /**
     * Get default view tip
     *
     * @return string
     */
    protected function getDefaultViewTip()
    {
        return '';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && ($this->isEditable() || $this->hasSeparateView());
    }

    // {{{ Content helpers

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return array(
            'class'          => $this->getContainerClass(),
            'data-is-escape' => $this->isEscapeValue() ? '1' : '',
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $parts = explode('\\', get_class($this->getEntity()));
        $class = strtolower(array_pop($parts));

        return 'inline-field'
            . ($this->isEditable() ? ' editable' : '')
            . ($this->hasSeparateView() ? ' has-view' : '')
            . (' ' . $class . '-' . $this->getParam(static::PARAM_FIELD_NAME));
    }

    /**
     * Get view container attributes
     *
     * @return array
     */
    protected function getViewContainerAttributes()
    {
        $attributes = array(
            'class' => array(
                'view',
                $this->isEditable() ? ' editable' : ' not-editable',
            ),
        );

        if ($this->getParam(static::PARAM_VIEW_TIP)) {
            $attributes['title'] = static::t($this->getParam(static::PARAM_VIEW_TIP));
        }

        return $attributes;
    }

    /**
     * Get field container attributes
     *
     * @return array
     */
    protected function getFieldContainerAttributes()
    {
        return array(
            'class' => array('field'),
        );
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/view.twig';
    }

    /**
     * Get field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'form_field/inline/field.twig';
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getViewValue(array $field)
    {
        $method = 'getViewValue' . ucfirst($field['field'][static::FIELD_NAME]);

        if (method_exists($this, $method)) {
            // $method assembled from 'getViewValue' + field short name
            $result = $this->$method($field);

        } else {
            $value = $field['widget']->getValue();
            $result = ('' === (string) $value) ? $this->getEmptyValue($field) : $value;
        }

        return $result;
    }

    /**
     * Get empty value
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getEmptyValue(array $field)
    {
        return '&nbsp;';
    }

    /**
     * Get field
     *
     * @param string $name Field name
     *
     * @return array
     */
    protected function getField($name)
    {
        $list = $this->getFields();

        return isset($list[$name]) ? $list[$name] : null;
    }

    /**
     * Get field widget
     *
     * @param string $name Field name
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldWidget($name)
    {
        $field = $this->getField($name);

        return $field ? $field['widget'] : null;
    }

    /**
     * Get field class name
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getFieldClassName(array $field)
    {
        $param = isset($field['field'][static::FIELD_PARAMS][static::PARAM_VIEW_FULL_WIDTH]) ? $field['field'][static::FIELD_PARAMS][static::PARAM_VIEW_FULL_WIDTH] : false;
        $fullWidth = $param ? 'full-width' : '';
        return 'subfield subfield-' . $field['field'][static::FIELD_NAME] . ' ' . $fullWidth;
    }

    /**
     * Check - escape value or not
     *
     * @return boolean
     */
    protected function isEscapeValue()
    {
        return true;
    }

    // }}}

    // {{{ Form field

    /**
     * Define fields
     *
     * @return array
     */
    abstract protected function defineFields();

    /**
     * Set value from request
     *
     * @param array $data Data OPTIONAL
     * @param mixed $key  Row key OPTIONAL
     *
     * @return void
     */
    public function setValueFromRequest(array $data = array(), $key = null)
    {
        $data = $data ?: \XLite\Core\Request::getInstance()->getData();

        foreach ($this->getFields() as $field) {
            $method = 'setValue' . ucfirst($field['field'][static::FIELD_NAME]);
            if (method_exists($this, $method)) {
                // $method assemble from 'setValue' + field name
                $this->$method($field, $data, $key);

            } else {
                $this->setFieldValue($field, $data, $key);
            }
        }
    }

    /**
     * Validate
     *
     * @return array
     */
    public function validate()
    {
        $result = array(true, null);

        foreach ($this->getFields() as $field) {
            $result = $this->validateField($field);

            if (!$result[0]) {
                break;
            }
        }

        return $result;
    }

    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        foreach ($this->getFields() as $field) {
            $method = 'saveValue' . ucfirst($field['field'][static::FIELD_NAME]);
            if (method_exists($this, $method)) {
                // $method assemble from 'saveValue' + field name
                $this->$method($field);

            } else {
                $this->saveFieldValue($field);
            }
        }
    }

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return \XLite\Core\Translation::lbl(ucfirst($this->getParam(static::PARAM_FIELD_NAME)));
    }

    /**
     * Get fields
     *
     * @return array
     */
    protected function getFields()
    {
        if (null === $this->fields) {
            $this->fields = array();
            foreach ($this->defineFields() as $name => $field) {
                if (isset($field[static::FIELD_CLASS])) {
                    $field[static::FIELD_NAME] = isset($field[static::FIELD_NAME]) ? $field[static::FIELD_NAME] : $name;
                    $field[static::FIELD_PARAMS] = $this->getFieldParams($field);

                    $this->fields[$name] = array(
                        'field'  => $field,
                        'widget' => $this->getWidget($field[static::FIELD_PARAMS], $field[static::FIELD_CLASS]),
                    );
                }
            }
        }

        return $this->fields;
    }

    /**
     * Get field widgets
     *
     * @return array
     */
    protected function getFieldWidgets()
    {
        $list = array();

        foreach ($this->getFields() as $name => $field) {
            $list[$name] = $field['widget'];
        }

        return $list;
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $parts = $this->getNameParts($field);
        $label = isset($field[static::FIELD_LABEL]) ? $field[static::FIELD_LABEL] : $field[static::FIELD_NAME];

        $list = array(
            'fieldOnly' => true,
            'nameParts' => $parts,
            'fieldName' => array_shift($parts) . ($parts ? ('[' . implode('][', $parts) . ']') : ''),
            'value'     => $this->getFieldEntityValue($field),
            'label'     => \XLite\Core\Translation::lbl($label),
        );

        if (!empty($field[static::FIELD_PARAMS]) && is_array($field[static::FIELD_PARAMS])) {
            $list = array_merge($list, $field[static::FIELD_PARAMS]);
        }

        return array_merge($list, $this->prepareAdditionalFieldParams($field));
    }

    /**
     * Prepare additional field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function prepareAdditionalFieldParams(array $field)
    {
        $params = $this->getParam(static::PARAM_FIELD_PARAMS);

        $style = $this->getAdditionalFieldStyle($field);

        if ($style) {

            // Add style to the field attributes list

            $attrs = \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES;

            if (empty($params[$attrs])) {
                $params[$attrs] = array();
            }

            $params[$attrs]['class'] = (!empty($params[$attrs]['class']) ? $params[$attrs]['class'] . ' ' : '')
                . $style;
        }

        return $params;
    }

    /**
     * Get additional CSS classes for the field widget
     *
     * @return string
     */
    protected function getAdditionalFieldStyle($field)
    {
        return null;
    }

    /**
     * Save field value
     *
     * @param array $field Field
     *
     * @return void
     */
    protected function saveFieldValue(array $field)
    {
        $value = $field['widget']->getValue();
        $value = $this->preprocessValueBeforeSave($value);

        $method = 'preprocessValueBeforeSave' . ucfirst($field['field'][static::FIELD_NAME]);
        if (method_exists($this, $method)) {
            // $method assemble from 'preprocessValueBeforeSave' + field name
            $value = $this->$method($value);
        }

        $this->saveFieldEntityValue($field, $value);
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $entity = $this->getEntity();
        $method = 'set' . \XLite\Core\Converter::convertToCamelCase($field['field'][static::FIELD_NAME]);

        if (method_exists($entity, $method) || ($entity instanceof MagicMethodsIntrospectionInterface && $entity->hasMagicMethod($method))) {
            // $method assemble from 'set' + field name
            $entity->$method($value);
        }
    }

    /**
     * Get field name parts
     *
     * @param array $field Field
     *
     * @return array
     */
    protected function getNameParts(array $field)
    {
        if (!$this->getEntityUniqueIdentifier()) {
            if ($this->getParam(static::PARAM_ITEMS_LIST)) {
                $parts = array(
                    $this->getParam(static::PARAM_ITEMS_LIST)->getCreateDataPrefix(),
                    0,
                    $field[static::FIELD_NAME],
                );

            } else {
                $parts = array(
                    $field[static::FIELD_NAME],
                    0,
                );
            }

        } elseif (!$this->getParam(static::PARAM_ITEMS_LIST)) {
            $parts = array(
                $field[static::FIELD_NAME],
                $this->getEntityUniqueIdentifier(),
            );

        } else {
            $parts = array(
                $this->getParam(static::PARAM_ITEMS_LIST)->getDataPrefix(),
                $this->getEntityUniqueIdentifier(),
                $field[static::FIELD_NAME],
            );

        }

        return $parts;
    }

    /**
     * Get entity unique identifier
     *
     * @return mixed
     */
    protected function getEntityUniqueIdentifier()
    {
        return $this->getEntity()->getUniqueIdentifier();
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        $method = 'get' . \XLite\Core\Converter::convertToCamelCase($field[static::FIELD_NAME]);

        // $method assembled from 'get' + field short name
        return $this->getEntity()->$method();
    }

    /**
     * Set field value
     *
     * @param array $field Field
     * @param array $data  Data
     * @param mixed $key   Row key OPTIONAL
     *
     * @return void
     */
    protected function setFieldValue(array $field, array $data, $key = null)
    {
        $this->transferValueToField($field, $this->isolateFieldValue($field, $data, $key));
    }

    /**
     * Isolate field value
     *
     * @param array $field Field info
     * @param array $data  Data
     * @param mixed $key   Row key OPTIONAL
     *
     * @return mixed
     */
    protected function isolateFieldValue(array $field, array $data, $key = null)
    {
        $found = true;

        foreach ($field['field'][static::FIELD_PARAMS]['nameParts'] as $part) {
            if (0 === $part && null !== $key) {
                $part = $key;
            }

            if (isset($data[$part])) {
                $data = &$data[$part];

            } else {
                $found = false;
                break;
            }
        }

        return $found ? $data : null;
    }

    /**
     * Transfer isolated value to field
     *
     * @param array $field Filed info
     * @param mixed $value Value
     *
     * @return void
     */
    protected function transferValueToField(array $field, $value)
    {
        if (null !== $value) {
            $field['widget']->setValue($value);
        }
    }

    /**
     * Preprocess value before save
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function preprocessValueBeforeSave($value)
    {
        return $value;
    }

    /**
     * Validate field
     *
     * @param array $field Field info
     *
     * @return array
     */
    protected function validateField(array $field)
    {
        $method = 'validate' . ucfirst($field['field'][static::FIELD_NAME]);

        return method_exists($this, $method)
            ? $this->$method($field)
            : $field['widget']->validate();
    }

    // }}}
}
