<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Remove data items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class RemoveData extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Types
     */
    const TYPE_PRODUCTS   = 'products';
    const TYPE_CATEGORIES = 'categories';
    const TYPE_ORDERS     = 'orders';
    const TYPE_CUSTOMERS  = 'customers';

    const LIMIT = 100;

    /**
     * Cached list
     *
     * @var   array
     */
    protected $cachedList;

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'remove_data';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('remove_data'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'page/remove_data/style.css';

        return $list;
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }


    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_MAIN     => true,
                static::COLUMN_NAME     => static::t('Name'),
                static::COLUMN_ORDERBY  => 100,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return null;
    }

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (null === $this->cachedList) {
            $this->cachedList = array();
            foreach ($this->getPlainData() as $id => $cell) {
                $this->cachedList[] = new \XLite\Model\RemoveDataCell(array('id' => $id) + $cell);
            }
        }

        return $countOnly ? count($this->cachedList) : $this->cachedList;
    }

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        return array(
            static::TYPE_PRODUCTS   => array(
                'name' => static::t('Products'),
            ),
            static::TYPE_CATEGORIES => array(
                'name' => static::t('Categories'),
            ),
            static::TYPE_ORDERS     => array(
                'name' => static::t('Orders'),
            ),
            static::TYPE_CUSTOMERS  => array(
                'name' => static::t('Customers'),
            ),
        );
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        $method = $this->buildMetodName($entity, 'isAllowRemove%s');

        return false !== $this->$method();
    }

    /**
     * Check - allow remove products or not
     *
     * @return boolean
     */
    protected function isAllowRemoveProducts()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
    }

    /**
     * Check - allow remove categories or not
     *
     * @return boolean
     */
    protected function isAllowRemoveCategories()
    {
        return 1 < \XLite\Core\Database::getRepo('XLite\Model\Category')->count();
    }

    /**
     * Check - allow remove orders or not
     *
     * @return boolean
     */
    protected function isAllowRemoveOrders()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Order')->count();
    }

    /**
     * Check - allow remove customers or not
     *
     * @return boolean
     */
    protected function isAllowRemoveCustomers()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = 'C';

        $countC = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd, true);

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = 'N';

        $countN = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd, true);

        return 0 < ($countC + $countN);
    }

    /**
     * Build metod name
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param string               $pattern Pattern
     *
     * @return string
     */
    protected function buildMetodName(\XLite\Model\AEntity $entity, $pattern)
    {
        switch ($entity->getId()) {
            case static::TYPE_PRODUCTS:
                $name = 'Products';
                break;

            case static::TYPE_CATEGORIES:
                $name = 'Categories';
                break;

            case static::TYPE_ORDERS:
                $name = 'Orders';
                break;

            case static::TYPE_CUSTOMERS:
                $name = 'Customers';
                break;

            default:
        }

        return $name ? sprintf($pattern, $name) : null;
    }

    // {{{ Process

    /**
     * Find for remove
     *
     * @param mixed $id Entity id
     *
     * @return \XLite\Model\AEntity
     */
    protected function findForRemove($id)
    {
        $result = null;
        $list = $this->getPageData();
        foreach ($list as $entity) {
            if ($entity->getId() == $id) {
                $result = $entity;
                break;
            }
        }

        return $result;
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $method = $this->buildMetodName($entity, 'remove%s');

        return false !== $this->$method();
    }

    /**
     * Remove products
     *
     * @return integer
     */
    protected function removeProducts()
    {
        return $this->removeCommon('XLite\Model\Product');
    }

    /**
     * Remove products
     *
     * @return integer
     */
    protected function removeCategories()
    {
        return $this->removeCommon('XLite\Model\Category');
    }

    /**
     * Remove orders
     *
     * @return integer
     */
    protected function removeOrders()
    {
        return $this->removeCommon('XLite\Model\Order');
    }

    /**
     * Remove customers
     *
     * @return integer
     */
    protected function removeCustomers()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Profile');

        $i = 1;
        $count = 0;
        foreach ($repo->iterateByCustomers() as $data) {
            $repo->delete($data[0], false);
            $count++;
            $i++;

            if ($count >= static::LIMIT) {
                \XLite\Core\Database::getEM()->flush();
                $count = 0;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return $i;
    }

    /**
     * Remove (common routine)
     *
     * @param string $repoName Repository name
     *
     * @return integer
     */
    protected function removeCommon($repoName)
    {
        $repo = \XLite\Core\Database::getRepo($repoName);

        $i = 1;
        $count = 0;
        foreach ($repo->iterateAll() as $data) {
            $repo->delete($data[0], false);
            $count++;
            $i++;

            if ($count >= static::LIMIT) {
                \XLite\Core\Database::getEM()->flush();
                $count = 0;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return $i;
    }

    // }}}

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' remove-data';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\RemoveData';
    }
}
