<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use XLite\Module\XC\MailChimp\Core\MailChimp;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use \XLite\Module\XC\MailChimp\Logic\UploadingData;

/**
 * Class MailchimpStoreData
 */
class MailchimpStoreData extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isCheckProcessNotFinished()
    {
        $eventName = UploadingData\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS)
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(
                UploadingData\Generator::getCancelFlagVarName()
            );
    }

    /**
     * @inheritDoc
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'startUploadProducts',
                'startUploadOrders',
                'startUploadAll',
            ]
        );
    }

    public function doActionUpdateStores()
    {
        $storeName = MailChimp::getInstance()->getStoreName();

        $lists = \XLite\Core\Request::getInstance()->lists ?: [];

        foreach ($lists as $listId => $value) {
            $storeId = MailChimp::getInstance()->getStoreId($listId);
            if ($storeId) {
                $ecCore = MailChimpECommerce::getInstance();
                if (!$ecCore->getStore($storeId)) {
                    $ecCore->createStore(
                        [
                            'campaign_id'   => '',
                            'store_id'      => $storeId,
                            'store_name'    => $storeName,
                            'currency_code' => \XLite::getInstance()->getCurrency()->getCode(),
                            'is_main'       => $value
                        ],
                        $listId
                    );
                } else {
                    $existingStore = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\Store')->find($storeId);
                    if ($existingStore) {
                        $existingStore->setMain($value);
                    } else {
                        $ecCore->createStoreReference(
                            $listId,
                            $storeId,
                            $storeName,
                            $value
                        );
                    }
                }  
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }
    
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadAll()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run([
            'steps' => [
                'products',
                'orders',
            ],
            'lists' => $lists
        ]);
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadProducts()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run([
            'steps' => [
                'products',
            ],
            'lists' => $lists
        ]);
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionStartUploadOrders()
    {
        $lists = \XLite\Core\Request::getInstance()->lists;

        UploadingData\Generator::run([
            'steps' => [
                'orders',
            ],
            'lists' => $lists
        ]);
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        UploadingData\Generator::cancel();

        $this->setReturnURL($this->buildURL('mailchimp_store_data'));
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->process_completed) {
            \XLite\Core\TopMessage::addInfo('Uploading data has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('mailchimp_store_data')
            );

        } elseif ($request->process_failed) {
            \XLite\Core\TopMessage::addError('Uploading data has been stopped.');

            $this->setReturnURL(
                $this->buildURL('mailchimp_store_data')
            );
        }
    }
}
