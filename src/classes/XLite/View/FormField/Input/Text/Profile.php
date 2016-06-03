<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Autocomplete profile field
 */
class Profile extends \XLite\View\FormField\Input\Text\Base\Autocomplete
{
    /**
     * Widget params
     */
    const PARAM_PROFILE_ID = 'profileId';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PROFILE_ID => new \XLite\Model\WidgetParam\TypeInt('Profile Id', 0),
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/input/autocomplete/profile.js';

        return $list;
    }

    /**
     * Get dictionary name
     *
     * @return string
     */
    protected function getDictionary()
    {
        return 'profiles';
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getValueByProfileId() ?: parent::getValue();
    }

    /**
     * Get default profile value
     *
     * @return string
     */
    protected function getValueByProfileId()
    {
        $result = null;

        $profileId = $this->getParam(static::PARAM_PROFILE_ID);

        if (0 < $profileId) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
            if ($profile) {
                $result = $profile->getName() . ' (' . $profile->getLogin() . ')';
            }
        }

        return $result;
    }
}
