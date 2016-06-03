<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Order history events repository
 * todo: rename to OrderHistoryEvent
 */
class OrderHistoryEvents extends \XLite\Model\Repo\ARepo
{
    /**
     * Returns events by given order
     *
     * @param integer|\XLite\Model\Order $order Order
     *
     * @return \XLite\Model\OrderHistoryEvents[]
     */
    public function findAllByOrder($order)
    {
        return $this->defineFindAllByOrder($order)->getResult();
    }

    /**
     * Register event to the order
     *
     * @param integer $orderId     Order identificator
     * @param string  $code        Event code
     * @param string  $description Event description
     * @param array   $data        Data for event description OPTIONAL
     * @param string  $comment     Event comment OPTIONAL
     * @param array   $details     Event details OPTIONAL
     *
     * @return void
     */
    public function registerEvent($orderId, $code, $description, array $data = array(), $comment = '', $details = array())
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if (!$order->isRemoving()) {
            $event = new \XLite\Model\OrderHistoryEvents(
                array(
                    'date'         => \XLite\Core\Converter::time(),
                    'code'         => $code,
                    'description'  => $description,
                    'data'         => $data,
                    'comment'      => $comment,
                )
            );

            if (!empty($details)) {
                $event->setDetails($details);
            }

            if (\XLite\Core\Auth::getInstance()->getProfile()) {
                $event->setAuthor(\XLite\Core\Auth::getInstance()->getProfile());
            }

            $event->setOrder($order);

            $order->addEvents($event);

            $this->insert($event);
        }
    }

    /**
     * Returns query builder for findAllByOrder method
     *
     * @param integer|\XLite\Model\Order $order Order
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllByOrder($order)
    {
        return $this->createQueryBuilder()
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->addOrderBy('o.date', 'DESC');
    }
}
