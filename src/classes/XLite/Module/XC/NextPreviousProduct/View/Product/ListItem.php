<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\View\Product;

use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeString;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Widget parameters
     */
    const PARAM_PAGE_ID          = 'pageId';
    const PARAM_POSITION_ON_PAGE = 'positionOnPage';
    const PARAM_ITEM_LIST_CLASS  = 'itemListClass';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PAGE_ID          => new TypeInt('Page id'),
            self::PARAM_POSITION_ON_PAGE => new TypeInt('Position on page'),
            self::PARAM_ITEM_LIST_CLASS  => new TypeString('Item list class'),
        );
    }

    /**
     * json string for data attribute
     *
     * @return string
     */
    protected function getDataString()
    {
        return json_encode($this->defineDataForDataString());
    }

    /**
     * Define data for getDataString() method
     *
     * @return array
     */
    protected function defineDataForDataString()
    {
        return array(
            'class'      => $this->getParam(self::PARAM_ITEM_LIST_CLASS),
            'pageId'     => $this->getParam(self::PARAM_PAGE_ID),
            'position'   => $this->getParam(self::PARAM_POSITION_ON_PAGE),
            'parameters' => array(),
        );
    }

    /**
     * Get cookie path
     *
     * @return string
     */
    protected function getCookiePath()
    {
        $result = null;

        if (
            LC_USE_CLEAN_URLS
            && (bool)\XLite::getInstance()->getOptions(array('clean_urls', 'use_canonical_urls_only'))
        ) {
            // Get store URL
            $url = \XLite\Core\Request::getInstance()->isHTTPS()
                ? 'http://' . \XLite::getInstance()->getOptions(array('host_details', 'http_host'))
                : 'https://' . \XLite::getInstance()->getOptions(array('host_details', 'https_host'));

            $url .= \XLite::getInstance()->getOptions(array('host_details', 'web_dir'));

            $urlParts = parse_url($url);

            // Result is path to store
            $result = isset($urlParts['path']) ? $urlParts['path'] : '/';
        }

        return $result;
    }
}