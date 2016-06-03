<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\FormField\Select;

/**
 * Profile selector widget
 */
class ProfileSelector extends \XLite\View\FormField\Select\Model\ProfileSelector
{
    /**
     * Widget param names
     */
    const PARAM_REVIEW = 'review';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_REVIEW => new \XLite\Model\WidgetParam\TypeObject(
                'Review', null, false
            ),
        );
    }

    /**
     * Defines the text value of the model
     *
     * @return string
     */
    protected function getTextValue()
    {
        $review = $this->getParam(static::PARAM_REVIEW);

        $name = $review ? $review->getReviewerName() : '';
        $email = $review ? $review->getEmail() : '';

        if (0 < intval($this->getValue())) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(intval($this->getValue()));

            if ($profile) {
                $email = $profile->getLogin();
                $name = $profile->getName(false) ?: $name;
            }
        }

        return $name . ($email ? ' &lt;' . $email . '&gt;' : '');
    }

    /**
     * Defines the name of the text value input
     *
     * @return string
     */
    protected function getTextName()
    {
        return $this->getParam(static::PARAM_NAME) . '_text';
    }
}
