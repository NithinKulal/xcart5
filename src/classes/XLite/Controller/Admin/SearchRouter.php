<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Search router
 */
class SearchRouter extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return true if specified search code is allowed to currently logged in user
     *
     * @param string $code Search code
     *
     * @return boolean
     */
    public static function isSearchCodeAllowed($code)
    {
        $result = false;

        $permissions = static::getSearchCodePermissions();

        if (!empty($permissions[$code])) {
            $result = static::isSearchPermissionAllowed($permissions[$code]);
        }

        return $result;
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $result = parent::checkACL();

        if (!$result) {
            foreach (static::getSearchCodePermissions() as $code => $permissions) {
                if (static::isSearchPermissionAllowed($permissions)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get list of search code permissions
     *
     * @return array
     */
    protected static function getSearchCodePermissions()
    {
        return array(
            'product' => array(
                'manage catalog',
            ),
            'order' => array(
                'manage orders',
            ),
            'profile' => array(
                'manage users',
            ),
        );
    }

    /**
     * Check permissions from the list and return true if one of them is allowed
     *
     * @param array $permissions List of permissions
     *
     * @return boolean
     */
    protected static function isSearchPermissionAllowed($permissions)
    {
        $result = false;

        foreach ($permissions as $p) {
            if (\XLite\Core\Auth::getInstance()->isPermissionAllowed($p)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Search 
     * 
     * @return void
     */
    protected function doActionSearch()
    {
        $url = $this->defineRedirectURL();

        $this->setReturnURL($url ?: $this->buildURL(''));
    }

    /**
     * Define redirect URL 
     * 
     * @return string
     */
    protected function defineRedirectURL()
    {
        $request = $this->getPreprocessedRequest();

        switch ($request->code) {
            case 'product':
                $url = $this->buildURL(
                    'product_list',
                    null,
                    array(
                        'substring'   => $request->substring,
                        'fast_search' => true,
                    )
                );
                break;

            case 'order':
                $url = $this->buildURL(
                    'order_list',
                    null,
                    array(
                        'substring'   => $request->substring,
                        'fast_search' => true,
                    )
                );
                break;

            case 'profile':
                $url = $this->buildURL(
                    'profile_list',
                    null,
                    array(
                        'pattern'     => $request->substring,
                        'fast_search' => true,
                    )
                );
                break;

            default:
                $url = null;
        }

        \XLite\Core\Request::getInstance()->setcookie('XCartAdminHeaderSearchType', $request->code);

        return $url;
    }

    /**
     * Get preprocessed request data
     *
     * @return \XLite\Core\Request
     */
    protected function getPreprocessedRequest()
    {
        $request = \XLite\Core\Request::getInstance();

        $request->substring = trim($request->substring);

        if (!empty($request->substring)) {

            $data = explode(':', $request->substring);

            $data[0] = trim($data[0]);

            $codes = $this->getAllowedCodes();

            if (!empty($codes[$data[0]])) {
                $request->code = $codes[$data[0]];
                $request->substring = trim($data[1]);
            }
        }

        return $request;
    }

    /**
     * Get allowed search codes
     *
     * @return array
     */
    protected function getAllowedCodes()
    {
        return array(
            'p' => 'product',
            'o' => 'order',
            'u' => 'profile',
        );
    }
}
