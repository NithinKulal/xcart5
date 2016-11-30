<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Product;

use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeString;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\ProductDataMapper;

/**
 * Class ListItem
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    const PARAM_LIST_READABLE_NAME = 'itemListReadableName';
    const PARAM_GA_POSITION_ON_LIST = 'gaPositionOnList';

    /**
     * @return bool
     */
    public function shouldRegisterImpression()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled();
    }

    /**
     * Get impression GA event data
     *
     * @return string
     */
    public function getImpressionData()
    {
        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        $result = json_encode(
            $this->getRawImpressionData(),
            JSON_FORCE_OBJECT
        );

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $result;
    }

    /**
     * @return array
     */
    protected function getRawImpressionData()
    {
        $listName = $this->getReadableListName() ?: $this->getItemListWidgetTarget();
        $position = $this->getGaPositionInList() ?: '';
        $categoryName = $this->getCategoryName();

        return [
            'ga-type'   => 'impression',
            'ga-action' => 'pageview',
            'data'      => ProductDataMapper::getImpressionData(
                $this->getProduct(),
                $categoryName,
                $listName,
                $position
            )
        ];
    }

    /**
     * Readable list name for GoogleAnalytics
     *
     * @return string
     */
    protected function getReadableListName()
    {
        return $this->getParam(static::PARAM_LIST_READABLE_NAME);
    }

    /**
     * @return mixed
     */
    protected function getGaPositionInList()
    {
        return $this->getParam(static::PARAM_GA_POSITION_ON_LIST);
    }

    /**
     * @return string
     */
    protected function getCategoryName()
    {
        $categoryName = '';

        if (method_exists($this, 'getCategory')
            || method_exists(\XLite::getController(), 'getCategory')
        ) {
            $categoryName = $this->getCategory()
                ? $this->getCategory()->getName()
                : '';
        }

        return $categoryName;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_LIST_READABLE_NAME  => new TypeString('Item list readable name'),
            static::PARAM_GA_POSITION_ON_LIST => new TypeInt('Item list position on list'),
        );
    }
}