<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
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
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment;

/**
 * Payment backend transaction repository
 */
class XpcTransactionData extends \XLite\Model\Repo\ARepo
{

    /**
     * Allowable search params
     */
    const SEARCH_CARD_ID        = 'cardId'; 
    const SEARCH_PROFILE_ID     = 'profileId';
    const SEARCH_ORDER_ID       = 'orderId';
    const SEARCH_LOGIN          = 'login';
    const SEARCH_RECHARGES_ONLY = 'rechargesOnly';
    const SEARCH_PAYMENT_ACTIVE = 'paymentActive';

    /**
     * Get default alias
     *
     * @return string
     */
    public function getDefaultAlias()
    {
        return 'xpc';
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        if (!isset($alias)) {
            $alias = $this->getDefaultAlias();
        }

        $qb = parent::createQueryBuilder($alias, $indexBy)
            ->linkInner($alias . '.transaction', 'pt')
            ->linkInner('pt.order', 'o')
            ->linkInner('o.profile', 'p')
            ->linkInner('pt.payment_method', 'pm');

        $qb->andWhere('pt.status != :badstatus')
            ->setParameter('badstatus', 'F');

        return $qb;
    }

    // {{{ Search

    /**
     * Common search
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function search(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $queryBuilder = $this->createQueryBuilder();

        $this->currentSearchCnd = $cnd;

        foreach ($this->currentSearchCnd as $key => $value) {
            $this->callSearchConditionHandler($value, $key, $queryBuilder, $countOnly);
        }

        return $countOnly
            ? $this->searchCount($queryBuilder)
            : $this->searchResult($queryBuilder);
    }

    /**
     * Search count only routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchCount(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->select('COUNT(DISTINCT xpc.id)');

        return intval($qb->getSingleScalarResult());
    }

    /**
     * Search result routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchResult(\Doctrine\ORM\QueryBuilder $qb)
    {
        return $qb->addOrderBy('o.order_id', 'DESC')->getResult();
    }

    /**
     * Call corresponded method to handle a search condition
     *
     * @param mixed                      $value        Condition data
     * @param string                     $key          Condition name
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function callSearchConditionHandler($value, $key, \Doctrine\ORM\QueryBuilder $queryBuilder, $countOnly)
    {
        if ($this->isSearchParamHasHandler($key)) {
            $this->{'prepareCnd' . ucfirst($key)}($queryBuilder, $value, $countOnly);
        }
    }

    /**
     * Check if param can be used for search
     *
     * @param string $param Name of param to check
     *
     * @return boolean
     */
    protected function isSearchParamHasHandler($param)
    {
        return in_array($param, $this->getHandlingSearchParams());
    }

    /**
     * Return list of handling search params
     *
     * @return array
     */
    protected function getHandlingSearchParams()
    {
        return array(
            static::SEARCH_CARD_ID,
            static::SEARCH_PROFILE_ID,
            static::SEARCH_LOGIN,
            static::SEARCH_ORDER_ID,
            static::SEARCH_RECHARGES_ONLY,
            static::SEARCH_PAYMENT_ACTIVE,
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndCardId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if (!is_null($value)) {
            $queryBuilder->andWhere('xpc.id = :card_id')
                ->setParameter('card_id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('p.profile_id = :profile_id')
                ->setParameter('profile_id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('o.order_id = :order_id')
                ->setParameter('order_id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndLogin(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('p.login = :login')
                ->setParameter('login', $value);
        }
    }


    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder  Query builder to prepare
     * @param mixed                      $rechargesOnly Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndRechargesOnly(\Doctrine\ORM\QueryBuilder $queryBuilder, $rechargesOnly = false)
    {
        if ($rechargesOnly) {
            $queryBuilder->andWhere('xpc.use_for_recharges = :use_for_recharges')
                ->setParameter('use_for_recharges', 'Y');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder  Query builder to prepare
     * @param mixed                      $rechargesOnly Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndPaymentActive(\Doctrine\ORM\QueryBuilder $queryBuilder, $paymentActive = false)
    {
        if ($paymentActive) {
            $queryBuilder->andWhere('pm.enabled = true');
        }
    }


    // }}}

    // {{{ Helpers

    /**
     * Check - card is valid by profile id
     *
     * @param integer $cardId    Card id
     * @param integer $profileId Profile id
     *
     * @return boolean
     */
    public function isCardValidForProfileId($cardId, $profileId)
    {
        return $this->createQueryBuilder()
            ->andWhere('p.profile_id = :profile_id AND xpc.id = :card_id')
            ->setParameter('profile_id', $profileId)
            ->setParameter('card_id', $cardId)
            ->count();
    }

    /**
     * Check - card is valid by login
     * 
     * @param integer $cardId Card id
     * @param string  $login  Login
     *  
     * @return boolean
     */
    public function isCardValidForLogin($cardId, $login)
    {
        return $this->createQueryBuilder()
            ->andWhere('p.login = :login AND xpc.id = :card_id')
            ->setParameter('login', $login)
            ->setParameter('card_id', $cardId)
            ->count();
    }

    // }}}

    // {{{ Finders

    /**
     * Find by profile id
     *
     * @param integer $profileId        Profile id
     * @param boolean $forRechargesOnly 'For recharges only' flag OPTIONAL
     *
     * @return \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData
     */
    public function findByProfileId($profileId, $forRechargesOnly = false)
    {
        return $this->defineFindByProfileIdQuery($profileId, $forRechargesOnly)->getResult();
    }

    /**
     * Find any by profile id
     *
     * @param integer $profileId        Profile id
     * @param boolean $forRechargesOnly 'For recharges only' flag OPTIONAL
     *
     * @return \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData
     */
    public function findAnyByProfileId($profileId, $forRechargesOnly = false)
    {
        return $this->defineFindByProfileIdQuery($profileId, $forRechargesOnly)->getSingleResult();
    }


    /**
     * Find by login
     *
     * @param string  $login            Login
     * @param boolean $forRechargesOnly 'For recharges only' flag OPTIONAL
     *
     * @return \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData
     */
    public function findByLogin($login, $forRechargesOnly = false)
    {
        return $this->defineFindByLoginQuery($login, $forRechargesOnly)->getResult();
    }

    /**
     * Find any by login 
     * 
     * @param string  $login            Login
     * @param boolean $forRechargesOnly 'For recharges only' flag OPTIONAL
     *  
     * @return \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData
     */
    public function findAnyByLogin($login, $forRechargesOnly = false)
    {
        return $this->defineFindByLoginQuery($login, $forRechargesOnly)->getSingleResult();
    }

    /**
     * Define query for findByProfileId method
     *
     * @param integer $profileId        Profile id
     * @param boolean $forRechargesOnly 'For recharges only' flag
     *
     * @return \XLite\Model\Querybuilder\AQueryBuilder
     */
    protected function defineFindByProfileIdQuery($profileId, $forRechargesOnly)
    {
        $qb = $this->createQueryBuilder($this->getFields())
            ->andWhere('p.profile_id = :profile_id')
            ->setParameter('profile_id', $profileId);

        if ($forRechargesOnly) {
            $qb->andWhere('xpc.use_for_recharges = :y')
                ->setParameter('y', 'Y');
        }

        return $qb;
    }

    /**
     * Define query for findByLogin method
     * 
     * @param string  $login            Login
     * @param boolean $forRechargesOnly 'For recharges only' flag
     *  
     * @return \XLite\Model\Querybuilder\AQueryBuilder
     */
    protected function defineFindByLoginQuery($login, $forRechargesOnly)
    {
        $qb = $this->createQueryBuilder($this->getFields())
            ->andWhere('p.login = :login')
            ->setParameter('login', $login);

        if ($forRechargesOnly) {
            $qb->andWhere('xpc.use_for_recharges = :y')
                ->setParameter('y', 'Y');
        }

        return $qb;
    }

    // }}}
}

