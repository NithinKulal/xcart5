<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Controller\Admin;

/**
 * Wholesale pricing page controller (Product modify section)
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_WHOLESALE_PRICING = 'wholesale_pricing';

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (!$this->isNew()) {
            $list[static::PAGE_WHOLESALE_PRICING] = static::t('Wholesale pricing');
        }

        return $list;
    }

    /**
     * Check if wholesale prices enabled for current product
     *
     * @return boolean
     */
    public function isWholesalePricesEnabled()
    {
        return $this->getProduct()->isWholesalePricesEnabled();
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list[static::PAGE_WHOLESALE_PRICING] = 'modules/CDev/Wholesale/pricing/body.twig';
        }

        return $list;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionWholesalePricesUpdate()
    {
        $list = new \XLite\Module\CDev\Wholesale\View\ItemsList\WholesalePrices();
        $list->processQuick();

        // Additional correction to re-define end of subtotal range for each discount
        \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')
            ->correctQuantityRangeEnd($this->getProduct());
    }
}
