<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;

/**
 * Order repository
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    public function findDumpOrder()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->paymentStatus = [
            \XLite\Model\Order\Status\Payment::STATUS_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
        ];
        $cnd->orderBy = ['o.date', 'desc'];
        $cnd->limit = [0, 1];

        $result = $this->search($cnd);

        if (count($result) === 0) {
            unset($cnd->paymentStatus);

            $result = $this->search($cnd);
        }

        return count($result) ? $result[0] : null;
    }
}
