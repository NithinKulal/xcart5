<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * No modules installed
 */
class NoModulesInstalled extends \XLite\View\Dialog
{
    /**
     * Limit
     *
     * @var integer
     */
    protected static $limit = null;

    /**
     * Add widget specific CSS files
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'No promotion modules installed';
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return static::t(
            'To boost your sales try to use Discounts coupons, Sale, Product Advisor, Volume discounts addons. Also you may be interested in all Marketing extensions from our Marketplace',
            $this->getDescriptionData()
        );
    }

    /**
     * Description specific data
     *
     * @return array
     */
    public function getDescriptionData()
    {
        return array(
            'discountCoupons'   => $this->getModuleURL('Coupons', 'CDev'),
            'sale'              => $this->getModuleURL('Sale', 'CDev'),
            'productAdvisor'    => $this->getModuleURL('ProductAdvisor', 'CDev'),
            'volumeDiscounts'   => $this->getModuleURL('VolumeDiscounts', 'CDev'),
            'marketingTag'      => $this->getTagURL('Marketing'),
        );
    }

    /**
     * Module URL for marketplace
     *
     * @param string $name   Name
     * @param string $author Author
     *
     * @return string
     */
    protected function getModuleURL($name, $author)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $name);
    }

    /**
     * Tag URL for marketplace
     *
     * @param string $tagName Tag name
     *
     * @return string
     */
    protected function getTagURL($tagName)
    {
        return $this->buildURL('addons_list_marketplace', '', array('tag' => $tagName));
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'no_modules_installed';
    }

}
