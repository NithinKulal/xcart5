<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model\Repo;

/**
 * Order repository
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function prepareCndMessages(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && \XLite\Module\XC\VendorMessages\Main::isWarehouse()) {
            if (!empty($value)) {
                switch ($value) {
                    case 'U':
                        if (\XLite\Core\Auth::getInstance()->isVendor()) {
                            $queryBuilder->linkInner('o.children')
                                ->linkInner('children.messages', 'cmessages')
                                ->linkLeft('cmessages.readers', 'r0c', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0c.reader = :reader')
                                ->linkLeft('cmessages.readers', 'r1c')
                                ->andWhere('children.vendor = :vendor')
                                ->andHaving(
                                    'COUNT(r1c.id) != SUM(IF(r0c.id IS NULL, 0, 1)) OR COUNT(r1c.id) = 0'
                                )
                                ->setParameter('vendor', \XLite\Core\Auth::getInstance()->getProfile())
                                ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile());

                        } else {
                            $queryBuilder->linkLeft('o.messages')
                                ->linkLeft('o.children')
                                ->linkLeft('children.messages', 'cmessages')
                                ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                                ->linkLeft('messages.readers', 'r1')
                                ->linkLeft('cmessages.readers', 'r0c', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0c.reader = :reader2')
                                ->linkLeft('cmessages.readers', 'r1c')
                                ->andHaving(
                                    '(COUNT(messages) > 0 AND (COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0)) '
                                    . 'OR (COUNT(cmessages) > 0 AND (COUNT(r1c.id) != SUM(IF(r0c.id IS NULL, 0, 1)) OR COUNT(r1c.id) = 0))'
                                )
                                ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile())
                                ->setParameter('reader2', \XLite\Core\Auth::getInstance()->getProfile());
                        }
                        break;

                    case 'A':
                        if (\XLite\Core\Auth::getInstance()->isVendor()) {
                            $queryBuilder->linkInner('o.children', 'children', \Doctrine\ORM\Query\Expr\Join::WITH, 'children.vendor = :vendor')
                                ->linkInner('children.messages', 'cmessages')
                                ->setParameter('vendor', \XLite\Core\Auth::getInstance()->getProfile());

                        } else {
                            $queryBuilder->linkLeft('o.messages')
                                ->linkLeft('o.children')
                                ->linkLeft('children.messages', 'cmessages')
                                ->andWhere('messages.id IS NOT NULL OR cmessages.id IS NOT NULL');
                        }
                        break;

                    case 'D':
                        if (\XLite\Core\Auth::getInstance()->isVendor()) {
                            $queryBuilder->linkInner('o.children')
                                ->linkInner('children.messages', 'cmessages')
                                ->andWhere('children.is_opened_dispute = :opened_dispute')
                                ->setParameter('opened_dispute', true);

                        } else {
                            $queryBuilder->linkLeft('o.messages')
                                ->linkLeft('o.children')
                                ->linkLeft('children.messages', 'cmessages')
                                ->andWhere('messages.id IS NOT NULL OR cmessages.id IS NOT NULL')
                                ->andWhere('o.is_opened_dispute = :opened_dispute OR children.is_opened_dispute = :opened_dispute2')
                                ->setParameter('opened_dispute', true)
                                ->setParameter('opened_dispute2', true);
                        }
                        break;

                    default:
                }
            }

        } else {
            parent::prepareCndMessages($queryBuilder, $value);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (
            is_array($value)
            && $value[0] == 'read_messages'
            && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()
            && \XLite\Module\XC\VendorMessages\Main::isWarehouse()
        ) {
            $queryBuilder->linkLeft('o.messages', 'om')
                ->linkLeft('om.readers', 'or0', \Doctrine\ORM\Query\Expr\Join::WITH, 'or0.reader = :reader_order')
                ->linkLeft('o.children', 'oc')
                ->linkLeft('oc.messages', 'omc')
                ->linkLeft('omc.readers', 'orc0', \Doctrine\ORM\Query\Expr\Join::WITH, 'orc0.reader = :reader_order2')
                ->addSelect('IF(COUNT(om) = SUM(IF(or0.id IS NULL, 0, 1)) AND COUNT(omc) = SUM(IF(orc0.id IS NULL, 0, 1)), 1, 0) read_order')
                ->andWhere('om.id IS NOT NULL OR omc.id IS NOT NULL')
                ->addOrderBy('read_order', 'asc')
                ->addOrderBy('o.date', 'desc')
                ->setParameter('reader_order', $value[2])
                ->setParameter('reader_order2', $value[2]);

        } else {
            parent::prepareCndOrderBy($queryBuilder, $value);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareCndMessageSubstring(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && \XLite\Module\XC\VendorMessages\Main::isWarehouse()) {
                if (\XLite\Core\Auth::getInstance()->isVendor()) {
                    $queryBuilder->linkInner('o.children', 'children', \Doctrine\ORM\Query\Expr\Join::WITH, 'children.vendor = :vendor')
                        ->linkInner('children.messages', 'cmessages')
                        ->andWhere('cmessages.body LIKE :message_substring')
                        ->setParameter('vendor', \XLite\Core\Auth::getInstance()->getProfile())
                        ->setParameter('message_substring', '%' . $value . '%');

                } else {
                    $queryBuilder->linkLeft('o.messages')
                        ->linkLeft('o.children')
                        ->linkLeft('children.messages', 'cmessages')
                        ->andWhere('messages.body LIKE :message_substring OR cmessages.body LIKE :message_substring')
                        ->setParameter('message_substring', '%' . $value . '%');
                }

            } else {
                parent::prepareCndMessageSubstring($queryBuilder, $value);
            }
        }
    }

}
