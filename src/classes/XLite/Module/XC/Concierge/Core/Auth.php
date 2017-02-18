<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core;

use XLite\Module\XC\Concierge\Core\Message\Identify;
use XLite\Module\XC\Concierge\Core\Message\Reset;
use XLite\Module\XC\Concierge\Core\Track\LoginFailed;
use XLite\Module\XC\Concierge\Core\Track\Track;

/**
 * Auth
 */
abstract class Auth extends \XLite\Core\Auth implements \XLite\Base\IDecorator
{
    /**
     * Logs in user to cart
     *
     * @param string $login      User's login
     * @param string $password   User's password
     * @param string $secureHash Secret token OPTIONAL
     *
     * @return \XLite\Model\Profile|integer
     */
    public function login($login, $password, $secureHash = null)
    {
        $result = parent::login($login, $password, $secureHash);

        Mediator::getInstance()->initOptions();
        if (is_object($result) && $result instanceof \XLite\Model\Profile) {
            //$config = \XLite\Core\Config::getInstance();
            //Mediator::getInstance()->addMessage(
            //    new Identify($this->getConciergeUserId(), $this->getConciergeCompanyId(), $result, $config)
            //);
            Mediator::getInstance()->addMessage(new Track('Logged In'));

        } else {
            Mediator::getInstance()->addMessage(new LoginFailed($result));
        }

        return $result;
    }

    public function logoff()
    {
        parent::logoff();

        Mediator::getInstance()->addMessage(new Track('Logged Out'));
        //Mediator::getInstance()->addMessage(new Reset());
    }

    /**
     * @return string|null
     */
    public function getConciergeUserId()
    {
        $externalUserId = $this->getExternalConciergeUserId();
        $profile = $this->getProfile();

        if ($profile) {
            $userId = $profile->getConciergeUserId();
            if (!$userId) {
                $userId = $externalUserId ?: $this->generateConciergeUserId($profile);
                $profile->setConciergeUserId($userId);
            }

            return $userId;

        } else {

            return $externalUserId;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getConciergeCompanyId()
    {
        $companyId = \XLite\Core\Config::getInstance()->XC->Concierge->company_id;
        if (!$companyId) {
            $companyId = $this->generateConciergeCompanyId();
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'company_id',
                    'value'    => $companyId,
                ]
            );
        }

        return $companyId;
    }

    /**
     * @return string|null
     */
    protected function getExternalConciergeUserId()
    {
        $userId = \XLite\Core\Request::getInstance()->segmentUserId
            ?: $companyId = \XLite\Core\Config::getInstance()->XC->Concierge->user_id;

        if ($userId) {
            \XLite\Core\Session::getInstance()->segmentUserId = $userId;
        }

        return \XLite\Core\Session::getInstance()->segmentUserId;
    }

    /**
     * @return string
     */
    protected function generateConciergeUserId()
    {
        $profile = $this->getProfile();

        return $profile->getLogin();
    }

    /**
     * @return string
     */
    protected function generateConciergeCompanyId()
    {
        $authCode = \Includes\Utils\ConfigParser::getOptions(['installer_details', 'auth_code']);
        $secretKey = \Includes\Utils\ConfigParser::getOptions(['installer_details', 'shared_secret_key']);

        return $authCode
            ? md5($authCode . $secretKey . \LC_START_TIME)
            : md5(\LC_START_TIME);
    }
}
