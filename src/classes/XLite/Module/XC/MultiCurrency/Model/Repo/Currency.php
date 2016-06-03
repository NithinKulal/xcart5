<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model\Repo;

/**
 * Currency repository
 */
class Currency extends \XLite\Model\Repo\Currency implements \XLite\Base\IDecorator
{
    /**
     * Get all inactive currencies
     *
     * @return \XLite\Model\Currency
     */
    public function getAllInactiveCurrencies()
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder->leftJoin('c.active_currency', 'ac')
            ->andWhere('ac.active_currency_id IS NULL')
            ->orderBy('translations.name', 'ASC');

        if ($this->getTranslationCode() != \XLite::getDefaultLanguage()) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );
            $query->addOrderBy('defaults.name', 'ASC');
        }

        return $query->getResult();
    }
}