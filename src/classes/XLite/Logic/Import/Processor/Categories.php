<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Categories import processor
 */
class Categories extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Categories imported');
    }

    /**
     * Mark all images as processed
     *
     * @return void
     */
    public function markAllImagesAsProcessed()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->unmarkAsProcessed();
        \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Banner')->unmarkAsProcessed();
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'path'              => array(
                static::COLUMN_IS_KEY          => true,
            ),
            'enabled'           => array(),
            'showTitle'         => array(),
            'position'          => array(),
            'memberships'       => array(
                static::COLUMN_IS_MULTIPLE     => true
            ),
            'image'             => array(),
            'banner'            => array(),
            'cleanURL'          => array(
                static::COLUMN_LENGTH          => 255,
            ),
            'name'              => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'description'       => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ),
            'metaTags'          => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
            'metaDesc'          => array(
                static::COLUMN_IS_MULTILINGUAL => true,
            ),
            'metaTitle'         => array(
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ),
        );
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'CATEGORY-ENABLED-FMT'              => 'Wrong enabled format',
                'CATEGORY-SHOW-TITLE-FMT'           => 'Wrong show title format',
                'CATEGORY-POSITION-FMT'             => 'Wrong position format',
                'CATEGORY-NAME-FMT'                 => 'The name is empty',
                'CATEGORY-IMG-LOAD-FAILED'          => 'Error of image loading. Make sure the "images" directory has write permissions.',
                'CATEGORY-IMG-URL-LOAD-FAILED'      => "Couldn't download the image {{value}} from URL",
                'CATEGORY-BANNER-LOAD-FAILED'       => 'Error of banner loading. Make sure the "images" directory has write permissions.',
                'CATEGORY-BANNER-URL-LOAD-FAILED'   => "Couldn't download the banner {{value}} from URL",
            );
    }

    /**
     * Verify 'path' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPath($value, array $column)
    {
    }

    /**
     * Verify 'enabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('CATEGORY-ENABLED-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'show title' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyShowTitle($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('CATEGORY-SHOW-TITLE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('CATEGORY-POSITION-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'memberships' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMemberships($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $membership) {
                if (!$this->verifyValueAsEmpty($membership) && !$this->verifyValueAsMembership($membership)) {
                    $this->addWarning('GLOBAL-MEMBERSHIP-FMT', array('column' => $column, 'value' => $membership));
                }
            }
        }
    }

    /**
     * Verify 'image' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyImage($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('GLOBAL-IMAGE-FMT', array('column' => $column, 'value' => $value));
        } elseif (!$this->verifyValueAsEmpty($value) && $this->verifyValueAsURL($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('CATEGORY-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'banner' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBanner($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('GLOBAL-IMAGE-FMT', array('column' => $column, 'value' => $value));
        } elseif (!$this->verifyValueAsEmpty($value) && $this->verifyValueAsURL($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('CATEGORY-BANNER-URL-LOAD-FAILED', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'clean URL' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyCleanURL($value, array $column)
    {
    }

    /**
     * Verify 'name' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('CATEGORY-NAME-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'description' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDescription($value, array $column)
    {
    }

    /**
     * Verify 'meta tags' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTags($value, array $column)
    {
    }

    /**
     * Verify 'meta desc' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaDesc($value, array $column)
    {
    }

    /**
     * Verify 'meta title' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTitle($value, array $column)
    {
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'enabled' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeEnabledValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'show title' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeShowTitleValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizePositionValue($value)
    {
        return abs(intval($value));
    }

    // }}}

    // {{{ Import

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        \Xlite\Core\Database::getRepo('XLite\Model\Product')->setBlockQuickDataFlag(true);

        return parent::importData($data);
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        return $this->getCategoryByPath(isset($data['path']) ? $data['path'] : '', false);
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        return $this->addCategoryByPath(isset($data['path']) ? $data['path'] : '');
    }

    /**
     * Import 'path' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importPathColumn(\XLite\Model\Category $model, $value, array $column)
    {
        // Just skip this field
    }

    /**
     * Import 'memberships' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param array                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importMembershipsColumn(\XLite\Model\Category $model, array $value, array $column)
    {
        if ($value) {
            if ($model->getMemberships()) {
                foreach ($model->getMemberships() as $membership) {
                    $membership->getCategories()->removeElement($model);
                }
                $model->getMemberships()->clear();
            }

            if (!$this->verifyValueAsNull($value)) {
                foreach ($value as $membership) {
                    $membership = $this->normalizeValueAsMembership($membership);
                    if ($membership) {
                        $model->addMemberships($membership);
                        $membership->addCategory($model);
                    }
                }
            }
        }
    }

    /**
     * Import 'image' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importImageColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $path = $value;
        if ($value && !$this->verifyValueAsNull($value) && $this->verifyValueAsFile($path)) {
            $image = $model->getImage();

            $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;

            if ($image) {
                $compare = $this->getImageFilter($file);
                $image = $compare($image) ? $image : null;
                $readable = \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $file);
            }

            if (!$image || !$readable) {
                $image = new \XLite\Model\Image\Category\Image();

                if ($this->verifyValueAsURL($file)) {
                    $success = $image->loadFromURL($file, true);

                } else {
                    $success = $image->loadFromLocalFile(LC_DIR_ROOT . $file);
                }

                if (!$success) {
                    if ($image->getLoadError() === 'unwriteable') {
                        $this->addError('CATEGORY-IMG-LOAD-FAILED', array('column' => $column, 'value' => $path));
                    } elseif ($image->getLoadError()) {
                        $this->addWarning('CATEGORY-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $path));
                    }

                } else {
                    if ($model->getImage()) {
                        \XLite\Core\Database::getEM()->remove($model->getImage());
                        \XLite\Core\Database::getEM()->flush();
                    }
                    $image->setNeedProcess(1);
                    $image->setCategory($model);
                    $model->setImage($image);
                    \XLite\Core\Database::getEM()->persist($image);
                }
            }
        } elseif ($value && $this->verifyValueAsURL($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('CATEGORY-IMG-URL-LOAD-FAILED', array('column' => $column, 'value' => $value));
        }

        if ($value && $this->verifyValueAsNull($value)) {
            if ($model->getImage()) {
                \XLite\Core\Database::getEM()->remove($model->getImage());
                $model->setImage(null);
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Import 'banner' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importBannerColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $path = $value;
        if ($value && !$this->verifyValueAsNull($value) && $this->verifyValueAsFile($path)) {
            $image = $model->getBanner();

            $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;

            if ($image) {
                $compare = $this->getImageFilter($file);
                $image = $compare($image) ? $image : null;
                $readable = \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $file);
            }

            if (!$image || !$readable) {
                $image = new \XLite\Model\Image\Category\Banner();

                if ($this->verifyValueAsURL($file)) {
                    $success = $image->loadFromURL($file, true);

                } else {
                    $success = $image->loadFromLocalFile(LC_DIR_ROOT . $file);
                }

                if (!$success) {
                    if ($image->getLoadError() === 'unwriteable') {
                        $this->addError('CATEGORY-BANNER-LOAD-FAILED', array('column' => $column, 'value' => $path));
                    } elseif ($image->getLoadError()) {
                        $this->addWarning('CATEGORY-BANNER-URL-LOAD-FAILED', array('column' => $column, 'value' => $path));
                    }

                } else {
                    if ($model->getBanner()) {
                        \XLite\Core\Database::getEM()->remove($model->getBanner());
                        \XLite\Core\Database::getEM()->flush();
                    }
                    $image->setNeedProcess(1);
                    $image->setCategory($model);
                    $model->setBanner($image);
                    \XLite\Core\Database::getEM()->persist($image);
                }
            }
        } elseif ($value && $this->verifyValueAsURL($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('CATEGORY-BANNER-URL-LOAD-FAILED', array('column' => $column, 'value' => $value));
        }

        if ($value && $this->verifyValueAsNull($value)) {
            if ($model->getBanner()) {
                \XLite\Core\Database::getEM()->remove($model->getBanner());
                $model->setBanner(null);
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Import 'cleanURL' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importCleanURLColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $this->updateCleanURL($model, $value);
    }

    // }}}
}
