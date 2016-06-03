<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel;

/**
 * Abstract search panel
 */
abstract class ASearchPanel extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ITEMS_LIST = 'itemsList';

    /**
     * Condition cell names
     */
    const CONDITION_CLASS    = 'class';
    const CONDITION_TEMPLATE = 'template';
    const CONDITION_CELL     = 'cell';

    /**
     * Conditions
     *
     * @var   array
     */
    protected $conditions;

    /**
     * Hidden conditions
     *
     * @var   array
     */
    protected $hiddenConditions;

    /**
     * Actions
     *
     * @var   array
     */
    protected $actions;

    /**
     * Get form class
     *
     * @return string
     */
    abstract protected function getFormClass();

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ITEMS_LIST => new \XLite\Model\WidgetParam\TypeObject(
                'ItemsList object',
                null,
                false,
                'XLite\View\ItemsList\Model\Table'
            ),
        );
    }

    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        $options = $this->getOwnFormOptions();

        $itemsListForwardedOptions = $this->getItemsListForwardedFormOptions();
        if (is_array($itemsListForwardedOptions)) {
            $options = \Includes\Utils\ArrayManager::mergeRecursiveDistinct(
                $options,
                $itemsListForwardedOptions
            );
        }

        return $options;
    }

    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getOwnFormOptions()
    {
        return array(
            'class'     => $this->getFormClass(),
            'name'      => str_replace('\\', '', get_class($this) .'_search'),
            'target'    => null,
            'action'    => null,
            'params'    => $this->getFormParams(),
        );
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getItemsListForwardedFormOptions()
    {
        return $this->getItemsList() && method_exists($this->getItemsList(), 'getSearchFormOptions')
            ? $this->getItemsList()->getSearchFormOptions()
            : null;
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = array();

        if ($this->getItemsList()) {
            $params['itemsList'] = get_class($this->getItemsList());
        }

        return $params;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     * @TODO: restructure them to make possible the multiple search panels and items list appearance on one page
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return '';
    }

    /**
     * Define the specific widget params to send them into the JS code
     *
     * @return array
     */
    protected function getSearchPanelParams()
    {
        return array(
            'linked'               => $this->getLinkedItemsList(),
            'hideAdditionalFields' => (bool) $this->getCurrentSearchFilter(),
        );
    }

    /**
     * Get itemsList
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsList()
    {
        return $this->getParam(static::PARAM_ITEMS_LIST);
    }

    // {{{ Conditions

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $result = null;
        $list = $this->getItemsList();
        if ($list) {
            $storage = $list::getSearchValuesStorage();

            $result = $storage->getValue($paramName);
        }

        return $result;
    }

    /**
     * Get conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        if (!isset($this->conditions)) {
            $this->conditions = $this->defineConditions();
            $this->conditions = $this->prepareConditions($this->conditions);
        }

        return $this->conditions;
    }

    /**
     * Get hidden conditions
     *
     * @return array
     */
    protected function getHiddenConditions()
    {
        if (!isset($this->hiddenConditions)) {
            $this->hiddenConditions = $this->defineHiddenConditions();
            $this->hiddenConditions = $this->prepareConditions($this->hiddenConditions);
        }

        return $this->hiddenConditions;
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return array();
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return array();
    }

    /**
     * Prepare conditions
     *
     * @param array $conditions Conditions
     *
     * @return array
     */
    protected function prepareConditions(array $conditions)
    {
        foreach ($conditions as $name => $condition) {
            if (is_array($condition)) {
                if (!isset($condition[\XLite\View\FormField\AFormField::PARAM_NAME])) {
                    $condition[\XLite\View\FormField\AFormField::PARAM_NAME] = $name;
                }
                if (!isset($condition[\XLite\View\FormField\AFormField::PARAM_FIELD_ONLY])
                    && !isset($condition[\XLite\View\FormField\AFormField::PARAM_LABEL])
                ) {
                    $condition[\XLite\View\FormField\AFormField::PARAM_FIELD_ONLY] = true;
                }

                if (!isset($condition[\XLite\View\FormField\AFormField::PARAM_VALUE])) {
                    if (empty($condition[static::CONDITION_CELL])) {
                        $condition[static::CONDITION_CELL] = $name;
                    }
                    $condition[\XLite\View\FormField\AFormField::PARAM_VALUE] = $this->prepareConditionValue($condition);
                }
                $conditions[$name] = $this->getWidgetByParams($condition);
            }
        }

        return $conditions;
    }

    /**
     * Prepare the value of the condition
     *
     * @param array $condition
     *
     * @return mixed
     */
    protected function prepareConditionValue($condition)
    {
        return $this->getCondition($condition[static::CONDITION_CELL]);
    }

    // }}}

    // {{{ Actions

    /**
     * Get actions
     *
     * @return array
     */
    protected function getActions()
    {
        if (!isset($this->actions)) {
            $this->actions = $this->defineActions();
            $this->actions = $this->prepareActions($this->actions);
        }

        return $this->actions;
    }

    /**
     * Define actions
     *
     * @return array
     */
    protected function defineActions()
    {
        return array(
            'submit' => array(
                'class'                                     => '\XLite\View\Button\Submit',
                \XLite\View\Button\AButton::PARAM_LABEL     => static::t('Search'),
                \XLite\View\Button\AButton::PARAM_BTN_SIZE  => \XLite\View\Button\AButton::BTN_SIZE_DEFAULT,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE  => '',
            ),
        );
    }

    /**
     * Prepare actions
     *
     * @param array $actions Actions
     *
     * @return array
     */
    protected function prepareActions(array $actions)
    {
        foreach ($actions as $name => $action) {
            if (is_array($action)) {
                $actions[$name] = $this->getWidgetByParams($action);
            }
        }

        return $actions;
    }

    // }}}

    // {{{ Visual

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'search_panel/style.css';

        return $list;
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     * During the viewers initialization the JS files are collecting into the static storage.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'search_panel/controller.js';
        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 0 < count($this->getConditions());
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'search_panel/body.twig';
    }

    /**
     * Get container form class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return implode('-', $this->getViewClassKeys());
    }

    /**
     * Combines the nested list name from the parent list name and a suffix
     *
     * @param string $part Suffix to be added to the parent list name
     *
     * @return string
     */
    protected function getNestedListName($part)
    {
        $keys = $this->getViewClassKeys();

        if ('searchpanel' == $keys[0]) {
            unset($keys[0]);

        } elseif ('searchpanel' == $keys[2]) {
            unset($keys[2]);
        }

        return 'searchPanel.' . implode('.', $keys) . '.' . $part;
    }

    // }}}

    // {{{ Filter

    /**
     * Return true if search panel should use filters
     *
     * @return boolean
     */
    protected function isUseFilter()
    {
        return false;
    }

    /**
     * Get list of available filters for search panel
     *
     * @return array
     */
    protected function getSavedFilterOptions()
    {
        $result = array();

        $filters = $this->defineFilterOptions();

        if ($filters) {
            $resetFilterName = $this->getClearFilterName();

            if ($resetFilterName) {
                $resetFilter = new \XLite\Model\SearchFilter();
                $resetFilter->setName($resetFilterName);
                $result[0] = $resetFilter;
            }

            foreach($filters as $filter) {
                $result[$filter->getId()] = $filter;
            }
        }

        return $result;
    }

    /**
     * Define search filters options
     *
     * @return array
     */
    protected function defineFilterOptions()
    {
        $result = array();

        $key = $this->getSearchFilterKeyCell();

        if ($key) {

            $filters = \XLite\Core\Database::getRepo('XLite\Model\SearchFilter')->findByFilterKey($key);

            if ($filters) {
                foreach($filters as $filter) {
                    $result[$filter->getId()] = $filter;
                }
            }
        }

        return $result;
    }

    /**
     * Get name of the 'Reset filter' option
     *
     * @return string
     */
    protected function getClearFilterName()
    {
        return static::t('All items');
    }

    /**
     * Return true if filter may be removed
     *
     * @param \XLite\Model\SearchFilter $filter Search filter model object
     *
     * @return boolean
     */
    protected function isFilterRemovable($filter)
    {
        return $filter && 0 < intval($filter->getId());
    }

    // }}}
}
