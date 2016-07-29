<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
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
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias = 'xpc';

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

    /**
     * Search result routine.
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    protected function searchResult()
    {
        $qb = $this->searchState['queryBuilder'];
        $qb->addOrderBy('o.order_id', 'DESC');

        return parent::searchResult();
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
        if ($value) {
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

