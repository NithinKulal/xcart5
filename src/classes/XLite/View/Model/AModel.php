<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

use XLite\Core\MagicMethodsIntrospectionInterface;

/**
 * Abstract model widget
 */
abstract class AModel extends \XLite\View\Dialog
{
    /**
     * Widget param names
     */
    const PARAM_MODEL_OBJECT      = 'modelObject';
    const PARAM_USE_BODY_TEMPLATE = 'useBodyTemplate';

    /**
     * Indexes in field schemas
     *
     * FIXME: keep this list synchronized with the classes,
     * derived from the \XLite\View\FormField\AFormField
     */
    const SCHEMA_CLASS          = 'class';
    const SCHEMA_VALUE          = \XLite\View\FormField\AFormField::PARAM_VALUE;
    const SCHEMA_REQUIRED       = \XLite\View\FormField\AFormField::PARAM_REQUIRED;
    const SCHEMA_ATTRIBUTES     = \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES;
    const SCHEMA_NAME           = \XLite\View\FormField\AFormField::PARAM_NAME;
    const SCHEMA_LABEL          = \XLite\View\FormField\AFormField::PARAM_LABEL;
    const SCHEMA_LABEL_PARAMS   = \XLite\View\FormField\AFormField::PARAM_LABEL_PARAMS;
    const SCHEMA_FIELD_ONLY     = \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY;
    const SCHEMA_PLACEHOLDER    = \XLite\View\FormField\Input\Base\StringInput::PARAM_PLACEHOLDER;
    const SCHEMA_COMMENT        = \XLite\View\FormField\AFormField::PARAM_COMMENT;
    const SCHEMA_HELP           = \XLite\View\FormField\AFormField::PARAM_HELP;
    const SCHEMA_LINK_HREF      = \XLite\View\FormField\AFormField::PARAM_LINK_HREF;
    const SCHEMA_LINK_TEXT      = \XLite\View\FormField\AFormField::PARAM_LINK_TEXT;
    const SCHEMA_LINK_IMG       = \XLite\View\FormField\AFormField::PARAM_LINK_IMG;
    const SCHEMA_TRUSTED        = \XLite\View\FormField\AFormField::PARAM_TRUSTED;

    const SCHEMA_OPTIONS = \XLite\View\FormField\Select\ASelect::PARAM_OPTIONS;
    const SCHEMA_IS_CHECKED = \XLite\View\FormField\Input\Checkbox::PARAM_IS_CHECKED;

    const SCHEMA_MODEL_ATTRIBUTES = 'model_attributes';

    const SCHEMA_DEPENDENCY = \XLite\View\FormField\AFormField::PARAM_DEPENDENCY;

    /**
     * Session cell to store form data
     */
    const SAVED_FORMS     = 'savedForms';
    const SAVED_FORM_DATA = 'savedFormData';

    /**
     * Form sections
     */
    // Title for this section will not be displayed
    const SECTION_DEFAULT = 'default';
    // This section will not be displayed
    const SECTION_HIDDEN  = 'hidden';

    /**
     * Indexes in the "formFields" array
     */
    const SECTION_PARAM_WIDGET = 'sectionParamWidget';
    const SECTION_PARAM_FIELDS = 'sectionParamFields';

    /**
     * Name prefix of the methods to handle actions
     */
    const ACTION_HANDLER_PREFIX = 'performAction';

    /**
     * Dependency
     */
    const DEPENDENCY_SHOW = 'show';
    const DEPENDENCY_HIDE = 'hide';

    /**
     * Current form object
     *
     * @var \XLite\View\Model\AModel
     */
    protected static $currentForm = null;

    /**
     * List of form fields
     *
     * @var array
     */
    protected $formFields = null;

    /**
     * List of files form fields
     *
     * @var array
     */
    protected $filesFormFields;

    /**
     * Names of the form fields (hash)
     *
     * @var array
     */
    protected $formFieldNames = array();

    /**
     * Form error messages cache
     *
     * @var array
     */
    protected $errorMessages = null;

    /**
     * Form saved data cache
     *
     * @var array
     */
    protected $savedData = null;

    /**
     * Available form sections
     *
     * @var array
     */
    protected $sections = array(
        self::SECTION_DEFAULT => null,
        self::SECTION_HIDDEN  => null,
    );

    /**
     * Current action
     *
     * @var string
     */
    protected $currentAction = null;

    /**
     * Data from request
     *
     * @var array
     */
    protected $requestData = null;

    /**
     * schemaDefault
     *
     * @var array
     */
    protected $schemaDefault = array();

    /**
     * schemaHidden
     *
     * @var array
     */
    protected $schemaHidden = array();

    /**
     * The list of fields (field names) that must be excluded from the array(data) for mapping to the object
     *
     * @var array
     */
    protected $excludedFields = array();

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function getDefaultModelObject();

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    abstract protected function getFormClass();

    /**
     * Get instance to the current form object
     *
     * @return \XLite\View\Model\AModel
     */
    public static function getCurrentForm()
    {
        return self::$currentForm;
    }

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        if (!empty($sections)) {
            $this->sections = \Includes\Utils\ArrayManager::filterByKeys($this->sections, $sections);
        }

        parent::__construct($params);

        $this->startCurrentForm();
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
        $value = $this->getSavedData($name);

        if (!isset($value)) {
            $value = $this->getRequestData($name);

            if (!isset($value)) {
                // Check if $name is in fields list
                $fields = $this->getFormFields(true);
                if ($fields && false !== array_search($name, $fields)) {
                    $value = $this->getModelObjectValue($name);
                }
            }
        }

        return $value;
    }

    /**
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        return !((bool) $this->getErrorMessages());
    }

    /**
     * Perform some action for the model object
     *
     * @param string $action Action to perform
     * @param array  $data   Form data OPTIONAL
     *
     * @return boolean
     */
    public function performAction($action, array $data = array())
    {
        // Save some data
        $this->currentAction = $action;
        $this->defineRequestData($data);

        $requestData = $this->prepareDataForMapping();

        // Map model object with the request data
        $this->setModelProperties($requestData);

        // Do not call "callActionHandler()" method if model object is not valid
        $result = $this->isValid() && $this->callActionHandler();

        if ($result) {
            $this->postprocessSuccessAction();

        } else {
            $this->rollbackModel();
            $this->saveFormData($requestData);
            $this->postprocessErrorAction();
        }

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/model.css';

        return $list;
    }

    /**
     * Return fields' saved values for current form (saved data itself)
     *
     * @param string $name Parameter name OPTIONAL
     *
     * @return array
     */
    public function getSavedData($name = null)
    {
        if (!isset($this->savedData)) {
            $this->savedData = $this->getSavedForm(self::SAVED_FORM_DATA);
        }

        return isset($name)
            ? (isset($this->savedData[$name]) ? $this->savedData[$name] : null)
            : $this->savedData;
    }

    /**
     * getRequestData
     *
     * @param string $name Index in the request data OPTIONAL
     *
     * @return mixed
     */
    public function getRequestData($name = null)
    {
        if (!isset($this->requestData)) {
            $this->defineRequestData(array(), $name);
        }

        return isset($name)
            ? (isset($this->requestData[$name]) ? $this->requestData[$name] : null)
            : $this->requestData;
    }

    /**
     * setRequestData
     *
     * @param string $name  Index in the request data
     * @param mixed  $value Value to set
     *
     * @return void
     */
    public function setRequestData($name, $value)
    {
        $this->requestData[$name] = $value;
    }

    /**
     * Return model object to use
     *
     * @return \XLite\Model\AEntity
     */
    public function getModelObject()
    {
        return $this->getParam(self::PARAM_MODEL_OBJECT);
    }


    /**
     * Check if current form is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return true;
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return $this->checkAccess() ? parent::getBodyTemplate() : 'access_denied.twig';
    }

    /**
     * getAccessDeniedMessage
     *
     * @return string
     */
    protected function getAccessDeniedMessage()
    {
        return 'Access denied';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'model';
    }

    /**
     * Return text for header
     *
     * @return string
     */
    protected function getHeaderText()
    {
        return null;
    }

    /**
     * getFormDir
     *
     * @param string $template Template file basename OPTIONAL
     *
     * @return string
     */
    protected function getFormDir($template = null)
    {
        return 'form';
    }

    /**
     * Return form templates directory name
     *
     * @param string $template Template file base name
     *
     * @return string
     */
    protected function getFormTemplate($template)
    {
        return $this->getFormDir($template) . '/' . $template . '.twig';
    }

    /**
     * Return list of form fields for certain section
     *
     * @param string $section Section name
     *
     * @return array
     */
    protected function getFormFieldsForSection($section)
    {
        $method = __FUNCTION__ . ucfirst($section);

        // Return the method getFormFieldsForSection<SectionName>
        return method_exists($this, $method) ? $this->$method() : $this->translateSchema($section);
    }

    /**
     * Define form field classes and values
     *
     * @return void
     */
    protected function defineFormFields()
    {
        $this->formFields = array();

        foreach ($this->sections as $section => $label) {
            $this->formFields[$section] = array(
                self::SECTION_PARAM_WIDGET => $this->defineSectionWidget($section, [self::SCHEMA_LABEL => $label]),
                self::SECTION_PARAM_FIELDS => $this->getFormFieldsForSection($section),
            );
        }
    }

    /**
     * @param string $section
     * @param array  $params
     *
     * @return \XLite\View\AView
     */
    protected function defineSectionWidget($section, $params)
    {
        return new \XLite\View\FormField\Separator\Regular($params);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $object = $this->getDefaultModelObject();

        $this->widgetParams += array(
            self::PARAM_MODEL_OBJECT => new \XLite\Model\WidgetParam\TypeObject(
                'Object', $object, false, $object ? get_class($object) : ''
            ),
            self::PARAM_USE_BODY_TEMPLATE => new \XLite\Model\WidgetParam\TypeBool(
                'Use default body template', false
            ),
        );
    }

    /**
     * useBodyTemplate
     *
     * @return boolean
     */
    protected function useBodyTemplate()
    {
        return $this->getParam(self::PARAM_USE_BODY_TEMPLATE) ? true : parent::useBodyTemplate();
    }

    /**
     * Flag if the panel widget for buttons is used
     *
     * @return boolean
     */
    protected function useButtonPanel()
    {
        return !is_null($this->getButtonPanelClass());
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return \XLite::isAdminZone()
            ? '\XLite\View\StickyPanel\Model\Model'
            : null;
    }

    /**
     * Get button panel
     *
     * @return \XLite\View\StickyPanel\Model\AModel
     */
    protected function getButtonPanel()
    {
        $buttonPanel = null;

        if ($this->useButtonPanel()) {
            $class = $this->getButtonPanelClass();
            $buttonPanel = new $class;
            $buttons = $this->getFormButtons();

            if ($buttons
                && method_exists($buttonPanel, 'setButtons')
            ) {
                $buttonPanel->setButtons($buttons);
            }
        }

        return $buttonPanel;
    }

    /**
     * Add (if required) an additional part to the form name
     *
     * @param string $name Name to prepare
     *
     * @return string
     */
    protected function composeFieldName($name)
    {
        return $name;
    }

    /**
     * Return model field name for a provided form field name
     *
     * @param string $name Name of form field
     *
     * @return string
     */
    protected function getModelFieldName($name)
    {
        return $name;
    }

    /**
     * Return field mappings structure for the model
     *
     * @return array
     */
    protected function getFieldMappings()
    {
        if (!isset($this->fieldMappings)) {

            // Collect metadata for fields of class and its translation class if there is one.
            $metaData = \XLite\Core\Database::getEM()->getClassMetadata(get_class($this->getModelObject()));
            $this->fieldMappings = $metaData->fieldMappings;

            $metaDataTranslationClass = isset($metaData->associationMappings['translations'])
                ? $metaData->associationMappings['translations']['targetEntity']
                : false;

            if ($metaDataTranslationClass) {

                $metaDataTranslation = \XLite\Core\Database::getEM()->getClassMetadata($metaDataTranslationClass);
                $this->fieldMappings += $metaDataTranslation->fieldMappings;
            }
        }

        return $this->fieldMappings;
    }

    /**
     * Return field mapping info for a given $name key
     *
     * @param string $name Field name
     *
     * @return array
     */
    protected function getFieldMapping($name)
    {
        $fieldMappings = $this->getFieldMappings();
        $fieldName = $this->getModelFieldName($name);

        return isset($fieldMappings[$fieldName]) ? $fieldMappings[$fieldName] : null;
    }

    /**
     * Return widget attributes that are collected from the model properties
     *
     * @param string $name Field name
     * @param array  $data Field info
     *
     * @return array
     */
    protected function getModelAttributes($name, array $data)
    {
        $fieldMapping = $this->getFieldMapping($name);

        $result = array();

        if ($fieldMapping) {

            foreach ($data[static::SCHEMA_MODEL_ATTRIBUTES] as $widgetAttribute => $modelAttribute) {

                if (isset($fieldMapping[$modelAttribute])) {

                    $result[$widgetAttribute] = $fieldMapping[$modelAttribute];
                }
            }
        }

        return $result;
    }

    /**
     * Perform some operations when creating fields list by schema
     *
     * @param string $name Node name
     * @param array  $data Field description
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        if (!isset($data[static::SCHEMA_NAME])) {
            $data[static::SCHEMA_NAME] = $this->composeFieldName($name);
        }

        $data[static::SCHEMA_VALUE] = $this->getDefaultFieldValue($name);

        $data[static::SCHEMA_ATTRIBUTES] = !empty($data[static::SCHEMA_ATTRIBUTES]) ? $data[static::SCHEMA_ATTRIBUTES] : array();
        $data[static::SCHEMA_ATTRIBUTES] += isset($data[static::SCHEMA_MODEL_ATTRIBUTES]) ? $this->getModelAttributes($name, $data) : array();

        $data[static::SCHEMA_DEPENDENCY] = isset($data[static::SCHEMA_DEPENDENCY]) ? $data[static::SCHEMA_DEPENDENCY] : array();

        return $data;
    }

    /**
     * Return list of files form fields
     *
     * @return array
     */
    protected function getFilesFormFields()
    {
        if (!isset($this->filesFormFields)) {
            $this->filesFormFields = array();
            foreach ($this->formFields as $section) {
                foreach ($section[static::SECTION_PARAM_FIELDS] as $k => $v) {
                    if (is_subclass_of($v, '\XLite\View\FormField\FileUploader\AFileUploader')) {
                        $this->filesFormFields[$k] = $v;
                    }
                }
            }
        }

        return $this->filesFormFields;
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
        $filesData = array();
        if ($this->getFilesFormFields()) {
            foreach ($this->getFilesFormFields() as $k => $v) {
                if (isset($data[$k])) {
                    $filesData[$k] = $data[$k];
                    unset($data[$k]);
                }
            }
        }

        $data = $this->mapCleanURL($data);

        $model = $this->prepareObjectForMapping();

        foreach ($data as $name => $value) {
            // Correct data: remove fields which cannot be mapped to the model
            $method = 'set' . \XLite\Core\Converter::convertToCamelCase($name);

            $methodExists = method_exists($model, $method)
                            || ($model instanceof MagicMethodsIntrospectionInterface && $model->hasMagicMethod($method));

            // $method - assemble from 'set' + property name
            if (!$methodExists && !$model->isPropertyExists($name)) {
                unset($data[$name]);
            }
        }

        $model->map($data);

        if ($filesData) {

            $errors = $this->processFiles($filesData);

            if ($errors) {
                $this->processFileUploadErrors($errors);
            }
        }
    }

    /**
     * Process file upload errors.
     * $errors has format: array( array(<message>,<message params>), ... )
     *
     * @param array $errors Array of errors
     *
     * @return void
     */
    protected function processFileUploadErrors($errors)
    {
        foreach ($errors as $error) {
            \XLite\Core\TopMessage::addError(static::t($error[0], !empty($error[1]) ? $error[1] : array()));
        }
    }

    /**
     * Process files
     *
     * @param array $data Data to save
     *
     * @return void
     */
    protected function processFiles(array $data)
    {
        $errors = array();

        $model = $this->getModelObject();

        foreach ($data as $field => $d) {
            $errors = array_merge($errors, $model->processFiles($field, $d));
        }

        return $errors;
    }

    /**
     * Process clean url data
     *
     * @param array $data
     *
     * @return array
     */
    protected function mapCleanURL($data)
    {
        /** @var \XLite\Model\Repo\CleanURL $cleanURLRepo */
        $cleanURLRepo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        if (
            $this->getPostedData('autogenerateCleanURL')
            || (
                isset($data['cleanURL'])
                && empty($data['cleanURL'])
            )
        ) {
            $data['cleanURL'] = $cleanURLRepo->generateCleanURL(
                $this->getDefaultModelObject(),
                $data[$cleanURLRepo->getBaseFieldName($this->getDefaultModelObject())]
            );
        }

        if ($this->getPostedData('forceCleanURL')) {
            $conflictEntity = $cleanURLRepo->getConflict(
                $data['cleanURL'],
                $this->getDefaultModelObject(),
                $this->getModelId()
            );

            if ($conflictEntity && $data['cleanURL'] !== $conflictEntity->getCleanURL()) {
                /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
                $cleanURLs = $conflictEntity->getCleanURLs();
                /** @var \XLite\Model\CleanURL $cleanURL */
                foreach ($cleanURLs as $cleanURL) {
                    if ($data['cleanURL'] === $cleanURL->getCleanURL()) {
                        $cleanURLs->removeElement($cleanURL);
                        \XLite\Core\Database::getEM()->remove($cleanURL);
                        $this->getModelObject()->setCleanURL($data['cleanURL'], true);
                        unset($data['cleanURL']);

                        break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Fetch saved forms data from session
     *
     * @return array
     */
    protected function getSavedForms()
    {
        return \XLite\Core\Session::getInstance()->get(self::SAVED_FORMS);
    }

    /**
     * Return saved data for current form (all or certain field(s))
     *
     * @param string $field Data field to return OPTIONAL
     *
     * @return array
     */
    protected function getSavedForm($field = null)
    {
        $data = $this->getSavedForms();
        $name = $this->getFormName();

        $data = isset($data[$name]) ? $data[$name] : array();

        if (isset($field) && isset($data[$field])) {
            $data = $data[$field];
        }

        return $data;
    }

    /**
     * Save form fields in session
     *
     * @param mixed $data Data to save
     *
     * @return void
     */
    protected function saveFormData($data)
    {
        $savedData = $this->getSavedForms();

        if (isset($data)) {
            $savedData[$this->getFormName()] = array(
                self::SAVED_FORM_DATA => $data,
            );

        } else {
            $savedData[$this->getFormName()] = array();
        }

        \XLite\Core\Session::getInstance()->set(self::SAVED_FORMS, empty($savedData) ? null : $savedData);
    }

    /**
     * Clear form fields in session
     *
     * @return void
     */
    protected function clearFormData()
    {
        $this->saveFormData(null);
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataDeletedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('Data have been deleted successfully');
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionCreate()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionUpdate()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionModify()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionDelete()
    {
        $this->addDataDeletedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessAction()
    {
        $method = __FUNCTION__ . ucfirst($this->currentAction);

        if (method_exists($this, $method)) {
            // Run the corresponded function
            $this->$method();
        }

        $this->setActionSuccess();
    }

    /**
     * Perform some actions on error
     *
     * @return void
     */
    protected function postprocessErrorAction()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        $method = __FUNCTION__ . ucfirst($this->currentAction);

        if (method_exists($this, $method)) {
            // Run corresponded function
            $this->$method();
        }

        $this->setActionError();
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $em = \XLite\Core\Database::getEM();
        $model = $this->getModelObject();
        if ($em->contains($model)) {
            $em->refresh($model);
        }
    }

    /**
     * Save reference to the current form
     *
     * @return void
     */
    protected function startCurrentForm()
    {
        self::$currentForm = $this;
    }

    /**
     * Called after the includeCompiledFile()
     *
     * @return void
     */
    protected function closeView()
    {
        parent::closeView();

        $this->clearFormData();
    }

    /**
     * getFieldBySchema
     * TODO - should use the Factory class
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        $result = null;

        $class = $data[self::SCHEMA_CLASS];

        if (\XLite\Core\Operator::isClassExists($class)) {
            $method = 'prepareFieldParams' . \XLite\Core\Converter::convertToCamelCase($name);

            if (method_exists($this, $method)) {
                // Call the corresponded method
                $data = $this->$method($data);
            }

            $result = new $class($this->getFieldSchemaArgs($name, $data));
        }

        return $result;
    }

    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        $result = array();

        foreach ($schema as $name => $data) {
            $field = $this->getFieldBySchema($name, $data);
            if ($field) {
                $result[$name] = $field;
            }
        }

        return $result;
    }

    /**
     * Remove empty sections
     *
     * @return void
     */
    protected function filterFormFields()
    {
        // First dimension - sections list
        foreach ($this->formFields as $section => &$data) {

            // Second dimension - fields
            foreach ($data[self::SECTION_PARAM_FIELDS] as $index => $field) {

                if (!$field->checkVisibility()) {
                    // Exclude field from list if it's not visible
                    unset($data[self::SECTION_PARAM_FIELDS][$index]);
                } else {
                    // Else include this field into the list of available fields
                    $this->formFieldNames[] = $field->getName();
                }
            }

            // Remove whole section if it's empty
            if (empty($data[self::SECTION_PARAM_FIELDS])) {
                unset($this->formFields[$section]);
            }
        }
    }

    /**
     * Wrapper for the "getFieldsBySchema()" method
     *
     * @param string $name Schema short name
     *
     * @return array
     */
    protected function translateSchema($name)
    {
        $schema = 'schema' . ucfirst($name);

        return property_exists($this, $schema) ? $this->getFieldsBySchema($this->$schema) : array();
    }

    /**
     * Return list of form fields
     *
     * @param boolean $onlyNames Flag; return objects or only the indexes OPTIONAL
     *
     * @return array
     */
    protected function getFormFields($onlyNames = false)
    {
        if (!isset($this->formFields)) {
            $this->defineFormFields();
            $this->filterFormFields();
        }

        return $onlyNames ? $this->formFieldNames : $this->formFields;
    }

    /**
     * Return certain form field
     *
     * @param string  $section        Section where the field located
     * @param string  $name           Field name
     * @param boolean $preprocessName Flag; prepare field name or not OPTIONAL
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFormField($section, $name, $preprocessName = true)
    {
        $result = null;
        $fields = $this->getFormFields();

        if ($preprocessName) {
            $name = $this->composeFieldName($name);
        }

        if (isset($fields[$section][self::SECTION_PARAM_FIELDS][$name])) {
            $result = $fields[$section][self::SECTION_PARAM_FIELDS][$name];
        }

        return $result;
    }

    /**
     * Return list of form fields to display
     *
     * @return array
     */
    protected function getFormFieldsForDisplay()
    {
        $result = $this->getFormFields();
        unset($result[self::SECTION_HIDDEN]);

        return $result;
    }

    /**
     * Display section header or not
     *
     * @param string $section Name of section to check
     *
     * @return boolean
     */
    protected function isShowSectionHeader($section)
    {
        return !in_array($section, array(self::SECTION_DEFAULT, self::SECTION_HIDDEN));
    }

    /**
     * prepareRequestData
     *
     * @param array $data Request data
     *
     * @return array
     */
    protected function prepareRequestData(array $data)
    {
        return $data;
    }

    /**
     * Prepare and save passed data
     *
     * @param array       $data Passed data OPTIONAL
     * @param string|null $name Index in request data array (optional) OPTIONAL
     *
     * @return void
     */
    protected function defineRequestData(array $data = array(), $name = null)
    {
        if (empty($data)) {
            $data = $this->prepareRequestParamsList();
        }
        // FIXME: check if there is the way to avoid this
        $this->formFields = null;

        // TODO: check if there is more convenient way to do this
        $this->requestData = $this->prepareRequestData($data);
        $this->requestData = \Includes\Utils\ArrayManager::filterByKeys(
            $this->requestData,
            $this->getFormFields(true)
        );

        $this->requestData = $this->prepareRequestDataByFormFields($this->requestData);
    }

    /**
     * Prepare request data by form fields (typecasting)
     *
     * @param array $requestData Request data
     *
     * @return array
     */
    protected function prepareRequestDataByFormFields($requestData)
    {
        $schemas = $this->getAllSchemaCells();
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        foreach ($requestData as $name => $value) {

            $formField = $this->getFormFieldsByName($name);

            if (
                isset($formField)
                && is_object($formField)
                && method_exists($formField, 'prepareRequestData')
            ) {

                if ($formField->isTrusted() || !empty($schemas[$name][static::SCHEMA_TRUSTED])) {
                    // Formfield value is trusted
                    $value = $nonFilteredData[$name];
                }

                // prepare request data (typecasting)
                $requestData[$name] = $formField->prepareRequestData($value);
            }
        }

        return $requestData;
    }

    /**
     * Get all schemas data
     *
     * @return array
     */
    protected function getAllSchemaCells()
    {
        $result = array();

        if (method_exists($this, 'getSchemaFields')) {
            // Some classes define schema fields by method getSchemaFields()
            $result = $this->getSchemaFields();

        } else {
            // Get schema fields from properties schemaSectionName if defined
            foreach ($this->sections as $section => $label) {
                $schema = 'schema' . ucfirst($section);

                if (isset($this->$schema) && is_array($this->$schema)) {
                    $result = array_merge($result, $this->$schema);
                }
            }
        }

        return $result;
    }

    /**
     * Get form field by name
     *
     * @param string $name Field name
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFormFieldsByName($name)
    {
        $result = null;

        foreach ($this->getFormFields() as $formFields) {
            if (isset($formFields[static::SECTION_PARAM_FIELDS][$name])) {
                $result = $formFields[static::SECTION_PARAM_FIELDS][$name];

                break;
            }
        }

        return $result;
    }

    /**
     * Return list of the "Button" widgets
     * Do not use this method if you want sticky buttons panel.
     * The sticky buttons panel class has the buttons definition already.
     *
     * @return array
     */
    protected function getFormButtons()
    {
        return array();
    }

    /**
     * Prepare error message before display
     *
     * @param string $message Message itself
     * @param array  $data    Current section data
     *
     * @return string
     */
    protected function prepareErrorMessage($message, array $data)
    {
        if (isset($data[self::SECTION_PARAM_WIDGET])) {
            $sectionTitle = $data[self::SECTION_PARAM_WIDGET]->getLabel();
        }

        if (!empty($sectionTitle)) {
            $message = $sectionTitle . ': ' . $message;
        }

        return $message;
    }

    /**
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        foreach ($data[self::SECTION_PARAM_FIELDS] as $field) {
            if ($this->checkDependency($field)) {
                list($flag, $message) = $field->validate();
                if (!$flag) {
                    $this->addErrorMessage($field->getName(), $message, $data);
                }
            }
        }
    }

    /**
     * Validate form field.
     * This method is called from FormField object to perform additional validation on the form side.
     *
     * @param \XLite\View\FormField\AFormField $field Form field object
     *
     * @return array
     */
    public function validateFormField($field)
    {
        $result = array(true, null);

        $name = $field->getName();

        if (in_array($name, $this->getFormFields(true))) {
            $method = 'validateFormField' . \XLite\Core\Converter::convertToCamelCase($name) . 'Value';
            if (method_exists($this, $method)) {
                $result = $this->$method($field, $this->getFormFields());
            }
        }

        return $result;
    }

    /**
     * Return list of form error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        if (!isset($this->errorMessages)) {
            $this->errorMessages = array();

            foreach ($this->getFormFields() as $section => $data) {
                $this->validateFields($data, $section);
            }
        }

        return $this->errorMessages;
    }

    /**
     * addErrorMessage
     *
     * @param string $name    Error name
     * @param string $message Error message
     * @param array  $data    Section data OPTIONAL
     *
     * @return void
     */
    protected function addErrorMessage($name, $message, array $data = array())
    {
        $this->errorMessages[$name] = $this->prepareErrorMessage($message, $data);
    }

    /**
     * Some JavaScript code to insert at the begin of form page
     *
     * @return string
     */
    protected function getTopInlineJSCode()
    {
        return null;
    }

    /**
     * Some JavaScript code to insert at the end of form page
     *
     * @return string
     */
    protected function getBottomInlineJSCode()
    {
        return null;
    }

    /**
     * Call the corresponded method for current action
     *
     * @param string $action Action name OPTIONAL
     *
     * @return boolean
     */
    protected function callActionHandler($action = null)
    {
        $action = self::ACTION_HANDLER_PREFIX . ucfirst($action ?: $this->currentAction);

        // Run the corresponded method
        return $this->$action();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionCreate()
    {
        return $this->getModelObject()->create();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        return $this->getModelObject()->update();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionModify()
    {
        if ($this->getModelObject()->isPersistent()) {
            $this->currentAction = 'update';
            $result = $this->callActionHandler('update');

        } else {
            $this->currentAction = 'create';
            $result = $this->callActionHandler('create');
        }

        return $result;
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionDelete()
    {
        return $this->getModelObject()->delete();
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
        $model = $this->getModelObject();

        $result = null;
        if (is_object($model)) {
            $result = $this->getModelValue($model, $name);
        }

        return $result;
    }

    /**
     * Get model value by name
     *
     * @param \XLite\Model\AEntity $model Model object
     * @param string               $name  Property name
     *
     * @return mixed
     */
    protected function getModelValue($model, $name)
    {
        $method = 'get' . \XLite\Core\Converter::convertToCamelCase($name);
        // $method - assemble from 'get' + property name
        return method_exists($model, $method) || ($model instanceof MagicMethodsIntrospectionInterface && $model->hasMagicMethod($method))
            ? $model->$method()
            : ($model->isPropertyExists($name) ? $model->getterProperty($name) : null);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() || $this->isExported();
    }

    /**
     * Add field into the list of excluded fields
     *
     * @param string $fieldName Field name
     *
     * @return void
     */
    protected function excludeField($fieldName)
    {
        $this->excludedFields[] = $fieldName;
    }

    /**
     * Prepare request data for mapping into model object.
     * Model object is provided with methods:
     * prepareObjectForMapping <- getModelObject <- getDefaultModelObject (or getParam(self::PARAM_MODEL_OBJECT))
     *
     * Use $this->excludeField($fieldName) method to remove unnecessary data from request.
     *
     * Call $this->excludeField() method in "performAction*" methods before parent::performAction* call.
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = $this->getRequestData();

        // Remove fields in the $excludedFields list from the data for mapping
        if (!empty($this->excludedFields)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->excludedFields)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Prepare object for mapping
     *
     * @return \XLite\Model\AEntity
     */
    protected function prepareObjectForMapping()
    {
        return $this->getModelObject();
    }

    /**
     * Return name of the current form
     *
     * @return string
     */
    protected function getFormName()
    {
        return get_class($this);
    }

    /**
     * Display view sublist
     *
     * @param string $suffix    List suffix
     * @param array  $arguments List arguments OPTIONAL
     *
     * @return void
     */
    protected function displayViewSubList($suffix, array $arguments = array())
    {
        $class = preg_replace('/^.+\\\View\\\Model\\\/Ss', '', get_called_class());
        $class = str_replace('\\', '.', $class);
        if (preg_match('/\\\Module\\\(a-z0-9+)\\\(a-z0-9+)\\\View\\\Model\\\/Sis', get_called_class(), $match)) {
            $class = $match[1] . '.' . $match[2] . '.' . $class;
        }
        $class = strtolower($class);

        $list = 'crud.' . $class . '.' . $suffix;

        $arguments = $this->assembleViewSubListArguments($suffix, $arguments);

        $this->displayViewListContent($list, $arguments);
    }

    /**
     * Assemble biew sublist arguments
     *
     * @param string $suffix    List suffix
     * @param array  $arguments Arguments
     *
     * @return array
     */
    protected function assembleViewSubListArguments($suffix, array $arguments)
    {
        $arguments['model'] = $this;
        $arguments['useBodyTemplate'] = false;

        return $arguments;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'model-properties';
    }

    /**
     * Get item class
     *
     * @param integer                          $index  Item index
     * @param integer                          $length Items list length
     * @param \XLite\View\FormField\AFormField $field  Current item
     *
     * @return string
     */
    protected function getItemClass($index, $length, \XLite\View\FormField\AFormField $field)
    {
        $classes = preg_grep('/.+/Ss', array_map('trim', explode(' ', $field->getWrapperClass())));

        if (1 === $index) {
            $classes[] = 'first';
        }

        if ($length == $index) {
            $classes[] = 'last';
        }

        if ($field->getParam(static::SCHEMA_DEPENDENCY)) {
            $classes[] = 'has-dependency';
        }

        return implode(' ', $classes);
    }

    /**
     * Get field commented data
     *
     * @param \XLite\View\FormField\AFormField $filed Field
     *
     * @return array
     */
    protected function getFieldCommentedData($filed)
    {
        $commentedData = array();

        if ($filed->getParam(static::SCHEMA_DEPENDENCY)) {
            $commentedData['dependency'] = $filed->getParam(static::SCHEMA_DEPENDENCY);
        }

        return $commentedData;
    }

    /**
     * Check dependency
     *
     * @param \XLite\View\FormField\AFormField $field Field
     *
     * @return boolean
     */
    protected function checkDependency($field)
    {
        $dependency = $field->getParam(\XLite\View\FormField\AFormField::PARAM_DEPENDENCY);
        $result = true;

        foreach ($dependency as $depType => $dependencies) {
            foreach ($dependencies as $depField => $depValue) {

                if (static::DEPENDENCY_SHOW == $depType) {
                    if ($this->checkRequestHasExpectedValue($depField, $depValue)) {
                        if (false !== $result) {
                            $result = true;
                        }
                    } else {
                        $result = false;
                    }
                } else {
                    if ($this->checkRequestHasExpectedValue($depField, $depValue)) {
                        $result = false;
                    } else {
                        if (false !== $result) {
                            $result = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check request has expected value
     *
     * @param string $fieldName     FieldName
     * @param mixed  $expectedValue Expected value (may be array of values)
     *
     * @return boolean
     */
    protected function checkRequestHasExpectedValue($fieldName, $expectedValue)
    {
        return $this->getRequestData($fieldName) == $expectedValue
            || (is_array($expectedValue) && in_array($this->getRequestData($fieldName), $expectedValue));
    }

    /**
     * Return true if specific section is collapsible
     *
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsible($section)
    {
        return false;
    }

    /**
     * Return true if specific section is collapsed
     *
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsed($section)
    {
        return false;
    }
}
