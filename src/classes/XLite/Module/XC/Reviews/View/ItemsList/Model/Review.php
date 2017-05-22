<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\ItemsList\Model;

/**
 * Reviews items list (common reviews page)
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Review extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_REVIEWER         = 'r.reviewerName';
    const SORT_BY_MODE_RATING           = 'r.rating';
    const SORT_BY_MODE_STATUS           = 'r.status';
    const SORT_BY_MODE_ADDITION_DATE    = 'r.additionDate';

    /**
     * Widget param names
     */
    const PARAM_SEARCH_DATE_RANGE   = 'dateRange';
    const PARAM_SEARCH_KEYWORDS     = 'keywords';
    const PARAM_SEARCH_RATING       = 'rating';
    const PARAM_SEARCH_TYPE         = 'type';
    const PARAM_SEARCH_STATUS       = 'status';

    /**
     * The product selector cache
     *
     * @var mixed
     */
    protected $productSelectorWidget = null;

    /**
     * The profile selector cache
     *
     * @var mixed
     */
    protected $profileSelectorWidget = null;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('reviews'));
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\Module\XC\Reviews\View\SearchPanel\Review\Main';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.review.blank');
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
        return 'reviews';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = array();

        $productId = \XLite\Core\Request::getInstance()->product_id;
        if ($productId) {
            $params['product_id'] = $productId;
        }

        return array_merge(
            parent::getFormParams(),
            $params
        );
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_DATE_RANGE => static::PARAM_SEARCH_DATE_RANGE,
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_KEYWORDS   => static::PARAM_SEARCH_KEYWORDS,
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_RATING     => static::PARAM_SEARCH_RATING,
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE       => static::PARAM_SEARCH_TYPE,
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_STATUS     => static::PARAM_SEARCH_STATUS,
        );
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/reviews/style.css';
        $list[] = 'modules/XC/Reviews/review/style.css';
        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.css';
        $list[] = 'vote_bar/vote_bar.css';

        $list = array_merge($list, $this->getProductSelectorWidget()->getCSSFiles());
        $list = array_merge($list, $this->getProfileSelectorWidget()->getCSSFiles());

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

        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.js';

        $list = array_merge($list, $this->getProductSelectorWidget()->getJSFiles());
        $list = array_merge($list, $this->getProfileSelectorWidget()->getJSFiles());

        return $list;
    }

    /**
     * Getter of the product selector widget
     *
     * @return \XLite\View\FormField\Select\Model\ProductSelector
     */
    protected function getProductSelectorWidget()
    {
        if (is_null($this->productSelectorWidget)) {
            $this->productSelectorWidget = new \XLite\View\FormField\Select\Model\ProductSelector();
        }

        return $this->productSelectorWidget;
    }

    /**
     * Getter of the product selector widget
     *
     * @return \XLite\View\FormField\Select\Model\ProductSelector
     */
    protected function getProfileSelectorWidget()
    {
        if (is_null($this->profileSelectorWidget)) {
            $this->profileSelectorWidget = new \XLite\View\FormField\Select\Model\ProfileSelector();
        }

        return $this->profileSelectorWidget;
    }

    /**
     * Return profile id
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity
     *
     * @return int
     */
    public function getProfileId(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        return $entity->getProfile()
            ? $entity->getProfile()->getProfileId()
            : 0;
    }

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->sortByModes += array(
            static::SORT_BY_MODE_REVIEWER       => 'Reviewer',
            static::SORT_BY_MODE_RATING         => 'Rating',
            static::SORT_BY_MODE_STATUS         => 'Status',
            static::SORT_BY_MODE_ADDITION_DATE  => 'Addition date',
        );

        parent::__construct($params);
    }

    // {{{ Search

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Get right actions templates name
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();

        $list[] = 'modules/XC/Reviews/' . $this->getDir() . '/' . $this->getPageBodyDir() . '/review/action.link.twig';

        return $list;
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                $paramValue = trim($paramValue);
            }

            if (static::PARAM_SEARCH_DATE_RANGE === $requestParam && is_array($paramValue)) {
                foreach ($paramValue as $i => $date) {
                    if (is_string($date) && false !== strtotime($date)) {
                        $paramValue[$i] = strtotime($date);
                    }
                }

            } elseif (static::PARAM_SEARCH_DATE_RANGE === $requestParam && $paramValue) {
                $paramValue = \XLite\View\FormField\Input\Text\DateRange::convertToArray($paramValue);
            }

            if ('' !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XLite\Module\XC\Reviews\Model\Repo\Review::P_ORDER_BY} = $this->getOrderBy();

        // Comment this line to search reviews and ratings
        // $result->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE} =
        //    \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY;

        return $result;
    }

    // }}}

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SEARCH_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
            static::PARAM_SEARCH_KEYWORDS => new \XLite\Model\WidgetParam\TypeString('Product, SKU or customer info', ''),
            static::PARAM_SEARCH_RATING => new \XLite\Model\WidgetParam\TypeString('Rating', ''),
            static::PARAM_SEARCH_TYPE => new \XLite\Model\WidgetParam\TypeString('Review type', ''),
            static::PARAM_SEARCH_STATUS => new \XLite\Model\WidgetParam\TypeString('Status', ''),
        );

    }

    /**
     * Get column value for 'product' column
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getProductColumnValue(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        return $entity->getProduct()->getName();
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'product' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Product'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'product',
                static::COLUMN_ORDERBY  => 100,
            ),
            'reviewerName' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Reviewer'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/reviewer_info.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_REVIEWER,
                static::COLUMN_ORDERBY  => 200,
            ),
            'review' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Review'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/review.twig',
                static::COLUMN_ORDERBY  => 250,
            ),
            'rating' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Rating'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/rating.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_RATING,
                static::COLUMN_ORDERBY  => 300,
            ),
            'status' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Status'),
                static::COLUMN_TEMPLATE => 'modules/XC/Reviews/reviews/cell/status.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_STATUS,
                static::COLUMN_ORDERBY  => 400,
            ),
            'additionDate' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Date'),
                static::COLUMN_SORT     => static::SORT_BY_MODE_ADDITION_DATE,
                static::COLUMN_ORDERBY  => 500,
            ),
        );
    }

    /**
     * Preprocess addition date
     *
     * @param integer                               $date   Date
     * @param array                                 $column Column data
     * @param \XLite\Module\XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function preprocessAdditionDate($date, array $column, \XLite\Module\XC\Reviews\Model\Review $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Unknown');
    }

    /**
     * Return true if review is approved
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity Review
     *
     * @return boolean
     */
    protected function isApproved(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        return \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED == $entity->getStatus();
    }

    /**
     * Return full review content (to display in tooltip)
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getReviewFullContent(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        return str_replace("\n", '<br />', func_htmlspecialchars($entity->getReview()));
    }

    /**
     * Return shortened review content
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity Review
     *
     * @return string
     */
    protected function getReviewShortContent(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        $review = $entity->getReview();
        $review = trim($review);

        if (function_exists('mb_substr')) {

            $value = mb_substr($review, 0, 30, 'utf-8');

            $result = $value
                . (
                    mb_strlen($value, 'utf-8') != mb_strlen($review, 'utf-8')
                    ? '...'
                    : ''
                );

        } else {

            $value = substr($review, 0, 30);

            $result = $value
                . (
                    strlen($value) != strlen($review)
                    ? '...'
                    : ''
                );
        }

        return func_htmlspecialchars($result);
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
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' reviews';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\Reviews\View\StickyPanel\ItemsList\Review';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\Reviews\Model\Review';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('review');
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
        if ('product' == $column[static::COLUMN_CODE]) {
            $result = \XLite\Core\Converter::buildURL(
                'product',
                '',
                array('product_id' => $entity->getProduct()->getProductId())
            );
        } else {
            $result = parent::buildEntityURL($entity, $column);
        }

        return $result;
    }
    
    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_ADDITION_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC;
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
}
