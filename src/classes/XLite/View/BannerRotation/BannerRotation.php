<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\BannerRotation;

use \XLite\Logic\BannerRotation\Processor;

/**
 * BannerRotation widget
 *
 * @ListChild (list="center", zone="customer", weight="100")
 */
class BannerRotation extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'main';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'banner_rotation/style.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'banner_rotation/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'banner_rotation/body.twig';
    }

    /**
     * Get images
     *
     * @return array
     */
    protected function getImages()
    {
        $slides = \XLite\Core\Database::getRepo('XLite\Model\BannerRotationSlide')->findBy(
            array('enabled' => true),
            array('position' => 'ASC')
        );

        $images = array_map(
            function($slide) {
                return $slide->getImage();
            },
            $slides
        );

        return array_filter($images);
    }

    /**
     * Get image count
     *
     * @return int
     */
    protected function getImageCount()
    {
        $cacheParams   = $this->getCacheParameters();
        $cacheParams[] = 'getImageCount';

        return $this->executeCached(function () {
            return count($this->getImages());
        }, $cacheParams);
    }

    /**
     * Is banner rotation enabled
     *
     * @return boolean
     */
    protected function isRotationEnabled()
    {
        return \XLite\Core\Config::getInstance()->BannerRotation->enabled && 1 < $this->getImageCount();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
               && \XLite\Core\Config::getInstance()->BannerRotation->enabled
               && $this->getImageCount() > 0;
    }

    /**
     * Get commented data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array(
            'pause'     => false,
            'interval'  => round(\XLite\Core\Config::getInstance()->BannerRotation->interval * 1000),
        );
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }
}
