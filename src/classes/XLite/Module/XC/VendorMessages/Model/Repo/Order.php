<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model\Repo;

/**
 * Order repository
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Allowable search params
     */
    const SEARCH_MESSAGES          = 'messages';
    const SEARCH_MESSAGE_SUBSTRING = 'messageSubstring';

    /**
     * @inheritdoc
     */
    protected function getHandlingSearchParams()
    {
        $list = parent::getHandlingSearchParams();
        $list[] = static::SEARCH_MESSAGES;
        $list[] = static::SEARCH_MESSAGE_SUBSTRING;

        return $list;
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param integer                                 $value        Condition data
     *
     * @return void
     */
    protected function prepareCndMessages(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            switch ($value) {
                case 'U':
                    $queryBuilder->linkInner('o.messages')
                        ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                        ->linkLeft('messages.readers', 'r1')
                        ->andHaving('COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
                        ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile());
                    break;

                case 'A':
                    $queryBuilder->linkInner('o.messages');
                    break;

                default:
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param string                                  $value        Condition data
     */
    protected function prepareCndMessageSubstring(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner('o.messages')
                ->andWhere('messages.body LIKE :message_substring')
                ->setParameter('message_substring', '%' . $value . '%');
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (is_array($value) && $value[0] == 'read_messages') {
            $queryBuilder->linkInner('o.messages')
                ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                ->addSelect('IF(COUNT(messages) = SUM(IF(r0.id IS NULL, 0, 1)), 1, 0) read_order')
                ->addOrderBy('read_order', 'asc')
                ->addOrderBy('o.date', 'desc')
                ->setParameter('reader', $value[2]);

        } else {
            parent::prepareCndOrderBy($queryBuilder, $value);
        }
    }
}
