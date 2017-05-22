<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Core\Cache\ExecuteCachedTrait;

class Promo extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    /**
     * @param $id
     *
     * @return string
     */
    public function getPromoContent($id)
    {
        $result = '';
        $promoData = $this->getPromoData($id);

        if (!$promoData) {
            return $result;
        }

        if (isset($promoData['module'])) {
            $url = $this->getRecommendedModuleURL($promoData['module']);

            if ($url) {
                $result = static::t($promoData['content'], ['url' => $url]);
            }
        } else {
            $result = $promoData['content'];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getPromoList()
    {
        return [
            'multi-currency-1' => [
                'module'    => 'XC\MultiCurrency',
                'content'   => 'Need a way to set multicurrency prices? [Install the addon]'
            ],
            'wholesale-prices-1' => [
                'module'    => 'CDev\WholeSale',
                'content'   => 'Need a way to set wholesale prices? [Install the addon]'
            ],
            'product-variants-1' => [
                'module'    => 'XC\ProductVariants',
                'content'   => 'Need a way to set up product variants? [Install the addon]'
            ],
            'banner-system-1' => [
                'module'    => 'QSL\Banner',
                'content'   => 'Get a more powerful banner system for your store'
            ],
            'pdf-invoice-1' => [
                'module'    => 'QSL\PDFInvoice',
                'content'   => 'Get a more customizeable PDF invoice solution for your store'
            ],
            'seo-promo-1' => [
                'content'   => static::t('Want help with SEO? Ask X-Cart Guru', [
                    'url' => \XLite::getXCartURL('http://www.x-cart.com/seo-consulting.html')
                ])
            ],
            'g2a-egoods-1' => [
                'content'   => static::t('Your payment module to accept payments for digital items. [Get it now]!', [
                    'url' => \XLite\Core\Converter::buildURL(
                        'addons_list_marketplace',
                        '',
                        [ 'moduleName' => 'G2APay\\G2APay' ]
                    )
                ])
            ],
        ];
    }

    /**
     * @param $id
     *
     * @return mixed|null
     */
    public function getPromoData($id)
    {
        $list = $this->getPromoList();

        if(!isset($list[$id])) {
            return null;
        }

        return $list[$id];
    }

    /**
     * Get recommended module URL
     *
     * @param string $moduleName
     *
     * @return string
     */
    protected function getRecommendedModuleURL($moduleName)
    {
        $cacheParams = [ $moduleName ];

        return $this->executeCachedRuntime(function() use ($moduleName) {
            list($author, $name) = explode('\\', $moduleName);

            /** @var \XLite\Model\Module $module */
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
                [
                    'author' => $author,
                    'name'   => $name,
                ],
                [ 'fromMarketplace' => 'ASC' ]
            );

            $result = null;

            if ($module && !$module->getEnabled()) {
                // Module disabled or not installed
                $result = $module->getFromMarketplace()
                    ? $module->getMarketplaceURL()
                    : $module->getInstalledURL();
            }

            return $result;
        }, $cacheParams);
    }

}
