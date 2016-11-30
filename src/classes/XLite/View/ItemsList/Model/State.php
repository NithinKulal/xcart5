<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * States items list
 */
class State extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Search parameter name
     */
    const PARAM_COUNTRY_CODE = 'country_code';


    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'states/css/style.css';

        return $list;
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
        return 'states';
    }

    /**
     * Get search panel widget class
     *
     * Disable the search form for states (XCN-2775)
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return null;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'state' => array (
                static::COLUMN_NAME   => \XLite\Core\Translation::lbl('State'),
                static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => array('required' => true),
                static::COLUMN_ORDERBY  => 100,
            ),
            'code' => array (
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Code'),
                static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => array('required' => true),
                static::COLUMN_ORDERBY  => 200,
            )
        );
        if ($this->getValidCountry()->hasRegions()) {
            $columns['region'] = array (
                static::COLUMN_NAME      => '',
                static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Select\Region',
                static::COLUMN_PARAMS => array(
                    'required'  => false,
                    'country'   => $this->getValidCountry()->getCode()
                ),
                static::COLUMN_ORDERBY  => 300,
            );
        }
        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\State';
    }

    /**
     * Return true if widget can be displayed
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getValidCountry();
    }

    /**
     * Check if country is valid and return country object
     *
     * @return \XLite\Model\Country
     */
    protected function getValidCountry()
    {
        $countryCode = $this->getCountryCode();

        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        return $country;
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
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = null;

        $country = $this->getValidCountry();

        if (!$country) {
            \XLite\Core\TopMessage::addError(
                'State cannot be created with unknown country code X',
                array('code' => $this->getCountryCode())
            );

        } else {
            $entity = parent::createEntity();
            $entity->setCountry($country);
        }

        return $entity;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add state';
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
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X states have been removed', array('count' => $count));
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X states have been successfully created', array('count' => $count));
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' states' . ' states-' . $this->getCountryCode();
    }

    /**
     * Get pager parameters
     *
     * @return array
     */
    protected function getPagerParams()
    {
        $params = parent::getPagerParams();

        $params[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE] = 50;

        return $params;
    }

    /**
     * Get current country code
     *
     * @return string
     */
    protected function getCountryCode()
    {
        return \XLite::getController()->getCountryCode();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\State::P_COUNTRY_CODE  => static::PARAM_COUNTRY_CODE,
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
            static::PARAM_COUNTRY_CODE  => new \XLite\Model\WidgetParam\TypeString('Country', $this->getCountryCode()),
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
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                static::PARAM_COUNTRY_CODE => $this->getParam(static::PARAM_COUNTRY_CODE)
            )
        );
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $validated = parent::prevalidateNewEntity($entity);

        if (!$entity->getCountry()) {
            $validated = false;
        }

        if ($validated) {
            $exists = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndCode($entity->getCountry()->getCode(), $entity->getCode());
            if ($exists){
                \XLite\Core\TopMessage::addWarning(
                    'There is already state with code {{code}} in {{country}}',
                    [
                        'code' => $entity->getCode(),
                        'country' => $entity->getCountry()->getCountry(),
                    ]
                );
                $validated = false;
            }
        }

        return $validated;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\State\Admin\Search';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $result = parent::getCommonParams();

        $result['country_code'] = $this->getCountryCode();

        return $result;
    }
}
