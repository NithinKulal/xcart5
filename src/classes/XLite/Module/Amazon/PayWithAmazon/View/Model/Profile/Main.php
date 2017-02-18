<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\Main
 * @Decorator\Depend ("!CDev\SocialLogin")
 */
class Main extends \XLite\View\Model\Profile\Main implements \XLite\Base\IDecorator
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if ($this->getModelObject()->isSocialProfile()) {
            unset($this->mainSchema['password'], $this->mainSchema['password_conf']);
        }

        return parent::getFormFieldsForSectionMain();
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $params = array(
            'text_before' => static::t('Or register with'),
            'buttonStyle' => 'icon',
        );

        $widget = $this->getWidget($params, 'XLite\Module\Amazon\PayWithAmazon\View\Login\Widget');
        if ($widget->isVisible()) {
            $result['social-login'] = $widget;
        }

        return $result;
    }
}
