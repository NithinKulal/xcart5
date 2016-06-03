<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Search;

/**
 * Abstract items lsit search cell
 */
abstract class ASearch extends \XLite\View\AView
{
    /**
     * Widget parameters 
     */
    const PARAM_COLUMN = 'column';

    /**
     * Fields attributes 
     */
    const FIELD_NAME  = 'name';
    const FIELD_CLASS = 'class';
    const FIELD_TITLE = 'title';

    /**
     * Fields 
     * 
     * @var array
     */
    protected $fields;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_COLUMN => new \XLite\Model\WidgetParam\TypeCollection('Column', array()),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getColumn();
    }

    /**
     * Get column 
     * 
     * @return array
     */
    protected function getColumn()
    {
        return $this->getParam(static::PARAM_COLUMN);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/search/field.twig';
    }

    // {{{ Fields and conditions

    /**
     * Define fields 
     * 
     * @return array
     */
    abstract protected function defineFields();

    /**
     * Get fields 
     * 
     * @return array
     */
    protected function getFields()
    {
        if (!isset($this->fields)) {
            $this->fields = array();
            foreach ($this->defineFields() as $field) {
                if (!empty($field[static::FIELD_CLASS])) {
                    $this->fields[] = array(
                        'field'  => $field,
                        'widget' => $this->getWidget($this->assembleFieldParameters($field), $field[static::FIELD_CLASS]),
                    );
                }
            }
        }

        return $this->fields;
    }

    /**
     * Get field by name
     * 
     * @param string $name Name
     *  
     * @return void
     */
    protected function displayField($name)
    {
        $field = null;

        foreach ($this->getFields() as $f) {
            if ($name == $f['field'][static::FIELD_NAME]) {
                $field = $f;
                break;
            }
        }

        if ($field) {
            $field['widget']->display();
        }
    }

    /**
     * Assemble field parameters 
     * 
     * @param array $field Field info
     *  
     * @return array
     */
    protected function assembleFieldParameters(array $field)
    {
        $parameters = array(
            'fieldName'  => $field[static::FIELD_NAME],
            'attributes' => array('class' => $this->assembleFieldClass($field)),
            'value'      => $this->getCondition($field[static::FIELD_NAME]),
            'fieldOnly'  => true,
            'fieldId'    => 'search-field-' . $field[static::FIELD_NAME],
        );

        if (!empty($field[static::FIELD_TITLE])) {
            $parameters['label'] = $field[static::FIELD_TITLE];
        }

        return $parameters;
    }

    /**
     * Assemble field class
     *
     * @param array $field Field info
     *
     * @return string
     */
    protected function assembleFieldClass(array $field)
    {
        $name = preg_replace('/[^a-z0-9]/iSs', '-', $field[static::FIELD_NAME]);
        $name = str_replace('--', '-', $name);

        return 'search-field ' . $name . ' not-significant';
    }

    /**
     * Get condition 
     * 
     * @param string $name Condition name
     *  
     * @return mixed
     */
    protected function getCondition($name)
    {
        return parent::getCondition($name);
    }

    // }}}
}
