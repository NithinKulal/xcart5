<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Moneybookers\Controller\Admin;

/**
 * Skrill settings controller
 */
class MoneybookersSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'method_id');

    /**
     * Module string name for payment methods
     */
    const MODULE_NAME = 'CDev_Moneybookers';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Skrill settings');
    }

    /**
     * Validate email
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $email = \XLite\Core\Request::getInstance()->email;
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $secretWord = trim(\XLite\Core\Request::getInstance()->secret_word);

        // Save settings
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Moneybookers',
                'name'     => 'email',
                'value'    => $email,
            )
        );
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Moneybookers',
                'name'     => 'id',
                'value'    => $id,
            )
        );
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Moneybookers',
                'name'     => 'secret_word',
                'value'    => $secretWord,
            )
        );
    }

    /**
     * Set order id prefix
     *
     * @return void
     */
    protected function doActionSetOrderPrefix()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Moneybookers',
                'name'     => 'prefix',
                'value'    => trim(\XLite\Core\Request::getInstance()->prefix),
            )
        );
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = $this->getMethodId()
                ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($this->getMethodId())
                : null;
        }

        return $this->paymentMethod && static::MODULE_NAME === $this->paymentMethod->getModuleName()
            ? $this->paymentMethod
            : null;
    }

    /**
     * Get method id from request
     *
     * @return integer
     */
    public function getMethodId()
    {
        return \XLite\Core\Request::getInstance()->method_id;
    }
}
