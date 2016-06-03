<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Payment method selection  controller
 */
class PaymentMethodSelection extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('search'));
    }

    /**
     * Constructor
     *
     * @param array $params Constructor parameters
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . '_' . md5(microtime(true));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        switch ($this->getPaymentType()) {
            case \XLite\Model\Payment\Method::TYPE_ALTERNATIVE:
                $result = static::t('Add alternative payment method');
                break;

            case \XLite\Model\Payment\Method::TYPE_OFFLINE:
                $result = static::t('Add offline payment method');
                break;

            default:
                $result = '';
                break;
        }

        return $result;
    }

    /**
     * Return payment methods type which is provided to the widget
     *
     * @return string
     */
    protected function getPaymentType()
    {
        return \XLite\Core\Request::getInstance()->{\XLite\View\Button\Payment\AddMethod::PARAM_PAYMENT_METHOD_TYPE};
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = parent::getSearchParams();

        $searchParams[\XLite\View\Pager\APager::PARAM_PAGE_ID] = 1;

        return $searchParams;
    }

    /**
     * Return true if 'Install' link should be displayed
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function isDisplayInstallModuleLink(\XLite\Model\Payment\Method $method)
    {
        return $method->getModuleName()
            && !$this->isModuleEnabled($method)
            && !$this->isDisplayInstallModuleButton($method);
    }

    /**
     * Return true if payment method's module is enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method model object
     *
     * @return boolean
     */
    protected function isModuleEnabled(\XLite\Model\Payment\Method $method)
    {
        $result = true;

        $result = (bool)$method->getProcessor();

        if ($method->getModuleEnabled() != $result) {
            $method->setModuleEnabled($result);
            $method->update();
        }

        return $result;
    }

    /**
     * Return true if 'Install' button should be displayed
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function isDisplayInstallModuleButton(\XLite\Model\Payment\Method $method)
    {
        $result = false;

        if ($method->getModuleName() && !$method->isModuleInstalled()) {
            $module = $method->getModule();
            $result = $module
                && $module->getFromMarketplace()
                && $module->canEnable(false)
                && (
                    $module->isFree()
                    || $module->isPurchased()
                )
                && $this->isLicenseAllowed($module);

        }

        return $result;
    }

    /**
     * Check if module license is available and allowed
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isLicenseAllowed(\XLite\Model\Module $module)
    {
        return \XLite\Model\Module::NOT_XCN_MODULE == $module->getXcnPlan()
            || (\XLite\Model\Module::NOT_XCN_MODULE < $module->getXcnPlan() && 1 == $module->getEditionState());
    }

    /**
     * Returns URL to payment module
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getPaymentModuleURL(\XLite\Model\Payment\Method $method)
    {
        $result = '';

        if ($method->isModuleInstalled()) {

            // Payment module is installed

            $result = $method->getModule()->getInstalledURL();

        } else {

            // Payment module is not installed
            $module = $method->getModule();
            if ($module) {
                $result = $module->getMarketplaceURL();
            }
        }

        return $result;
    }

    /**
     * Get message on empty search results
     *
     * @return string
     */
    public function getNoPaymentMethodsFoundMessage()
    {
        $params = $this->getSearchParams();

        $request = \XLite\Core\Request::getInstance();

        $paramCountry = \XLite\View\ItemsList\Model\Payment\OnlineMethods::PARAM_COUNTRY;
        $paramSubstring = \XLite\View\ItemsList\Model\Payment\OnlineMethods::PARAM_SUBSTRING;

        if (!empty($request->{$paramCountry})) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(
                array(
                    'code' => $request->{$paramCountry}
                )
            );
        }

        return static::t(
            'No payment methods found based on the selected criteria',
            array(
                'substring' => $request->{$paramSubstring},
                'country'   => !empty($country) ? $country->getCountry() : static::t('All countries'),
            )
        );
    }
}
