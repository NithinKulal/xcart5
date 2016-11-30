<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Subscriptions;

/**
 * MailChimp mail lists
 */
class ProfileSubscriptions extends \XLite\Module\XC\MailChimp\View\ItemsList\Subscriptions\AMailChimpSubscriptions
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = $this->getDir() . '/subscriptions_list.less';

        return $return;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        $return[] = $this->getDir() . '/subscriptions_list.js';

        return $return;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/subscriptions_list.twig';
    }

    /**
     * Check if profile is subscribed to the MailChimp list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list    MailChimp list
     * @param \XLite\Model\Profile|null                      $profile Profile
     *
     * @return boolean
     */
    protected function checkIfSubscribed(\XLite\Module\XC\MailChimp\Model\MailChimpList $list, $profile)
    {
        return $list->isProfileSubscribed($profile);
    }

    /**
     * Get checkbox name from list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName MailChimp list
     *
     * @return string
     */
    protected function getGroupCheckboxName(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName)
    {
        return sprintf(
            'interest[%s][%s]',
            $groupName->getGroup()->getList()->getId(),
            $groupName->getId()
        );
    }

    /**
     * Get checkbox ID from list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName MailChimp list
     *
     * @return string
     */
    protected function getGroupCheckboxId(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName)
    {
        return sprintf(
            'interest-%s-%s',
            $groupName->getGroup()->getList()->getId(),
            $groupName->getId()
        );
    }

    /**
     * Check if profile is subscribed to the MailChimp list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName   $groupName      MailChimp list
     * @param \XLite\Model\Profile|null                             $profile        Profile
     *
     * @return boolean
     */
    protected function checkIfSubscribedToGroup(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName, $profile)
    {
        return (isset($profile) && !\XLite\Core\Auth::getInstance()->checkProfile($profile))
            ? true
            : $groupName->isProfileChecked($profile);
    }
}
