<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Search product
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Search extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin
{
    /**
     * Widget param names
     */
    const PARAM_SUBSTRING         = 'substring';
    const PARAM_CATEGORY_ID       = 'categoryId';
    const PARAM_SEARCH_IN_SUBCATS = 'searchInSubcats';
    const PARAM_BY_TITLE          = 'by_title';
    const PARAM_BY_DESCR          = 'by_descr';
    const PARAM_BY_SKU            = 'by_sku';
    const PARAM_INVENTORY         = 'inventory';
    const PARAM_ENABLED           = 'enabled';

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
            static::SORT_BY_MODE_PRICE  => 'Price',
            static::SORT_BY_MODE_NAME   => 'Name',
            static::SORT_BY_MODE_SKU    => 'SKU',
            static::SORT_BY_MODE_AMOUNT => 'Amount',
        );

        parent::__construct($params);
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Product\Admin\Main';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        if ('low' === \XLite\Core\Request::getInstance()->{static::PARAM_INVENTORY}) {
            $params[static::PARAM_INVENTORY] = 'low';
        }

        return $params;
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('product_list'));
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
        return 'product_list';
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/product/style.css';
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/product/search_panel_style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'sku' => array(
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('SKU'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_BY_MODE_SKU,
                static::COLUMN_ORDERBY => 100,
            ),
            'name' => array(
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_BY_MODE_NAME,
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'product',
            ),
            'category' => array(
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Category'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 300,
            ),
            'price' => array(
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Price',
                static::COLUMN_PARAMS  => array('min' => 0),
                static::COLUMN_SORT    => static::SORT_BY_MODE_PRICE,
                static::COLUMN_ORDERBY => 400,
            ),
            'qty' => array(
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Stock'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text\Integer\ProductQuantity',
                static::COLUMN_SORT    => static::SORT_BY_MODE_AMOUNT,
                static::COLUMN_ORDERBY => 500,
            ),
        );
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL('product');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add product';
    }

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
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array_merge(parent::getListNameSuffixes(), array('search'));
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Product\Admin\Search';
    }

    /**
     * Should search params values be saved to session or not
     *
     * @return boolean
     */
    protected function saveSearchConditions()
    {
        return true;
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return array(
            'target'    => 'product_list'
        );
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        $return = new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );

        return $return;
    }
    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            array(
                static::PARAM_SUBSTRING    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler('substring'),
                    'widget'            => array(
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                        \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search keywords'),
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    ),
                ),
                static::PARAM_CATEGORY_ID    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler('categoryId'),
                    'widget'            => array(
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Select\Category',
                        \XLite\View\FormField\Select\Category::PARAM_DISPLAY_ANY_CATEGORY => true,
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    ),
                ),
                static::PARAM_INVENTORY    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler('inventory'),
                    'widget'            => array(
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Select\InventoryState',
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    ),
                ),
                'by_conditions'      => array(
                    'widget'            => array(
                        \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE    => \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_TEMPLATE => 'product/search/parts/condition.by_conditions.twig',
                    ),
                ),
                static::PARAM_BY_TITLE    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler(\XLite\Model\Repo\Product::P_BY_TITLE),
                ),
                static::PARAM_BY_DESCR    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler(\XLite\Model\Repo\Product::P_BY_DESCR),
                ),
                static::PARAM_BY_SKU    => array(
                    'condition'     => new \XLite\Model\SearchCondition\RepositoryHandler(\XLite\Model\Repo\Product::P_BY_SKU),
                ),
                static::PARAM_ENABLED           => array(
                    'condition'     => new \XLite\Model\SearchCondition\Expression\TypeEquality('enabled'),
                    'widget'    => array(
                        \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE    => \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Select\Product\AvailabilityStatus',
                        \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Availability'),
                    ),
                ),
            )
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
    }

    // {{{ Search

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\XLite\Model\Repo\Product::P_ORDER_BY} = $this->getOrderBy();

        // Prepare filter by 'enabled' field
        $enabledFieldName = \XLite\Model\Repo\Product::P_ENABLED;

        if ($result->{$enabledFieldName} && $result->{$enabledFieldName}->getValue()) {
            $booleanValue = 'enabled' === $result->{$enabledFieldName}->getValue()
                ? true
                : false;

            $result->{$enabledFieldName}->setValue($booleanValue);

        } else {
            unset($result->{$enabledFieldName});
        }

        // Correct filter param 'Search in subcategories'
        if (empty($result->{static::PARAM_CATEGORY_ID})) {
            unset($result->{static::PARAM_CATEGORY_ID});
            unset($result->{static::PARAM_SEARCH_IN_SUBCATS});

        } else {
            $result->{static::PARAM_SEARCH_IN_SUBCATS} = true;
        }

        return $result;
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_NAME;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if ('qty' == $column[static::COLUMN_CODE] && !$entity->getInventoryEnabled()) {
            $class .= ' infinity';
        }

        return $class;
    }

    /**
     * Check - has specified column attention or not
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return boolean
     */
    protected function hasColumnAttention(array $column, \XLite\Model\AEntity $entity = null)
    {
        return parent::hasColumnAttention($column, $entity)
            || ('qty' == $column[static::COLUMN_CODE] && $entity && $entity->isLowLimitReached());
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

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
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

    // }}}

    /**
     * Preprocess category
     *
     * @param integer              $date   Date
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessCategory($date, array $column, \XLite\Model\Product $entity)
    {
        return $date
            ? func_htmlspecialchars($date->getName())
            : '';
    }
}
