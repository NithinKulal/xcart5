<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\ItemsList\Model;

/**
 * Order items list
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Postprocess inserted entity
     *
     * @param \XLite\Model\OrderItem $entity OrderItem entity
     * @param array                  $line   Array of entity data from request
     *
     * @return boolean
     */
    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        $result = parent::postprocessInsertedEntity($entity, $line);

        if ($result && \XLite\Controller\Admin\Order::isNeedProcessStock()) {

            // Process PIN codes on order save
            $order = $this->getOrder();

            if ($order->isProcessed()) {
                $order->processPINCodes();
            }
        }

        return $result;
    }
}
