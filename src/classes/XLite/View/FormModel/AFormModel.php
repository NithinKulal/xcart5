<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Callback;

/**
 * Class AFormModel
 */
abstract class AFormModel extends \XLite\View\AView
{
    const SECTION_DEFAULT = 'default';

    /** @var array $schema Schema runtime cache */
    protected $schema;

    /** @var array $theme */
    protected $theme = [];

    /** @var array $commonFiles */
    protected $commonFiles = [];

    /** @var array $jsFiles */
    protected $jsFiles = [];

    /** @var array $cssFiles */
    protected $cssFiles = [];

    /**
     * Composition helper
     * @todo: documentation
     *
     * @param $schema
     * @param $patch
     *
     * @return array
     */
    protected static function compose($schema, $patch)
    {
        foreach ($patch as $sectionName => $section) {
            foreach ($section as $fieldName => $fields) {
                if (isset($schema[$sectionName][$fieldName])) {
                    /** @var array|string $originalField */
                    $originalField = $schema[$sectionName][$fieldName];

                    if (is_scalar($originalField)) {
                        $originalField = ['label' => $originalField];
                    }

                    $correctDependency = false;
                    if (!isset($originalField['type'])
                        || $originalField['type'] !== 'XLite\View\FormModel\Type\Base\CompositeType'
                    ) {
                        /**
                         * Take 'label' and 'label_description' from original field and append 'type' and 'fields' to
                         * definition
                         */
                        $originalField = array_replace(
                            array_intersect_key(
                                $originalField,
                                array_flip(['label', 'label_description', 'position'])
                            ),
                            [
                                'type'   => 'XLite\View\FormModel\Type\Base\CompositeType',
                                'fields' => [
                                    $fieldName => array_replace(
                                        $originalField,
                                        ['position' => 100]
                                    ),
                                ],
                            ]
                        );

                        $correctDependency = true;
                    }

                    /** @todo: avoid array_marge() */
                    $originalField['fields'] = array_merge($originalField['fields'], $fields);
                    $schema[$sectionName][$fieldName] = $originalField;

                    if ($correctDependency) {
                        /**
                         * Correct dependency
                         * If some field depends on $section->$fieldName it must depend on
                         * $section->$fieldName->$fieldName
                         */
                        $schema = static::correctSchemaDependency($schema, $sectionName, $fieldName);
                    }
                }
            }
        }

        return $schema;
    }

    /**
     * @param $schema
     * @param $section
     * @param $field
     *
     * @return array
     *
     */
    protected static function correctSchemaDependency($schema, $section, $field)
    {
        foreach ($schema as $sectionName => $sectionDefinition) {
            foreach ($sectionDefinition as $fieldName => $fieldDefinition) {
                if (!isset($fieldDefinition['type'])
                    || $fieldDefinition['type'] !== 'XLite\View\FormModel\Type\Base\CompositeType'
                ) {
                    $schema[$sectionName][$fieldName]
                        = static::correctFieldDependency($fieldDefinition, $section, $field);

                } elseif (isset($fieldDefinition['type'], $fieldDefinition['fields'])
                    && $fieldDefinition['type'] === 'XLite\View\FormModel\Type\Base\CompositeType'
                ) {
                    $schema[$sectionName][$fieldName]
                        = static::correctFieldDependency($fieldDefinition, $section, $field);

                    foreach ($fieldDefinition['fields'] as $subFieldName => $subFieldDefinition) {
                        $schema[$sectionName][$fieldName]['fields'][$subFieldName]
                            = static::correctFieldDependency($subFieldDefinition, $section, $field);

                    }
                }
            }
        }

        return $schema;
    }

    /**
     * @param $fieldDefinition
     * @param $section
     * @param $field
     *
     * @return array
     */
    protected static function correctFieldDependency($fieldDefinition, $section, $field)
    {
        if (isset($fieldDefinition['show_when'])) {
            $dependency = $fieldDefinition['show_when'];
            if (isset($dependency[$section][$field])) {
                $dependencyRule = $dependency[$section][$field];

                $isEndRule = !is_array($dependencyRule)
                    || !array_filter(
                        array_map(
                            function ($item) {
                                return !is_int($item);
                            },
                            array_keys($dependencyRule)
                        )
                    );

                if ($isEndRule) {
                    $fieldDefinition['show_when'][$section][$field] = [$field => $dependencyRule];
                }
            }
        }

        if (isset($fieldDefinition['hide_when'])) {
            $dependency = $fieldDefinition['hide_when'];
            if (isset($dependency[$section][$field])) {
                $dependencyRule = $dependency[$section][$field];

                $isEndRule = !is_array($dependencyRule)
                    || !array_filter(
                        array_map(
                            function ($item) {
                                return !is_int($item);
                            },
                            array_keys($dependencyRule)
                        )
                    );

                if ($isEndRule) {
                    $fieldDefinition['hide_when'][$section][$field] = [$field => $dependencyRule];
                }
            }
        }

        return $fieldDefinition;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $this->getSchema();

        return array_merge(
            parent::getJSFiles(),
            ['form_model/controller.js', 'form_model/constraints.js'],
            $this->jsFiles ? call_user_func_array('array_merge', $this->jsFiles) : []
        );
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $this->getSchema();

        return array_merge(
            parent::getCSSFiles(),
            $this->cssFiles ? call_user_func_array('array_merge', $this->cssFiles) : []
        );
    }

    /**
     * Returns {@link Form} object.
     *
     * Need to create {@link FormView} (used in {@see AFormModel::getFormView})
     * Also need to validate submitted data
     *
     * @return Form
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function getForm()
    {
        $generator = new FormGenerator();
        $dataObject = $this->getDataObject();
        $viewObject = $this->getViewObject() ?: $dataObject;

        return $generator->generate($this->getSchema(), $dataObject, $viewObject, $this->getFormOptions($dataObject));
    }

    /**
     * @param mixed $object
     *
     * @return array
     */
    protected function getFormOptions($object)
    {
        $options = [
            'method' => 'POST',
            'action' => $this->getFormAction(),
        ];

        $returnURL = $this->getReturnURL();
        if ($returnURL) {
            $options['return_url'] = $returnURL;
        }

        /** Attach DTO level validation if exists */
        if (is_object($object) && method_exists($object, 'validate')) {
            $options['constraints'] = new Callback([get_class($object), 'validate']);
        }

        return $options;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (\LC_DEVELOPER_MODE && !\XLite\Core\Config::getInstance()->Performance->aggregate_js) {
            // @todo: this doesn't work with our minification (first getter or setter terminate object definition)
            $list[static::RESOURCE_JS][] = 'vue/vue.js';
            $list[static::RESOURCE_JS][] = 'vue-validator/dist/vue-validator.js';
        } else {
            $list[static::RESOURCE_JS][] = 'vue/vue.min.js';
            $list[static::RESOURCE_JS][] = 'vue-validator/dist/vue-validator.min.js';
        }
        $list[static::RESOURCE_JS][] = 'js/object_hash.js';

        $this->getSchema();
        $subResources = $this->commonFiles
            ? call_user_func_array('array_merge_recursive', $this->commonFiles)
            : [];

        return call_user_func_array('array_merge_recursive', [$list, $subResources]);
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_model/body.twig';
    }

    /**
     * Do not render form_start and form_end if null returned
     * @todo: to widget params
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return null;
    }

    /**
     * @todo: to widget params
     *
     * @return string
     */
    protected function getAction()
    {
        return '';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getReturnURLParams()
    {
        return $this->getActionParams();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams = array_replace(
            $this->widgetParams,
            [
                'object' => new \XLite\Model\WidgetParam\TypeObject('', []),
            ]
        );
    }

    /**
     * Data object
     *
     * Used to get form values also to populate submitted data to
     * If no object given with widget param "object" current controller special
     * method will be used
     *
     * @see AFormModel::getViewObject()
     *
     * @return mixed
     */
    protected function getDataObject()
    {
        return $this->getParam('object') ?: $this->getViewObject();
    }

    /**
     * @return string
     */
    protected function getViewObjectGetterName()
    {
        return 'getFormModelObject';
    }

    /**
     * View object
     *
     * Take view object from current controller special method
     *
     * @see AFormModel::getViewObjectGetterName()
     *
     * @return mixed
     */
    protected function getViewObject()
    {
        $controller = \XLite::getController();
        $getter = $this->getViewObjectGetterName();

        return method_exists($controller, $getter) ? $controller->{$getter}() : [];
    }

    /**
     * @return string
     */
    protected function getViewDataGetterName()
    {
        return 'getFormModelData';
    }

    /**
     * Data object
     *
     * Returns object with submitted data from current controller special method
     * Used only when errors occurred to show submitted data
     *
     * @see AFormModel::getViewDataGetterName()
     *
     * @return mixed
     */
    protected function getViewData()
    {
        $controller = \XLite::getController();
        $getter = $this->getViewDataGetterName();

        return method_exists($controller, $getter) ? $controller->{$getter}() : [];
    }

    /**
     * Returns section definition
     *
     * If only one section defined as string (not one element array)
     * then no section separation needed in {@link AFormModel::defineSchema()}
     *
     * Available definitions:
     * - *string value* - Defined unlabelled section (no section separation need in {@link AFoemModel::defineFields()})
     * - *array* - Several section (when string given as element w/o key - unlabeled section will be generated
     * with given service name, when string given with string key then value used as section label, when array given
     * with key then key will be used as serive name and array can contain several fields (label, help, description,
     * position, collapse, expanded)
     *
     * @return array|string
     */
    protected function defineSections()
    {
        return self::SECTION_DEFAULT;
    }

    /**
     * @param array|string $sections
     *
     * @return array
     */
    protected function prepareSections($sections)
    {
        $result = [];
        $position = 0;

        /**
         * @var int|string   $name
         * @var array|string $schema
         */
        foreach ((array) $sections as $name => $schema) {
            if (is_int($name)) {
                list($name, $schema) = [$schema, []];
            }

            if (is_scalar($schema)) {
                $schema = ['label' => $schema];
            }

            $schema = (array) $schema;

            if (array_key_exists('position', $schema) && is_numeric($schema['position'])) {
                $position = (int) $schema['position'];

            } else {
                $schema['position'] = $position += 0.001;
            }

            $result[$name] = $schema;
        }

        uasort($result, function ($a, $b) {
            $a = (float) $a['position'];
            $b = (float) $b['position'];

            return $a === $b ? 0 : ($a > $b ? 1 : -1);
        });

        return $result;
    }

    /**
     * @return array
     */
    abstract protected function defineFields();

    /**
     * @param array $sections
     *
     * @return array
     */
    protected function prepareFields($sections)
    {
        $result = [];

        foreach ($sections as $name => $fields) {
            $result[$name] = $this->prepareSectionFields($fields);
        }

        return $result;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function prepareSectionFields($fields)
    {
        $result = [];
        $position = 0;

        foreach ($fields as $name => $schema) {
            if (is_int($name)) {
                list($name, $schema) = [$schema, []];
            }

            if (is_scalar($schema)) {
                $schema = ['label' => $schema];
            }

            if (!array_key_exists('type', $schema)) {
                $schema['type'] = 'Symfony\Component\Form\Extension\Core\Type\TextType';
            }

            if (array_key_exists('position', $schema) && is_numeric($schema['position'])) {
                $position = (int) $schema['position'];

            } else {
                $schema['position'] = $position += 0.001;
            }

            $type = $schema['type'];
            $this->collectFieldResources($type);

            if ($type === 'XLite\View\FormModel\Type\Base\CompositeType' && isset($schema['fields'])) {
                $schema['fields'] = $this->prepareSectionFields($schema['fields']);
            }

            $result[$name] = $schema;
        }

        uasort($result, function ($a, $b) {
            $a = (float) $a['position'];
            $b = (float) $b['position'];

            return $a === $b ? 0 : ($a > $b ? 1 : -1);
        });

        return $result;
    }

    /**
     * @param string|AbstractType $type
     */
    protected function collectFieldResources($type)
    {
        $resources = [
            'theme'       => 'getThemeFile',
            'commonFiles' => 'getCommonFiles',
            'jsFiles'     => 'getJSFiles',
            'cssFiles'    => 'getCSSFiles',
        ];

        foreach ($resources as $resource => $method) {
            $this->collectFieldResource($type, $resource, $method);
        }
    }

    /**
     * @param string|AbstractType $type
     * @param string              $resource
     * @param string|null         $method
     */
    protected function collectFieldResource($type, $resource, $method)
    {
        if (!isset($this->{$resource}[$type]) && class_exists($type) && method_exists($type, $method)) {
            $this->{$resource}[$type] = $type::{$method}();
        }
    }

    protected function defineSchema()
    {
        $result = [];

        $fieldsDefinition = $this->defineFields();
        $sectionsDefinition = $this->defineSections();
        if (is_string($sectionsDefinition)) {
            $fieldsDefinition = [$sectionsDefinition => $fieldsDefinition];
        }

        $fields = $this->prepareFields($fieldsDefinition);

        foreach ($this->prepareSections($sectionsDefinition) as $name => $sectionDefinition) {
            if (isset($fields[$name])) {
                $result[$name] = [
                    'section' => $sectionDefinition,
                    'fields'  => $fields[$name],
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getSchema()
    {
        if (null === $this->schema) {
            $this->schema = $this->defineSchema();
        }

        return $this->schema;
    }

    /**
     * @return FormView
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    protected function getFormView()
    {
        $form = $this->getForm();

        /**
         * Populate view data (need if errors occurred)
         */
        $data = $this->getViewData();
        if ($data) {
            $form->submit($data);
        }

        return $form->createView();
    }

    /**
     * @return string
     */
    protected function getFormAction()
    {
        $target = $this->getTarget();
        $action = $this->getAction();
        $params = $this->getActionParams();

        return null !== $target ? \XLite\Core\Converter::buildURL($target, $action, $params) : '';
    }

    /**
     * @return string
     */
    protected function getReturnURL()
    {
        $target = $this->getTarget();
        $params = $this->getReturnURLParams();

        return null !== $target ? \XLite\Core\Converter::buildURL($target, '', $params) : '';
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
            ? 'XLite\View\StickyPanel\FormModel\Panel'
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
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        return [
            'submit' => new \XLite\View\Button\Submit(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL    => static::t('Save changes'),
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                ]
            ),
        ];
    }

    /**
     * @todo: implement in template
     *
     * @return boolean
     */
    protected function isFormTagVisible()
    {
        return null !== $this->getTarget();
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        return array_values(array_merge(
            ['form_model/theme.twig'],
            array_filter(array_unique($this->theme))
        ));
    }
}
