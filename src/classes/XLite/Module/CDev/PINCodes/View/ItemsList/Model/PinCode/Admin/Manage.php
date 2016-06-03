<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\ItemsList\Model\PinCode\Admin;

/**
 * Manage newslists
 *
 * @ListChild (list="product.pinCodes", zone="admin", weight="500")
 */
class Manage extends \XLite\Module\CDev\PINCodes\View\ItemsList\Model\PinCode\APinCode
{
    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'code' => array(
                static::COLUMN_NAME    => static::t('PIN code'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 100,
            ),
            'statusData' => array(
                static::COLUMN_TEMPLATE => 'modules/CDev/PINCodes/pin_codes/status.twig',
                static::COLUMN_ORDERBY  => 200,
            ),
            'createDate' => array(
                static::COLUMN_NAME     => static::t('Created'),
                static::COLUMN_TEMPLATE => 'modules/CDev/PINCodes/pin_codes/date.twig',
                static::COLUMN_ORDERBY  => 300,
            ),
        );
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return '';
    }

    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'modules/CDev/PINCodes/product/no_pin_codes.twig';
    }

    // {{{ Search

    /**
     * Return items list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->product = \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->find(\XLite\Core\Request::getInstance()->product_id);

        return \XLite\Core\Database::getRepo('\XLite\Module\CDev\PINCodes\Model\PinCode')->search($cnd, $countOnly);
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams[static::PARAM_PRODUCT_ID] = \XLite\Core\Request::getInstance()->product_id;

        return $this->commonParams;
    }
    // }}}

    // {{{ Content helpers

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return '';
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);
        if ($entity && $entity->getIsSold()) {
            $classes[] = 'sold';
        }

        return $classes;
    }

    // }}}

    // {{{ Behavoirs

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

}

