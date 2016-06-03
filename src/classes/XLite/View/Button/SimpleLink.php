<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Link as link
 */
class SimpleLink extends \XLite\View\Button\Link
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/link.twig';
    }

    /**
     * HREF and target _blank specific attributes are added
     *
     * @return array
     */
    protected function getLinkAttributes()
    {
        if (!$this->getParam(self::PARAM_JS_CODE)) {
            $result = array(
                'href' => $this->getLocationURL(),
            );

            if ($this->getParam(static::PARAM_BLANK)) {
                $result['target'] = '_blank';
            }
        } else {
            $result = parent::getLinkAttributes();
        }

        return $result;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return $this->getParam(static::PARAM_STYLE)
            . ($this->isDisabled() ? ' disabled' : '');
    }

    /**
     * Defines the button specific attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {
        $list = parent::getButtonAttributes();

        unset($list['type']);

        return $list;
    }
}
