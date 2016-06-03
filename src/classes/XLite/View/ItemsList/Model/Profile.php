<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Profiles items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Profile extends \XLite\View\ItemsList\Model\Table
{
    /**
     * List of search params for this widget (cache)
     *
     * @var array
     */
    protected $searchParams;

    /**
     * Widget param names
     */
    const PARAM_PATTERN         = 'pattern';
    const PARAM_USER_TYPE       = 'user_type';
    const PARAM_MEMBERSHIP      = 'membership';
    const PARAM_COUNTRY         = 'country';
    const PARAM_STATE           = 'state';
    const PARAM_CUSTOM_STATE    = 'customState';
    const PARAM_ADDRESS         = 'address';
    const PARAM_PHONE           = 'phone';
    const PARAM_DATE_TYPE       = 'date_type';
    const PARAM_DATE_PERIOD     = 'date_period';
    const PARAM_DATE_RANGE      = 'dateRange';
    const PARAM_STATUS          = 'status';
    const PARAM_LOGIN           = 'login';

    /**
     * Allowed sort criterion
     */
    const SORT_BY_MODE_LOGIN        = 'p.login';
    const SORT_BY_MODE_NAME         = 'fullname';
    const SORT_BY_MODE_ACCESS_LEVEL = 'p.access_level';
    const SORT_BY_MODE_CREATED      = 'p.added';
    const SORT_BY_MODE_LAST_LOGIN   = 'p.last_login';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        $this->sortByModes += array(
            static::SORT_BY_MODE_LOGIN          => 'Login/Email',
            static::SORT_BY_MODE_NAME           => 'Name',
            static::SORT_BY_MODE_ACCESS_LEVEL   => 'Access level',
            static::SORT_BY_MODE_CREATED        => 'Created',
            static::SORT_BY_MODE_LAST_LOGIN     => 'Last login',
        );

        parent::__construct($params);
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('profile_list'));
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'profile_list';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Profile\Admin\Main';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'login' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Login/E-mail'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'profile',
                static::COLUMN_SORT     => static::SORT_BY_MODE_LOGIN,
                static::COLUMN_ORDERBY  => 100,
            ),
            'name' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'address_book',
                static::COLUMN_MAIN     => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_NAME,
                static::COLUMN_ORDERBY  => 200,
            ),
            'access_level' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Access level'),
                static::COLUMN_SORT     => static::SORT_BY_MODE_ACCESS_LEVEL,
                static::COLUMN_ORDERBY  => 300,
            ),
            'orders_count' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Orders'),
                static::COLUMN_TEMPLATE => 'profiles/parts/cell/orders.twig',
                static::COLUMN_ORDERBY  => 400,
            ),
            'added' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Created'),
                static::COLUMN_SORT     => static::SORT_BY_MODE_CREATED,
                static::COLUMN_ORDERBY  => 500,
            ),
            'last_login' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Last login'),
                static::COLUMN_SORT     => static::SORT_BY_MODE_LAST_LOGIN,
                static::COLUMN_ORDERBY  => 600,
            ),
        );
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_LAST_LOGIN;
    }

    /**
     * getSortOrderModeDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Profile';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'profiles/style.css';

        return $list;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('profile', null, array('mode' => 'register'));
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add user';
    }

    // {{{ Behaviors

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        // Admin user cannot remove own account
        return parent::isAllowEntityRemove($entity)
            && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() !== $entity->getProfileId();
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' profiles';
    }

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if ('access_level' == $column[static::COLUMN_CODE] && $entity && $entity->getAnonymous()) {
            $class = trim($class . ' anonymous');
        }

        return $class;
    }
    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\Profile';
    }

    // {{{ Search

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Profile::SEARCH_PATTERN      => static::PARAM_PATTERN,
            \XLite\Model\Repo\Profile::SEARCH_USER_TYPE    => static::PARAM_USER_TYPE,
            \XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP   => static::PARAM_MEMBERSHIP,
            \XLite\Model\Repo\Profile::SEARCH_COUNTRY      => static::PARAM_COUNTRY,
            \XLite\Model\Repo\Profile::SEARCH_STATE        => static::PARAM_STATE,
            \XLite\Model\Repo\Profile::SEARCH_CUSTOM_STATE => static::PARAM_CUSTOM_STATE,
            \XLite\Model\Repo\Profile::SEARCH_ADDRESS      => static::PARAM_ADDRESS,
            \XLite\Model\Repo\Profile::SEARCH_PHONE        => static::PARAM_PHONE,
            \XLite\Model\Repo\Profile::SEARCH_DATE_TYPE    => static::PARAM_DATE_TYPE,
            \XLite\Model\Repo\Profile::SEARCH_DATE_PERIOD  => static::PARAM_DATE_PERIOD,
            \XLite\Model\Repo\Profile::SEARCH_DATE_RANGE   => static::PARAM_DATE_RANGE,
            \XLite\Model\Repo\Profile::SEARCH_STATUS       => static::PARAM_STATUS,
            \XLite\Model\Repo\Profile::SEARCH_AND_LOGIN    => static::PARAM_LOGIN,
        );
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
            static::PARAM_PATTERN         => new \XLite\Model\WidgetParam\TypeString('Pattern', ''),
            static::PARAM_USER_TYPE       => new \XLite\Model\WidgetParam\TypeSet('Type', '', false, array('', 'A', 'C')),
            static::PARAM_MEMBERSHIP      => new \XLite\Model\WidgetParam\TypeString('Membership', ''),
            static::PARAM_COUNTRY         => new \XLite\Model\WidgetParam\TypeString('Country', ''),
            static::PARAM_STATE           => new \XLite\Model\WidgetParam\TypeInt('State', null),
            static::PARAM_CUSTOM_STATE    => new \XLite\Model\WidgetParam\TypeString('State name (custom)', ''),
            static::PARAM_ADDRESS         => new \XLite\Model\WidgetParam\TypeString('Address', ''),
            static::PARAM_PHONE           => new \XLite\Model\WidgetParam\TypeString('Phone', ''),
            static::PARAM_DATE_TYPE       => new \XLite\Model\WidgetParam\TypeSet('Date type', '', false, array('', 'R', 'L')),
            static::PARAM_DATE_PERIOD     => new \XLite\Model\WidgetParam\TypeSet('Date period', '', false, array('', 'M', 'W', 'D', 'C')),
            static::PARAM_DATE_RANGE      => new \XLite\Model\WidgetParam\TypeString('Date range', null),
            static::PARAM_STATUS          => new \XLite\Model\WidgetParam\TypeString('Status', null),
            static::PARAM_LOGIN           => new \XLite\Model\WidgetParam\TypeString('Login', null),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Get permitted user types
     * 
     * @return array Array of ids
     */
    protected function getPermittedUserTypes(){
        $permittedUserTypes = array('N', 'C');

        if (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $adminRoles = array_map(
                function($role){
                    return $role->getId();
                },
                \XLite\Core\Database::getRepo('XLite\Model\Role')->findAll()
            );
            $permittedUserTypes = array_merge($adminRoles, $permittedUserTypes);
        }

        return $permittedUserTypes;
    }
    
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\XLite\Model\Repo\Profile::P_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                $paramValue = trim($paramValue);
            }

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }
        $result->{\XLite\Model\Repo\Profile::SEARCH_ONLY_REAL} = true;

        if ($result->{\XLite\Model\Repo\Profile::SEARCH_COUNTRY}) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find(
                $result->{\XLite\Model\Repo\Profile::SEARCH_COUNTRY}
            );
            if (!$country || !$country->hasStates()) {
                $result->{\XLite\Model\Repo\Profile::SEARCH_STATE} = null;
            }
            if (!$country || $country->hasStates()) {
                $result->{\XLite\Model\Repo\Profile::SEARCH_CUSTOM_STATE} = null;
            }
        }

        if (filter_var($result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN}, FILTER_VALIDATE_EMAIL)) {
            $result->{\XLite\Model\Repo\Profile::SEARCH_AND_LOGIN} = $result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN};
            $result->{\XLite\Model\Repo\Profile::SEARCH_PATTERN} = null;
        } else {
            $result->{\XLite\Model\Repo\Profile::SEARCH_AND_LOGIN} = null;
        }

        if ($result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP}) {
            $membershipCondition = $result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP};

            if (is_array($membershipCondition)) {
                $membershipIds = array_reduce(
                    $membershipCondition,
                    function ($carry, $item) {
                        $item = explode('_', $item);
                        if (2 == count($item)) {
                            $carry[$item[0]][] = $item[1];
                        }

                        return $carry;
                    },
                    array()
                );

                $result->{\XLite\Model\Repo\Profile::SEARCH_MEMBERSHIP} = $membershipIds;
            }
        }

        $permittedUserTypes = $this->getPermittedUserTypes();
        if (
            isset($result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0]) 
            && '' !== $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0]
        ) {
            $userTypes = array_filter(
                $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE},
                function($type) use ($permittedUserTypes){
                    return in_array($type, $permittedUserTypes);
                }
            );
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $userTypes;

        } elseif (!\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $permittedUserTypes;
        }

        return $result;
    }

    // }}}

    /**
     * Preprocess added
     *
     * @param integer              $date   Date
     * @param array                $column Column data
     * @param \XLite\Model\Profile $entity Profile
     *
     * @return string
     */
    protected function preprocessAdded($date, array $column, \XLite\Model\Profile $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Unknown');
    }

    /**
     * Preprocess last login
     *
     * @param integer              $date   Date
     * @param array                $column Column data
     * @param \XLite\Model\Profile $entity Profile
     *
     * @return string
     */
    protected function preprocessLastLogin($date, array $column, \XLite\Model\Profile $entity)
    {
        return $date
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Never');
    }

    /**
     * Preprocess access level
     *
     * @param integer              $accessLevel Access level
     * @param array                $column      Column data
     * @param \XLite\Model\Profile $entity      Profile
     *
     * @return string
     */
    protected function preprocessAccessLevel($accessLevel, array $column, \XLite\Model\Profile $entity)
    {
        if (0 == $accessLevel) {
            $result = $entity->getAnonymous()
                ? static::t('Anonymous')
                : static::t('Customer');

            if ($entity->getMembership()
                || $entity->getPendingMembership()
            ) {
                $result .= ' (';
            }

            if ($entity->getMembership()) {
                $result .= $entity->getMembership()->getName();
            }

            if ($entity->getPendingMembership()) {
                if ($entity->getMembership()) {
                    $result .= ', ';
                }

                $result .= static::t('requested for') . ' '
                    . $entity->getPendingMembership()->getName();
            }

            if ($entity->getMembership()
                || $entity->getPendingMembership()
            ) {
                $result .= ')';
            }

        } else {
            $result = static::t('Administrator');
        }

        return $result;
    }
}
