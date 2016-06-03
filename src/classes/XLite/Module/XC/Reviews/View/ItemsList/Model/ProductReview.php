<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\ItemsList\Model;

/**
 * Reviews list for tab in product details page
 *
 */
class ProductReview extends \XLite\Module\XC\Reviews\View\ItemsList\Model\Review
{
    /**
     * Widget param names
     */
    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('product'));
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
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return null;
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
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $allowedColumns = array(
            'reviewerName',
            'rating',
            'status',
            'additionDate',
        );

        $columns = parent::defineColumns();

        // Remove redundant columns
        foreach ($columns as $k => $v) {
            if (!in_array($k, $allowedColumns)) {
                unset($columns[$k]);
            }
        }

        $columns['useForMeta'] = array(
            static::COLUMN_NAME      => static::t('SEO'),
            static::COLUMN_HEAD_HELP => static::t('Select the review that should be included into the rich snippet shown for the page of this product when the page appears in search results by Google and other major search engines'),
            static::COLUMN_CLASS     => '\XLite\View\FormField\Inline\Input\Radio\Radio',
            static::COLUMN_EDIT_ONLY => true,
            static::COLUMN_PARAMS => array(
                'fieldName' => 'useForMeta',
            ),
            static::COLUMN_ORDERBY   => 600,
        );

        return $columns;
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
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $productId = $this->getProductId();

        $result->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT}
            = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId);

        return $result;
    }

    /**
     * Get AJAX-specific URL parameters
     *
     * @return array
     */
    protected function getAJAXSpecificParams()
    {
        $params = parent::getAJAXSpecificParams();
        $params[static::PARAM_PRODUCT_ID] = $this->getProductId();

        return $params;
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);

        if ('useForMeta' ==  $column[static::COLUMN_CODE]) {
            $result = $result && $entity->isApproved();
        }

        return $result;
    }
}
