<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ImageResize;

/**
 * ImageResize
 */
class Generator extends \XLite\Logic\AGenerator
{
    const MODEL_PRODUCT = 'XLite\Model\Image\Product\Image';
    const MODEL_CATEGORY = 'XLite\Model\Image\Category\Image';

    /**
     * Image sizes
     *
     * @var array
     */
    protected static $imageSizes = array();

    /**
     * Image sizes cache
     *
     * @var array
     */
    protected static $imageSizesCache = null;

    /**
     * Options
     *
     * @var \ArrayObject
     */
    protected $options;

    /**
     * Returns available image sizes
     *
     * @return array
     */
    public static function defineImageSizes()
    {
        return array(
            static::MODEL_PRODUCT => array(
                'XXSThumbnail'     => array(40, 40),
                'XSThumbnail'      => array(60, 60), // Product thumbnail in the list of detailed images (details page)
                'SMThumbnail'      => array(80, 80), // Product thumbnail on the cart items list
                'MDThumbnail'      => array(122, 122),
                'CommonThumbnail'  => array(110, 110), // Products list thumbnail (mainly for sidebar lists)
                'SBSmallThumbnail' => array(160, 160), // Sidebar products list small thumbnail
                'SBBigThumbnail'   => array(160, 160), // Sidebar products list big thumbnail
                'LGThumbnailList'  => array(160, 160), // Center products list thumbnail
                'LGThumbnailGrid'  => array(160, 160), // Center products grid thumbnail
                'Default'          => array(300, 300), // Product thumbnail on the details page
                'LGDefault'        => array(600, 600), // Product detailed image on the details page
            ),
            static::MODEL_CATEGORY => array(
                'XXSThumbnail' => array(40, 40),
                'MDThumbnail'  => array(122, 122),
                'LGThumbnail'  => array(160, 160),
                'Default'      => array(160, 160), // Category thumbnail
            )
        );
    }

    /**
     * Get list of images sizes which administrator can edit via web interface
     *
     * @return array
     */
    public static function getEditableImageSizes()
    {
        return array(
            static::MODEL_PRODUCT => array(
                'LGThumbnailList',
                'LGThumbnailGrid',
                'Default',
            ),
            static::MODEL_CATEGORY => array(
                'Default',
            )
        );
    }

    /**
     * Add new sizes (or rewrite existing)
     *
     * @param array $sizes Image sizes
     */
    public static function addImageSizes(array $sizes)
    {
        static::$imageSizes = static::mergeImageSizes(static::$imageSizes, $sizes);
    }

    /**
     * Merge two sizes arrays
     *
     * @param array $baseSizes Base sizes
     * @param array $newSizes  New sizes
     *
     * @return array
     */
    public static function mergeImageSizes(array $baseSizes, array $newSizes)
    {
        foreach ($newSizes as $model => $modelSizes) {
            if (!is_array($modelSizes)) {
                continue;
            }

            foreach ($modelSizes as $name => $size) {
                if (!isset($baseSizes[$model])) {
                    $baseSizes[$model] = array();
                }

                if (is_numeric($name)) {
                    $baseSizes[$model][] = $size;

                } else {
                    $baseSizes[$model][$name] = $size;
                }
            }
        }

        return $baseSizes;
    }

    /**
     * Returns sizes for given class
     *
     * @param string $class Class
     *
     * @return array
     */
    public static function getModelImageSizes($class)
    {
        $sizes = static::getImageSizes();

        return isset($sizes[$class]) ? $sizes[$class] : array();
    }

    /**
     * Returns all sizes
     *
     * @param string $model Model OPTIONAL
     * @param string $code  Code OPTIONAL
     *
     * @return array
     */
    public static function getImageSizes($model = null, $code = null)
    {
        if (!isset(static::$imageSizesCache)) {
            $baseSizes = static::defineImageSizes();
            static::$imageSizesCache = static::mergeImageSizes($baseSizes, static::$imageSizes);

            $dbImageSizes = static::getDbImageSizes();
            if ($dbImageSizes) {
                static::$imageSizesCache = static::mergeImageSizes(static::$imageSizesCache, $dbImageSizes);
            }
        }

        if (!is_null($model) && !is_null($code)) {
            $result = isset(static::$imageSizesCache[$model][$code]) ? static::$imageSizesCache[$model][$code] : null;

        } else {
            $result = static::$imageSizesCache;
        }

        return $result;
    }

    /**
     * Get images sizes from database
     *
     * @return array
     */
    public static function getDbImageSizes()
    {
        $result = array();

        $sizes = \XLite\Core\Layout::getInstance()->getCurrentImagesSettings();

        if ($sizes) {
            foreach ($sizes as $size) {
                $result[$size->getModel()][$size->getCode()] = array(
                    $size->getWidth(),
                    $size->getHeight(),
                );
            }
        }

        return $result;
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array(
            'XLite\Logic\ImageResize\Step\Categories',
            'XLite\Logic\ImageResize\Step\Products',
        );
    }

    // }}}

    // {{{ Error / warning routines

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return 'resizeTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return 'resizeCancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'imageResize';
    }

    // }}}
}
