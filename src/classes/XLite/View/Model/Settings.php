<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\AModel
{
    /**
     * Row index delta (for calculation of odd/even CSS classes for field rows)
     *
     * @var integer
     */
    protected $rowIndexDelta = 0;

    public function __construct(array $params = array(), array $sections = array())
    {
        $this->sections = $this->getSectionFromOptions();

        parent::__construct($params, $sections);
    }

    protected function getSectionFromOptions()
    {
        $sections = ['default' => null];
        $index = 0;

        foreach ($this->getOptions() as $option) {
            if ('separator' === $option->getType()) {
                if (0 === $index) {
                    $sections = [$option->getName() => $option->getOptionName()];
                } else {
                    $sections[$option->getName()] = $option->getOptionName();
                }
            }

            $index++;
        }

        $sections['hidden'] = null;

        return $sections;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'settings/summary/controller.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'settings/summary/summary.css';

        return $list;
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFields()
    {
        $list = array();

        foreach ($this->getOptions() as $option) {
            $cell = $this->getFormFieldByOption($option);
            if ($cell) {
                $list[$option->getName()] = $cell;
            }
        }

        return $list;
    }

    /**
     * Get schema fields
     *
     * @param string $section
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $flag = 'default' === $section;
        $list = [];

        foreach ($this->getOptions() as $option) {

            if ($flag) {
                if ('separator' === $option->getType()) {
                    break;
                }

                $cell = $this->getFormFieldByOption($option);
                if ($cell) {
                    $list[$option->getName()] = $cell;
                }
            } elseif ('separator' === $option->getType()) {
                $flag = $option->getName() === $section;
            }
        }

        return $list;
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = null;
        $class = $this->detectFormFieldClassByOption($option);

        if ($class) {
            $cell = array(
                static::SCHEMA_CLASS    => $class,
                static::SCHEMA_LABEL    => $option->getOptionName(),
                \XLite\View\FormField\AFormField::PARAM_HELP => $option->getOptionComment(),
                static::SCHEMA_REQUIRED => false,
            );

            if ($this->isOptionRequired($option)) {
                $cell[static::SCHEMA_REQUIRED] = true;
            }

            $parameters = $option->getWidgetParameters();
            if ($parameters && is_array($parameters)) {
                $cell += $parameters;
            }
        }

        return $cell;
    }

    /**
     * Detect form field class by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return string
     */
    protected function detectFormFieldClassByOption(\XLite\Model\Config $option)
    {
        $class = null;
        $type = $option->getType() ?: 'text';

        switch ($type) {
            case 'textarea':
                $class = 'XLite\View\FormField\Textarea\Simple';
                break;

            case 'checkbox':
                $class = 'XLite\View\FormField\Input\Checkbox\Simple';
                break;

            case 'country':
                $class = 'XLite\View\FormField\Select\Country';
                break;

            case 'state':
                $class = 'XLite\View\FormField\Select\State';
                break;

            case 'currency':
                $class = 'XLite\View\FormField\Select\Currency';
                break;

            case 'separator':
                $class = 'XLite\View\FormField\Separator\Regular';
                break;

            case 'text':
                $class = 'XLite\View\FormField\Input\Text';
                break;

            case 'hidden':
                break;

            default:
                if (preg_match('/^\\\?XLite\\\/S', $option->getType())) {
                    $class = $option->getType();
                }
        }

        return $class;
    }

    /**
     * Check - option is required or not
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return boolean
     */
    protected function isOptionRequired(\XLite\Model\Config $option)
    {
        return false;
    }

    /**
     * Get form fields for default section
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $result = $this->getFieldsBySchema($this->getSchemaFields());

        // For country <-> state synchronization
        $this->setStateSelectorIds($result);

        return $result;
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
        $result = $this->getFieldsBySchema($this->getSchemaFieldsForSection($section));

        // For country <-> state synchronization
        $this->setStateSelectorIds($result);

        return $result;
    }

    /**
     * Get array of country/states selector fields which should be synchronized
     *
     * @return array
     */
    protected function getCountryStateSelectorFields()
    {
        return array(
            'location_country' => array(
                'location_state',
                'location_custom_state',
            ),
            'anonymous_country' => array(
                'anonymous_state',
                'anonymous_custom_state',
            ),
        );
    }

    /**
     * Pass the DOM IDs of the "State" selectbox to the "CountrySelector" widget
     *
     * @param array &$fields Widgets list
     *
     * @return void
     */
    protected function setStateSelectorIds(array &$fields)
    {
        $data = $this->getCountryStateSelectorFields();

        foreach ($data as $countryField => $stateFields) {
            if (isset($fields[$countryField], $stateFields[0], $stateFields[1])) {
                $fields[$countryField]->setStateSelectorIds(
                    str_replace('_', '-', $stateFields[0]), // States selector ID
                    str_replace('_', '-', $stateFields[1])  // Custom state input ID
                );
            }
        }
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
        $data = $this->getCountryStateSelectorFields();

        foreach ($data as $countryField => $stateFields) {
            if ($stateFields[1] === $field->getName()) {
                $this->rowIndexDelta++;
            }
        }

        $index += $this->rowIndexDelta;

        return parent::getItemClass($index, $length, $field);
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

        if ('css_js_performance' === $this->getTarget()) {
            $url = $this->buildURL('css_js_performance', 'clean_aggregation_cache');
            $result['clear_aggregation_cache'] = new \XLite\View\Button\Tooltip(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Clean aggregation cache',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Clean aggregation cache help text'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE => 'self.location=\'' . $url . '\'',
                )
            );

            $url = $this->buildURL('css_js_performance', 'clean_view_cache');
            $result['clear_widgets_cache'] = new \XLite\View\Button\Tooltip(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Clean widgets cache',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Clean widgets cache help text'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE => 'self.location=\'' . $url . '\'',
                )
            );
        }

        if ('cache_management' === $this->getTarget()) {
            $url = $this->buildURL('cache_management', 'rebuild');
            $result['re_deploy_cache'] = new \XLite\View\Button\Tooltip(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Re-deploy the store',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Re-deploy the store help text'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE => sprintf('if (confirm("' . static::t("Are you sure?") .'")) self.location="%s";', $url),
                )
            );

            $url = $this->buildURL('cache_management', 'quick_data');
            $result['quick_data'] = new \XLite\View\Button\Tooltip(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Calculate quick data',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Calculate quick data help text'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE => 'self.location=\'' . $url . '\'',
                )
            );
        }

        return $result;
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        return true;
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
        $value = null;

        foreach ($this->getOptions() as $option) {
            if ($option->getName() === $name) {
                $value = $option->getValue();
                break;
            }
        }

        return $value;
    }

    /**
     * Allow any content in config options
     *
     * @param string $name Option name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        $result = parent::isParamTrusted($name);

        if (!$result) {
            $options = $this->getEditableOptions();

            foreach ($options as $option) {
                if ($option->getName() == $name && $option->getCategory() == 'Company') {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
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
        $optionsToUpdate = $this->getOptionsToUpdate($data);

        $this->updateOptions($optionsToUpdate);
    }

    /**
     * Get options to update
     *
     * @param array $data Data to set
     *
     * @return array
     */
    protected function getOptionsToUpdate(array $data)
    {
        $result = array();

        // Find changed options and store them in $optionsToUpdate
        foreach ($this->getEditableOptions() as $key => $option) {
            $name  = $option->getName();
            $newValue = $this->sanitizeOptionValue($option, isset($data[$name]) ? $data[$name] : null);

            if ($this->isChanged($option, $newValue)) {
                $option->setNewValue($newValue);
                $result[] = $option;
            }
        }

        return $result;
    }

    /**
     * Update options
     *
     * @param array $options Options
     *
     * @return void
     */
    protected function updateOptions(array $options)
    {
        foreach ($options as $option) {
            if ($this->preprocessOption($option)) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    array(
                        'category' => $option->getCategory(),
                        'name'     => $option->getName(),
                        'type'     => $option->getType(),
                        'value'    => $option->getNewValue(),
                        'orderby'  => $option->getOrderby(),
                    )
                );
            }
        }
    }

    /**
     * Preprocess option change and return true on success
     *
     * @param \XLite\Model\Config $option Option entity
     *
     * @return boolean
     */
    protected function preprocessOption($option)
    {
        $result = true;

        $method = 'preprocess' . \XLite\Core\Converter::convertToCamelCase($option->name) . 'Option';

        if (method_exists($this, $method)) {
            $result = $this->$method($option);
        }

        return $result;
    }

    /**
     * Preprocess option Environment - update_wave
     *
     * @param \XLite\Model\Config $option Option entity
     *
     * @return boolean
     */
    protected function preprocessUpgradeWaveOption($option)
    {
        $result = false;
        $value = $option->getNewValue();
        $waves = \XLite\Core\Marketplace::getInstance()->getWaves();

        if ($waves && isset($waves[$value])) {
            if (\XLite\Core\Marketplace::getInstance()->changeKeysWave($value)) {
                \XLite\Core\TopMessage::addInfo('Upgrade access level has been successfully assigned to your license keys');
                $result = true;

            } else {
                \XLite\Core\TopMessage::addError('Could not assign upgrade access level to your license keys');
            }
        }

        return $result;
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $options = $this->getOptions();
        $exclude = array('separator', 'hidden');
        foreach ($options as $key => $option) {
            if (in_array($option->getType(), $exclude, true)) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Profile
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
        return 'XLite\View\Form\Settings';
    }

    /**
     * Check if option value is changed
     *
     * @param \XLite\Model\Config $option Config option
     * @param string              $value  New value
     *
     * @return string
     */
    protected function isChanged($option, $value)
    {
        return null !== $value && $option->getValue() !== $value;
    }

    /**
     * Sanitize option new value
     *
     * @param \XLite\Model\Config $option Config option
     * @param string              $value  New value
     *
     * @return string
     */
    protected function sanitizeOptionValue($option, $value)
    {
        $category = $option->getCategory();
        $name = $option->getName();

        $validationMethod = 'sanitize'
            . \Includes\Utils\Converter::convertToCamelCase($category)
            . \Includes\Utils\Converter::convertToCamelCase($name);

        if (method_exists($this, $validationMethod)) {
            $value = $this->$validationMethod($value);
        }

        $type = $option->getType();

        if ('checkbox' === $type) {
            $result = empty($value) ? 'N' : 'Y';

        } elseif ('serialized' === $type && null !== $value && is_array($value)) {
            $result = serialize($value);

        } elseif ('text' === $type) {
            $result = null !== $value ? trim($value) : '';

        } elseif('XLite\View\FormField\Input\PasswordWithValue' === $type) {
            $result = null !== $value ? $value : null;

        } else {
            $result = null !== $value ? $value : '';
        }

        return $result;
    }

    /**
     * Sanitize value of option Company->company_website
     *
     * @param string $value Input value
     *
     * @return string
     */
    protected function sanitizeCompanyCompanyWebsite($value)
    {
        $value = trim($value);

        return $value
            ? preg_replace('/^(http:\/\/|http(s):\/\/|((\w{1,6}):\/\/)|)(.+)$/S', 'http\\2://\\5', $value)
            : '';
    }

    /**
     * @param string $section
     * @param array  $params
     *
     * @return \XLite\View\AView
     */
    protected function defineSectionWidget($section, $params)
    {
        if ($this->isSectionCollapsible($section)) {
            $params['section'] = $section;
            $params['collapsed'] = $this->isSectionCollapsed($section);

            return new \XLite\View\FormField\Separator\Collapsible($params);
        }

        return parent::defineSectionWidget($section, $params);
    }
}
