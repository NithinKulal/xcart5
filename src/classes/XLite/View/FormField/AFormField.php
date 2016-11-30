<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Abstract form field
 */
abstract class AFormField extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_VALUE      = 'value';
    const PARAM_REQUIRED   = 'required';
    const PARAM_ATTRIBUTES = 'attributes';
    const PARAM_NAME       = 'fieldName';
    const PARAM_ID         = 'fieldId';
    const PARAM_LABEL      = 'label';
    const PARAM_LABEL_PARAMS = 'labelParams';
    const PARAM_COMMENT    = 'comment';
    const PARAM_HELP       = 'help';
    const PARAM_HELP_WIDGET       = 'helpWidget';
    const PARAM_LABEL_HELP        = 'labelHelp';
    const PARAM_LABEL_HELP_WIDGET = 'labelHelpWidget';
    const PARAM_FIELD_ONLY      = 'fieldOnly';
    const PARAM_WRAPPER_CLASS   = 'wrapperClass';

    /** @deprecated */
    const PARAM_USE_COLON       = 'useColon';
    const PARAM_LINK_HREF       = 'linkHref';
    const PARAM_LINK_TEXT       = 'linkText';
    const PARAM_LINK_IMG        = 'linkImg';
    const PARAM_TRUSTED         = 'trusted';
    const PARAM_NO_PARENT_FORM  = 'noParentForm';

    const PARAM_IS_ALLOWED_FOR_CUSTOMER = 'isAllowedForCustomer';

    const PARAM_DEPENDENCY = 'dependency';

    /**
     * Available field types
     */
    const FIELD_TYPE_LABEL      = 'label';
    const FIELD_TYPE_TEXT       = 'text';
    const FIELD_TYPE_PASSWORD   = 'password';
    const FIELD_TYPE_SELECT     = 'select';
    const FIELD_TYPE_CHECKBOX   = 'checkbox';
    const FIELD_TYPE_RADIO      = 'radio';
    const FIELD_TYPE_TEXTAREA   = 'textarea';
    const FIELD_TYPE_SEPARATOR  = 'separator';
    const FIELD_TYPE_ITEMS_LIST = 'itemsList';
    const FIELD_TYPE_HIDDEN     = 'hidden';
    const FIELD_TYPE_LISTBOX    = 'listbox';
    const FIELD_TYPE_FILE       = 'file';
    const FIELD_TYPE_COMPLEX    = 'complex';

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * validityFlag
     *
     * @var boolean
     */
    protected $validityFlag;

    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = true;

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Name-to-ID translation table
     *
     * @var array
     */
    protected $nameTranslation = array(
        '[' => '-',
        ']' => '',
        '_' => '-',
    );

    /**
     * Return field type
     *
     * @return string
     */
    abstract public function getFieldType();

    /**
     * Return field template
     *
     * @return string
     */
    abstract protected function getFieldTemplate();

    /**
     * Return field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getParam(self::PARAM_NAME);
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getParam(self::PARAM_VALUE);
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->getWidgetParams(self::PARAM_VALUE)->setValue($value);
    }

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getParam(self::PARAM_LABEL);
    }

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabelParams()
    {
        return $this->getParam(self::PARAM_LABEL_PARAMS);
    }

    /**
     * Get formatted label
     *
     * @return string
     */
    public function getFormattedLabel()
    {
        return static::t($this->getLabel(), $this->getLabelParams());
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        return $this->getParam(self::PARAM_ID) ?: strtolower(strtr($this->getName(), $this->nameTranslation));
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return $this->getParam(static::PARAM_TRUSTED) ?: false;
    }

    /**
     * Validate field value
     *
     * @return mixed
     */
    public function validate()
    {
        $this->setValue($this->sanitize());

        return array(
            $this->getValidityFlag(),
            $this->getValidityFlag() ? null : $this->errorMessage
        );
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/form_field.css';

        return $list;
    }

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        if (isset($params[self::PARAM_NAME])) {
            $this->name = $params[self::PARAM_NAME];
        };

        parent::__construct($params);
    }

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return $this->getParam(self::PARAM_WRAPPER_CLASS);
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        // todo: Check if NULL is mining field value so we must use array_key_exists instead of isset
        if (isset($params['value'])) {
            $this->setValue($params['value']);
        }
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return $value;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/form_field.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'form_field';
    }

    /**
     * checkSavedValue
     *
     * @return boolean
     */
    protected function checkSavedValue()
    {
        return null !== $this->callFormMethod('getSavedData', array($this->getName()));
    }

    /**
     * Get validity flag (and run field validation procedure)
     *
     * @return boolean
     */
    protected function getValidityFlag()
    {
        if (null === $this->validityFlag) {
            $this->validityFlag = $this->checkFieldValidity();
        }

        return $this->validityFlag;
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return $this->getValidityFlag() ? null : $this->errorMessage;
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return $this->getValue();
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        return array(
            'id'   => $this->getFieldId(),
            'name' => $this->getName(),
        );
    }

    /**
     * setCommonAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        foreach ($this->getCommonAttributes() as $name => $value) {
            if (!isset($attrs[$name])) {
                $attrs[$name] = $value;
            }
        }

        if (!isset($attrs['class'])) {
            $attrs['class'] = '';
        }
        $classes = preg_grep('/.+/S', array_map('trim', explode(' ', $attrs['class'])));
        $classes = $this->assembleClasses($classes);
        $attrs['class'] = implode(' ', $classes);

        return $attrs;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $validationRules = $this->assembleValidationRules();
        if ($validationRules) {
            $classes[] = 'validate[' . implode(',', $validationRules) . ']';
        }

        $classes[] = $this->isFormControl() ? 'form-control' : '';

        return $classes;
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return true;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        return $this->isRequired() ? array('required') : array();
    }

    /**
     * prepareAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        if (!$this->getValidityFlag() && $this->checkSavedValue()) {
            $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . 'form_field_error';
        }

        return $this->setCommonAttributes($attrs);
    }

    /**
     * Check if field is required
     *
     * @return boolean
     */
    protected function isRequired()
    {
        return $this->getParam(self::PARAM_REQUIRED);
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return $this->prepareAttributes($this->getParam(self::PARAM_ATTRIBUTES));
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getAttributesCode()
    {
        $result = '';

        foreach ($this->getAttributes() as $name => $value) {
            $result .= ' ' . $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return $result;
    }

    /**
     * Some JavaScript code to insert
     *
     * @todo   Remove it. Use getFormFieldJSData method instead.
     * @return string
     */
    protected function getInlineJSCode()
    {
        return null;
    }

    /**
     * getDefaultName
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return null;
    }

    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return null !== $this->name ? $this->callFormMethod('getDefaultFieldValue', array($this->name)) : null;
    }

    /**
     * Validate field on form side
     *
     * @return array
     */
    protected function validateFormField()
    {
        $isValid = true;
        $errorMessage = null;

        if (null !== $this->name) {
            $result = $this->callFormMethod('validateFormField', array($this));
            if (is_array($result)) {
                list($isValid, $errorMessage) = $result;
            }
        }

        return array($isValid, $errorMessage);
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return null;
    }

    /**
     * getDefaultLabelParams
     *
     * @return array
     */
    protected function getDefaultLabelParams()
    {
        return array();
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return array();
    }

    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return false;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ID         => new \XLite\Model\WidgetParam\TypeString('Id', ''),
            self::PARAM_NAME       => new \XLite\Model\WidgetParam\TypeString('Name', $this->getDefaultName()),
            self::PARAM_VALUE      => new \XLite\Model\WidgetParam\TypeString('Value', $this->getDefaultValue()),
            self::PARAM_LABEL      => new \XLite\Model\WidgetParam\TypeString('Label', $this->getDefaultLabel()),
            self::PARAM_LABEL_PARAMS => new \XLite\Model\WidgetParam\TypeCollection('Label params', $this->getDefaultLabelParams()),
            self::PARAM_REQUIRED   => new \XLite\Model\WidgetParam\TypeBool('Required', false),
            self::PARAM_COMMENT    => new \XLite\Model\WidgetParam\TypeString('Comment', null),
            self::PARAM_HELP       => new \XLite\Model\WidgetParam\TypeString('Help', null),
            self::PARAM_HELP_WIDGET       => new \XLite\Model\WidgetParam\TypeString('Help widget class name', null),
            self::PARAM_LABEL_HELP        => new \XLite\Model\WidgetParam\TypeString('Label help', null),
            self::PARAM_LABEL_HELP_WIDGET => new \XLite\Model\WidgetParam\TypeString('Label help widget class name', null),
            self::PARAM_ATTRIBUTES => new \XLite\Model\WidgetParam\TypeCollection('Attributes', $this->getDefaultAttributes()),
            self::PARAM_WRAPPER_CLASS => new \XLite\Model\WidgetParam\TypeString('Wrapper class', $this->getDefaultWrapperClass()),

            /** @deprecated */
            self::PARAM_USE_COLON     => new \XLite\Model\WidgetParam\TypeBool('Use colon', false),

            self::PARAM_LINK_HREF     => new \XLite\Model\WidgetParam\TypeString('Link href', ''),
            self::PARAM_LINK_TEXT     => new \XLite\Model\WidgetParam\TypeString('Link text', ''),
            self::PARAM_LINK_IMG      => new \XLite\Model\WidgetParam\TypeString('Link img', ''),
            self::PARAM_NO_PARENT_FORM => new \XLite\Model\WidgetParam\TypeBool('Form field has no parent form', false),

            self::PARAM_IS_ALLOWED_FOR_CUSTOMER => new \XLite\Model\WidgetParam\TypeBool(
                'Is allowed for customer',
                $this->isAllowedForCustomer
            ),
            self::PARAM_FIELD_ONLY    => new \XLite\Model\WidgetParam\TypeBool(
                'Skip wrapping with label and required flag, display just a field itself',
                $this->getDefaultParamFieldOnly()
            ),
            self::PARAM_DEPENDENCY => new \XLite\Model\WidgetParam\TypeCollection('Dependency', array()),
            self::PARAM_TRUSTED    => new \XLite\Model\WidgetParam\TypeBool('Trusted (value may contain anything)', false),
        );
    }

    /**
     * Check field value validity
     *
     * @return boolean
     */
    protected function checkFieldValue()
    {
        return '' !== $this->getValue();
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = true;
        $this->errorMessage = null;

        if ($this->isRequired() && !$this->checkFieldValue()) {
            $this->errorMessage = $this->getRequiredFieldErrorMessage();
            $result = false;

        } else {
            list($result, $this->errorMessage) = $this->validateFormField();
        }

        return $result;
    }

    /**
     * Get required field error message
     *
     * @return string
     */
    protected function getRequiredFieldErrorMessage()
    {
        return \XLite\Core\Translation::lbl('The X field is empty', array('name' => $this->getLabel()));
    }

    /**
     * checkFieldAccessability
     *
     * @return boolean
     */
    protected function checkFieldAccessability()
    {
        return $this->getParam(self::PARAM_IS_ALLOWED_FOR_CUSTOMER) || \XLite::isAdminZone();
    }

    /**
     * callFormMethod
     *
     * @param string $method Class method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    protected function callFormMethod($method, array $args = array())
    {
        $result = null;

        if (!$this->getParam(static::PARAM_NO_PARENT_FORM)) {

            $form = \XLite\View\Model\AModel::getCurrentForm();

            $result = $form && method_exists($form, $method)
                ? call_user_func_array(array($form, $method), $args)
                : null;
        }

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->checkFieldAccessability();
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        $suffix = preg_replace(
            '/^.+\\\(?:Module\\\([a-zA-Z0-9]+\\\[a-zA-Z0-9]+\\\))?View\\\FormField\\\(.+)$/Ss',
            '$1$2',
            get_called_class()
        );
        $suffix = str_replace('\\', '-', strtolower($suffix));

        return 'input ' . $suffix;
    }

    /**
     * Get label container class
     *
     * @return string
     */
    protected function getLabelContainerClass()
    {
        $class = 'table-label ' . $this->getFieldId() . '-label';

        if ($this->isRequired()) {
            $class .= ' table-label-required';
        }

        return $class;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = 'table-value ' . $this->getFieldId() . '-value';

        if ($this->isRequired()) {
            $class .= ' table-value-required';
        }

        return $class;
    }

    /**
     * Return some data for JS external scripts if it is needed.
     *
     * @return array
     */
    protected function getFormFieldJSData()
    {
        return null;
    }

    /**
     * Check for label help present
     *
     * @return boolean
     */
    protected function hasLabelHelp()
    {
        return $this->getParam(static::PARAM_LABEL_HELP) || $this->getParam(static::PARAM_LABEL_HELP_WIDGET);
    }

    /**
     * Check for help present
     *
     * @return boolean
     */
    protected function hasHelp()
    {
        return $this->getParam(static::PARAM_HELP) || $this->getParam(static::PARAM_HELP_WIDGET);
    }
}
