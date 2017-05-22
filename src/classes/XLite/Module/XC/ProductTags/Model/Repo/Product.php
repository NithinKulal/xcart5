<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Model\Repo;

/**
 * The Product model repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    const P_TAGS     = 'tags';
    const P_BY_TAG   = 'byTag';
    const TAG_FIELD  = 'tt.name';

    /**
     * Return conditions parameters that are responsible for substring set of fields.
     *
     * @return array
     */
    protected function getConditionBy()
    {
        $list = parent::getConditionBy();

        $list[] = static::P_BY_TAG;

        return $list;
    }

    /**
     * Return fields set for tag search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsByTag()
    {
        return array(
            self::TAG_FIELD,
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndTags(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_array($value)) {
            $queryBuilder->linkInner('p.tags', 't')
                ->andWhere('t.id IN (\'' . implode("','", $value) . '\')')
                ->groupBy('p.product_id');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkLeft('p.tags', 't');
        $this->addTranslationJoins($queryBuilder, 't', 'tt', $this->getTranslationCode());

        parent::prepareCndSubstring($queryBuilder, $value);
    }
}
