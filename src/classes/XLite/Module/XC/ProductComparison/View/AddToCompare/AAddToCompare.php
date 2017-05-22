<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\AddToCompare;

/**
 * Add to compare widget
 *
 *
 */
abstract class AAddToCompare extends \XLite\View\Container
{
    /**
     * Checkbox id
     *
     * @var string
     */
    protected $checkboxId;

    /**
     * Product id
     *
     * @var string
     */
    protected $productId;

    /**
     * Get checkbox id
     *
     * @param integer $productId Product id
     *
     * @return string
     */
    public function getCheckboxId($productId)
    {
        if (
            !isset($this->checkboxId)
            || $productId != $this->productId
        ) {
            $this->checkboxId = 'product' . rand() . $productId;
        }
        
        $this->productId = $productId;

        return $this->checkboxId;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/script.js';
        $list[] = 'modules/XC/ProductComparison/compare/script.js';

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

        $list[] = $this->getDir() . '/style.css';
        $list[] = 'modules/XC/ProductComparison/compare/style.css';

        return $list;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getTitle();
    }

    /**
     * Is checked
     *
     * @param integer $productId Product id
     *
     * @return boolean
     */
    public function isChecked($productId)
    {
        $ids = \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductIds();

        return $ids
            && isset($ids[$productId]);
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Is empty
     *
     * @return boolean
     */
    protected function isEmptyList()
    {
        return 0 == \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductsCount();
    }

}
