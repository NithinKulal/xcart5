<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData\Step;

use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Logic\DataMapper\Order;

class Orders extends AStep
{
    /**
     * @param $model
     */
    public function addBatchModel($model)
    {
        $model = \XLite\Core\Database::getEM()->merge($model);

        $key = 'batch_data_' . get_class($this);
        $options = $this->generator->getOptions();

        if (!isset($options[$key])) {
            $options[$key] = [];
        }

        $options[$key][] = Order::getDataByOrder(
            null,
            null,
            null,
            $model,
            false
        );

        $this->generator->setOptions($options);
    }

    /**
     * Process models
     *
     * @param array $models Models
     *
     * @return void
     */
    protected function processBatch(array $batchData)
    {
        foreach ($this->getStores() as $storeId) {
            $result = MailChimpECommerce::getInstance()->createOrdersBatchFromMappedData(
                $storeId,
                $batchData
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
