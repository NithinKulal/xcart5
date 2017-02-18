<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Message;

use XLite\Module\XC\Concierge\Core\AMessage;

class Identify extends AMessage
{
    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $companyId;

    /**
     * @var \XLite\Model\Profile
     */
    protected $profile;

    /**
     * @var \XLite\Core\CommonCell
     */
    protected $config;

    /**
     * Identify constructor.
     *
     * @param string                 $userId
     * @param string                 $companyId
     * @param \XLite\Model\Profile   $profile
     * @param \XLite\Core\CommonCell $config
     */
    public function __construct($userId, $companyId, $profile, $config)
    {
        $this->userId    = $userId;
        $this->profile   = $profile;
        $this->config    = $config;
        $this->companyId = $companyId;
    }

    public function getType()
    {
        return static::TYPE_IDENTIFY;
    }

    public function getArguments()
    {
        $result = [];

        // The database ID for the user.
        $userId = $this->getUserId();
        if ($userId) {
            $result[] = $userId;
        }

        // A dictionary of traits you know about the user, like their email or name.
        $result[] = $this->getTraits() ?: new \stdClass();

        // A dictionary of options.
        $result[] = $this->getOptions();

        return $result;
    }

    /**
     * https://segment.com/docs/integrations/intercom/
     * @return array
     */
    protected function getTraits()
    {
        $result = [];

        $profile = $this->getProfile();
        if ($profile) {
            $result['email']     = $profile->getLogin();
            $result['name']      = $profile->getName();
            $result['createdAt'] = $profile->getAdded();
        }

        $config = $this->getConfig();
        if ($config) {
            $company = [];

            $company['id']        = $this->getCompanyId();
            $company['name']      = $config->Company->company_name;
            $company['createdAt'] = $config->Version->timestamp;

            $result['company'] = $company;
        }

        $result['host'] = $_SERVER['HTTP_HOST'];

        return $result;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \XLite\Core\CommonCell $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
}
