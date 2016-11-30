<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

use XLite\Module\XC\MailChimp\Logic\DataMapper\Cart;
use \XLite\Module\XC\MailChimp\Logic\DataMapper\Order;
use XLite\Module\XC\MailChimp\Main;

require_once LC_DIR_MODULES . 'XC' . LC_DS . 'MailChimp' . LC_DS . 'lib' . LC_DS . 'MailChimp.php';

/**
 * MailChimp core class
 */
class MailChimp extends \XLite\Base\Singleton
{
    const SUBSCRIPTION_FIELD_NAME = 'subscribe';
    const SUBSCRIPTION_TO_ALL_FIELD_NAME = 'subscribeToAll';

    const MC_FIRST_NAME = 'FNAME';
    const MC_LAST_NAME  = 'LNAME';

    /**
     * MailChimp API class
     *
     * @var \XLite\Module\XC\MailChimp\Core\MailChimpLoggableAPI
     */
    protected $mailChimpAPI = null;

    /**
     * Check if current subscription select type is select box
     *
     * @return boolean
     */
    public static function isSelectBoxElement()
    {
        return \XLite\Module\XC\MailChimp\View\FormField\Select\ElementType::SELECT
        == \XLite\Core\Config::getInstance()->XC->MailChimp->subscriptionElementType;
    }

    /**
     * Check if module has API key populated
     *
     * @return boolean
     */
    public static function hasAPIKey()
    {
        return \XLite\Core\Config::getInstance()->XC
            && \XLite\Core\Config::getInstance()->XC->MailChimp
            && \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey;
    }

    /**
     * @return mixed
     */
    public function getStoreName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * @param string $listId
     *
     * @return bool|string
     */
    public function getStoreId($listId = null)
    {
        $rawId  = \Includes\Utils\URLManager::getShopURL();
        $rawId .= static::getInstance()->getStoreName();
        $rawId .= $listId ?: 'no_list_id';

        return md5($rawId);
    }

    /**
     * @param $campId
     *
     * @return bool|string
     */
    public function getStoreIdByCampaign($campId)
    {
        return static::getInstance()->getStoreId(
            static::getInstance()->getListIdByCampaignId($campId)
        );
    }

    /**
     * @param $campId
     *
     * @return bool
     */
    public function getListIdByCampaignId($campId)
    {
        $campaignInfo = MailChimpECommerce::getInstance()->getCampaign($campId);

        if(!$campaignInfo
            || !isset($campaignInfo['recipients'])
            || !$campaignInfo['recipients']
        ) {
            return false;
        }

        $list = isset($campaignInfo['recipients']['list_id'])
            ? $campaignInfo['recipients']
            : $campaignInfo['recipients'][0];

        return isset($list['list_id'])
            ? $list['list_id']
            : null;
    }
    
    /**
     * Subscribe profile to all lists
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     *
     * @throws MailChimpException
     */
    public static function processSubscriptionAll(\XLite\Model\Profile $profile)
    {
        if (!\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured()) {
            return;
        }

        static::processSubscriptionInput(
            $profile,
            static::getAllListsDataToSubscribe(),
            static::getAllGroupNamesToSubscribe()
        );
    }

    /**
     * Subscribe/unsubscribe profile based on the form input data
     *
     * @param \XLite\Module\XC\MailChimp\Model\Profile $profile Profile
     * @param array|string         $data    Subscriptions data
     *
     * @return void
     *
     * @throws MailChimpException
     */
    public static function processSubscriptionInput(\XLite\Model\Profile $profile, $data, $interests = null)
    {
        if (!\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured() || !$data) {
            return;
        }

        if (!is_null($profile)) {
            $currentlySubscribed = $profile->getMailChimpListsIds();

            if (self::isSelectBoxElement()) {
                $tmpData = array();

                foreach ($currentlySubscribed as $listId) {
                    $tmpData[$listId] = '';
                }

                if (!empty($data)) {
                    if (!is_array($data)) {
                        $data = array($data => 1);
                    }

                    foreach ($data as $key => $value) {
                        $tmpData[$key] = $value;
                    }
                }

                $data = $tmpData;
            }

            $toSubscribe = array();
            $toUnsubscribe = array();
            $listGroupToSet = array();

            if (!$interests || !is_array($interests)) {
                $interests = \XLite\Core\Request::getInstance()->interest;
            }

            foreach ($data as $listId => $v) {

                if (
                    1 == $v
                    && !in_array($listId, $currentlySubscribed)
                ) {
                    $toSubscribe[] = $listId;
                } elseif (
                    1 != $v
                    && in_array($listId, $currentlySubscribed)
                ) {
                    $toUnsubscribe[] = $listId;
                }
                
                if (isset($interests[$listId]) && is_array($interests[$listId])) {
                    $listGroupToSet[$listId] = $interests[$listId];
                }
            }

            try {
                if (!empty($toUnsubscribe)) {
                    $profile->doUnsubscribeFromMailChimpLists($toUnsubscribe);
                }

                if (!empty($toSubscribe)) {
                    $profile->doSubscribeToMailChimpLists($toSubscribe);

                    $profile->checkSegmentsConditions();
                }

                foreach ($listGroupToSet as $listId => $values) {
                    $profile->checkGroupsConditions($listId, $values);
                }

            } catch (\Exception $e) {
                throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /**
     * Get the error message from exception
     *
     * @param MailChimpException $e Exception
     *
     * @return string
     */
    public static function getMessageTextFromError(MailChimpException $e)
    {
        $message = $e->getMessage();

        if (
            strpos($message, self::MC_FIRST_NAME) !== false
            || strpos($message, self::MC_LAST_NAME) !== false
        ) {
            $message .= "\n<br />\n" . \XLite\Core\Translation::getInstance()->translate('First name or last name are empty. Please add a new address to your address book or modify existing and fill in those fields in order to subscribe to this list.');
        }

        return $message;
    }

    /**
     * Update MailChimp lists
     *
     * @return void
     */
    public function updateMailChimpLists()
    {
        $mailChimpLists = $this->getMailChimpLists();

        \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->updateLists($mailChimpLists['lists']);
    }

    /**
     * Check if current MailChimp lists has removed list
     *
     * @return boolean
     */
    public function hasRemovedMailChimpLists()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->hasRemovedMailChimpLists();
    }

    /**
     * Get MailChimp Lists
     *
     * @return array
     *
     * @throws MailChimpException
     */
    public function getMailChimpLists()
    {
        try{
            return $this->mailChimpAPI->get('lists');
        } catch (\Exception $e) {
            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get MailChimp automations
     *
     * @return array
     *
     * @throws MailChimpException
     */
    public function getAutomations()
    {
        try{
            return $this->mailChimpAPI->get('automations');
        } catch (\Exception $e) {
            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get MailChimp automation emails
     *
     * @return array
     *
     * @throws MailChimpException
     */
    public function getAutomationEmails($automationId)
    {
        try{
            return $this->mailChimpAPI->get("automations/{$automationId}/emails");
        } catch (\Exception $e) {
            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Trigger MailChimp automation email
     *
     * @throws MailChimpException
     */
    public function triggerAutomationEmail($automationId, $automationEmailId, $email)
    {
        try{
            $url = "automations/{$automationId}/emails/{$automationEmailId}/queue";
            $data = [
                'email_address' => $email
            ];

            return $this->mailChimpAPI->post($url, $data);

        } catch (\Exception $e) {
            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Subscriptions data
     *
     * @return array
     */
    public static function getAllListsDataToSubscribe()
    {
        $result = array();

        $cnd = new \XLite\Core\CommonCell();

        $cnd->enabled = true;
        $cnd->subscribeByDefault = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'subscribeByDefault',
            true
        );

        $allLists = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->search($cnd);

        foreach ($allLists as $list) {
            $result[$list->getId()] = 1;
        }

        return $result;
    }

    /**
     * Groups data
     *
     * @return array
     */
    public static function getAllGroupNamesToSubscribe()
    {
        $result = array();

        $cnd = new \XLite\Core\CommonCell();

        $cnd->enabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'enabled',
            true
        );
        $cnd->subscribeByDefault = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'subscribeByDefault',
            true
        );
        $cnd->groupEnabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.enabled',
            true
        );
        $cnd->listSubscribedByDefault = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.list.subscribeByDefault',
            true
        );
        $cnd->listEnabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'group.list.enabled',
            true
        );

        $all = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpGroupName')
            ->search($cnd);

        foreach ($all as $item) {
            $listId = $item->getGroup()->getList()->getId();
            if (!isset($result[$listId])) {
                $result[$listId] = [];
            }
            
            $result[$listId][$item->getId()] = 1;
        }
        
        return $result;
    }

    /**
     * Subscribe email to MailChimp list
     *
     * @param string $id    MailChimp list ID
     * @param string $email E-mail
     *
     * @return array
     */
    public function doSubscribe($id, $email, $firstName, $lastName)
    {
        $hash = md5(mb_strtolower($email));

        $data = [
            'email_type'        => 'html',
            'email_address'     => $email,
            'status'            => \XLite\Core\Config::getInstance()->XC->MailChimp->doubleOptinDisabled
                ? 'subscribed'
                : 'pending',
            'merge_fields' => [
                self::MC_FIRST_NAME => $firstName,
                self::MC_LAST_NAME  => $lastName,
            ],
        ];

        $this->mailChimpAPI->setActionMessageToLog('Profile subscription');
        return $this->mailChimpAPI->put("lists/{$id}/members/{$hash}", $data);
    }

    /**
     * Unsubscribe email to MailChimp list
     *
     * @param string $id    MailChimp list ID
     * @param string $email E-mail
     *
     * @return array
     */
    public function doUnsubscribe($id, $email)
    {
        $hash = md5(mb_strtolower($email));

        $this->mailChimpAPI->setActionMessageToLog('Profile unsubscription');
        return $this->mailChimpAPI->delete("lists/{$id}/members/{$hash}");
    }

    /**
     * Create batch
     *
     * @param array  $operations Operations
     *
     * @return array
     */
    public function batch($operations)
    {
        return $this->mailChimpAPI->post('batches', [ 'operations' => $operations ]);
    }

    /**
     * Create batch
     *
     * @param string  $id Batch id
     *
     * @return array
     */
    public function getBatch($id)
    {
        $result = $this->mailChimpAPI->get("batches/{$id}");

        return $this->mailChimpAPI->success()
            ? $result
            : null;
    }

    /**
     * Subscribe batch
     *
     * @param string $id     MailChimp list ID
     * @param array  $emails E-mails
     *
     * @return array
     */
    public function doSubscribeBatch($id, array $emails)
    {
        $data = array();

        foreach ($emails as $subscribeData) {
            $hash = md5(mb_strtolower($subscribeData['email']));

            $data[] = [
                'method'    => "PUT",
                'path'      => "lists/{$id}/members/{$hash}",
                'body'      => json_encode([
                    'email_type'        => 'html',
                    'email_address'     => $subscribeData['email'],
                    'status'            => \XLite\Core\Config::getInstance()->XC->MailChimp->doubleOptinDisabled
                        ? 'subscribed'
                        : 'pending',
                    'merge_fields' => [
                        self::MC_FIRST_NAME => $subscribeData['firstName'],
                        self::MC_LAST_NAME  => $subscribeData['lastName'],
                    ],
                ]),
            ];
        }

        return $this->mailChimpAPI->post('batches', [ 'operations' => $data ]);
    }

    /**
     * Unsubscribe batch
     *
     * @param string $id     MailChimp list ID
     * @param array  $emails E-mails
     *
     * @return array
     */
    public function doUnsubscribeBatch($id, array $emails)
    {
        $data = array();

        foreach ($emails as $subscribeData) {
            $hash = md5(mb_strtolower($subscribeData['email']));

            $data[] = [
                'method'    => "DELETE",
                'path'      => "lists/{$id}/members/{$hash}",
            ];
        }

        return $this->mailChimpAPI->post('batches', [ 'operations'=> $data ]);
    }

    /**
     * Send ECommerce360 cart data
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function createOrUpdateCart(\XLite\Model\Cart $cart)
    {
        $storeName = static::getInstance()->getStoreName();
        $storeId = static::getInstance()->getStoreIdByCampaign(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID}
        );

        $data = Cart::getDataByCart(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_TRACKING_CODE},
            $cart,
            MailChimpECommerce::getInstance()->isCustomerExists(
                $storeId,
                $cart->getProfile()->getProfileId()
                    ?: \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID}
            )
        );
        $storeData = [
            'campaign_id'   => $data['campaign_id'],
            'store_id'      => $storeId,
            'store_name'    => $storeName,
            'currency_code' => $data['currency_code']
        ];

        $ecCore = MailChimpECommerce::getInstance();

        // Create store if not exists
        if (!$ecCore->getCart($storeId, $cart->getOrderId())) {
            $result = $this->execCartRelatedRequest(
                "ecommerce/stores/{$storeId}/carts",
                $data,
                $cart->getItems(),
                $storeData,
                false
            );

        } else {
            $cartId = $data['id'];

            $result = $this->execCartRelatedRequest(
                "ecommerce/stores/{$storeId}/carts/{$cartId}",
                $data,
                $cart->getItems(),
                $storeData,
                true
            );
        }
        
        return $result;
    }

    /**
     * Send ECommerce360 cart data
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function createCart(\XLite\Model\Cart $cart)
    {
        $storeName = static::getInstance()->getStoreName();
        $storeId = static::getInstance()->getStoreIdByCampaign(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID}
        );

        $data = Cart::getDataByCart(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_TRACKING_CODE},
            $cart,
            MailChimpECommerce::getInstance()->isCustomerExists(
                $storeId,
                \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID}
            )
        );

        $this->mailChimpAPI->setActionMessageToLog('Creating cart');
        $result = $this->execOrderRelatedRequest(
            "ecommerce/stores/{$storeId}/carts",
            $data,
            $cart->getItems(),
            [
                'campaign_id'   => $data['campaign_id'],
                'store_id'      => $storeId,
                'store_name'    => $storeName,
                'currency_code' => $data['currency_code']
            ]
        );

        return $result;
    }

    /**
     * Remove ECommerce360 cart data
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function removeCart(\XLite\Model\Cart $cart)
    {
        $storeId = static::getInstance()->getStoreIdByCampaign(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID}
        );

        $this->mailChimpAPI->setActionMessageToLog('Removing cart');
        return MailChimpECommerce::getInstance()->removeCart(
            $storeId,
            $cart->getOrderId()
        );
    }

    /**
     * Send ECommerce360 order data
     *
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public function createOrder(\XLite\Model\Order $order)
    {
        $storeName = static::getInstance()->getStoreName();
        $storeId = static::getInstance()->getStoreIdByCampaign(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID}
        );

        $orderData = Order::getDataByOrder(
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_CAMPAIGN_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID},
            \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_TRACKING_CODE},
            $order,
            MailChimpECommerce::getInstance()->isCustomerExists(
                $storeId,
                \XLite\Core\Request::getInstance()->{Request::MAILCHIMP_USER_ID}
            )
        );

        $this->mailChimpAPI->setActionMessageToLog('Creating order');
        return $this->execOrderRelatedRequest(
            "ecommerce/stores/{$storeId}/orders",
            $orderData,
            $order->getItems(),
            [
                'campaign_id'   => $orderData['campaign_id'],
                'store_id'      => $storeId,
                'store_name'    => $storeName,
                'currency_code' => $orderData['currency_code']
            ]
        );
    }

    /**
     * @param string                   $url
     * @param array                    $data
     * @param \XLite\Model\OrderItem[] $lines
     * @param array                    $storeData
     * @param bool                     $update
     *
     * @return array|bool|false
     */
    public function execCartRelatedRequest($url, $data, $lines, $storeData, $update = false)
    {
        $ecCore = MailChimpECommerce::getInstance();

        // Create products
        foreach ($lines as $item) {
            $ecCore->createProductFast($storeData['store_id'], $item->getObject());
        }


        $this->mailChimpAPI->setActionMessageToLog($update ? 'Cart updated' : 'Cart created');
        $result = $update
            ? $this->mailChimpAPI->patch($url, $data)
            : $this->mailChimpAPI->post($url, $data);

        return $this->mailChimpAPI->success()
            ? $result
            : null;
    }

    /**
     * @param string                   $url
     * @param array                    $data
     * @param \XLite\Model\OrderItem[] $lines
     * @param array                    $storeData
     * @param bool                     $update
     *
     * @return array|bool|false
     */
    public function execOrderRelatedRequest($url, $data, $lines, $storeData, $update = false)
    {
        $ecCore = MailChimpECommerce::getInstance();

        $storeId  = $storeData['store_id'];

        // Create store if not exists
        if (!$ecCore->isStoreExists($storeId)) {
            $ecCore->createStore($storeData);
        }

        // Create products if not exists
        foreach ($lines as $item)
        {
            $productId = $item->getObject() ? $item->getObject()->getProductId() : $item->getItemId;
            $product = $ecCore->isProductExists($storeId, $productId);

            if ($item->getObject()
                && (!$product
                    || \DateTime::createFromFormat('c', $product['published_at_foreign']) < $item->getObject()->getUpdateDate()
                )
            ) {
                if ($product) {
                    $ecCore->removeProduct($storeId, $productId);
                }

                $this->mailChimpAPI->setActionMessageToLog($product ? 'Product updated' : 'Product created');
                $ecCore->createProductFast($storeId, $item->getObject());
            }
        }


        $this->mailChimpAPI->setActionMessageToLog($update ? 'Order/Cart updated' : 'Order/Cart created');
        $result = $update
            ? $this->mailChimpAPI->patch($url, $data)
            : $this->mailChimpAPI->post($url, $data);

        return $this->mailChimpAPI->success()
            ? $result 
            : null;
    }

    /**
     * Get segments
     *
     * @param string $listId MailChimp list ID
     *
     * @return array
     */
    public function getSegments($listId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Get segments');
        $segments = $this->mailChimpAPI->get("lists/{$listId}/segments");
        
        if (!$segments) {
            return [];
        }

        $segments = $segments['segments'];

        return [
            'static'    => array_filter($segments, function($segment) {
                return $segment['type'] === 'static';
            }),
            'saved'    => array_filter($segments, function($segment) {
                return $segment['type'] === 'saved';
            }),
            'fuzzy'    => array_filter($segments, function($segment) {
                return $segment['type'] === 'fuzzy';
            }),
        ];
    }

    /**
     * Add email to list segment
     *
     * @param string $listId    MailChimp list ID
     * @param string $segmentId Segment ID
     * @param array  $emails    Emails
     *
     * @return array
     */
    public function addToSegment($listId, $segmentId, array $emails)
    {
        $batch = array();

        foreach ($emails as $email) {
            $batch[] = [ 'email' => $email ];
        }

        $this->mailChimpAPI->setActionMessageToLog('Adding to segments');
        return $this->mailChimpAPI->post("lists/{$listId}/segments/{$segmentId}", [
            'members_to_add'    => $batch
        ]);
    }

    /**
     * Add interests to a member
     *
     * @param       $listId
     * @param       $subscriberEmail
     * @param array $interests
     *
     * @return array|false
     */
    public function addInterestsToMember($listId, $subscriberEmail, array $interests)
    {
        $subscriberHash = md5(mb_strtolower($subscriberEmail));

        $this->mailChimpAPI->setActionMessageToLog('Profile subscribing to group');
        return $this->mailChimpAPI->patch("lists/{$listId}/members/{$subscriberHash}", [
            'interests'    => $interests
        ]);
    }

    /**
     * Get groups
     *
     * @param string $listId MailChimp list ID
     *
     * @return array
     */
    public function getGroups($listId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Getting groups');
        $groups = $this->mailChimpAPI->get("lists/{$listId}/interest-categories");

        if (!$groups) {
            return [];
        }

        return $groups['categories'];
    }

    /**
     * Get group names
     *
     * @param string $listId    MailChimp list ID
     * @param string $groupId   MailChimp group ID
     *
     * @return array
     */
    public function getGroupNames($listId, $groupId)
    {
        $this->mailChimpAPI->setActionMessageToLog('Getting group interests');
        $names = $this->mailChimpAPI->get("lists/{$listId}/interest-categories/{$groupId}/interests");

        if (!$names) {
            return [];
        }

        return $names['interests'];
    }

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     *
     * @throws MailChimpException
     */
    protected function __construct()
    {
        parent::__construct();

        try {
            $this->mailChimpAPI = new \XLite\Module\XC\MailChimp\Core\MailChimpLoggableAPI(
                \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey
            );

        } catch (\Exception $e) {
            if (
                MailChimpException::MAILCHIMP_NO_API_KEY_ERROR == $e->getMessage()
                && \XLite::isAdminZone()
            ) {
                \XLite\Core\TopMessage::addError($e->getMessage());

                \XLite\Core\Operator::redirect(
                    \XLite\Core\Converter::buildURL('mailchimp_options')
                );
            }

            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
