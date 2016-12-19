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
abstract class ATabs extends \XLite\View\AView
{
    /** @var array Tabs */
    protected $tabs = [];

    /** @var array Cached result of the getTabs() method */
    protected $processedTabs;

    /**
     * Define tabs
     *
     * Information on tab widgets and their targets defined as an array(tab) descriptions:
     *
     *      array(
     *          $target => array(
     *              'weight'   => $weight // Weight of the tab
     *              'title'    => $tabTitle,
     *              'widget'   => $optionalWidgetClass,
     *              'template' => $optionalWidgetTemplate,
     *          ),
     *          ...
     *      );
     *
     * If a widget class is not specified for a target, the ATabs descendant will be used as the widget class.
     * If a template is not specified for a target, it will be used from the tab widget class.
     *
     * @return array
     */
    abstract protected function defineTabs();

    /**
     * Define tabs
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->tabs = $this->defineTabs();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $tab = $this->getSelectedTab();
        if (null !== $tab && !empty($tab['jsFiles'])) {
            if (is_array($tab['jsFiles'])) {
                $list = array_merge($list, $tab['jsFiles']);

            } else {
                $list[] = $tab['jsFiles'];
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

        $tab = $this->getSelectedTab();
        if (null !== $tab && !empty($tab['cssFiles'])) {
            if (is_array($tab['cssFiles'])) {
                $list = array_merge($list, $tab['cssFiles']);

            } else {
                $list[] = $tab['cssFiles'];
            }
        }

        return $list;
    }

    /**
     * Checks whether no widget class is specified for the selected tab
     *
     * @return boolean
     */
    public function isTemplateOnlyTab()
    {
        $tab = $this->getSelectedTab();

        return null !== $tab && empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether both a template and a widget class are specified for the selected tab
     *
     * @return boolean
     */
    public function isFullWidgetTab()
    {
        $tab = $this->getSelectedTab();

        return null !== $tab && !empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @return boolean
     */
    public function isWidgetOnlyTab()
    {
        $tab = $this->getSelectedTab();

        return null !== $tab && !empty($tab['widget']) && empty($tab['template']);
    }

    /**
     * Returns a widget class name for the selected tab
     *
     * @return string
     */
    public function getTabWidget()
    {
        $tab = $this->getSelectedTab();

        return isset($tab['widget']) ? $tab['widget'] : '';
    }

    /**
     * Returns a template name for the selected tab
     *
     * @return string
     */
    public function getTabTemplate()
    {
        $tab = $this->getSelectedTab();

        return isset($tab['template']) ? $tab['template'] : '';
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @return boolean
     */
    public function isCommonTab()
    {
        $tab = $this->getSelectedTab();

        return null !== $tab && empty($tab['widget']) && empty($tab['template']);
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
     * Returns the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs.twig';
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
     * Returns a list of targets for which the tabs are visible
     *
     * @return array
     */
    protected function getTabTargets()
    {
        return array_keys($this->tabs);
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && in_array($this->getCurrentTarget(), $this->getTabTargets(), true);
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
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target);
    }

    /**
     * Checks whether a tab is selected
     *
     * @param string $target Tab target
     *
     * @return boolean
     */
    protected function isSelectedTab($target)
    {
        return $target === $this->getCurrentTarget();
    }

    /**
     * Returns default values for a tab description
     *
     * @return array
     */
    protected function getDefaultTabValues()
    {
        return array(
            'title'    => '',
            'widget'   => '',
            'template' => '',
        );
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $tabs = $this->tabs;
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
            $this->processedTabs = [];
            $defaultValues = $this->getDefaultTabValues();

            foreach ($this->prepareTabs() as $target => $tab) {
                $tab['selected'] = $this->isSelectedTab($target);
                $params = isset($tab['url_params']) ? $tab['url_params'] : array();
                $tab['url'] = $params 
                    ? $this->buildURL($target, '', $params) 
                    : $this->buildTabURL($target);

                // Set default values for missing tab parameters
                $tab += $defaultValues;

                $this->processedTabs[$target] = $tab;
            }

        }

        return $this->processedTabs;
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

    /**
     * Returns a description of the selected tab. If no tab is selected, returns NULL.
     *
     * @return array
     */
    protected function getSelectedTab()
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getTabs(), $this->getCurrentTarget());
    }

    /**
     * Get tab link template
     *
     * @param array $tab Tab data
     *
     * @return boolean|string
     */
    protected function getTabLinkTemplate(array $tab)
    {
        return false;
    }

}
