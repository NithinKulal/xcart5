<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer\Page;

/**
 * APage
 */
abstract class APage extends \XLite\View\Product\Details\Customer\ACustomer
{
    const SPECIFICATION_TAB_LIST = 'product.details.page.tab.attributes';
    const DESCRIPTION_TAB_LIST = 'product.details.page.tab.description';

    /**
     * Tabs (cache)
     *
     * @var array
     */
    protected $tabs;

    /**
     * Attributes widgets cache
     *
     * @var array
     */
    protected $attributesWidgets = null;

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::getDir() . '/controller.js';

        return $list;
    }

    /**
     * Check - loupe icon is visible or not
     * 
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        return $this->getProduct()->hasImage();
    }

    // {{{ Tabs

    /**
     * Compare tabs 
     * 
     * @param array $a Tab 1
     * @param array $b tab 2
     *  
     * @return integer
     */
    public function compareTabs(array $a, array $b)
    {
        if ($a['weight'] == $b['weight']) {
            $result = 0;

        } elseif ($a['weight'] > $b['weight']) {
            $result = 1;

        } else {
            $result = -1;
        }

        return $result;
    }

    /**
     * Get tabs
     *
     * @return array
     */
    protected function getTabs()
    {
        if (!isset($this->tabs)) {
            $list = $this->defineTabs();
            $i = 0;
            foreach ($list as $k => $data) {
                $id = isset($data['id'])
                    ? $data['id']
                    : preg_replace('/\W+/Ss', '-', strtolower($k));

                $list[$k] = array(
                    'index'  => $i,
                    'id'     => 'product-details-tab-' . $id,
                    'name'   => is_array($data) && !empty($data['name'])
                        ? $data['name']
                        : static::t($k),
                    'weight' => isset($data['weight']) ? $data['weight'] : $i,
                );

                if (is_string($data)) {
                    $list[$k]['template'] = $data;

                } elseif (is_array($data) && !empty($data['template'])) {
                    $list[$k]['template'] = $data['template'];

                } elseif (is_array($data) && !empty($data['list'])) {
                    $list[$k]['list'] = $data['list'];

                } elseif (is_array($data) && !empty($data['widget'])) {
                    $parameters = array(
                        'product' => $this->getProduct(),
                    );
                    if (!empty($data['parameters'])) {
                        $parameters += $data['parameters'];
                    }
                    $list[$k]['widgetObject'] = $this->getWidget($parameters, $data['widget']);
                    unset($data['widget']);

                } else {
                    unset($list[$k]);
                }

                $i++;
            }

            $this->tabs = $list;
            uasort($this->tabs, array($this, 'compareTabs'));
        }

        return $this->tabs;
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $list = array();

        $this->defineDescriptionTab($list);
        $this->defineSpecificationTab($list);

        return $list;
    }

    /**
     * Define the description tab
     *
     * @param array $list
     */
    protected function defineDescriptionTab(&$list)
    {
        if ($this->hasDescription()) {
            $list['Description'] = array(
                'list' => static::DESCRIPTION_TAB_LIST,
            );
        }
    }

    /**
     * Define the specification tab
     *
     * @param array $list
     */
    protected function defineSpecificationTab(&$list)
    {
        /**
         * The specification tab is visible if the attributes list is configured
         * as visible in the separated tab and at least one attribute widget is visible in it
         */
        if (
            $this->getProduct()->getAttrSepTab()
            && $this->isAttributesVisible()
        ) {
            $list['Specification'] = array(
                'list' => static::SPECIFICATION_TAB_LIST,
            );
        }
    }


    /**
     * Attributes widgets are collected into the inner cache
     * and the specification visibility is defined
     * as the visibility at least one of the attribute widget
     *
     * @return boolean
     */
    protected function isAttributesVisible()
    {
        $visible = false;
        foreach ($this->getAttributesWidgets() as $aWidget) {
            if ($aWidget->isVisible()) {
                $visible = true;
                break;
            }
        }

        return $visible;
    }

    /**
     * Define attributes widgets
     *
     * @return void
     */
    protected function defineAttributesWidgets()
    {
        $this->attributesWidgets = array();

        $product = $this->getProduct();

        if ($product->getProductClass()) {
            $this->attributesWidgets[] = $this->getWidget(
                array(
                    'product'      => $product,
                    'productClass' => $product->getProductClass()
                ),
                '\XLite\View\Product\Details\Customer\Attributes'
            );
        }

        $this->attributesWidgets[] = $this->getWidget(
            array(
                'product' => $product
            ),
            '\XLite\View\Product\Details\Customer\Attributes'
        );

        $this->attributesWidgets[] = $this->getWidget(
            array(
                'product'      => $product,
                'personalOnly' => true
            ),
            '\XLite\View\Product\Details\Customer\Attributes'
        );

        if ($product->getProductClass()) {
            foreach ($product->getProductClass()->getAttributeGroups() as $group) {
                $this->attributesWidgets[] = $this->getWidget(
                    array(
                        'product' => $product,
                        'group'   => $group
                    ),
                    '\XLite\View\Product\Details\Customer\Attributes'
                );
            }
        }

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(null) as $group) {
            $this->attributesWidgets[] = $this->getWidget(
                array(
                    'product' => $product,
                    'group'   => $group
                ),
                '\XLite\View\Product\Details\Customer\Attributes'
            );
        }
    }

    /**
     * Get attributes widgets
     *
     * @return array
     */
    protected function getAttributesWidgets()
    {
        if (is_null($this->attributesWidgets)) {
            $this->defineAttributesWidgets();
        }

        return $this->attributesWidgets;
    }

    /**
     * Get tab class
     *
     * @param array $tab Tab
     *
     * @return string
     */
    protected function getTabClass(array $tab)
    {
        return $this->isTabActive($tab) ? 'active' : '';
    }

    /**
     * Get tab container style
     *
     * @param array $tab Tab
     *
     * @return string
     */
    protected function getTabStyle(array $tab)
    {
        return $this->isTabActive($tab) ? '' : 'display: none;';
    }

    /**
     * Check tab activity
     *
     * @param array $tab Tab info cell
     *
     * @return boolean
     */
    protected function isTabActive(array $tab)
    {
        return 0 === $tab['index'];
    }

    // }}}

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return [];
    }
}
