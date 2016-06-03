<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Banner
 */
class Banner extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Widget CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/banner';
    }

    /**
     * Retrieve all banners collection.
     * The first one banner is considered
     *
     * @return array
     */
    protected function retrieveBannerCollection()
    {
        return \XLite\Core\Marketplace::getInstance()->getAllBanners();
    }

    /**
     * Return banner URL
     *
     * @return array
     */
    protected function getMainBanner()
    {
        $banners = $this->retrieveBannerCollection();

        return $banners[0];
    }

    /**
     * Retrieve the banners collection
     *
     * @return array
     */
    protected function getBannersCollection()
    {
        $banners = $this->retrieveBannerCollection();
        unset($banners[0]);

        return $banners;
    }

    /**
     * Retrieve banner specific URL
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerURL($banner)
    {
        return !empty($banner['banner_url'])
            ? $banner['banner_url']
            : $this->getBannerModuleURL($banner);
    }

    /**
     * Retrieve banner specific URL
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerModuleURL($banner)
    {
        list($author, $module) = explode('-', $banner['banner_module']);

        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $module);
    }

    /**
     * Return true if banner URL is an external URL
     *
     * @param array $banner Banner
     *
     * @return boolean
     */
    protected function isBannerExternalURL($banner)
    {
        return !empty($banner['banner_url']);
    }

    /**
     * Retrieve banner image
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerImg($banner)
    {
        return $banner['banner_img'];
    }

    /**
     * Retrieve the main banner URL
     *
     * @return string
     */
    protected function getMainBannerURL()
    {
        return $this->getBannerURL($this->getMainBanner());
    }

    /**
     * Return true if main banner URL is an external URL
     *
     * @param array $banner Banner
     *
     * @return boolean
     */
    protected function isMainBannerExternalURL()
    {
        return $this->isBannerExternalURL($this->getMainBanner());
    }

    /**
     * Retrieve the main banner image
     *
     * @return string
     */
    protected function getMainBannerImg()
    {
        return $this->getBannerImg($this->getMainBanner());
    }

    /**
     * Defines the search string for the landing page
     *
     * @return string
     */
    protected function getSubstring()
    {
        return '';
    }

    /**
     * Defines the tags data structure
     *
     * @return array
     */
    protected function getTagsData()
    {
        $result = array();

        foreach (\XLite\Core\Marketplace::getInstance()->getAllTags() as $tag) {
            $result[$tag] = $this->buildURL(
                'addons_list_marketplace',
                '',
                array(
                    'tag'         => $tag,
                    'clearPager'  => '1',
                    'clearSearch' => 1,
                )
            );
        }

        return $result;
    }
}
