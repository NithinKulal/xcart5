<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Products list controller
 */
class ProductList extends \XLite\Controller\Admin\ACL\Catalog
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
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Products');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: '\XLite\View\ItemsList\Model\Product\Admin\Search';
    }

    /**
     * Process 'no action'
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {

            // Refresh search parameters from the request
            $this->fillSearchValuesStorage();

            // Get ItemsList widget
            $widget = $this->getItemsList();

            // Search for single product entity
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceOf \XLite\Model\Product) {
                // Prepare redirect to product page
                $url = $this->buildURL('product', '', array('product_id' => $entity->getProductId()));
                $this->setReturnURL($url);
            }
        }
    }

    /**
     * Do action clone
     *
     * @return void
     */
    protected function doActionClone()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findByIds(array_keys($select));
            if (0 < count($products)) {
                foreach ($products as $product) {
                    $newProduct = $product->cloneEntity();
                    $newProduct->updateQuickData();
                }
                if (1 < count($products)) {
                    $this->setReturnURL($this->buildURL('cloned_products'));

                } else {
                    $this->setReturnURL($this->buildURL('product', '', array('product_id' => $newProduct->getId())));
                }
            }

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Do action enable
     *
     * @return void
     */
    protected function doActionEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    array('enabled' => true)
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Do action disable
     *
     * @return void
     */
    protected function doActionDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    array('enabled' => false)
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Do action delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully deleted'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Do action search
     *
     * @return void
     */
    protected function doActionSearch()
    {
        parent::doActionSearchItemsList();
    }

    /**
     * Do action search
     *
     * @return void
     */
    protected function doActionSearchItemsList()
    {
        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(array('mode' => 'search', 'searched' => 1)));
    }

    /**
     * Return search parameters for product list.
     * It is based on search params from Product Items list viewer
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return parent::getSearchParams()
            + $this->getSearchParamsCheckboxes();
    }

    /**
     * Return search parameters for product list given as checkboxes: (0, 1) values
     *
     * @return array
     */
    protected function getSearchParamsCheckboxes()
    {
        $productsSearchParams = array();

        $itemsListClass = $this->getItemsListClass();
        $cBoxFields = array(
            $itemsListClass::PARAM_SEARCH_IN_SUBCATS,
            $itemsListClass::PARAM_BY_TITLE,
            $itemsListClass::PARAM_BY_DESCR,
        );

        foreach ($cBoxFields as $requestParam) {
            $productsSearchParams[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam) ? 1 : 0;
        }

        return $productsSearchParams;
    }

}
