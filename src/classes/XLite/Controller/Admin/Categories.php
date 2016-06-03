<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Categories controller
 */
class Categories extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * 'selectorData' target used to get categories for selector on edit product page
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_unique(array_merge(parent::defineFreeFormIdActions(), array('selectorData')));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return !$this->isVisible()
            ? static::t('No category defined')
            : (($categoryName = $this->getCategoryName())
                ? static::t('Manage category (X)', array('category_name' => $categoryName))
                : static::t('Manage categories')
            );
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (
            !$this->getCategoryId() || $this->getCategory()
        );
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory() ? $this->getCategory()->getName() : '';
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->find($this->getCategoryId());
        }

        return $this->category;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * @todo: Refactor: use ajax event as result
     *
     * @return string Json object with results (select2 format)
     */
    public function doActionSelectorData()
    {
        /**
         * @todo:
         *      For empty term return recent categories
         *      For non empty term return category with path
         */

        $request = \XLite\Core\Request::getInstance();
        $term = $request->term;
        $page = $request->page ?: 0;
        /** @var \XLite\Model\Repo\Category $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Category');

        $pageLimit = 10;

        /** @var \XLite\Model\Category[] $data */
        $data = [];
        $more = false;
        if ($term) {
            $cnd = new \XLite\Core\CommonCell(
                [
                    'term' => $term,
                    'orderBy' => [['c.depth', 'asc'], ['translations.name', 'asc']]
                ]
            );
            $count = $repo->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_COUNT);

            $cnd->limit = [$page * $pageLimit, $pageLimit];
            $data = $repo->search($cnd);

            $more = $count >= ($page + 1) * $pageLimit;
        } else {
            $cnd = new \XLite\Core\CommonCell(
                [
                    'lastUsage' => true,
                    'orderBy' => [['c.lastUsage', 'desc'], ['translations.name', 'asc']]
                ]
            );
            $count = $repo->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_COUNT);

            $cnd->limit = [$page * $pageLimit, $pageLimit];
            $data = $repo->search($cnd);

            $more = $count >= ($page + 1) * $pageLimit;
        }

        $jsonData = [];
        foreach ($data as $category) {
            $name = [];
            foreach ($repo->getCategoryPath($category->getId()) as $cateogryInPath) {
                $name[] = $cateogryInPath->getName();
            }
            $jsonData[] = ['id' => $category->getId(), 'text' => implode('/', $name), 'term' => $term];
        }

        echo json_encode(['results' => $jsonData, 'pagination' => ['more' => $more]]);
        exit;
    }
}
