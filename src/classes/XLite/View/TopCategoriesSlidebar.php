<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Sidebar categories list
 *
 * @ListChild (list="layout.header.categories", zone="customer", weight="10")
 */
class TopCategoriesSlidebar extends \XLite\View\SideBarBox
{
    /**
     * Widget parameter names
     */
    const PARAM_DISPLAY_MODE = 'displayMode';
    const PARAM_ROOT_ID      = 'rootId';
    const PARAM_IS_SUBTREE   = 'is_subtree';

    /**
     * Allowed display modes
     */
    const DISPLAY_MODE_LIST = 'list';
    const DISPLAY_MODE_TREE = 'tree';
    const DISPLAY_MODE_PATH = 'path';


    /**
     * Display modes (template directories)
     *
     * @var array
     */
    protected $displayModes = array(
        self::DISPLAY_MODE_LIST => 'List',
        self::DISPLAY_MODE_TREE => 'Tree',
        self::DISPLAY_MODE_PATH => 'Path',
    );

    /**
     * Current category path id list
     *
     * @var array
     */
    protected $pathIds;

    protected $categories = null;

    /**
     * categoriesPath runtime cache
     * @var array
     */
    protected static $categoriesPath;

    /**
     * Preprocess DTO
     *
     * @param  array    $categoryDTO
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {
        $categoryDTO['link']                = $this->buildURL('category', '', array('category_id' => $categoryDTO['id']));
        $categoryDTO['hasSubcategories']    = 0 < $categoryDTO['subcategoriesCount'];
        $categoryDTO['children']            = array();

        if (!$categoryDTO['name']) {
            $categoryDTO['name'] = $this->getFirstTranslatedName($categoryDTO['id']);
        }

        return $categoryDTO;
    }

    /**
     * Get name fallback
     *
     * @param integer $categoryId Category id
     *
     * @return string
     */
    protected function getFirstTranslatedName($categoryId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->getFirstTranslatedName($categoryId);
    }

    /**
     * Get cache parameters for proprocessed DTOs
     *
     * @return array
     */
    protected function getProcessedDTOsCacheParameters()
    {
        $cacheParameters = array(
            'categoriesDTOs',
            \XLite\Core\Session::getInstance()->getLanguage()
                ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
                : '',
            \XLite\Core\Database::getRepo('XLite\Model\Category')->getVersion(),
            LC_USE_CLEAN_URLS
        );

        $auth = \XLite\Core\Auth::getInstance();
        if ($auth->isLogged()
            && $auth->getProfile()->getMembership()
        ) {
            $cacheParameters[] = $auth->getProfile()->getMembership()->getMembershipId();
        }

        return $cacheParameters;
    }

    /**
     * Collect categories collection
     *
     * @return array
     */
    protected function collectCategories()
    {
        $cacheKey = md5(serialize($this->getProcessedDTOsCacheParameters()));
        $driver = \XLite\Core\Database::getCacheDriver();

        if ($driver->contains($cacheKey)) {
            return $driver->fetch($cacheKey);
        }

        $preprocessedDTOs = array();

        $dtos = \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategoriesAsDTO();
        foreach ($dtos as $key => $categoryDTO) {
            $preprocessedDTOs[$categoryDTO['id']] = $this->preprocessDTO($categoryDTO);
        }

        $postprocessedDTOs = $this->postprocessDTOs($preprocessedDTOs);
        $driver->save($cacheKey, $postprocessedDTOs);

        return $postprocessedDTOs;
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function postprocessDTOs($categories)
    {
        return $categories;
    }

    /**
     * Get widge title
     *
     * @return string
     */
    protected function getHead()
    {
        return false;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'categories/' . $this->getParam(self::PARAM_DISPLAY_MODE);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'categories/list/body.twig';
    }

    /**
     * Return subcategories list
     *
     * @param integer $categoryId Category id OPTIONAL
     *
     * @return array
     */
    protected function getCategories($categoryId = null)
    {
        if (null === $this->categories) {
            $this->categories = $this->collectCategories();
        }

        if (!$categoryId) {
            $categoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
        }

        return array_filter($this->categories, function ($item) use ($categoryId) {
            return isset($item['parent_id']) && (int) $item['parent_id'] === (int) $categoryId;
        });
    }


    /**
     * Check if category included into active trail or not
     *
     * @param integer $categoryId Category id
     *
     * @return boolean
     */
    protected function isActiveTrail($categoryId)
    {
        if ($this->pathIds === null) {
            $this->pathIds = array();

            if (static::$categoriesPath === null) {
                static::$categoriesPath = \XLite\Core\Database::getRepo('\XLite\Model\Category')
                    ->getCategoryPath($this->getCategoryId());
            }

            if (is_array(static::$categoriesPath)) {

                foreach (static::$categoriesPath as $cat) {

                    $this->pathIds[] = $cat->getCategoryId();

                }

            }

        }

        return in_array($categoryId, $this->pathIds);
    }

    /**
     * Display item CSS class name as HTML attribute
     *
     * @param integer               $index    Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    public function displayLinkClass($index, $count, $category)
    {
        $className = $this->assembleItemClassName($index, $count, $category);

        return $className ? ' class="' . $className . '"' : '';
    }

    /**
     * Display item CSS class name as HTML attribute
     *
     * @param integer               $index    Item number
     * @param integer               $count    Items count
     * @param \XLite\Model\Category $category Current category
     *
     * @return string
     */
    public function displayItemClass($index, $count, $category)
    {
        $className = $this->assembleLinkClassName($index, $count, $category);

        return $className ? ' class="' . $className . '"' : '';
    }

    /**
     * Assemble item CSS class name
     *
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $classes = array();

        $active = $this->isActiveTrail($category['id']);

        if (!$category['hasSubcategories']) {
            $classes[] = 'leaf';

        } elseif (self::DISPLAY_MODE_LIST != $this->getParam(self::PARAM_DISPLAY_MODE)) {
            $classes[] = $active ? 'expanded' : 'collapsed';
        }

        if (0 == $index) {
            $classes[] = 'first';
        }

        $listParam = array(
            'rootId'     => $this->getParam('rootId'),
            'is_subtree' => $this->getParam('is_subtree'),
        );
        if (
            ($count - 1) == $index
            && $this->isViewListVisible('topCategories.children', $listParam)
        ) {
            $classes[] = 'last';
        }

        if ($active) {
            $classes[] = 'active-trail';
        }

        return implode(' ', $classes);
    }

    /**
     * Assemble item CSS class name
     *
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleLinkClassName($index, $count, $category)
    {
        $classes = array();

        $classes[] = \XLite\Core\Request::getInstance()->category_id == $category['id']
            ? 'active'
            : '';


        return implode(' ', $classes);
    }

    /**
     * Assemble item children container class name
     *
     * @param integer           $i      Item number
     * @param integer           $count  Items count
     * @param \XLite\View\AView $widget Current category FIXME! this variable is not used
     *
     * @return string
     */
    protected function assembleListItemClassName($i, $count, \XLite\View\AView $widget)
    {
        $classes = array('leaf');

        if (($count - 1) == $i) {
            $classes[] = 'last';
        }

        return implode(' ', $classes);
    }

    /**
     * ID of the default root category
     *
     * @return integer
     */
    protected function getDefaultCategoryId()
    {
        return $this->getRootCategoryId();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $rootId = $this->getDefaultCategoryId();

        $this->widgetParams += array(
            self::PARAM_DISPLAY_MODE => new \XLite\Model\WidgetParam\TypeSet(
                    'Display mode', static::DISPLAY_MODE_LIST, true, $this->displayModes
                ),
            self::PARAM_ROOT_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                    'Parent category ID (leave "' . $rootId . '" for root categories list)', $rootId, true, true
                ),
            self::PARAM_IS_SUBTREE => new \XLite\Model\WidgetParam\TypeBool(
                    'Is subtree', false, false
                ),
        );
    }

    /**
     * Checks whether it is a subtree
     *
     * @return boolean
     */
    protected function isSubtree()
    {
        return $this->getParam(self::PARAM_IS_SUBTREE) !== false;
    }

    // {{{ Cache

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache oarameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getCategoryId();

        $auth = \XLite\Core\Auth::getInstance();
        $list[] = ($auth->isLogged() && $auth->getProfile()->getMembership())
            ? $auth->getProfile()->getMembership()->getMembershipId()
            : '-';

        return $list;
    }

    // }}}


    /**
     * Check if categories menu located at the top
     *
     * @return boolean
     */
    protected function isDropdownMenu()
    {
       return $this->getRootCategoryId() == '0';
    }


    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' dropdown-menu categories-main-menu';
    }
}
