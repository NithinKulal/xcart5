<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Dashboard page widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Dashboard extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('main'));
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Add widget specific JS-files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'dashboard';
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        return array(
            'low-inventory' => array(
                'name'   => static::t('Low inventory products', array('count' => $this->getLowInventoryProductsAmount())),
                'widget' => 'XLite\View\ItemsList\Model\Product\Admin\LowInventoryBlock',
                'style'  => 0 < $this->getLowInventoryProductsAmount() ? 'non-empty' : 'empty',
            ),
            'top_sellers' => array(
                'name'   => 'Top selling products',
                'widget' => 'XLite\View\Product\TopSellersBlock',
                'style'  => 'non-empty',
            ),
        );
    }

    /**
     * Prepare tabs
     *
     * @return array
     */
    protected function getTabs()
    {
        $i = 0;
        $tabs = $this->defineTabs();

        foreach ($tabs as $k => $tab) {
            $isVisible = true;

            if ($tab['widget']) {
                $widget = new $tab['widget']();
                $isVisible = $widget->checkVisibility();
            }

            if ($isVisible) {
                $tabs[$k]['index'] = $i;
                $tabs[$k]['id']    = sprintf('dashboard-tab-%d', $i);
                $tabs[$k]['class'] = $k;
                $i++;
            } else {
                unset($tabs[$k]);
            }
        }

        return $tabs;
    }

    /**
     * Get tab style (inline)
     *
     * @param array $tab Tab data cell
     *
     * @return string
     */
    protected function getTabStyle(array $tab)
    {
        return $this->isTabActive($tab) ? '' : 'display: none;';
    }

    /**
     * Return true if specified tab is active
     *
     * @param array $tab Tab data cell
     *
     * @return boolean
     */
    protected function isTabActive(array $tab)
    {
        return 0 === $tab['index'];
    }

    /**
     * Get tab style (CSS classes)
     *
     * @param array $tab Tab data cell
     *
     * @return string
     */
    protected function getTabClass(array $tab)
    {
        $style = !empty($tab['style']) ? $tab['style'] : '';
        $classes = explode(' ', $style);
        $classes[] = 'tab';
        if ($this->isTabActive($tab)) {
            $classes[] = 'tab-current';
        }

        return implode(' ', $classes);
    }

    /**
     * Get amount of products in the list
     *
     * @return integer
     */
    protected function getLowInventoryProductsAmount()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->getLowInventoryProductsAmount();
    }

    /**
     * Get count of products in the Top sellers list
     *
     * @return boolean
     */
    protected function hasTopSellers()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->hasTopSellers();
    }
}
