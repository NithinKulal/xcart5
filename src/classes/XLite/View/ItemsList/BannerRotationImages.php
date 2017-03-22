<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

use XLite\View\FormField\FileUploader\AFileUploader;

/**
 * Coupons items list
 */
class BannerRotationImages extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'banner_rotation/images/style.css';

        return $list;
    }

    /**
     * Return true if param value may contain anything
     *
     * @param string $name Param name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        $result = parent::isParamTrusted($name);

        if (!$result && $name === 'link') {
            $result = true;
        }

        return $result;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'image' => [
                static::COLUMN_NAME    => static::t('Image'),
                static::COLUMN_LINK    => 'image',
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_PARAMS  => [
                    AFileUploader::PARAM_REQUIRED     => true,
                    AFileUploader::PARAM_IS_REMOVABLE => false,
                ],
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'link'  => [
                static::COLUMN_NAME    => static::t('Link'),
                static::COLUMN_LINK    => 'link',
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY => 200,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\BannerRotationSlide';
    }

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\BannerRotationSlide');

        return $countOnly
            ? $repo->count()
            : $repo->findBy([], ['position' => 'ASC']);
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add banner';
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
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
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
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
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

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' banner-rotation-images';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return '';
    }
}
