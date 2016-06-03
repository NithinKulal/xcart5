<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Profile;

/**
 * Profile
 */
class AProfile extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget parameters
     */
    const PARAM_SHOW_ANY_OPTION = 'showAnyOption';

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();
        if ($this->getParam(static::PARAM_SHOW_ANY_OPTION)) {
            $list = array('' => static::t('Any profile')) + $list;
        }
        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SHOW_ANY_OPTION => new \XLite\Model\WidgetParam\TypeBool('Display \'Any profile\' option', false),
        );
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Profile')->findAll() as $profile) {
            $list[$profile->getProfileId()] = $profile->getLogin();
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        return parent::setCommonAttributes($attrs)
            + array(
                'size' => 1,
            );
    }
}
