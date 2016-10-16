<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\Core;

/**
 * Add2CartPopup product sources class
 */
class Add2CartPopup extends \XLite\Base\Singleton
{
    /**
     * Runtime cache of resources list
     *
     * @var array
     */
    protected static $resourcesFiles = null;

    /**
     * Cache of total products count
     *
     * @var integer
     */
    protected $totalProductsCount = null;

    /**
     * Runtime cache of options
     */
    protected $options = null;


    /**
     * Add to cart widget is disabled:
     * - in admin area
     * - on the mobile phones
     * - on the cart and checkout pages (see getAdd2CartPopupExcludedTargets method)
     *
     * @see static::getAdd2CartPopupExcludedTargets
     *
     * @return boolean
     */
    public static function isAdd2CartPopupEnabled()
    {
        return !\XLite::isAdminZone()
            && !in_array(\XLite\Core\Request::getInstance()->target, static::getAdd2CartPopupExcludedTargets())
            && !\XLite\Core\MobileDetect::getInstance()->isMobilePhone();
    }

    /**
     * Return true if 'Add to Cart' popup should be enabled on the page
     *
     * @return boolean
     */
    public static function getResourcesFiles($type)
    {
        if (!isset(static::$resourcesFiles)) {

            static::$resourcesFiles = array();

            $widget = new \XLite\Module\XC\Add2CartPopup\View\Products;
            $widget->init();

            static::$resourcesFiles[\XLite\View\AView::RESOURCE_JS] = $widget->getJSFiles();
            static::$resourcesFiles[\XLite\View\AView::RESOURCE_CSS] = $widget->getCSSFiles();
        }

        return !empty(static::$resourcesFiles[$type]) ? static::$resourcesFiles[$type] : array();
    }

    /**
     * Get list of targets where 'Add to Cart' popup should not be displayed
     *
     * @return array
     */
    protected static function getAdd2CartPopupExcludedTargets()
    {
        return array(
            'cart',
            'checkout',
        );
    }

    /**
     * Get active product sources
     *
     * @return array
     */
    public function getActiveSources()
    {
        $result = array();

        $option = $this->getSelectedSourcesOption();

        $sources = $this->getSources();

        if ($option) {
            foreach ($option as $code => $data) {
                if (isset($sources[$code]) && $data['enabled'] && method_exists($this, $sources[$code]['method'])) {
                    $result[] = $sources[$code]['method'];
                }
            }
        }

        return $result;
    }

    /**
     * Get active product sources
     *
     * @return array
     */
    public function getSelectedSourcesOption()
    {
        $option = \XLite\Core\Config::getInstance()->XC->Add2CartPopup->product_sources;

        return unserialize($option) ?: array();
    }

    /**
     * Get array of options ordered by 'position' value
     *
     * @return array
     */
    public function getSourcesOptions()
    {
        if (!isset($this->options)) {

            $this->options = array();

            $sources = $this->getSources();
            $selectedOptions = $this->getSelectedSourcesOption();

            foreach ($sources as $code => $data) {

                $entity = new \XLite\Module\XC\Add2CartPopup\Model\Source;

                $sdata = array();
                $sdata['code'] = $code;
                $sdata['enabled'] = (isset($selectedOptions[$code]) && $selectedOptions[$code]['enabled']);
                $sdata['position'] = (isset($selectedOptions[$code]) ? intval($selectedOptions[$code]['position']) : 0);

                $entity->map($sdata);

                $this->options[] = $entity;
            }

            usort($this->options, array($this, 'compareOptions'));
        }

        return $this->options;
    }

    /**
     * Service method is used to sort options by 'position' value
     *
     * @param \XLite\Model\AEntity $s1 First option
     * @param \XLite\Model\AEntity $s2 Second option
     *
     * @return integer
     */
    protected function compareOptions($s1, $s2)
    {
        $a = $s1->getterProperty('position');
        $b = $s2->getterProperty('position');

        if ($a == $b) {
            $result = 0;

        } else {
            $result = ($a > $b ? 1 : -1);
        }

        return $result;
    }

    /**
     * Get product sources
     *
     * @return array
     */
    protected function getSources()
    {
        return $this->getSourceRandom();
    }

    /**
     * Get method name for random products list gathering
     *
     * @return string
     */
    protected function getSourceRandom()
    {
        return array(
            'R' => array(
                'method'   => 'getProductsRandom',
                'position' => '1000',
            ),
        );
    }

    /**
     * Get products randomly
     *
     * @param integer $productId  Current product ID
     * @param array   $productIds Product ID which must be excluded from the search results
     * @param integer $maxCount   Maximum number of products
     *
     * @return array
     */
    public function getProductsRandom($productId, $productIds, $maxCount)
    {
        $result = array();

        $totalProducts = $this->getTotalProductsCount();

        if ($totalProducts) {

            $cnd = new \XLite\Core\CommonCell;
            if ($productIds) {
                $cnd->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID} = $productIds;
            }
            $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_IN;
            $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = array(1, 90);
            $cnd->{\XLite\Model\Repo\Product::P_ORDER_BY} = array('p.product_id', time() % 2 ? 'ASC' : 'DESC');

            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, false);
            $count = count($products);

            for ($i = 0; $i < min($maxCount, $count); $i++) {

                $randomId = mt_rand(0, $count);

                if (!empty($products[$randomId]) && !isset($result[$randomId]) && $productId != $products[$randomId]->getProductId()) {
                    $result[$randomId] = $products[$randomId];

                } else {
                    $i--;
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Get total number of products
     *
     * @return integer
     */
    protected function getTotalProductsCount()
    {
        if (!isset($this->totalProductsCount)) {

            $cnd = new \XLite\Core\CommonCell;

            $this->totalProductsCount = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, true);
        }

        return $this->totalProductsCount;
    }
}
