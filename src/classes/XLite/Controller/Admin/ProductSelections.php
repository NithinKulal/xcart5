<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Product selections controller
 */
class ProductSelections extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('search'));
    }

    /**
     * Constructor
     *
     * @param array $params Constructor parameters
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $cellName = $this->getSessionCellName();
        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . '_' . $this->getCategoryId() . md5(microtime(true));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Select products from the list');
    }

    // {{{ Search

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID
     *
     * @return array
     */
    public function isExcludedProductId($productId)
    {
        return false;
    }

    /**
     * Specific title for the excluded product
     * By default it is 'Already added'
     *
     * @param integer $productId Product ID
     *
     * @return string
     */
    public function getTitleExcludedProduct($productId)
    {
        return static::t('Already added');
    }

    /**
     * Specific CSS class for the image of the excluded product.
     * You can check the Font Awesome CSS library if you want some specific icons
     *
     * @param integer $productId
     *
     * @return string
     */
    public function getStyleExcludedProduct($productId)
    {
        return 'fa-check-square';
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass()
            ?: '\XLite\View\ItemsList\Model\ProductSelection';
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $cellName = $this->getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $select = array();

        foreach ((array)\XLite\Core\Request::getInstance()->select as $key => $value) {
            $select[] = 'select[]=' . $key;
        }

        $this->setHardRedirect();

        $this->setReturnURL(
            \XLite\Core\Request::getInstance()->{\XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL}
            . '&' . implode('&', $select)
            . '&' . \XLite::FORM_ID . '=' . \XLite::getFormId(true)
        );
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        $className = $this->getItemsListClass();
        foreach ($className::getSearchParams() as $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        if ($this->getCategoryId()) {
            $searchParams['pageId'] = 0;
            $searchParams['categoryId'] = $this->getCategoryId() == $this->getRootCategoryId()
                ? 0
                : $this->getCategoryId();
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = $this->getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }

    // }}}

}
