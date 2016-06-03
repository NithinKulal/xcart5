<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Images settings items list widget
 */
class ImagesSettings extends \XLite\View\ItemsList\Model\Table
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'images_settings/script.js';

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
            'name' => array(
                static::COLUMN_NAME => static::t('Name'),
            ),
            'width' => array(
                static::COLUMN_NAME      => static::t('Width (px)'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_PARAMS    => array(
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN              => 0,
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_CTRL => false
                ),
            ),
            'height' => array(
                static::COLUMN_NAME      => static::t('Height (px)'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_PARAMS    => array(
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN              => 0,
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_CTRL => false
                ),
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
        return 'XLite\Model\ImageSettings';
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
        $options = $this->prepareOptions(
            \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->findAll()
        );

        return $countOnly ? count($options) : $options;
    }

    /**
     * Correct options list
     *
     * @param array $options Options list (list of images sizes)
     *
     * @return array
     */
    protected function prepareOptions($options)
    {
        $editableSizes = \XLite\Logic\ImageResize\Generator::getEditableImageSizes();
        $allSizes = \XLite\Logic\ImageResize\Generator::getImageSizes();

        if ($editableSizes) {

            $needUpdate = false;
                
            // Prepare temporary array for checking
            $tmp = array();
            foreach ($editableSizes as $model => $opt) {
                foreach ($opt as $code) {
                    $tmp[sprintf('%s-%s', $model, $code)] = array(
                        'model' => $model,
                        'code'  => $code,
                        'size'  => $allSizes[$model][$code],
                    );
                }
            }

            $existingSizes = array();

            // Search for options which must be removed
            foreach ($options as $key => $option) {

                $testKey = sprintf('%s-%s', $option->getModel(), $option->getCode());

                if (!isset($tmp[$testKey])) {
                    // Found size which is not present in the list of editable sizes - prepare to remove this
                    $toDelete[$option->getId()] = $option;
                    $needUpdate = true;

                } else {
                    // Keep option in the list of sizes presented in the database and not scheduled to be removed
                    $existingSizes[$testKey] = $option->getId();
                }
            }

            // Search for image sizes which should be added to the database
            foreach ($tmp as $k => $v) {
                if (!isset($existingSizes[$k])) {
                    // Found an option which is not presented in the database - prepare to add this
                    $entity = new \XLite\Model\ImageSettings();
                    $entity->setModel($v['model']);
                    $entity->setCode($v['code']);
                    $entity->setWidth($v['size'][0]);
                    $entity->setHeight($v['size'][1]);

                    $toInsert[] = $entity;
                    $needUpdate = true;
                }
            }

            if (!empty($toInsert)) {
                // Create new image sizes
                \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->insertInBatch($toInsert);
            }

            if (!empty($toDelete)) {
                // Remove obsolete image sizes
                \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->deleteInBatchById($toDelete);
            }

            if ($needUpdate) {
                \XLite\Core\Database::getEM()->clear();
                $options = \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->findAll();
            }

        } else {
            $options = array();
        }

        return $options;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
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
        return static::SORT_TYPE_NONE;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' images-settings';
    }

    /**
     * Get pager class name
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Disable specific sticky panel for item list
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }
}
