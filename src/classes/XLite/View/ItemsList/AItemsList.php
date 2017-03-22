<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Model\SearchCondition\IExpressionProvider;

/**
 * Base class for all lists
 */
abstract class AItemsList extends \XLite\View\Container
{
    use ExecuteCachedTrait;

    /**
     * Widget param names
     */
    const PARAM_SORT_BY    = 'sortBy';
    const PARAM_SORT_ORDER = 'sortOrder';

    /**
     * SQL orderby directions
     */
    const SORT_ORDER_ASC  = 'asc';
    const SORT_ORDER_DESC = 'desc';

    /**
     * Default layout template
     *
     * @var string
     */
    protected $defaultTemplate = 'common/dialog.twig';

    /**
     * commonParams
     *
     * @var array
     */
    protected $commonParams;

    /**
     * sortByModes
     *
     * @var array
     */
    protected $sortByModes = array();

    protected function getSortByModesField()
    {
        return $this->sortByModes;
    }

    protected function setSortByModesField($value)
    {
        $this->sortByModes = $value;
    }

    /**
     * sortOrderModes
     *
     * @var array
     */
    protected $sortOrderModes = array(
        self::SORT_ORDER_ASC  => 'Ascending',
        self::SORT_ORDER_DESC => 'Descending',
    );

    /**
     * Sorting widget IDs list
     *
     * @var array
     */
    protected static $sortWidgetIds = array();

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    abstract protected function getPageBodyDir();

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    abstract protected function getPagerClass();

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '';
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo|null
     */
    protected function getRepository()
    {
        $repositoryName = $this->defineRepositoryName();

        return $repositoryName
            ? \XLite\Core\Database::getRepo($repositoryName)
            : null;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $repository = $this->getRepository();

        return $repository ? $repository->search($cnd, $countOnly) : 0;
    }

    /**
     * Get processed session cell name for the certain list items widget
     *
     * @return string
     */
    public static function getConditionCellName()
    {
        return static::getSessionCellName() . '_processed';
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $isSidebar = $this instanceof Product\Customer\ACustomer && $this->isSidebar();

        if (!$isSidebar) {
            // Do not change call order
            $this->widgetParams += $this->getPager()->getWidgetParams();
            $this->requestParams = array_merge($this->requestParams, $this->getPager()->getRequestParams());
        }
    }

    /**
     * getActionURL
     *
     * @param array $params Params to modify OPTIONAL
     *
     * @return string
     */
    public function getActionURL(array $params = array())
    {
        return $this->getURL($params + $this->getURLParams());
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        // Static call of the non-static function
        $list[] = self::getDir() . '/items_list.js';

        $list[] = 'button/js/remove.js';

        $list[] = 'form_field/js/text.js';
        $list[] = 'form_field/input/text/float.js';
        $list[] = 'form_field/input/text/integer.js';
        $list[] = 'form_field/input/checkbox/switcher.js';

        $list[] = 'form_field/inline/controller.js';
        $list[] = 'form_field/inline/input/text.js';
        $list[] = 'form_field/inline/input/text/integer.js';
        $list[] = 'form_field/inline/input/text/price.js';

        return $list;
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        // Static call of the non-static function
        $list[] = self::getDir() . '/items_list.css';
        $list = self::preparePagerCSSFiles($list);

        $list[] = 'form_field/inline/style.css';
        $list[] = 'form_field/input/price.css';
        $list[] = 'form_field/input/symbol.css';
        $list[] = 'form_field/input/checkbox/switcher.css';

        return $list;
    }

    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return 'items-list';
    }

    /**
     * Return inner head for list widgets
     *
     * @return string
     */
    protected function getListHead()
    {
        return parent::getHead();
    }

    /**
     * Return CSS classes for list header
     *
     * @return string
     */
    protected function getListHeadClass()
    {
        return '';
    }

    /**
     * Return number of items in products list
     *
     * @return integer
     */
    protected function getItemsCount()
    {
        return $this->executeCachedRuntime(function () {
            $cacheParams   = $this->getCacheParameters();
            $cacheParams[] = 'getItemsCount';

            return $this->executeCached(function () {
                return $this->getData($this->getSearchCondition(), true);
            }, $cacheParams);
        });
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return 'itemsList';
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return 'items_list';
    }

    /**
     * prepare CSS file list for use with pager
     *
     * @param array $list CSS file list
     *
     * @return array
     */
    protected function preparePagerCSSFiles($list)
    {
        return array_merge($list, $this->getPager()->getCSSFiles());
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return self::getDir() . LC_DS . $this->getBodyTemplate();
    }

    /**
     * Return default template
     * See setWidgetParams()
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }

    /**
     * getPageBodyTemplate
     *
     * @return string
     */
    protected function getPageBodyTemplate()
    {
        return $this->getDir() . LC_DS . $this->getPageBodyDir() . LC_DS . $this->getPageBodyFile();
    }

    /**
     * getPageBodyFile
     *
     * @return string
     */
    protected function getPageBodyFile()
    {
        return 'body.twig';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return null;
    }

    /**
     * Check if list blank(no products in store for products list)
     *
     * @return boolean
     */
    protected function isListBlank()
    {
        return $this->getRepository() ? !$this->getRepository()->count() : false;
    }

    /**
     * Check if blank items list descriptions should be displayed
     *
     * @return bool
     */
    protected function isDisplayBlankItemsListDescription()
    {
        return $this->getBlankItemsListDescription() && $this->isListBlank();
    }

    /**
     * Return blank list template
     *
     * @return string
     */
    protected function getBlankListTemplate()
    {
        return $this->getBlankListDir() . LC_DS . $this->getBlankListFile();
    }

    /**
     * Return "blank list" template
     *
     * @return string
     */
    protected function getBlankListDir()
    {
        return $this->getDir();
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getBlankListFile()
    {
        return 'blank.twig';
    }

    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getEmptyListDir() . LC_DS . $this->getEmptyListFile();
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return $this->getDir();
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getEmptyListFile()
    {
        return 'empty.twig';
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false === $this->hasResults();
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('No items found.');
    }

    /**
     * Get pager parameters list
     *
     * @return array
     */
    protected function getPagerParams()
    {
        return array(
            \XLite\View\Pager\APager::PARAM_ITEMS_COUNT => $this->getItemsCount(),
            \XLite\View\Pager\APager::PARAM_LIST        => $this,
        );
    }

    /**
     * Get pager
     *
     * @return \XLite\View\Pager\APager
     */
    protected function getPager()
    {
        return $this->executeCachedRuntime(function () {
            return $this->getWidget($this->getPagerParams(), $this->getPagerClass());
        });
    }

    // {{{ SEARCH REGION

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return null;
    }

    /**
     * Check - search panel is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return $this->getSearchPanelClass()
            && $this->getRepository()
            && 0 < $this->getRepository()->count();
    }

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
     * Get session cell name for SessionSearchValuesStorage
     *
     * @return string
     */
    public static function getSearchSessionCellName()
    {
        return static::getSessionCellName() . '_search';
    }

    /**
     * Get search values storage
     *
     * @param boolean $forceFallback Force fallback to session storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    public static function getSearchValuesStorage($forceFallback = false)
    {
        $requestData = \XLite\Core\Request::getInstance()->getData();
        $requestStorage = new \XLite\View\ItemsList\RequestSearchValuesStorage(
            $requestData
        );

        $searchParamsNamesIntersect = array_intersect(
            array_keys(static::getSearchParams()),
            array_keys($requestData)
        );

        // If there is any of searchParams in request data we should not fallback to session storage
        // because in that case we need brand new search
        // Unless we are in searchMode, in which we should fallback to save values to session
        if (count($searchParamsNamesIntersect) === 0
            || (isset($requestData['mode']) && 'append' === $requestData['mode'])
            || $forceFallback
        ) {
            $sessionStorage = new \XLite\View\ItemsList\SessionSearchValuesStorage(
                static::getSearchSessionCellName()
            );
            $requestStorage->setFallbackStorage($sessionStorage);
        }

        return $requestStorage;
    }

    /**
     * Return widget param value
     * N.B. Backwards compatibility hack for getParam call
     * Now all search conditions sits in another session cell, so getParam() won't work
     *
     * Thats because doActionSearch sets values to new searchValuesStorage, but some of the old code
     * reads those values via getParam(), which is reads from old session cell
     *
     * @param string $param Param to fetch
     *
     * @return mixed
     */
    protected function getParam($param)
    {
        $result = parent::getParam($param);

        if (array_key_exists($param, static::getSearchParams())) {
            $result = static::getSearchValuesStorage()->getValue($param);
        }

        return $result;
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\OldSearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        if ($this->getOrderBy()) {
            $searchCase->{\XLite\Model\Repo\Order::P_ORDER_BY} = $this->getOrderBy();
        }

        return $searchCase;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $processor = static::getSearchCaseProcessor();

        return $this->postprocessSearchCase(
            $processor->getSearchCase()
        );
    }

    // }}}

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return false;
    }

    /**
     * Return params list to use for export
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getExportSearchCondition()
    {
        return $this->getSearchCondition();
    }

    /**
     * Get page data
     *
     * @return array
     */
    protected function getPageData()
    {
        return $this->executeCachedRuntime(function () {
            if ($this->isExportable()) {
                \XLite\Core\Session::getInstance()->{static::getConditionCellName()}
                    = $this->getExportSearchCondition();
            }

            return $this->getData($this->getLimitCondition());
        });
    }

    /**
     * Get limit condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        return $this->getPager()->getLimitCondition(null, null, $this->getSearchCondition());
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_ASC;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return null;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array|null
     */
    protected function getOrderBy()
    {
        $sortBy = $this->getSortBy();

        return $sortBy ? [$sortBy, $this->getSortOrder()] : [];
    }

    /**
     * getSortBy
     *
     * @return string
     */
    protected function getSortBy()
    {
        $paramSortBy = $this->getParam(static::PARAM_SORT_BY);

        if (empty($paramSortBy)
            || !in_array($paramSortBy, array_keys($this->sortByModes), true)
        ) {
            $paramSortBy = $this->getSortByModeDefault();
        }

        return $paramSortBy;
    }

    /**
     * getSortOrder
     *
     * @return string
     */
    protected function getSortOrder()
    {
        $paramSortOrder = $this->getParam(static::PARAM_SORT_ORDER);

        if (empty($paramSortOrder)
            || !in_array(
                $paramSortOrder,
                [static::SORT_ORDER_DESC, static::SORT_ORDER_ASC],
                true
            )
        ) {
            $paramSortOrder = $this->getSortOrderModeDefault();
        }

        return $paramSortOrder;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        if (!empty($this->sortByModes)) {
            $this->widgetParams += array(
                static::PARAM_SORT_BY => new \XLite\Model\WidgetParam\TypeSet(
                    'Sort by',
                    $this->getSortByModeDefault(),
                    false,
                    $this->sortByModes
                ),
                static::PARAM_SORT_ORDER => new \XLite\Model\WidgetParam\TypeSet(
                    'Sort order',
                    $this->getSortOrderModeDefault(),
                    false,
                    $this->sortOrderModes
                ),
            );
        }
    }

    /**
     * getJSHandlerClassName
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'ItemsList';
    }

    /**
     * Get URL common parameters
     * @todo: decompose to defineCommonParams, processCommonParams and getCommonParams; use ExecuteCachedTrait
     *      return $this->executeCachedRuntime(function () {
     *              return $this->processCommonParams($this->defineCommonParams());
     *          });
     *      to avoid initialisation check in children implementations
     *
     * @return array
     */
    protected function getCommonParams()
    {
        if (!isset($this->commonParams)) {
            $this->commonParams = array(
                static::PARAM_SESSION_CELL => $this->getSessionCell()
            );

            $this->commonParams = array_merge(
                $this->commonParams,
                $this->getSearchRequestParams()
            );
        }

        return $this->commonParams;
    }

    /**
     * @return array
     */
    public function getSearchRequestParams()
    {
        $result = [];

        foreach ($this->getSearchCondition() as $name => $condition) {
            $preprocessed = null;

            if (!is_object($condition)) {
                $preprocessed = $condition;
            } elseif ($condition instanceof IExpressionProvider) {
                $preprocessed = $condition->getValue();
            }

            if (!$preprocessed || !is_string($preprocessed)) {
                continue;
            }

            $result[$name] = $preprocessed;
        }

        return $result;
    }

    /**
     * Get AJAX-specific URL parameters
     *
     * @return array
     */
    protected function getAJAXSpecificParams()
    {
        return array(
            static::PARAM_AJAX_WIDGET => get_class($this),
            static::PARAM_AJAX_TARGET => \XLite\Core\Request::getInstance()->target,
        );
    }

    /**
     * getURLParams
     *
     * @return array
     */
    protected function getURLParams()
    {
        return ['target' => \XLite\Core\Request::getInstance()->target] + $this->getCommonParams();
    }

    /**
     * getURLAJAXParams
     *
     * @return array
     */
    protected function getURLAJAXParams()
    {
        return $this->getCommonParams() + $this->getAJAXSpecificParams();
    }

    /**
     * Return specific items list parameters that will be sent to JS code
     *
     * @return array
     */
    protected function getItemsListParams()
    {
        return array(
            'urlparams'     => $this->getURLParams(),
            'urlajaxparams' => $this->getURLAJAXParams(),
            'cell'          => $this->getSessionCell(),
        );
    }

    /**
     * Get sorting widget unique ID
     *
     * @param boolean $getLast Get last ID or next OPTIONAL
     *
     * @return string
     */
    protected function getSortWidgetId($getLast = false)
    {
        $class = get_called_class();

        if (!isset(static::$sortWidgetIds[$class])) {
            static::$sortWidgetIds[$class] = 0;
        }

        if (!$getLast) {
            static::$sortWidgetIds[$class]++;
        }

        return str_replace('\\', '-', $class) . '-sortby-' . static::$sortWidgetIds[$class];
    }

    /**
     * isSortByModeSelected
     *
     * @param string $sortByMode Value to check
     *
     * @return boolean
     */
    protected function isSortByModeSelected($sortByMode)
    {
        return $this->getSortBy() === $sortByMode;
    }

    /**
     * isSortOrderAsc
     *
     * @param string $sortOrder Sorting order
     *
     * @return boolean
     */
    protected function isSortOrderAsc($sortOrder = null)
    {
        return static::SORT_ORDER_ASC === ($sortOrder ?: $this->getSortOrder());
    }

    /**
     * getSortOrderToChange
     *
     * @param string $sortOrder Sorting order
     *
     * @return string
     */
    protected function getSortOrderToChange($sortOrder = null)
    {
        return $this->isSortOrderAsc($sortOrder) ? static::SORT_ORDER_DESC : static::SORT_ORDER_ASC;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && ($this->isDisplayWithEmptyList()
                || (($this->isCacheAvailable() && $this->hasCachedContent())
                    || $this->hasResults()
                )
            );
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return false;
    }

    /**
     * Public wrapper for hasResults method
     *
     * @return boolean
     */
    public function hasResultsPublic()
    {
        return $this->hasResults();
    }

    /**
     * Check if there are any results to display in list
     *
     * @return boolean
     */
    protected function hasResults()
    {
        return 0 < $this->getItemsCount();
    }

    /**
     * isHeaderVisible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return false;
    }

    /**
     * Check if head title is visible
     *
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return false;
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->getPager()->isVisible();
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return false;
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_SORT_BY;
        $this->requestParams[] = static::PARAM_SORT_ORDER;
    }

    /**
     * Get 'More' link URL
     *
     * @return string
     */
    public function getMoreLink()
    {
        return null;
    }

    /**
     * Get 'More' link title
     *
     * @return string
     */
    public function getMoreLinkTitle()
    {
        return null;
    }

    // {{{ Widget JS arguments

    /**
     * Get widget tag attributes
     *
     * @return array
     */
    protected function getWidgetTagAttributes()
    {
        $data = array(
            'class' => $this->getListCSSClasses(),
        );

        $arguments = $this->defineWidgetSignificantArguments();
        if ($arguments) {
            $data['data-widget-arguments'] = json_encode($arguments);
        }

        return $data;
    }

    /**
     * Define widget significant arguments
     *
     * @return array
     */
    protected function defineWidgetSignificantArguments()
    {
        return array(
            static::PARAM_SORT_BY    => $this->getSortBy(),
            static::PARAM_SORT_ORDER => $this->getSortOrder(),
        );
    }

    // }}}
}
