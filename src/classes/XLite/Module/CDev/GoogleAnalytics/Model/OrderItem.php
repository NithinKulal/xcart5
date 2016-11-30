<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Model;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

/**
 * Something customer can put into his cart (sic!)
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Category added name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $categoryAdded = '';

    /**
     * @return string
     */
    public function getCategoryAdded()
    {
        return $this->categoryAdded;
    }

    /**
     * @param string $categoryAdded
     */
    public function setCategoryAdded($categoryAdded)
    {
        $this->categoryAdded = $categoryAdded;
    }

    /**
     * Get event cell base information
     *
     * @return array
     */
    public function getEventCell()
    {
        $result = parent::getEventCell();

        if (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()) {
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

            $result['ga-data'] = OrderItemDataMapper::getData(
                $this,
                $this->getObject()->getCategory() ? $this->getObject()->getCategory()->getName() : ''
            );
            \XLite\Core\Translation::setTmpTranslationCode(null);
        }

        return $result;
    }
}