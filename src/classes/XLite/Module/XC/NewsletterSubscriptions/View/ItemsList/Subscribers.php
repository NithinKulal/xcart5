<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View\ItemsList;

/**
 * Payment transactions items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Subscribers extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/NewsletterSubscriptions/items_list/styles.less';

        return $list;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\NewsletterSubscriptions\View\StickyPanel\Subscribers';
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
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('newsletter_subscribers'));
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'email' => array(
                static::COLUMN_NAME     => static::t('Email'),
                static::COLUMN_CREATE_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Email',
            ),
            'profile' => array(
                static::COLUMN_NAME     => static::t('Profile'),
                static::COLUMN_LINK    => 'profile',
            ),
        );
    }

    /**
     * Preprocess value for Discount column
     *
     * @param mixed                 $value  Value
     * @param array                 $column Column data
     * @param \XLite\Model\AEntity  $entity Entity
     *
     * @return string
     */
    protected function preprocessProfile($value, array $column, \XLite\Model\AEntity $entity)
    {
        return $value
            ? $value->getLogin()
            : '';
    }

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isLink($column, $entity);

        if ($result && $column[static::COLUMN_LINK] === 'profile') {
            $result = $entity->getProfile();
        }

        return $result;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $product Entity
     * @param array                $column  Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $subscriber, array $column)
    {
        $url = '';

        if ($column[static::COLUMN_LINK] === 'profile') {
            if ($subscriber->getProfile()) {
                $url = $this->buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    array('profile_id' => $subscriber->getProfile()->getProfileId())
                );
            } else {
                $url = null;
            }

        } else {
            $url = parent::buildEntityURL($subscriber, $column);
        }

        return $url;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber';
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
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
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
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\SimpleSearchPanel';
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return array(
            'target'    => 'newsletter_subscribers'
        );
    }

    /**
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            array(
                'emailOrLogin'    => array(
                    'condition'         => new \XLite\Model\SearchCondition\Expression\TypeComposite(
                        array(
                            new \XLite\Model\SearchCondition\Expression\TypeLike('email'),
                            new \XLite\Model\SearchCondition\Expression\TypeLike('profile.login'),
                        )
                    ),
                    'widget'            => array(
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS   => 'XLite\View\FormField\Input\Text',
                        \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER     => static::t('Email or login'),
                    ),
                ),
            )
        );
    }
}
