<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Invoice widget
 *
 * @ListChild (list="order.children", weight="30")
 */
class Invoice extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ORDER = 'order';

    /**
     * Shipping modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $shippingModifier;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/invoice/style.css';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        );
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/invoice/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder();
    }

    /**
     * Returns invoice title
     *
     * @return string
     */
    protected function getInvoiceTitle()
    {
        return static::t('Invoice X', array('id' => $this->getOrder()->getOrderNumber()));
    }

    /**
     * Returns order items
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getOrderItems()
    {
        return $this->getOrder()->getItems();
    }

    /**
     * Get order formatted subtotal
     *
     * @return string
     */
    protected function getOrderSubtotal()
    {
        $order = $this->getOrder();

        return static::formatInvoicePrice($order->getSubtotal(), $order->getCurrency(), true);
    }

    /**
     * Get order formatted total
     *
     * @return string
     */
    protected function getOrderTotal()
    {
        $order = $this->getOrder();

        return static::formatInvoicePrice($order->getTotal(), $order->getCurrency(), true);
    }

    /**
     * Get shipping modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getShippingModifier()
    {
        if (null === $this->shippingModifier) {
            $this->shippingModifier = $this->getOrder()
                ->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->shippingModifier;
    }

    /**
     * Get item description block columns count
     *
     * @return integer
     */
    protected function getItemDescriptionCount()
    {
        return 3;
    }

    /**
     * Get columns span
     *
     * @return integer
     */
    protected function getColumnsSpan()
    {
        return 4 + count($this->getOrder()->getItemsExcludeSurcharges());
    }

    /**
     * Get payment methods with instructions
     *
     * @return array
     */
    protected function getPaymentInstructions()
    {
        $list = array();

        foreach ($this->getOrder()->getVisiblePaymentMethods() as $method) {
            if ($method->getInstruction()) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Format invoice price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict
     *                                            (trailing zeroes and so on options) OPTIONAL
     *
     * @return string
     */
    protected function formatInvoicePrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        return static::formatPrice($value, $currency, $strictFormat);
    }

    // {{{ Surcharges

    /**
     * Get surcharge totals
     *
     * @return array
     */
    protected function getSurchargeTotals()
    {
        return $this->getOrder()->getSurchargeTotals();
    }

    /**
     * Get surcharge class name
     *
     * @param string $type      Surcharge type
     * @param array  $surcharge Surcharge
     *
     * @return string
     */
    protected function getSurchargeClassName($type, array $surcharge)
    {
        return 'order-modifier '
            . $type . '-modifier '
            . strtolower($surcharge['code']) . '-code-modifier';
    }

    /**
     * Format surcharge value
     *
     * @param array $surcharge Surcharge
     *
     * @return string
     */
    protected function formatSurcharge(array $surcharge)
    {
        return static::formatPrice(
            abs($surcharge['cost']),
            $this->getOrder()->getCurrency(),
            \XLite::ADMIN_INTERFACE !== \XLite\Core\Layout::getInstance()->getInterface()
        );
    }

    // }}}

    // {{{ Address

    /**
     * Return specific data for address entry. Helper.
     *
     * @param \XLite\Model\Address $address   Address
     * @param boolean              $showEmpty Show empty fields OPTIONAL
     *
     * @return array
     */
    protected function getAddressSectionData(\XLite\Model\Address $address, $showEmpty = false)
    {
        $data = parent::getAddressSectionData($address, $showEmpty);
        $result = array();

        $name = array(
            'title'     => isset($data['title'])     ? $data['title']     : null,
            'firstname' => isset($data['firstname']) ? $data['firstname'] : null,
            'lastname'  => isset($data['lastname'])  ? $data['lastname']  : null,
        );

        foreach ($data as $serviceName => $field) {
            switch ($serviceName) {
                case 'title':
                case 'firstname':
                case 'lastname':
                    $result += array_filter($name);
                    unset($data['title'], $data['firstname'], $data['lastname']);
                    break;

                default:
                    $result[$serviceName] = $field;
                    break;
            }
        }

        return $result;
    }

    /**
     * Return true if hidden email field should be displayed in the shipping address section
     * TODO: this is a hack to avoid misalign in address section. Need to be revised later
     *
     * @return boolean
     */
    protected function isDisplayShippingEmail()
    {
        $order = $this->getOrder();

        $shippingFields = $this->getAddressSectionData($order->getProfile()->getShippingAddress());
        $isAddressTypeEnabled = isset($shippingFields['type']);

        $billingFields = $order->isPaymentSectionVisible()
            ? $this->getAddressSectionData($order->getProfile()->getBillingAddress())
            : array();

        return $shippingFields
            && $billingFields
            && (
                count($shippingFields) < count($billingFields)
                || !$isAddressTypeEnabled
            );
    }

    // }}}

    // {{{ Mail

    /**
     * Returns orders full url in admin area
     *
     * @return string
     */
    protected function getOrderAdminUrl()
    {
        return \XLite\Core\Converter::buildFullURL(
            'order',
            '',
            array('order_number' => $this->getOrder()->getOrderNumber()),
            \XLite::getAdminScript()
        );
    }

    /**
     * Returns template for address field
     *
     * @param string $type        Address type
     * @param string $serviceName Field service name
     * @param array  $field       Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFiledTemplate($type, $serviceName, $field)
    {
        $method = 'getAddressField' . \XLite\Core\Converter::convertToCamelCase($serviceName) . 'Template';

        return method_exists($this, $method)
            ? call_user_func(array($this, $method), $type, $field)
            : 'order/invoice/parts/address_field.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldTitleTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/title.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldFirstnameTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/firstname.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldLastnameTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/lastname.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldStreetTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/street.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldCityTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/city.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldCountryCodeTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/country_code.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldStateIdTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/state_id.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldZipcodeTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/zipcode.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldPhoneTemplate($type, $field)
    {
        return 'order/invoice/parts/address_field/phone.twig';
    }

    /**
     * Returns address field template
     *
     * @param string $type  Address type
     * @param array  $field Address field returned by \XLite\View\AView#getAddressSectionData
     *
     * @return string
     */
    protected function getAddressFieldTypeTemplate($type, $field)
    {
        return \XLite\Model\Address::SHIPPING === $type
            ? 'order/invoice/parts/address_field/type.twig'
            : null;
    }

    // }}
}
