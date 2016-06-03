<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Order\Admin;

/**
 * Order info sticky panel
 */
class Info extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Order info sticky panel must be visible anyway
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' always-visible';
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if (\XLite::getController()->isOrderEditable()) {
            $list['sendNotification'] = $this->getSendNotificationWidget();
            $list['recalculate'] = $this->getRecalculateButton();
        }

        return $list;
    }

    /**
     * Get send notification widget
     *
     * @return \Xlite\View\AView
     */
    protected function getSendNotificationWidget()
    {
        return $this->getWidget(
            array(
                'template' => 'order/page/parts/send_notification.twig',
                'checked'  => true,
            )
        );
    }

    /**
     * Get recalculate button
     *
     * @return \XLite\View\Button\Regular
     */
    protected function getRecalculateButton()
    {
        return $this->getWidget(
            array(
                'style'    => 'action recalculate',
                'label'    => $this->getRecalculateButtonLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getRecalculateButtonStyle(),
                \XLite\View\Button\Regular::PARAM_ACTION => 'recalculate',
            ),
            'XLite\View\Button\Regular'
        );
    }

    /**
     * Defines the label for the recalculate button
     *
     * @return string
     */
    protected function getRecalculateButtonLabel()
    {
        return static::t('Recalculate totals');
    }

    /**
     * Defines the style for the recalculate button
     *
     * @return string
     */
    protected function getRecalculateButtonStyle()
    {
        return 'regular-main-button';
    }

    /**
     * Get cell attributes
     *
     * @param integer           $idx    Cell index
     * @param string            $name   Cell name
     * @param \XLite\View\AView $button Button
     *
     * @return array
     */
    protected function getCellAttributes($idx, $name, \XLite\View\AView $button)
    {
        $attributes = parent::getCellAttributes($idx, $name, $button);

        if ('save' === $name) {
            $attributes['title'] = static::t(
                'The button is inactive either because no changes have been detected on the current page'
                . ' or because the order totals need to be recalculated before the order can be updated.'
            );
            $attributes['data-title'] = $attributes['title'];
        }

        return $attributes;
    }

    /**
     * Get cell class
     *
     * @param integer           $idx    Button index
     * @param string            $name   Button name
     * @param \XLite\View\AView $button Button
     *
     * @return string
     */
    protected function getCellClass($idx, $name, \XLite\View\AView $button)
    {
        $class = parent::getCellClass($idx, $name, $button);
        if ('save' === $name) {
            $class = trim($class . ' disabled');
        }

        return $class;
    }
}
