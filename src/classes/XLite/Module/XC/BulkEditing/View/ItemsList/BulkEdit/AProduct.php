<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit;

abstract class AProduct extends \XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\ABulkEditing
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/BulkEditing/items_list/selected/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' bulk-edit-product';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $result = parent::defineColumns();
        $result['name'] = [
            static::COLUMN_NAME    => static::t('Name'),
            static::COLUMN_NO_WRAP => true,
            static::COLUMN_ORDERBY => 0,
        ];

        return $result;
    }

    /**
     * Get entity value
     *
     * @param \XLite\Model\AEntity $entity Entity object
     * @param string               $name   Property name
     *
     * @return mixed
     */
    protected function getEntityValue($entity, $name)
    {
        return $name === 'name'
            ? $entity->getName()
            : parent::getEntityValue($entity, $name);
    }
}
