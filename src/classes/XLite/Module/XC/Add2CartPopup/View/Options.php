<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * Sources of products items list widget
 */
class Options extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'source' => array(
                static::COLUMN_NAME     => static::t('Product source'),
                static::COLUMN_TEMPLATE => 'modules/XC/Add2CartPopup/options/cell.source.twig',
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
        return 'XLite\Module\XC\Add2CartPopup\Model\Source';
    }

    /**
     * Return options list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $options = \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getInstance()->getSourcesOptions();

        return $countOnly ? count($options) : $options;
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
     * Mark list as non-removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as sortable
     *
     * @return boolean
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get name of products source
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getEntitySourceName($entity)
    {
        return static::t('a2cp-source-code-' . $entity->getterProperty('code'));
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' a2cp-sources';
    }

    // {{{ Sticky panel

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\Add2CartPopup\View\StickyPanel\Options';
    }

    // }}}
}
