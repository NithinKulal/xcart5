<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Categories items list
 */
class Category extends \XLite\View\ItemsList\Model\Table
{

    /**
     * Create counter
     *
     * @var integer
     */
    protected $createCount = 0;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'items_list/model/table/category/style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'items_list/model/table/category/controller.js';

        return $list;
    }

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
        return 'categories';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'id' => \XLite\Core\Request::getInstance()->id,
            )
        );
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getCategory()->getCategoryId();
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list['id'] = $this->getCategory()->getCategoryId();

        return $list;
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = 'id';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        parent::getCommonParams();

        $this->commonParams['id'] = $this->getCategory()->getCategoryId();

        return $this->commonParams;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        return \XLite\Core\Request::getInstance()->id
            ? \XLite\Core\Database::getRepo('XLite\Model\Category')->find(intval(\XLite\Core\Request::getInstance()->id))
            : \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();
    }

    /**
     * Check - current category is root cCategory or not
     *
     * @return boolean
     */
    protected function isRootCategory()
    {
        return !\XLite\Core\Request::getInstance()->id
            || \XLite\Core\Request::getInstance()->id == \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
    }

    /**
     * Get formatted path of current category
     *
     * @return string
     */
    protected function getFormattedPath()
    {
        $list = array();

        foreach ($this->getCategory()->getPath() as $category) {
            $list[] = '<a href="' . static::buildURL('categories', '', array('id' => $category->getCategoryId())). '">'
                . func_htmlspecialchars($category->getName())
                . '</a>';
        }

        return implode(' :: ', $list);
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'image' => array(
                static::COLUMN_NAME         => '',
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\EmptyField',
                static::COLUMN_PARAMS       => array('required' => false),
                static::COLUMN_ORDERBY      => 100,
            ),
            'name' => array(
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Category'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => array('required' => true),
                static::COLUMN_ORDERBY   => 200,
                static::COLUMN_NO_WRAP   => true,
                static::COLUMN_MAIN      => true,
                static::COLUMN_LINK      => 'category',
            ),
            'subcategories' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Subcat'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 300,
            ),
            'info' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Products'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 400,
                static::COLUMN_HEAD_HELP => 'If there are subcategories, the value in brackets stands for the sum of all products in this category and its subcategories.',
            ),
        );
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $parent = null;
        if (\XLite\Core\Request::getInstance()->id) {
            $parent = \XLite\Core\Database::getRepo('XLite\Model\Category')->find(intval(\XLite\Core\Request::getInstance()->id));
        }

        if (!$parent) {
            $parent = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();
        }

        $entity->setParent($parent);

        return $entity;
    }

    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        // Resort
        $pos = 10;
        $entity->setPos($pos);
        foreach ($entity->getParent()->getChildren() as $child) {
            $pos += 10;
            $child->setPos($pos);
        }
        $this->createCount++;
        parent::insertNewEntity($entity);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Category';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl(
            'category',
            null,
            array(
                'parent' => $this->getCategory()->getCategoryId(),
            )
        );
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New category';
    }

    // {{{ Behaviors

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' categories';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\Category';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            array('id' => $entity->getUniqueIdentifier())
        );
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XLite\Model\Repo\Category::SEARCH_PARENT} = \XLite\Core\Request::getInstance()->id
            ? intval(\XLite\Core\Request::getInstance()->id)
            : \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();

        return $result;
    }

    /**
     * Return params list to use for export
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getExportSearchCondition()
    {
        $params = parent::getExportSearchCondition();
        if (isset($params->{\XLite\Model\Repo\Category::SEARCH_PARENT})) {
            $params->{\XLite\Model\Repo\Category::SEARCH_SUBTREE} = $params->{\XLite\Model\Repo\Category::SEARCH_PARENT};
            unset($params->{\XLite\Model\Repo\Category::SEARCH_PARENT});
        }

        return $params;
    }

    /**
     * Returns condition to use in products count table
     * 
     * @return \XLite\Core\CommonCell
     */
    protected function getProductsCountCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;

        return $cnd;
    }

    // }}}

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'c.pos';
    }
}
