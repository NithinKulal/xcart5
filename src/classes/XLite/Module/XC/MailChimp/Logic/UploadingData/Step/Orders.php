<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData\Step;

use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;

class Orders extends AStep
{
    /**
     * Process models
     *
     * @param \XLite\Model\AEntity[] $models Models
     *
     * @return void
     */
    protected function processBatch(array $models)
    {
        /** @var \XLite\Model\Order $model */

        foreach ($this->getStores() as $storeId) {
            $result = MailChimpECommerce::getInstance()->createOrdersBatch(
                $storeId,
                $models
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function __construct(\XLite\Logic\AGenerator $generator)
    {
        parent::__construct($generator);

        if ($generator) {
            $this->getRepository()->setExportFilter(
                static::getLastYearFilter()
            );
        }
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order');
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected static function getLastYearFilter()
    {
        $start = new \DateTime("-1 year");

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Order::P_DATE} = array($start->getTimestamp());

        return $cnd;
    }

}