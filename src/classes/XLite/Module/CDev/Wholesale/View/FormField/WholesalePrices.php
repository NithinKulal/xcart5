<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormField;

/**
 * Wholesale prices
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class WholesalePrices extends \XLite\View\FormField\Inline\Label
{
    /**
     * Wholesale prices
     *
     * @var array
     */
    protected $wholesalePrices;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Wholesale/form_field/wholesale_prices.css';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/form_field/wholesale_prices.twig';
    }

    /**
     * Return wholesale prices
     *
     * @return array
     */
    protected function getWholesalePrices()
    {
        if (!isset($this->wholesalePrices)) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\Base\AWholesalePrice::P_ORDER_BY_MEMBERSHIP} = true;
            $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\Base\AWholesalePrice::P_ORDER_BY} = array('w.quantityRangeBegin', 'ASC');

            if ($this->getEntity()->getDefaultPrice()) {
                $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\WholesalePrice::P_PRODUCT} = $this->getEntity()->getProduct();

                $this->wholesalePrices = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->search($cnd);

            } else {

                $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\ProductVariantWholesalePrice::P_PRODUCT_VARIANT} = $this->getEntity();

                $this->wholesalePrices = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->search($cnd);
            }
        }

        return $this->wholesalePrices;
    }

    /**
     * Return link
     *
     * @return string
     */
    protected function getLink()
    {
        return $this->getEntity()->getDefaultPrice()
            ? $this->buildURL('product', null, array('product_id' => $this->getEntity()->getProduct()->getId(), 'page' => 'wholesale_pricing'))
            : $this->buildURL('product_variant', null, array('id' => $this->getEntity()->getId(), 'page' => 'wholesale_pricing'));
    }
}
