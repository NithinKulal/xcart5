<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function payWithAmazon_postRebuildHook_init()
{
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    /** @var \XLite\Model\Payment\Method $method */
    $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findOneBy(['service_name' => 'PayWithAmazon']);


    // configuration
    $configuration = [
        'amazon_pa_sid'          => 'merchant_id',
        'amazon_pa_mode'         => 'mode',
        'amazon_pa_access_key'   => 'access_key',
        'amazon_pa_secret_key'   => 'secret_key',
        'amazon_pa_client_id'    => 'client_id',
        'amazon_pa_currency'     => 'region',
        'amazon_pa_capture_mode' => 'capture_mode',
        'amazon_pa_sync_mode'    => 'sync_mode',
        'amazon_pa_order_prefix' => 'order_id_prefix',
    ];

    $amazonConfig = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon;
    foreach ($configuration as $name => $newName) {
        //$method->setSetting($newName, $amazonConfig->{$name});
        $setting = $method->getSettingEntity($newName);

        if ($setting) {
            $setting->setValue((string) $amazonConfig->{$name});

        } else {
            $setting = new \XLite\Model\Payment\MethodSetting();
            $setting->setName($newName);
            $setting->setValue((string) $amazonConfig->{$name});
            $setting->setPaymentMethod($method);
            $method->addSettings($setting);

            \XLite\Core\Database::getEM()->persist($setting);
        }

    }

    \XLite\Core\Database::getEM()->flush();

    $qb = \XLite\Core\Database::getRepo('XLite\Model\Order')->createQueryBuilder();
    $qb->select($qb->expr()->countDistinct('o.order_id'))
        ->linkLeft('o.details')
        ->andWhere('details.name = :name')
        ->setParameter('name', 'AmazonOrderReferenceId');

    $count = (int) $qb->getSingleScalarResult();

    return $count ? [0, (int) $qb->getSingleScalarResult()] : null;
}

function payWithAmazon_postRebuildHook_step($state)
{
    list($position, $count) = $state;

    $qb = \XLite\Core\Database::getRepo('XLite\Model\Order')->createQueryBuilder();
    $qb->linkLeft('o.details')
        ->andWhere('details.name = :name')
        ->setParameter('name', 'AmazonOrderReferenceId')
        ->orderBy('o.date', 'DESC')
        ->setFirstResult($position)
        ->setMaxResults(10);

    $orders = $qb->getResult();
    foreach ($orders as $order) {
        $position++;

        payWithAmazon_processOrder($order);
    }

    return $position === $count ? null : [$position, $count];
}

/**
 * @param \XLite\Model\Order $order
 */
function payWithAmazon_processOrder($order)
{
    $amazonOrderReferenceId = $order->getDetailValue('AmazonOrderReferenceId');
    $amazonAuthorizationId  = $order->getDetailValue('amazon_pa_auth_id');
    $authorizationStatus    = $order->getDetailValue('amazon_pa_auth_status');
    $amazonCaptureId        = $order->getDetailValue('amazon_pa_capture_id');
    $captureStatus          = $order->getDetailValue('amazon_pa_capture_status');
    $amazonRefundId         = $order->getDetailValue('amazon_pa_refund_id');
    $refundStatus           = $order->getDetailValue('amazon_pa_refund_status');

    foreach ($order->getPaymentTransactions() as $transaction) {
        \XLite\Core\Database::getEM()->remove($transaction);
    }
    $order->getPaymentTransactions()->clear();
    \XLite\Core\Database::getEM()->flush();

    switch ($order->getPaymentStatusCode()) {
        case \XLite\Model\Order\Status\Payment::STATUS_DECLINED:
            if (null === $amazonCaptureId) {
                // 1a
                // transaction: auth, failed
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);

                // transactionData: $authorizationStatus, $amazonAuthorizationId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');

                // backendTransaction: auth, failed
                $backendTransaction = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $backendTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);

            } else {
                // 5
                // transaction: auth, failed
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);

                // transactionData: $authorizationStatus ($captureStatus), $amazonAuthorizationId, $amazonCaptureId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $captureStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');
                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');

                // backendTransaction: auth, success
                $backendTransaction1 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $backendTransaction1->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // backendTransaction: capture, failed
                $backendTransaction2 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE);
                $backendTransaction2->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);
            }
            break;
        case \XLite\Model\Order\Status\Payment::STATUS_QUEUED:
            $client               = \XLite\Module\Amazon\PayWithAmazon\Main::getClient();
            $authorisationDetails = $client->getAuthorizationDetails([
                'amazon_authorization_id' => $amazonAuthorizationId,
            ]);
            $captureNow           = isset($authorisationDetails['AuthorizationDetails']['CaptureNow'])
                ? $authorisationDetails['AuthorizationDetails']['CaptureNow']
                : [];

            if ('true' !== $captureNow) {
                // 2a
                // transaction: auth, pending
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_PENDING);

                // transactionData: $authorizationStatus, $amazonAuthorizationId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');

                // backendTransaction: auth, inProgress
                $backendTransaction = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $backendTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_INPROGRESS);
            } else {
                // 2b
                // transaction: sale, pending
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_PENDING);

                // transactionData: $authorizationStatus, $amazonAuthorizationId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');

                // backendTransaction: sale, inProgress
                $backendTransaction = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
                $backendTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_INPROGRESS);
            }

            break;
        case \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED:
            // 3
            // transaction: auth, success
            $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            // transactionData: $authorizationStatus, $amazonAuthorizationId
            $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
            $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
            $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');

            // backendTransaction: auth, success
            $backendTransaction = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
            $backendTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            break;
        case \XLite\Model\Order\Status\Payment::STATUS_PAID:
            if (null === $captureStatus) {
                // 4
                // transaction: sale, success
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // transactionData: $authorizationStatus, $amazonAuthorizationId, $amazonCaptureId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');
                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');

                // backendTransaction: sale, success
                $backendTransaction = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
                $backendTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            } else {
                // 6
                // transaction: auth, success
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // transactionData: $authorizationStatus, $amazonAuthorizationId, $amazonCaptureId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');
                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');

                // backendTransaction: auth, success
                $backendTransaction1 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $backendTransaction1->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // backendTransaction: capture, success
                $backendTransaction2 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE);
                $backendTransaction2->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            }
            break;
        case \XLite\Model\Order\Status\Payment::STATUS_CANCELED:
            // 7
            // transaction: auth, void
            $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);

            // transactionData: $authorizationStatus, $amazonAuthorizationId
            $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
            $transaction->setDataCell('authorizationStatus', $authorizationStatus, 'Current status of the authorization');
            $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');

            // backendTransaction: auth, success
            $backendTransaction1 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
            $backendTransaction1->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            // backendTransaction: void, success
            $backendTransaction2 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID);
            $backendTransaction2->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            break;
        case \XLite\Model\Order\Status\Payment::STATUS_REFUNDED:
            if (null === $captureStatus) {
                // 8b
                // transaction: sale, success
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // transactionData: $authorizationStatus ($refundStatus), $amazonAuthorizationId, $amazonCaptureId, $amazonRefundId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $refundStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');
                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                $transaction->setDataCell('amazonRefundId', $amazonRefundId, 'AmazonRefundId identifier');

                // backendTransaction: sale, success
                $backendTransaction1 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
                $backendTransaction1->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // backendTransaction: refund, success
                $backendTransaction2 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND);
                $backendTransaction2->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            } else {
                // 8a
                // transaction: auth, success
                $transaction = payWithAmazon_addTransactionToOrder($order, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // transactionData: $authorizationStatus ($refundStatus), $amazonAuthorizationId, $amazonCaptureId, $amazonRefundId
                $transaction->setDataCell('amazonOrderReferenceId', $amazonOrderReferenceId, 'Amazon order reference ID');
                $transaction->setDataCell('authorizationStatus', $refundStatus, 'Current status of the authorization');
                $transaction->setDataCell('amazonAuthorizationId', $amazonAuthorizationId, 'The Amazon-generated identifier for this authorization transaction');
                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                $transaction->setDataCell('amazonRefundId', $amazonRefundId, 'AmazonRefundId identifier');

                // backendTransaction: auth, success
                $backendTransaction1 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
                $backendTransaction1->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // backendTransaction: capture, success
                $backendTransaction2 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE);
                $backendTransaction2->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

                // backendTransaction: refund, success
                $backendTransaction3 = $transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND);
                $backendTransaction3->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            }
            break;
    }
}

function payWithAmazon_addTransactionToOrder($order, $type)
{
    $method = \XLite\Module\Amazon\PayWithAmazon\Main::getMethod();

    $transaction = new \XLite\Model\Payment\Transaction();

    $transaction->setOrder($order);
    $transaction->setPaymentMethod($method);
    $transaction->setValue($order->getTotal());
    $transaction->setType($type);

    \XLite\Core\Database::getEM()->persist($transaction);

    return $transaction;
}

return function ($state) {
    if (null === $state) {
        return payWithAmazon_postRebuildHook_init();
    }

    return payWithAmazon_postRebuildHook_step($state);
};
