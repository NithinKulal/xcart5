<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"QSL\ShopByBrand"})
 */
abstract class StoreApiBrands extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    protected function getBrandsCount()
    {
        return Database::getRepo('XLite\Module\QSL\ShopByBrand\Model\Brand')->countEnabledBrands();
    }

    /**
     * Get products data
     *
     * @return array
     */
    public function getBrands()
    {
        $result = Database::getRepo('XLite\Module\QSL\ShopByBrand\Model\Brand')->getCategoryBrandsWithProductCount();

        return array_map(
            array($this, 'getBrand'),
            $result
        );
    }

    public function getBrand($record)
    {
        $brand = $record[0];

        return array(
            'name'        => $brand->getName(),
            'description' => $brand->getDescription(),
            'id'          => $brand->getBrandId(),
            'url'         => $this->getBrandUrl($brand),
        );
    }

    protected function getBrandUrl($brand)
    {
        $url = Converter::buildFullURL(
            'brand', '', array('brand_id' => $brand->getBrandId())
        );

        return $this->isMultiDomain() ? parse_url($url, PHP_URL_PATH) : $url;
    }
}
