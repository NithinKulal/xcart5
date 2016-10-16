<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\Model\Repo;

/**
 * Repository class for the Offer Type model.
 */
class OfferType extends \XLite\Model\Repo\Base\I18n
{
    const SEARCH_ENABLED = 'enabled';

    /**
     * Allowed sort criteria
     */
    const ORDER_BY_POSITION = 'o.position';
    const ORDER_BY_NAME     = 'o.name';

    /**
     * Returns the list of available offer types.
     * 
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     * 
     * @return array
     */
    public function findActiveOfferTypes($countOnly = false)
    {
        return $this->search(
            $this->getActiveOfferTypesConditions(),
            $countOnly ? static::SEARCH_MODE_COUNT : static::SEARCH_MODE_ENTITIES
        );
    }

    /**
     * Returns search conditions for retrieving active offer types.
     * 
     * @return \XLite\Core\CommonCell
     */
    public function getActiveOfferTypesConditions()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::P_ORDER_BY} = array(static::ORDER_BY_POSITION, 'ASC');
        $cnd->{self::SEARCH_ENABLED} = true;
        
        return $cnd;
    }
    
    /**
     * Prepare Enabled/Disabled search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'o.enabled <> 0' : 'o.enabled = 0');
        }
    }

}