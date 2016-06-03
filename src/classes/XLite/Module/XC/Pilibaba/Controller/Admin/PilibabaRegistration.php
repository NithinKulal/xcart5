<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Controller\Admin;

/**
 * Pilibaba settings page controller
 */
class PilibabaRegistration extends \XLite\Controller\Admin\AAdmin
{
    static protected $options = array(
        'live'   => array(
            'merchantNo'    => '0210000451',
            'privateKey'    => 'cuej80z6',
        ),
        'test'   => array(
            'merchantNo'    => '0210000451',
            'privateKey'    => 'cuej80z6',
        ),
    );

    /**
     * Get options
     *
     * @param string $name Option name
     *
     * @return array
     */
    protected static function getOption($name)
    {
        $mode = static::getMode();

        return static::$options[$mode][$name];
    }

    /**
     * Get mode: test/live
     *
     * @return string
     */
    protected static function getMode()
    {
        $method = \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();

        return $method
            ? $method->getSetting('mode')
            : 'test';
    }

    /**
     * get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Pilibaba registration');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\Pilibaba\View\Model\Registration';
    }

    /**
     * Prepare data for registration request
     *
     * @param mixed     $requestData Request data
     *
     * @return array
     */
    protected function getDataForRegistrationRequest($requestData)
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        $logisticsPartOfAppSecret = null;

        $data = array(
            'platformNo'    => static::getOption('merchantNo'),
            'email'         => $requestData['email'],
            'password'      => strtoupper(md5($requestData['password'])),
            'currency'      => $requestData['currency'],
        );

        if ($requestData['warehouse'] && $requestData['warehouse'] !== 'others') {
            $warehouse = unserialize(
                base64_decode($requestData['warehouse'])
            );

            if ($warehouse && $warehouse->id !== null) {
                $data['logistics'] = $warehouse->id;
                $logisticsPartOfAppSecret = $warehouse->id;
            }

        }
        if(!isset($data['logistics'])
            && $requestData['others_country']
        ) {
            $country = \XLite\Core\Database::getRepo('\XLite\Model\Country')
                ->findOneByCode($requestData['others_country']);

            if ($country) {
                $data['countryCode']        = $country->getCode3();
                $logisticsPartOfAppSecret   = $country->getCode3();
            }

        }

        if (!isset($data['logistics']) && !isset($data['countryCode'])) {
            throw new \Exception("Error Processing Request", 1);
        }

        $sign = $logisticsPartOfAppSecret . static::getOption('merchantNo') . static::getOption('privateKey')
            . $data['currency'] . $data['email'] . $data['password'];
        $data['appSecret'] = strtoupper(md5($sign));

        return $data;
    }

    /**
     * Check response
     *
     * @param $response Response
     *
     * @return array
     */
    protected function parseResponse($response)
    {
        if (!$response || 200 != $response->code) {
            throw new \Exception("Request was not completed", 1);
        }
        $responseData = json_decode($response->body);
        if ($responseData->code === "1" && $responseData->message) {
            throw new \Exception($responseData->message, 1);
        }
        if ($responseData->data === null
            || !isset($responseData->data->merchantNo)
            || !isset($responseData->data->privateKey)
        ) {
            throw new \Exception("Request has wrong structure", 1);
        }

        return $responseData;
    }

    /**
     * Save settings
     *
     * @param array $requestData    Request data
     * @param mixed $responseData   Response data
     *
     * @return void
     */
    protected function saveSettings($requestData, $responseData)
    {
        $method = \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();

        $method->setSetting('merchantNO',   $responseData->data->merchantNo);
        $method->setSetting('secretKey',    $responseData->data->privateKey);
        $method->setSetting('currency',     $requestData['currency']);
        $method->setSetting('warehouse',    $requestData['warehouse']);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Send request to PB API to register new merchant
     *
     * @return void
     */
    protected function doActionRegistrationRequest()
    {
        $url = static::getMode() === 'test'
            ? 'http://preen.pilibaba.com/autoRegist'
            : 'http://en.pilibaba.com/autoRegist';

        try {
            $requestData = \XLite\Core\Request::getInstance()->getData();
            $prepareddata = $this->getDataForRegistrationRequest($requestData);

            $request = new \XLite\Core\HTTP\Request($url);
            $request->verb = 'post';
            $request->setHeader('Content-type', 'application/json');
            $request->body = json_encode($prepareddata);

            $response = $request->sendRequest();

            if ($request->getErrorMessage()) {
                throw new \Exception($request->getErrorMessage(), 1);
            }

            $responseData = $this->parseResponse($response);
            $this->saveSettings($requestData, $responseData);
            $this->setReturnUrl($this->buildURL('pilibaba_settings'));
            \XLite\Core\TopMessage::addInfo('Registration process is complete');
        } catch (\Exception $e) {
            \XLite\Core\TopMessage::addError($e->getMessage());
        }
    }
}
