<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\AddToCompare;
use XLite\Module\XC\ProductComparison\Core\Data;

/**
 * Product comparison widget
 *
 * @Decorator\Depend("XC\ProductComparison")
 */
class ProductCompareIndicator extends \XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareIndicator implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductComparison/header_widget.js';

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
        $list[] = array(
            'file'  => 'modules/XC/ProductComparison/header_widget.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison';
    }
    
    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductComparison/header_indicator.twig';
    }

    /**
     * Return compared count
     * 
     * @return int
     */
    protected function getComparedCount()
    {
        return Data::getInstance()->getProductsCount();
    }

    /**
     * Check if recently updated
     *
     * @return boolean
     */
    protected function isRecentlyUpdated()
    {
        return Data::getInstance()->isRecentlyUpdated();
    }

    /**
     * Return compare url
     * 
     * @return string
     */
    protected function getCompareURL()
    {
        return $this->buildURL('compare');
    }

    /**
     * Check if disabled
     * 
     * @return bool
     */
    protected function isDisabled()
    {
        return $this->getComparedCount() < 2;
    }

    /**
     * Return title message
     * 
     * @return string
     */
    protected function getLinkHelpMessage()
    {
        return $this->isDisabled()
            ? static::t('Please add another product to comparison')
            : static::t('Go to comparison table');
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    protected function getPreloadedLabels()
    {
        $list = array(
            'Please add another product to comparison',
            'Go to comparison table',
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    /**
     * Return list of indicator element classes
     *
     * @return array
     */
    protected function getIndicatorClassesList()
    {
        $list = [];
        
        if ($this->isDisabled()) {
            $list[] = 'disabled';
        }

        if ($this->getComparedCount() > 0 && $this->isRecentlyUpdated()) {
            $list[] = 'recently-updated';
        }

        return $list;
    }

    /**
     * Return indicator element classes
     *
     * @return string
     */
    protected function getIndicatorClasses()
    {
        return implode(' ', $this->getIndicatorClassesList());
    }
}
