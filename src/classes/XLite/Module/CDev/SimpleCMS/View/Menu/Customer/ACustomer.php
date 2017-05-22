<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\Menu\Customer;

use XLite\View\CacheableTrait;

/**
 * Footer menu
 */
abstract class ACustomer extends \XLite\View\Menu\Customer\ACustomer implements \XLite\Base\IDecorator
{
    use CacheableTrait;

    /**
     * Check if url is same as current URL
     *
     * @param string    $url    URL to check
     *
     * @return boolean
     */
    protected function isSameAsCurrent($url)
    {
        return \XLite::getInstance()->getShopURL($url) === \XLite\Core\URLManager::getCurrentURL();
    }

    /**
     * Check - specified item is active or not
     *
     * @param array $item Menu item
     *
     * @return boolean
     */
    protected function isActiveItemWithoutLink(array $item)
    {
        $result = parent::isActiveItem($item);

        if (false === $item['controller']) {

            $result = $item['url'] && $this->isSameAsCurrent($item['url'])
                ?: $result;

        } else {

            if (!is_array($item['controller'])) {
                $item['controller'] = array($item['controller']);
            }

            $controller = \XLite::getController();

            foreach ($item['controller'] as $controllerName) {
                if ($controller instanceof $controllerName) {
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Check is parent should be active
     *
     * @param integer $id Id
     *
     * @return boolean
     */
    protected function checkChilden($id)
    {
        $item = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->find($id);
        $result = false;

        if ($item) {
            $children = $item->getChildren()->toArray();

            if ($children) {
                $found = false;
                foreach ($children as $child) {
                    $found = $this->checkChilden($child->getId());
                    if ($found) {
                        break;
                    }
                }
                $result = $found;
            } else {
                $result = $item->getURL() && $this->isSameAsCurrent($item->getURL());
            }
        }

        return $result;
    }

    /**
     * Check - specified item is active or not
     *
     * @param array $item Menu item
     *
     * @return boolean
     */
    protected function isActiveItem(array $item)
    {
        $linkMatched = isset($item['url'])
            && $item['url']
            && $this->isSameAsCurrent($item['url']);

        return ($linkMatched || $this->checkChilden($item['id']))
            ?:
            $this->isActiveItemWithoutLink($item);
    }

    protected function getCacheParameters()
    {
        $cacheParams = parent::getCacheParameters();

        $cacheParams[] = \XLite\Core\Auth::getInstance()->isLogged();

        $items = $this->executeCached([$this, 'getItems'], array_merge($cacheParams, ['getItems']));

        $matchedItemId = null;

        if (is_array($items)) {
            $url   = \XLite\Core\URLManager::getCurrentURL();
            $xlite = \XLite::getInstance();

            foreach ($items as $item) {
                if (isset($item['link']) && $xlite->getShopURL($item['link']) === $url || $this->isActiveItemWithoutLink($item)) {
                    $matchedItemId = $item['id'];

                    break;
                }
            }
        }

        if ($matchedItemId !== null) {
            $cacheParams[] = $matchedItemId;
        }

        return $cacheParams;
    }
}
