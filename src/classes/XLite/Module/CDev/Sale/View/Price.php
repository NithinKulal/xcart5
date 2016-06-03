<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Viewer
 */
abstract class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    const SALE_PRICE_LABEL = 'sale_price_label';

    protected $salePriceLabel = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/css/lc.css';

        return $list;
    }

    /**
     * Calculate "Sale percent off" value.
     *
     * @return integer
     */
    protected function getSalePercent()
    {
        $oldPrice = $this->getOldPrice();

        return 0 < $oldPrice
            ? round((1 - $this->getListPrice() / $oldPrice ) * 100)
            : 0;
    }

    /**
     * Return sale percent value
     *
     * @return float
     */
    protected function getSalePriceDifference()
    {
        return $this->getOldPrice() - $this->getCart()->getCurrency()->roundValue($this->getListPrice());
    }

    /**
     * Return old price value
     *
     * @return float
     */
    protected function getOldPrice()
    {
        return $this->getProduct()->getDisplayPriceBeforeSale();
    }

    /**
     * Return sale participation flag
     *
     * @return boolean
     */
    protected function participateSale()
    {
        return $this->getProduct()->getParticipateSale()
            && $this->getListPrice() < $this->getOldPrice();
    }

    /**
     * Return the "x% label" element
     *
     * @return array
     */
    protected function getLabels()
    {
        return parent::getLabels() + array($this->getSalePriceLabel());
    }

    /**
     * Return the specific sale price label info
     *
     * @return array
     */
    public function getSalePriceLabel()
    {
        if (!isset($this->salePriceLabel)) {
            if ($this->participateSale()) {
                $label = static::t('percent X off', array('percent' => $this->getSalePercent()));
                $this->salePriceLabel = array(
                    'green sale-price' => $label,
                );

                \XLite\Module\CDev\Sale\Core\Labels::addLabel($this->getProduct(), $this->salePriceLabel);
            }
        }

        return $this->salePriceLabel;
    }

    /**
     * Return the specific label info
     *
     * @param string $labelName
     *
     * @return array
     */
    protected function getLabel($labelName)
    {
        if (static::SALE_PRICE_LABEL === $labelName) {
            $result = $this->getSalePriceLabel();

        }  else {
            $result = parent::getLabel($labelName);
        }

        return $result;
    }
}
