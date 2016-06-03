<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Logic;

/**
 * Pilibaba payment processor
 */
class PilipayOrderAdapter
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            'credentials'   => array(
                'merchantNO'    => '',
                'secretKey'     => '',
            ),
            'urls'          => array(
                'callbackUrl'   => '',
                'returnUrl'     => ''
            ),
            'fees'   => array(
                'shipper'       => 0,
                'tax'           => 0,
            ),
            'orderPrefix'   => '',
        );
    }

    /**
     * Constructor
     *
     * @param \XLite\Model\Order    $order
     * @param array                 $options
     *
     * @return void
     */
    public function __construct(\XLite\Model\Order $order, array $options)
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        $this->order    = $order;
        $this->options  = array_merge(
            $this->getDefaultOptions(),
            $options
        );
    }

    /**
     * Get front checkout url
     *
     * @return string
     */
    protected function getCheckoutUrl()
    {
        return \XLite\Core\Converter::makeURLValid(
            \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'checkout',
                    '',
                    array(),
                    \XLite::getCustomerScript()
                )
            )
        );
    }

    /**
     * Process
     *
     * @return PilipayOrder
     */
    protected function process()
    {
        $order = new \PilipayOrder();
        $order->merchantNO      = $this->options['credentials']['merchantNO'];
        $order->appSecret       = $this->options['credentials']['secretKey'];
        $order->currencyType    = $this->order->getCurrency()->getCode();
        $order->orderNo         = $this->options['orderPrefix']
            ? $this->options['orderPrefix'] . '_' . $this->order->getPaymentTransactionId()
            : $this->order->getPaymentTransactionId();
        $order->orderAmount     = $this->order->getCurrency()->roundValue(
            $this->order->getTotal()
        );

        $date = new \DateTime();
        $date->setTimestamp($this->order->getDate());
        $order->orderTime       = $date->format(static::DATE_TIME_FORMAT);

        $nowDate = new \DateTime();
        $order->sendTime        = $nowDate->format(static::DATE_TIME_FORMAT);

        $order->redirectUrl     = $this->options['urls']['returnUrl'];
        $order->pageUrl         = $this->getCheckoutUrl();
        $order->serverUrl       = $this->options['urls']['callbackUrl'];
        $order->shipper         = $this->options['fees']['shipper'];
        $order->tax             = $this->options['fees']['tax'];

        foreach ($this->order->getItems() as $item) {
            $adapter = new PilipayGoodAdapter($item);
            $good = $adapter->getResult();

            if ($good) {
                $order->addGood($good);
            }
        }

        return $order;
    }

    /**
     * Get mapped result
     *
     * @return PilipayOrder
     */
    public function getResult()
    {
        if (!$this->order) {
            throw new \Exception("You should set a Order for this adapter", 1);
        }

        return $this->process();
    }
}
