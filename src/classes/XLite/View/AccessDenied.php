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
}
