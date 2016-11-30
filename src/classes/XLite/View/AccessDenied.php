<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Access denied
 *
 * @ListChild (list="center")
 */
class AccessDenied extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'access_denied';

        return $list;
    }

    /**
     * Add NOINDEX in meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = '<meta name="robots" content="noindex,nofollow"/>';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isForceLogin()) {
            $list[] = 'js/access_denied.js';
        }

        return $list;
    }

    /**
     * Should we force login
     *
     * @return  boolean
     */
    protected function isForceLogin()
    {
        $data = \XLite\Core\Request::getInstance()->getNonFilteredData();

        return isset($data['target'])
            && in_array(
                $data['target'],
                $this->getForceLoginTargets()
            );
    }

    /**
     * List of target for which we force login
     *
     * @return  array
     */
    protected function getForceLoginTargets()
    {
        $list = \XLite\View\Tabs\Account::getAllowedTargets();

        return array_merge(
            $list,
            array(
                'order'
            )
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'access_denied.twig';
    }

    /**
     * Return sing in <a> tag
     *
     * @return string
     */
    protected function getLoginLinkContent()
    {
        $widget = $this->getChildWidget('\XLite\View\Button\PopupLoginLink', ['label' => 'please sign in']);

        return $widget->getContent();
    }

    /**
     * Return sing in <a> tag
     *
     * @return string
     */
    protected function getContactLinkContent()
    {
        if (\XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('CDev\ContactUs')) {
            $location = $this->buildURL('contact_us');
        } else {
            $email = \XLite\Core\Config::getInstance()->Company->site_administrator;
            $location = 'mailto:' . $email;
        }

        return $location;
    }

    /**
     * Is title visible
     *
     * @return bool
     */
    protected function isPageTitleVisible()
    {
        return false;
    }

    /**
     * Checks whether user is logged
     *
     * @return bool
     */
    protected function isLogged()
    {
        return \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Return default page title
     *
     * @return string
     */
    protected function getDefaultPageTitle()
    {
        return static::t('Access denied');
    }
}
