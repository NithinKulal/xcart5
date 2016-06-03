<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * ATabs is a component allowing you to display multiple widgets as tabs depending
 * on their targets
 */
abstract class AJsTabs extends \XLite\View\AView
{
    /**
     * Cached result of the getTabs() method
     *
     * @var array
     */
    protected $processedTabs;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        foreach ($this->getTabs() as $tab) {
            if (!empty($tab['jsFiles'])) {
                if (is_array($tab['jsFiles'])) {
                    $list = array_merge($list, $tab['jsFiles']);

                } else {
                    $list[] = $tab['jsFiles'];
                }
            }
        }

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        foreach ($this->getTabs() as $tab) {
            if (!empty($tab['cssFiles'])) {
                if (is_array($tab['cssFiles'])) {
                    $list = array_merge($list, $tab['cssFiles']);

                } else {
                    $list[] = $tab['cssFiles'];
                }
            }
        }

        return $list;
    }

    /**
     * Checks whether no widget class is specified for the selected tab
     *
     * @param array $tab Tab
     *
     * @return boolean
     */
    public function isTemplateOnlyTab($tab)
    {
        return empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether both a template and a widget class are specified for the selected tab
     *
     * @param array $tab Tab
     *
     * @return boolean
     */
    public function isFullWidgetTab($tab)
    {
        return !empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @param array $tab Tab
     *
     * @return boolean
     */
    public function isWidgetOnlyTab($tab)
    {
        return !empty($tab['widget']) && empty($tab['template']);
    }

    /**
     * Returns a widget class name for the selected tab
     *
     * @param array $tab Tab
     *
     * @return string
     */
    public function getTabWidget($tab)
    {
        return isset($tab['widget']) ? $tab['widget'] : '';
    }

    /**
     * Returns a template name for the selected tab
     *
     * @param array $tab Tab
     *
     * @return string
     */
    public function getTabTemplate($tab)
    {
        return isset($tab['template']) ? $tab['template'] : '';
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @param array $tab Tab
     *
     * @return boolean
     */
    public function isCommonTab($tab)
    {
        return empty($tab['widget']) && empty($tab['template']);
    }

    /**
     * Flag: display (true) or hide (false) tabs
     *
     * @return boolean
     */
    protected function isWrapperVisible()
    {
        return true;
    }

    /**
     * Returns concatenated string of tab-pane css classes
     *
     * @param  array $tab Tab record
     * @return string
     */
    protected function printPaneClasses(array $tab)
    {
        $classes = isset($tab['paneClasses']) ? $tab['paneClasses'] : array();
        return implode(' ', $classes);
    }

    /**
     * Returns the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/js_tabs.twig';
    }

    /**
     * Returns the current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return 1 < count($this->getTabs());
    }

    /**
     * Returns tab URL
     *
     * @param string $name Tab name
     *
     * @return string
     */
    protected function buildTabURL($name)
    {
        return $this->buildURL(\XLite\Core\Request::getInstance()->target) . '#' . $name;
    }

    /**
     * Returns default values for a tab description
     *
     * @return array
     */
    protected function getDefaultTabValues()
    {
        return array(
            'title'     => '',
            'widget'    => '',
            'template'  => '',
        );
    }

    /**
     * Returns information on tab widgets and their targets defined as an array(tab) descriptions:
     *
     *      array(
     *          $target => array(
     *              'weight'    => $weight // Weight of the tab
     *              'title'     => $tabTitle,
     *              'widget'    => $optionalWidgetClass,
     *              'template'  => $optionalWidgetTemplate,
     *          ),
     *          ...
     *      );
     *
     * If a widget class is not specified for a target, the ATabs descendant will be used as the widget class.
     * If a template is not specified for a target, it will be used from the tab widget class.
     *
     * @return array
     */
    protected function defineTabs()
    {
        return array();
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $tabs = $this->defineTabs();
        // Manage the omitted weights of tabs
        $index = 1;
        foreach ($tabs as $target => $tab) {
            if (!isset($tab['weight'])) {
                $tabs[$target]['weight'] = $index;
            }
            $index++;
        }
        // Sort the tabs via compareTabs method
        uasort($tabs, array($this, 'compareTabs'));
        return $tabs;
    }

    /**
     * This method is used for comparing tabs
     * By default they are compared according their weight
     *
     * @param array $tab1
     * @param array $tab2
     *
     * @return boolean
     */
    public function compareTabs($tab1, $tab2)
    {
        return $tab1['weight'] > $tab2['weight'];
    }


    /**
     * Returns an array(tab) descriptions
     *
     * @return array
     */
    protected function getTabs()
    {
        // Process tabs only once
        if (null === $this->processedTabs) {
            $selected = false;
            $this->processedTabs = array();
            $defaultValues = $this->getDefaultTabValues();

            foreach ($this->prepareTabs() as $target => $tab) {
                $tab['url'] = $this->buildTabURL($target);

                if (isset($tab['selected']) && $tab['selected']) {
                    if ($selected) {
                        $tabs[$target]['selected'] = false;
                    }
                    $selected = true;
                }

                // Set default values for missing tab parameters
                $tab += $defaultValues;

                if (!$this->isStartAsActive()) {
                    $tab['selected'] = false;
                }

                $this->processedTabs[$target] = $tab;
            }

            if (!$selected && $this->isStartAsActive()) {
                $targets = array_keys($this->processedTabs);
                $this->processedTabs[$targets[0]]['selected'] = true;
            }
        }

        return $this->processedTabs;
    }

    /**
     * Checks if tabs should start with active tab-pane.
     *
     * @return boolean
     */
    protected function isStartAsActive()
    {
        return true;
    }

    /**
     * getTitle
     *
     * @return string
     */
    protected function getTitle()
    {
        return null;
    }
}
