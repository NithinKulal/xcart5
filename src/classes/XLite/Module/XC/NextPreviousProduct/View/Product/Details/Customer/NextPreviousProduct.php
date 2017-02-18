<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\View\Product\Details\Customer;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\View\CacheableTrait;

/**
 * Next previous product widget
 *
 * @ListChild (list="product.details.page.info", weight="5")
 */
class NextPreviousProduct extends \XLite\View\AView
{
    use CacheableTrait;
    use ExecuteCachedTrait;

    /**
     * Max count of cookies saved
     */
    const COOKIE_LIMIT = 10;

    /**
     * Icon width for dropdown box
     */
    const DROPDOWN_ICON_WIDTH  = '110';
    const DROPDOWN_ICON_HEIGHT = '110';

    /**
     * Add CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/NextPreviousProduct/style.css';

        return $list;
    }

    /**
     * Add JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/XC/NextPreviousProduct/next-previous-product.js';

        return $list;
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'product';

        return $result;
    }

    /**
     * @return integer
     */
    protected function getProductId()
    {
        return $this->executeCachedRuntime([\XLite::getController(), 'getProductId']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/NextPreviousProduct/product/next-previous.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cacheParams   = $this->getCacheParameters();
        $cacheParams[] = 'isVisible';

        $condition = $this->executeCached(function () {
            $cookieData = $this->getCookieData();
            if (isset($cookieData['disabled']) && $cookieData['disabled']) {
                return false;
            }

            if (null === $this->getItemsList()) {
                return false;
            }

            $items = $this->getNextPreviousItems();
            if (!is_array($items)) {
                return false;
            }
            $itemsCount = count($items);

            return $itemsCount <= 3 && $itemsCount > 1;

        }, $cacheParams);

        return parent::isVisible() && $condition;
    }

    /**
     * Get dropdown icon width
     *
     * @return string
     */
    protected function getIconWidth()
    {
        return static::DROPDOWN_ICON_WIDTH;
    }

    /**
     * Get dropdown icon height
     *
     * @return string
     */
    protected function getIconHeight()
    {
        return static::DROPDOWN_ICON_HEIGHT;
    }

    /**
     * Unset old cookie
     *
     * @return void
     */
    protected function unsetCookie()
    {
        $processed = [];

        foreach ($_COOKIE as $key => $value) {
            if (false === strpos($key, 'xc_np_product_')) {
                continue;
            }

            $json = json_decode($value, true);
            if (isset($json['created'])) {
                $processed[$json['created']] = $key;
            }
        }

        krsort($processed);
        $toUnset = array_slice($processed, static::COOKIE_LIMIT);

        if (isset($_COOKIE['xc_np_disable'])) {
            $toUnset[] = 'xc_np_product_' . $this->getProductId();
            $toUnset[] = 'xc_np_disable';
        }

        foreach ($toUnset as $cookieKey) {
            setcookie($cookieKey, '', time() - 3600);
        }
    }

    /**
     * Get cookie key
     *
     * @return string
     */
    protected function getCookieData()
    {
        $productId = $this->getProductId();

        $result = $this->executeCachedRuntime(function () use ($productId) {
            if (isset($_COOKIE['xc_np_disable'])) {
                return [];
            }

            $cookieKey = 'xc_np_product_' . $productId;

            return isset($_COOKIE[$cookieKey]) ? json_decode($_COOKIE[$cookieKey], true) : [];
        }, ['getCookieData', $productId]);

        $this->unsetCookie();

        return $result;
    }

    /**
     * @param array $data
     */
    protected function addCookieData($data)
    {
        $cookieData = $this->getCookieData();
        $cookieKey = 'xc_np_product_' . $this->getProductId();

        setcookie($cookieKey, json_encode(array_merge($cookieData, $data)), time() + 30 * 60);
    }

    /**
     * Check if previous item available
     *
     * @return boolean
     */
    protected function isPreviousAvailable()
    {
        $items = $this->getNextPreviousItems();

        return $items[0]->getProductId() == $this->getProductId() ? false : true;
    }

    /**
     * Check if next item available
     *
     * @return boolean
     */
    protected function isNextAvailable()
    {
        $items = $this->getNextPreviousItems();

        return $items[count($items) - 1]->getProductId() == $this->getProductId() ? false : true;
    }

    /**
     * Get previous item
     *
     * @return \XLite\Model\Product
     */
    protected function getPreviousItem()
    {
        $items        = $this->getNextPreviousItems();
        $previousItem = $items[0];
        if (!$this->isNextAvailable() && count($items) === 3) {
            $previousItem = $items[1];
        }

        return $previousItem;
    }

    /**
     * Get next item
     *
     * @return \XLite\Model\Product
     */
    protected function getNextItem()
    {
        $items    = $this->getNextPreviousItems();
        $nextItem = $items[count($items) - 1];
        if (!$this->isPreviousAvailable() && count($items) === 3) {
            $nextItem = $items[1];
        }

        return $nextItem;
    }

    /**
     * Is show next previous separator
     *
     * @return boolean
     */
    protected function isShowSeparator()
    {
        return $this->isNextAvailable() && $this->isPreviousAvailable();
    }

    /**
     * Get item URL
     *
     * @return \XLite\Model\Product
     *
     * @return string
     */
    protected function getItemURL($item)
    {
        $attributes = [
            'product_id' => $item->getProductId(),
        ];

        if ($this->isProductHasMultipleCategories($item)) {
            $attributes['category_id'] = $this->isStaticCategory()
                ? $this->getCategoryId()
                : $item->getCategory()->getId();
        }

        return $this->buildURL('product', '', $attributes);
    }

    /**
     * Return true if specified product assigned to multiple categories
     * (when cleanURL is enabled, URL for products should be built with category path)
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return boolean
     */
    protected function isProductHasMultipleCategories($product)
    {
        $result = LC_USE_CLEAN_URLS
            && !(bool) \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'use_canonical_urls_only']);

        if (!$result) {
            $result = 1 < count($product->getCategories());
        }

        return $result;
    }

    /**
     * Returns true if need set same category id
     * in next and previous URLs
     *
     * @return boolean
     */
    protected function isStaticCategory()
    {
        return $this->getItemsList() instanceof \XLite\View\ItemsList\Product\Customer\Category\ACategory
        || $this->getItemsList() instanceof \XLite\Module\CDev\Sale\View\SaleBlock;
    }

    /**
     * json string for data attribute
     *
     * @return string
     */
    protected function getDataStringPrevious()
    {
        $data = [
            'class'        => get_called_class(),
            'realPosition' => $this->getItemPosition() - 1,
            'sessionCell'  => $this->getSessionCellName(),
            'parameters'   => [
                'category_id' => (int) $this->getCategoryId(),
            ],
        ];

        return json_encode($data);
    }

    /**
     * json string for data attribute
     *
     * @return string
     */
    protected function getDataStringNext()
    {
        $data = [
            'class'        => get_called_class(),
            'realPosition' => $this->getItemPosition() + 1,
            'sessionCell'  => $this->getSessionCellName(),
            'parameters'   => [
                'category_id' => (int) $this->getCategoryId(),
            ],
        ];

        return json_encode($data);
    }

    /**
     * Get session cell name
     *
     * @return string
     */
    protected function getSessionCellName()
    {
        return $this->executeCachedRuntime(function () {
            $cookieData = $this->getCookieData();

            if (isset($cookieData['sessionCell'])) {
                return $cookieData['sessionCell'];
            }

            $result = null;
            if (isset($cookieData['class'])) {
                //$searchCondition = $itemsList->getSearchConditionWrapper();

                $searchCondition = \XLite\Core\Session::getInstance()
                    ->{$cookieData['class']::getSearchSessionCellName() . '_np'};
                $result          = hash(
                    'md4',
                    $cookieData['class']
                    . print_r($searchCondition, true)
                );

                $this->addCookieData(['sessionCell' => $result]);

                $conditionCellName                            = $result . '_conditionCell';
                \XLite\Core\Session::getInstance()->{$result} = [
                    'items_list'        => $cookieData['class'],
                    'conditionCellName' => $conditionCellName,
                ];

                \XLite\Core\Session::getInstance()->{$conditionCellName} = $searchCondition;
            }

            return $result;
        });
    }

    /**
     * Get items list
     *
     * @return \XLite\View\ItemsList\Product\Customer\ACustomer
     */
    protected function getItemsList()
    {
        return $this->executeCachedRuntime(function () {
            $result = null;

            $sessionListData = \XLite\Core\Session::getInstance()->{$this->getSessionCellName()};
            $listClass       = $sessionListData['items_list'];

            if (class_exists($listClass)) {
                $listClass::setNPMode(\XLite\View\ItemsList\Product\Customer\ACustomer::NP_MODE_READ);
                $listClass::setNPConditionCellName($sessionListData['conditionCellName']);
                $result = new $listClass();
            }

            return $result;
        });
    }

    /**
     * Get three items around current
     *
     * @return array
     */
    protected function getNextPreviousItems()
    {
        return $this->executeCachedRuntime(function () {
            $itemsList = $this->getItemsList();
            $result    = $itemsList ? $itemsList->getNextPreviousItems($this->getItemPosition()) : null;

            return array_values($result);
        });
    }

    /**
     * Get item position in search condition
     *
     * @return integer
     */
    protected function getItemPosition()
    {
        return $this->executeCachedRuntime(function () {
            $cookieData = $this->getCookieData();

            if (isset($cookieData['realPosition'])) {

                return $cookieData['realPosition'];

            } elseif (isset($cookieData['pageId'], $cookieData['position'])) {
                $itemsList = $this->getItemsList();
                if ($itemsList) {
                    $pageId   = max($cookieData['pageId'] - 1, 0);
                    $position = (int) $cookieData['position'];

                    $sessionCell  = \XLite\Core\Session::getInstance()->{$this->getSessionCellName()};
                    $itemsPerPage = isset($sessionCell['params']['itemsPerPage'])
                        ? $sessionCell['params']['itemsPerPage']
                        : $itemsList->getPagerWrapper()->getItemsPerPage();


                    return $pageId * $itemsPerPage + $position;
                }
            } else {
                $sessionCell = \XLite\Core\Session::getInstance()->{$this->getSessionCellName()};

                if (isset($sessionCell['cnd'])) {
                    $cnd       = $sessionCell['cnd'];
                    $productId = $this->getProductId();

                    if ($this->getItemsList() instanceof \XLite\View\ItemsList\Product\Customer\Category\Main) {
                        $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();
                    }
                    $ids = \XLite\Core\Database::getRepo('XLite\Model\Product')->searchOnlyIds($cnd);

                    foreach (array_values($ids) as $i => $id) {
                        if ($id['product_id'] == $productId) {
                            return $i;
                        }
                    }
                }
            }

            return 0;
        });
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list   = parent::getCacheParameters();
        $list[] = serialize(\XLite\Core\Session::getInstance()->{$this->getSessionCellName()});
        $list[] = $this->getProductId();

        return $list;
    }
}
